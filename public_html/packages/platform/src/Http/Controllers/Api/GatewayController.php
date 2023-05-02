<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Platform\Http\Controllers\Api;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ControllerDispatcher;
use MetaFox\Platform\Exceptions\NotFoundApiVersionException;
use MetaFox\Platform\Facades\ResourceGate;

class GatewayController extends Controller
{
    /** @var Request */
    protected $request;

    /** @var ControllerDispatcher */
    protected $dispatcher;

    /** @var Container */
    protected $container;

    /** @var string[] */
    protected $controllers = [
        'v1'   => '',
        'v1.1' => '',
    ];

    /**
     * ApiGatewayController constructor.
     *
     * @param Request              $request
     * @param ControllerDispatcher $dispatcher
     * @param Container            $container
     */
    public function __construct(
        Request $request,
        ControllerDispatcher $dispatcher,
        Container $container
    ) {
        $this->request = $request;
        $this->dispatcher = $dispatcher;
        $this->container = $container;
    }

    /**
     * @return mixed
     * @throws BindingResolutionException
     * @throws NotFoundApiVersionException
     */
    public function __invoke()
    {
        return $this->dispatchApiAction('__invoke');
    }

    /**
     * @inheritdoc
     * @throws BindingResolutionException
     * @throws NotFoundApiVersionException
     */
    public function __call($method, $parameters)
    {
        return $this->dispatchApiAction($method);
    }

    public function callAction($method, $parameters)
    {
        array_shift($parameters);

        return $this->{$method}(...array_values($parameters));
    }

    /**
     * @param $method
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws NotFoundApiVersionException
     */
    protected function dispatchApiAction($method)
    {
        return $this->dispatcher->dispatch(
            $this->request->route(),
            $this->getController(),
            $method
        );
    }

    /**
     * @return string
     */
    private function getFallbackVersion(): string
    {
        $reqVersion = ResourceGate::getVersion();
        $availableVersions = array_keys($this->controllers);

        rsort($availableVersions);

        foreach ($availableVersions as $version) {
            if (version_compare($reqVersion, $version, '>=')) {
                return $version;
            }
        }

        return last($availableVersions);
    }

    /**
     * @return Controller
     * @throws BindingResolutionException
     * @throws NotFoundApiVersionException
     */
    protected function getController()
    {
        $version = $this->getFallbackVersion();

        $controller = $this->controllers[$version] ?? null;

        if (!$controller) {
            throw new NotFoundApiVersionException();
        }

        return $this->container->make($controller);
    }
}
