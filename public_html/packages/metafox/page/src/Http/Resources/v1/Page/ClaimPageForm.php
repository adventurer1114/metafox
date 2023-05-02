<?php

namespace MetaFox\Page\Http\Resources\v1\Page;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
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
 * Class ClaimPageForm.
 * @property Model $resource
 */
class ClaimPageForm extends AbstractForm
{
    protected bool $isEdit             = true;
    protected bool $isPending          = false;
    protected const MAX_LENGTH_MESSAGE = 500;

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
        $basic = $this->addBasic();
        if ($this->isPending) {
            $basic->addFields(
                Builder::typography()->plainText(__p('page::phrase.your_claim_request_is_pending')),
            );

            return;
        }

        $basic->addFields(
            Builder::textArea('message')
                ->maxLength(self::MAX_LENGTH_MESSAGE)
                ->label(__p('core::phrase.message'))
                ->description(__p('page::phrase.claim_message_description'))
                ->yup(
                    Yup::string()
                        ->maxLength(self::MAX_LENGTH_MESSAGE)
                ),
            Builder::submit('submit')
                ->label(__p('core::phrase.send_request')),
        );
    }
}
