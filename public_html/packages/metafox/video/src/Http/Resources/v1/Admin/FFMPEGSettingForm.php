<?php

namespace MetaFox\Video\Http\Resources\v1\Admin;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use MetaFox\Core\Rules\FileExistRule;
use MetaFox\Form\AdminSettingForm as Form;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Video\Models\Video as Model;
use MetaFox\Video\Repositories\CategoryRepositoryInterface;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class SiteSettingForm.
 * @property ?Model $resource
 */
class FFMPEGSettingForm extends Form
{
    protected function prepare(): void
    {
        $vars   = [
            'video.ffmpeg.binaries',
            'video.ffprobe.binaries',
        ];

        $value = [];
        foreach ($vars as $var) {
            Arr::set($value, $var, Settings::get($var));
        }

        $this->title(__p('video::phrase.ffmpeg_configurations'))
            ->asPost()
            ->action(url_utility()->makeApiUrl('admincp/setting/video.ffmpeg'))
            ->setValue($value);
    }

    protected function initialize(): void
    {
        $this->addBasic()->addFields(
            Builder::text('video.ffmpeg.binaries')
                ->required()
                ->label(__p('video::phrase.video_path_to_ffmpeg'))
                ->description(__p('video::phrase.video_path_to_ffmpeg_description'))
                ->yup(Yup::string()->required()),
            Builder::divider(),
            Builder::text('video.ffprobe.binaries')
                ->required()
                ->label(__p('video::phrase.video_path_to_ffprobe'))
                ->description(__p('video::phrase.video_path_to_ffprobe_description'))
                ->yup(Yup::string()->required()),
        );

        $this->addDefaultFooter(true);
    }

    public function getCategoryRepository(): CategoryRepositoryInterface
    {
        return resolve(CategoryRepositoryInterface::class);
    }

    /**
     * @param  Request              $request
     * @return array<string, mixed>
     * @throws ValidationException
     */
    public function validated(Request $request): array
    {
        $data  = $request->all();
        $rules = [
            'video.ffmpeg.binaries'  => ['sometimes', new FileExistRule()],
            'video.ffprobe.binaries' => ['sometimes', new FileExistRule()],
        ];

        $validator = Validator::make($data, $rules);
        $validator->validate();

        return $data;
    }
}
