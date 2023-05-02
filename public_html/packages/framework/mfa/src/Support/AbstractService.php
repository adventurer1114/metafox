<?php

namespace MetaFox\Mfa\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use MetaFox\Core\Constants;
use MetaFox\Core\Repositories\DriverRepositoryInterface;
use MetaFox\Form\AbstractForm;
use MetaFox\Mfa\Contracts\ServiceInterface;
use MetaFox\Mfa\Models\Service;
use MetaFox\Mfa\Models\UserAuthToken;
use MetaFox\Mfa\Models\UserService;
use MetaFox\Platform\MetaFoxConstant;
use RuntimeException;

/**
 * Class ServiceManager.
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
abstract class AbstractService implements ServiceInterface
{
    private DriverRepositoryInterface $driverRepository;

    public function __construct(private Service $service)
    {
        $this->driverRepository = resolve(DriverRepositoryInterface::class);
    }

    public function toTitle(): string
    {
        return __p("mfa::phrase.{$this->service->name}_provider_title");
    }

    public function toDescription(): string
    {
        return __p("mfa::phrase.{$this->service->name}_provider_description");
    }

    public function toIcon(string $resolution = 'web'): string
    {
        return Arr::get($this->service->config, "icon.$resolution", '');
    }

    public function setupForm(UserService $userService, ?string $resolution = 'web'): AbstractForm
    {
        $service       = $this->service->name;
        $form          = $this->loadForm($userService, 'mfa.user_service.setup_form_' . $service, $resolution);
        if (!$form instanceof AbstractForm) {
            throw new RuntimeException("Could not initialize setup form for MFA service $service.");
        }

        return $form;
    }

    public function authForm(UserAuthToken $userAuthToken, ?string $resolution = 'web'): AbstractForm
    {
        $service       = $this->service->name;
        $form          = $this->loadForm($userAuthToken, 'mfa.user_service.auth_form_' . $service, $resolution);
        if (!$form instanceof AbstractForm) {
            throw new RuntimeException("Could not initialize auth form for MFA service $service.");
        }

        return $form;
    }

    public function verifyAuth(UserService $userService, array $params = []): bool
    {
        return true;
    }

    public function verifyActivation(UserService $userService, array $params = []): bool
    {
        return true;
    }

    private function loadForm(Model $resource, string $driverName, ?string $resolution = 'web'): ?AbstractForm
    {
        $driver = $this->driverRepository
            ->getDriver(Constants::DRIVER_TYPE_FORM, $driverName, $resolution ?? MetaFoxConstant::RESOLUTION_WEB);

        /* @var ?AbstractForm $form */
        return resolve($driver, ['resource' => $resource]);
    }
}
