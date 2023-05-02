<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Poll\Http\Resources\v1\Poll;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MetaFox\Form\AbstractField;
use MetaFox\Form\Builder;
use MetaFox\Form\Html\Privacy;
use MetaFox\Form\Section;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Poll\Http\Requests\v1\Poll\CreateFormRequest;
use MetaFox\Poll\Models\Answer;
use MetaFox\Poll\Policies\PollPolicy;
use MetaFox\Poll\Repositories\PollRepositoryInterface;

/**
 * @driverName poll.update
 * @driverType form
 */
class UpdatePollForm extends StorePollForm
{
    /**
     * @param  CreateFormRequest                              $request
     * @param  PollRepositoryInterface                        $repository
     * @param  int|null                                       $id
     * @return void
     * @throws AuthenticationException|AuthorizationException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function boot(CreateFormRequest $request, PollRepositoryInterface $repository, ?int $id = null): void
    {
        $context        = user();
        $this->resource = $repository->with([
            'answers' => fn (HasMany $query) => $query->orderBy('ordering'),
        ])->find($id);
        policy_authorize(PollPolicy::class, 'update', $context, $this->resource);
    }

    protected function prepare(): void
    {
        $currentAnswers = $this->resource->answers->map(function (Answer $answer) {
            return [
                'answer' => $answer->answer,
                'id'     => $answer->entityId(),
                'order'  => $answer->ordering,
            ];
        });

        $pollText  = $this->resource->pollText;
        $privacy   = $this->resource->privacy;
        $hasBanner = 0;
        if (is_string($this->resource->image)) {
            $hasBanner = 1;
        }

        if ($privacy == MetaFoxPrivacy::CUSTOM) {
            $lists = PrivacyPolicy::getPrivacyItem($this->resource);

            $listIds = [];
            if (!empty($lists)) {
                $listIds = array_column($lists, 'item_id');
            }

            $privacy = $listIds;
        }

        $file    = null;
        $imageId = $this->resource->image_file_id;

        if ($imageId) {
            $file = [
                'id'        => $imageId,
                'temp_file' => $imageId,
                'status'    => 'keep',
            ];
        }

        $this->title(__p('poll::phrase.edit_poll_title'))
            ->action(url_utility()->makeApiUrl('/poll/' . $this->resource->entityId()))
            ->asPut()
            ->setBackProps(__p('core::web.polls'))
            ->setValue([
                'question'     => $this->resource->question,
                'text'         => null != $pollText ? parse_output()->parse($pollText->text_parsed) : '',
                'answers'      => $currentAnswers,
                'close_time'   => $this->resource->closed_at,
                'public_vote'  => $this->resource->public_vote ? 1 : 0,
                'enable_close' => (int) (null != $this->resource->closed_at),
                'is_multiple'  => $this->resource->is_multiple ? 1 : 0,
                'owner_id'     => $this->resource->owner_id,
                'privacy'      => $privacy,
                'attachments'  => $this->resource->attachmentsForForm(),
                'has_banner'   => $hasBanner,
                'file'         => $file,
            ]);
    }

    protected function buildPrivacyField(): Privacy
    {
        return new Privacy([
            'name'        => 'privacy',
            'description' => __p('poll::phrase.control_who_can_see_this_poll'),
            'fullWidth'   => false,
            'minWidth'    => 275,
            'maxHeight'   => 40,
            // show when edit a blog not belong to any owners like pages, groups, events
            'showWhen' => ['eq', 'owner_id', $this->resource->userId()],
        ]);
    }

    protected function setButtonFields(Section $footer): void
    {
        $footer->addFields(
            Builder::submit('submit')
                ->label(__p('core::phrase.update'))
                ->setValue(1)
                ->flexWidth(true)
                ->setAttributes(['fontSize' => 18, 'fontWeight' => 700]),
            Builder::cancelButton()->sizeMedium(),
        );
    }

    protected function addCaptchaField(): ?AbstractField
    {
        return null;
    }
}
