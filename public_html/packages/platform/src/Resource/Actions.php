<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Platform\Resource;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Actions
{
    /** @var array<string,ActionItem> */
    protected array $actions = [];

    protected ?string $appName;

    protected ?string $resourceName;

    /**
     * @param ?string $appName
     * @param ?string $resourceName
     */
    public function __construct(string $appName = null, string $resourceName = null)
    {
        $this->appName      = $appName?? '';
        $this->resourceName = $resourceName??'';
    }

    /**
     * Add new column by field.
     *
     * @param string $name
     *
     * @return ActionItem
     */
    public function add(string $name): ActionItem
    {
        $action = new ActionItem($name);

        $this->actions[$name] = $action;

        return $action;
    }

    public function addActions(array $only = []): void
    {
        foreach ($only as $action) {
            $resource = str_replace('-', '_', $this->resourceName);
            $route    = sprintf('admin.%s.%s.%s', $this->appName, $this->resourceName, $action);
            if (Route::has($route)) {
                $this->add($action)
                    ->apiUrl(apiUrl($route, [$resource => ':id']));
            }
        }
    }

    /**
     * Edit page url pattern.
     *
     * etc: $actions->addEditPageUrl('links.editItem');
     *
     * Note: in ItemResource add attributes 'links.editItem'
     *
     * @param  string $name
     * @return $this
     */
    public function addEditPageUrl(string $name = 'links.editItem')
    {
        $this->add('edit')
            ->asFormDialog(false)
            ->link($name);

        return $this;
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    public function toArray(): array
    {
        $result = [];

        foreach ($this->actions as $name => $action) {
            $result[$name] = $action->toArray();
        }

        return $result;
    }
}
