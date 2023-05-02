<?php

namespace MetaFox\Group\Http\Resources\v1\Group;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Html\SingleUpdateInputField;

/**
 * Class PermissionForm.
 * @property int               $id       user id
 * @property array<int, mixed> $settings the list of privacy settings
 */
class PermissionForm extends AbstractForm
{
    private int $id;

    /**
     * @var array<int, mixed>
     */
    private array $settings;

    /**
     * @param array<int, mixed> $settings
     * @param int               $id
     * @param null              $resource
     */
    public function __construct(array $settings, int $id, $resource = null)
    {
        parent::__construct($resource);

        $this->id = $id;
        $this->settings = $settings;
    }

    protected function prepare(): void
    {
        $value = collect($this->settings)->pluck('value', 'var_name');

        $this
            ->title(__('group::phrase.group_permissions'))
            ->action('group/privacy/' . $this->id)
            ->secondAction('updateGroupPermission')
            ->asPut()
            ->setValue($value);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();
        foreach ($this->settings as $setting) {
            $basic->addField(
                new SingleUpdateInputField([
                    'name'          => $setting['var_name'],
                    'label'         => $setting['phrase'],
                    'options'       => $setting['options'],
                    'editComponent' => 'select',
                ])
            );
        }
    }
}
