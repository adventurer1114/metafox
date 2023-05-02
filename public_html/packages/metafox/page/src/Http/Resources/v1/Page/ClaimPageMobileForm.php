<?php

namespace MetaFox\Page\Http\Resources\v1\Page;

use Illuminate\Auth\AuthenticationException;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Page\Models\Page as Model;
use MetaFox\Page\Repositories\PageClaimRepositoryInterface;
use MetaFox\Page\Repositories\PageRepositoryInterface;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class ClaimPageMobileForm.
 * @property Model $resource
 */
class ClaimPageMobileForm extends AbstractForm
{
    protected bool $isEdit             = true;
    protected bool $isPending          = false;
    protected const MAX_LENGTH_MESSAGE = 500;

    /**
     * @throws AuthenticationException
     */
    public function boot(
        PageRepositoryInterface $repository,
        PageClaimRepositoryInterface $claimRepository,
        ?int $id
    ): void {
        $this->resource  = $repository->find($id);
        $this->isPending = $claimRepository->isPendingRequest(user(), $this->resource->entityId());
    }

    protected function prepare(): void
    {
        $resource = $this->resource;
        $this->asPost()
            ->title(__p('page::phrase.claim_page'))
            ->action(url_utility()->makeApiUrl('page-claim/' . $resource->entityId()));
    }

    protected function initialize(): void
    {
        $this->addHeader(['showRightHeader' => !$this->isPending])->component('FormHeader');
        $basic = $this->addBasic();
        if ($this->isPending) {
            $basic->addFields(
                Builder::typography()->plainText(__p('page::phrase.your_claim_request_is_pending'))
            );

            return;
        }
        $basic->addFields(
            Builder::textArea('message')
                ->maxLength(self::MAX_LENGTH_MESSAGE)
                ->description(__p('page::phrase.claim_message_description'))
                ->label(__p('core::phrase.message'))
                ->yup(
                    Yup::string()
                        ->maxLength(self::MAX_LENGTH_MESSAGE)
                ),
        );
    }
}
