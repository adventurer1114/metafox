<?php

namespace MetaFox\Queue\Http\Resources\v1\Admin;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;

/**
 * | --------------------------------------------------------------------------
 * | Form Configuration
 * | --------------------------------------------------------------------------
 * | stub: src/Http/Resources/v1/Admin/SiteSettingForm.stub.
 */

/**
 * Class SiteSettingForm.
 * @codeCoverageIgnore
 * @ignore
 */
class SiteSettingForm extends AbstractForm
{
    private array $variables = [];

    private bool $disabled = true;

    protected function prepare(): void
    {
        $vars = [
            'queue.default',
        ];

        $values = [];

        foreach ($vars as $var) {
            Arr::set($values, $var, Settings::get($var));
        }

        $this->variables = [
            'queue.default' => config('queue.default'),
        ];

        foreach ($this->variables as $key => $value) {
            if (empty($value)) {
                $this->disabled = false;
            } else {
                Arr::set($values, $key, $value);
            }
        }

        $this->title(__p('core::phrase.settings'))
            ->action('admincp/setting/queue')
            ->asPost()
            ->setValue($values);
    }

    protected function initialize(): void
    {
        $default = $this->variables['queue.default'];

        $options = $this->getQueueOptions();

        if (is_string($default) && MetaFoxConstant::EMPTY_STRING != $default) {
            $options = Arr::prepend($options, [
                'value' => $default,
                'label' => Str::ucfirst($default),
            ]);
        }

        $this->addBasic()
            ->addFields(
                Builder::choice('queue.default')
                    ->options($options)
                    ->label(__p('queue::phrase.default_label'))
                    ->disabled($this->disabled)
                    ->description(__p('queue::phrase.default_desc'))
            );

        $this->addDefaultFooter(true);
    }

    private function getQueueOptions(): array
    {
        return [
            ['value' => 'database', 'label' => 'Database'],
        ];
    }
}
