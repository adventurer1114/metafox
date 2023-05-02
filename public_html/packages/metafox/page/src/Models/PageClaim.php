<?php

namespace MetaFox\Page\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MetaFox\Page\Database\Factories\PageClaimFactory;
use MetaFox\Page\Notifications\ClaimNotification;
use MetaFox\Platform\Contracts\HasUrl;
use MetaFox\Platform\Contracts\IsNotifyInterface;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;
use MetaFox\Platform\UserRole;
use MetaFox\User\Models\User;
use MetaFox\User\Support\Facades\User as UserSupport;

/**
 * Class PageClaim.
 * @property        int              $status_id
 * @property        int              $page_id
 * @property        Page             $page
 * @property        string|null      $message
 * @method   static PageClaimFactory factory(...$parameters)
 */
class PageClaim extends Model implements IsNotifyInterface, HasUrl
{
    use HasEntity;
    use HasUserMorph;
    use HasFactory;

    public const ENTITY_TYPE    = 'page_claim';
    public const STATUS_PENDING = 0;
    public const STATUS_APPROVE = 1;
    public const STATUS_DENY    = 2;

    protected $table = 'page_claims';

    protected $fillable = [
        'status_id',
        'page_id',
        'user_id',
        'user_type',
        'message',
    ];

    protected static function newFactory(): PageClaimFactory
    {
        return PageClaimFactory::new();
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id', 'id')->withTrashed();
    }

    public function toNotification(): ?array
    {
        $adminId = Settings::get('page.admin_in_charge_of_page_claims');
        /** @var User $admin */
        $admin  = User::query()->find($adminId);
        $admins = [];
        $users  = UserSupport::getUsersByRoleId(UserRole::SUPER_ADMIN_USER);

        foreach ($users as $user) {
            /* @var User $user */
            $admins[] = $user;
        }

        if (null != $admin) {
            $admins = array_merge($admins, [$admin]);
        }

        return [$admins, new ClaimNotification($this)];
    }

    public function toLink(): ?string
    {
        if (!$this->page instanceof Page) {
            return null;
        }

        return $this->page->toLink();
    }

    public function toUrl(): ?string
    {
        if (!$this->page instanceof Page) {
            return null;
        }

        return $this->page->toUrl();
    }

    public function toRouter(): ?string
    {
        if (!$this->page instanceof Page) {
            return null;
        }

        return $this->page->toRouter();
    }
}
