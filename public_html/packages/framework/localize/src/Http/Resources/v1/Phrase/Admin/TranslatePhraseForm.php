<?php

namespace MetaFox\Localize\Http\Resources\v1\Phrase\Admin;

use Illuminate\Http\Request;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use Str;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class TranslatePhraseForm.
 * @property ?string $resource
 * @ignore
 * @codeCoverageIgnore
 */
class TranslatePhraseForm extends AbstractForm
{
    public function boot(Request $request): void
    {
        if (!($key = $request->get('key'))) {
            return;
        }

        $this->resource = $key;
    }

    protected function prepare(): void
    {
        $key = $this->resource;

        $text = $key ? __p($key) : $key;

        if ($text === $key) {
            // should be trim phrase.
            $text = preg_replace('#^([\w-]+)::([\w-]+).([\w-]+)#', '$3', $text);

            $text = Str::ucfirst(Str::lower(Str::replace('_', ' ', $text)));
        }

        $this->title(__p('core::phrase.edit'))
            ->action('admincp/phrase/translate')
            ->asPost()
            ->setValue([
                'translation_key'  => $this->resource,
                'translation_text' => $text,
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();
        $basic->addFields(
            Builder::text('translation_key')
                ->required()
                ->readOnly()
                ->label(__p('localize::phrase.key_name')),
            Builder::textArea('translation_text')
                ->required()
                ->label(__p('localize::phrase.translation')),
        );

        $this->addDefaultFooter(true);
    }
}
