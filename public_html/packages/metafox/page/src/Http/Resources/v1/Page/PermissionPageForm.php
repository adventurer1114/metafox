<?php

namespace MetaFox\Page\Http\Resources\v1\Page;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Html\SingleUpdateInputField;
use MetaFox\Page\Models\Page as Model;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class PermissionPageForm.
 * @property ?Model $resource
 */
class PermissionPageForm extends AbstractForm
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

    /** @var bool */
    protected $isEdit = false;

    protected function prepare(): void
    {
        $value = collect($this->settings)->pluck('value', 'var_name');

        $this
            ->title(__('page::phrase.page_permissions'))
            ->action('page/privacy/' . $this->id)
            ->secondAction('updatePagePermission')
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
