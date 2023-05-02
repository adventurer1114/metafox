<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Platform\Support;

use Illuminate\Database\Eloquent\SoftDeletes;
use MetaFox\Platform\Facades\PolicyGate;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\Platform\UserRole;
use MetaFox\User\Contracts\UserHasValuePermission;

/**
 * Trait HasUserEntity.
 * @mixin HasContent
 * @mixin HasEntity
 *
 * @property string $deleted_at
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
trait HasUser
{
    use HasContent;
    use SoftDeletes;
    use HasBigNumberId;

    /**
     * @return bool|null
     */
    public function isPendingMode(): ?bool
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getPendingMessage(): ?string
    {
        if (!$this->hasPendingMode()) {
            return null;
        }

        return __p('core::phrase.thanks_for_your_item_for_approval');
    }

    /**
     * @return string|null
     */
    public function getApprovedMessage(): ?string
    {
        if (!$this->hasPendingMode()) {
            return null;
        }

        return __p('core::phrase.an_admin_approved_your_post_in_entity_type_title', [
            'entity_type'  => $this->entityType(),
            'entity_title' => $this->toTitle(),
        ]);
    }

    public function getDeclinedMessage(): ?string
    {
        if (!$this->hasPendingMode()) {
            return null;
        }

        return __p('core::phrase.an_admin_declined_your_post_in_entity_type_title', [
            'entity_type'  => $this->entityType(),
            'entity_title' => $this->toTitle(),
        ]);
    }

    /**
     * @return bool
     */
    public function hasPendingMode(): bool
    {
        return null !== $this->isPendingMode();
    }

    /**
     * @param mixed        $abilities
     * @param array<mixed> $arguments
     *
     * @return bool
     */
    public function can($abilities, $arguments = []): bool
    {
        if (!is_string($abilities)) {
            abort(500, 'Metafox is not supported');
        }
        if (!isset($arguments[0])) {
            abort(500, 'Missing the first argument');
        }

        $className = $arguments[0];
        unset($arguments[0]);

        if (!is_string($className)) {
            $className = get_class($className);
        }

        $policy = PolicyGate::getPolicyFor($className);

        if (!$policy) {
            // should return true by default if there is no policy handler
            return true;
        }

        return $policy->{$abilities}($this, ...$arguments);
    }

    public function getItemPrivacy(): ?int
    {
        return null;
    }

    /**
     * @return bool
     */
    public function isGuest(): bool
    {
        return $this->entityId() === MetaFoxConstant::GUEST_USER_ID;
    }

    /**
     * @return bool
     */
    public function hasAdminRole(): bool
    {
        return method_exists($this, 'hasRole') && $this->hasRole(UserRole::ADMIN_USER);
    }

    /**
     * @return bool
     */
    public function hasSuperAdminRole(): bool
    {
        return method_exists($this, 'hasRole') && $this->hasRole(UserRole::SUPER_ADMIN_USER);
    }

    /**
     * @return bool
     */
    public function hasContentPrivacy(): bool
    {
        return false;
    }

    /**
     * @return int|null
     */
    public function getRepresentativePrivacy(): ?int
    {
        return null;
    }

    public function getRepresentativePrivacyDetail(int $privacy): ?array
    {
        return null;
    }

    public function getSmallestRoleId(): int
    {
        if (!$this instanceof UserHasValuePermission) {
            return 0;
        }

        return $this->roles->pluck('id')->sort()->first();
    }

    public function toDeclinedContentLink(): ?string
    {
        return null;
    }

    public function toDeclinedContentUrl(): ?string
    {
        return null;
    }

    public function isDeleted(): bool
    {
        return array_key_exists('deleted_at', $this->attributes) && $this->deleted_at;
    }

    public function preferredLocale(): ?string
    {
        return null;
    }
}
