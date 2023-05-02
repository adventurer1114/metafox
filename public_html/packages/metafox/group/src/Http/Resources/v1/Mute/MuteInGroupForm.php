<?php

namespace MetaFox\Group\Http\Resources\v1\Mute;

use Carbon\CarbonInterval;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\Section;
use MetaFox\Group\Http\Requests\v1\Mute\MuteInGroupFormRequest;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Yup\Yup;

class MuteInGroupForm extends AbstractForm
{
    /**
     * @var
     */
    protected $groupId;

    /**
     * @var
     */
    protected $userId;

    protected function prepare(): void
    {
        $this->action('group-mute')
            ->asPost()
            ->title(__p('group::phrase.mute_member'))
            ->setValue([
                'group_id' => $this->groupId,
                'user_id'  => $this->userId,
            ]);
    }

    /**
     * @throws Exception
     */
    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::hidden('group_id'),
            Builder::hidden('user_id'),
        );
        $this->handleFieldMuteExpire($basic);

        $this->addFooter()
            ->addFields(
                Builder::submit()->sizeSmall()->label(__p('group::phrase.mute')),
                Builder::cancelButton()->sizeSmall(),
            );
    }

    /**
     * @throws Exception
     */
    protected function getOptions(): array
    {
        return Cache::rememberForever('group_mute_times', function () {
            $array = Settings::get('group.time_muted_member_option');

            $options = [];

            foreach ($array as $value) {
                if (is_int($value)) {
                    $value = $value . 'd'; // default Day
                }

                $carbon = CarbonInterval::make($value);

                $options[] = [
                    'value'     => $value,
                    'label'     => ucwords(CarbonInterval::make($value)->cascade()->forHumans()),
                    'timestamp' => $carbon->total('seconds'),
                ];
            }

            if (!count($options)) {
                return [];
            }

            $options = collect($options)
                ->unique('timestamp')
                ->all();

            usort($options, function ($a, $b) {
                if ($a['timestamp'] == $b['timestamp']) {
                    return 0;
                }

                if ($a['timestamp'] > $b['timestamp']) {
                    return 1;
                }

                return -1;
            });

            return $options;
        });
    }

    public function boot(MuteInGroupFormRequest $request): void
    {
        $params = $request->validated();

        $this->groupId = Arr::get($params, 'group_id');

        $this->userId = Arr::get($params, 'user_id');
    }

    /**
     * @throws Exception
     */
    protected function handleFieldMuteExpire(Section $basic): void
    {
        if (!empty($this->getOptions())) {
            $basic->addField(
                Builder::radioGroup('expired_at')
                    ->required()
                    ->label(__p('group::phrase.how_long_do_you_want_to_muted_user_name_label', [
                        'user_name' => 'test',
                    ]))
                    ->description(__p('group::phrase.how_long_do_you_want_to_muted_user_name_desc'))
                    ->options($this->getOptions())
                    ->yup(
                        Yup::string()
                            ->required(__p('group::phrase.you_must_choose_time_for_muting'))
                    )
            );

            return;
        }
        $basic->addField(
            Builder::typography('expired_at')
                ->color('text.secondary')
                ->plainText(__p('group::phrase.group_member_can_view_the_group_but_wont_be_able_post'))
        );
    }
}
