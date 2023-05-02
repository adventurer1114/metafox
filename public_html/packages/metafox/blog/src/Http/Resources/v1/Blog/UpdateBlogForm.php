<?php

namespace MetaFox\Blog\Http\Resources\v1\Blog;

use MetaFox\Blog\Http\Requests\v1\Blog\CreateFormRequest;
use MetaFox\Blog\Policies\BlogPolicy;
use MetaFox\Blog\Repositories\BlogRepositoryInterface;
use MetaFox\Form\AbstractField;
use MetaFox\Form\Builder;
use MetaFox\Form\Html\Privacy;
use MetaFox\Form\Html\Submit;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class UpdateBlogForm.
 */
class UpdateBlogForm extends StoreBlogForm
{
    public function boot(CreateFormRequest $request, BlogRepositoryInterface $repository, ?int $id = null): void
    {
        $context        = user();
        $this->resource = $repository->find($id);
        policy_authorize(BlogPolicy::class, 'update', $context, $this->resource);
    }

    protected function prepare(): void
    {
        $blogText = $this->resource->blogText;
        $privacy  = $this->resource->privacy;

        if ($privacy == MetaFoxPrivacy::CUSTOM) {
            $lists = PrivacyPolicy::getPrivacyItem($this->resource);

            $listIds = [];
            if (!empty($lists)) {
                $listIds = array_column($lists, 'item_id');
            }

            $privacy = $listIds;
        }

        $this
            ->title(__p('blog::phrase.edit_blog'))
            ->action(url_utility()->makeApiUrl("blog/{$this->resource->entityId()}"))
            ->setBackProps(__p('core::web.blog'))
            ->asPut()
            ->setValue([
                'title'       => $this->resource->title,
                'module_id'   => $this->resource->module_id,
                'owner_id'    => $this->resource->owner_id,
                'text'        => $blogText != null ? parse_output()->parse($blogText->text_parsed) : '',
                'categories'  => $this->resource->categories->pluck('id')->toArray(),
                'privacy'     => $privacy,
                'published'   => !$this->resource->is_draft,
                'tags'        => $this->resource->tags,
                'attachments' => $this->resource->attachmentsForForm(),
                'draft'       => 0,
            ]);
    }

    protected function buildPrivacyField(): Privacy
    {
        // show when edit a blog not belong to any owners like pages, groups, events
        return Builder::privacy()
            ->description(__p('blog::phrase.control_who_can_see_this_blog'))
            ->showWhen(['eq', 'owner_id', $this->resource->userId()]);
    }

    protected function buildPublishButton(): AbstractField
    {
        return new Submit([
            'label'     => !$this->resource->isDraft() ? __p('core::phrase.update') : __p('core::phrase.publish'),
            'flexWidth' => true,
        ]);
    }

    protected function buildSaveAsDraftButton(): AbstractField
    {
        return new Submit([
            'name'     => 'draft',
            'color'    => 'primary',
            'variant'  => 'outlined',
            'label'    => __p('core::phrase.update'),
            'value'    => 1,
            'showWhen' => ['falsy', 'published'],
        ]);
    }
}
