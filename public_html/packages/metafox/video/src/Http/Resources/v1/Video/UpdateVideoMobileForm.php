<?php

namespace MetaFox\Video\Http\Resources\v1\Video;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Video\Http\Requests\v1\Video\CreateFormRequest;
use MetaFox\Video\Models\Video as Model;
use MetaFox\Video\Policies\VideoPolicy;
use MetaFox\Video\Repositories\VideoRepositoryInterface;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class UpdateVideoMobileForm.
 * @property Model $resource
 *
 * @driverType form-mobile
 * @driverName video.video.update
 */
class UpdateVideoMobileForm extends UploadVideoMobileForm
{
    /**
     * @param  CreateFormRequest        $request
     * @param  VideoRepositoryInterface $repository
     * @param  int|null                 $id
     * @return void
     * @throws AuthorizationException
     * @throws AuthenticationException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function boot(CreateFormRequest $request, VideoRepositoryInterface $repository, ?int $id = null): void
    {
        $context        = user();
        $this->resource = $repository->find($id);
        $this->setOwner($this->resource->owner);

        policy_authorize(VideoPolicy::class, 'update', $context, $this->resource);
    }

    protected function prepare(): void
    {
        $privacy = $this->resource->privacy;
        if ($privacy == MetaFoxPrivacy::CUSTOM) {
            $lists = PrivacyPolicy::getPrivacyItem($this->resource);

            $listIds = [];
            if (!empty($lists)) {
                $listIds = array_column($lists, 'item_id');
            }

            $privacy = $listIds;
        }

        $videoText = $this->resource->videoText;
        $text      = '';
        if ($videoText) {
            $text = parse_output()->parse($videoText->text_parsed);
        }

        if (MetaFoxConstant::EMPTY_STRING == $text) {
            if ($this->resource->group_id > 0) {
                $reactItem = $this->resource->reactItem();

                if (null !== $reactItem) {
                    $text = $reactItem->content;
                }
            }
        }

        $this->title(__p('video::phrase.edit_video'))
            ->action(url_utility()->makeApiResourceUrl($this->resource->entityType(), $this->resource->entityId()))
            ->asPut()
            ->setValue([
                'title'        => $this->resource->title,
                'text'         => $text,
                'categories'   => $this->resource->categories->pluck('id')->toArray(),
                'privacy'      => $privacy,
                'owner_id'     => $this->resource->owner_id,
                'useThumbnail' => true,
                'module_id'    => $this->resource->entityType(),
            ]);
    }
}
