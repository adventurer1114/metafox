<?php

namespace MetaFox\Quiz\Http\Resources\v1\Quiz\Admin;

use Illuminate\Support\Arr;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Quiz\Models\Quiz as Model;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class SiteSettingForm.
 * @property Model $resource
 */
class SiteSettingForm extends AbstractForm
{
    /** @var bool */
    protected $isEdit = false;

    protected function prepare(): void
    {
        $module = 'quiz';
        $vars   = [
            'quiz.min_length_quiz_question',
            'quiz.max_length_quiz_question',
            'quiz.minimum_name_length',
            'quiz.maximum_name_length',
            'quiz.show_success_as_percentage_in_result',
        ];

        $value = [];

        foreach ($vars as $var) {
            Arr::set($value, $var, Settings::get($var));
        }

        $this
            ->title(__p('core::phrase.settings'))
            ->action(url_utility()->makeApiUrl('admincp/setting/' . $module))
            ->asPost()
            ->setValue($value);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::text('quiz.min_length_quiz_question')
                ->asNumber()
                ->preventScrolling()
                ->label(__p('quiz::phrase.site_settings.minimum_length_for_quiz_question'))
                ->yup(
                    Yup::number()
                        ->required()
                        ->unint()
                        ->min(1)
                        ->setError('typeError', __p('core::validation.numeric', ['attribute' => '${path}']))
                ),
            Builder::text('quiz.max_length_quiz_question')
                ->asNumber()
                ->preventScrolling()
                ->label(__p('quiz::phrase.site_settings.maximum_length_for_quiz_question'))
                ->yup(
                    Yup::number()
                        ->required()
                        ->unint()
                        ->when(
                            Yup::when('min_length_quiz_question')
                                ->is('$exists')
                                ->then(Yup::number()->min(['ref' => 'min_length_quiz_question']))
                        )
                        ->min(1)
                        ->setError('typeError', __p('core::validation.numeric', ['attribute' => '${path}']))
                ),
            Builder::text('quiz.minimum_name_length')
                ->asNumber()
                ->preventScrolling()
                ->label(__p('quiz::phrase.site_settings.minimum_name_length_for_quiz'))
                ->yup(
                    Yup::number()
                        ->required()
                        ->unint()
                        ->min(1)
                        ->setError('typeError', __p('core::validation.numeric', ['attribute' => '${path}']))
                ),
            Builder::text('quiz.maximum_name_length')
                ->asNumber()
                ->preventScrolling()
                ->label(__p('quiz::phrase.site_settings.maximum_name_length_for_quiz'))
                ->yup(
                    Yup::number()
                        ->required()
                        ->unint()
                        ->when(
                            Yup::when('minimum_name_length')
                                ->is('$exists')
                                ->then(Yup::number()->min(['ref' => 'minimum_name_length']))
                        )
                        ->min(1)
                        ->setError('typeError', __p('core::validation.numeric', ['attribute' => '${path}']))
                ),
            Builder::switch('quiz.show_success_as_percentage_in_result')
                ->label(__p('quiz::phrase.site_settings.show_success_as_percentage_in_result'))
                ->description(__p('quiz::phrase.site_settings.show_success_as_percentage_in_result_desc'))
        );

        $this->addDefaultFooter(true);
    }
}
