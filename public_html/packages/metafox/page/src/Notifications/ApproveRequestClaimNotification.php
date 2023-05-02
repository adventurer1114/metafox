<?php

namespace MetaFox\Page\Notifications;

use Illuminate\Auth\AuthenticationException;
use MetaFox\Notification\Messages\MailMessage;
use MetaFox\Page\Models\Page;
use MetaFox\Page\Models\PageClaim as Model;
use MetaFox\Platform\Contracts\IsNotifiable;
use MetaFox\Platform\Notifications\Notification;

/**
 * Class ApproveRequestClaimNotification.
 * @property Model $model
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ApproveRequestClaimNotification extends Notification
{
    protected string $type = 'approve_claim_page';
    /**
     * @var int
     */
    protected int $userId;

    /**
     * @var string
     */
    protected string $userType;

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function setUserType(string $userType): self
    {
        $this->userType = $userType;

        return $this;
    }

    public function toMail(IsNotifiable $notifiable): MailMessage
    {
        return (new MailMessage())
            ->line('The introduction to the notification.')
            ->action('Notification Action', 'https://laravel.com')
            ->line('Thank you for using our application!');
    }

    public function toArray(IsNotifiable $notifiable): array
    {
        return [
            'data'      => $this->model->toArray(),
            'item_id'   => $this->model->entityId(),
            'item_type' => $this->model->entityType(),
            'user_id'   => $this->userId,
            'user_type' => $this->userType,
        ];
    }

    /**
     * @throws AuthenticationException
     */
    public function callbackMessage(): ?string
    {
        $page    = $this->model->page;
        $title   = $page instanceof Page ? $page->toTitle() : null;
        $context = user();

        if ($page->userId() != $context->entityId()) {
            return $this->localize('page::notification.your_have_been_removed_as_owner_of_page_title', ['title' => $title]);
        }

        return $this->localize('page::notification.your_have_now_become_the_new_owner_of_title', ['title' => $title]);
    }

    public function toLink(): ?string
    {
        $page = $this->model->page;

        return $page instanceof Page ? $page->toLink() : null;
    }
}
