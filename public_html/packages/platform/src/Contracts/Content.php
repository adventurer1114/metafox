<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Platform\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

/**
 * Interface Content.
 *
 * @mixin Model
 *
 * @property User       $user
 * @property User       $owner
 * @property UserEntity $userEntity
 * @property UserEntity $ownerEntity
 * @property Collection $comments
 * @property int        $total_comment
 * @property int        $total_reply
 * @property int        $total_like
 * @property string     $created_at
 * @property string     $updated_at
 */
interface Content extends Entity, HasAmounts, HasFeed, HasPolicy, HasUrl, HasTitle
{
    /**
     * @return int
     */
    public function userId(): int;

    /**
     * @return string
     */
    public function userType(): string;

    /**
     * @return int
     */
    public function ownerId(): int;

    /**
     * @return string
     */
    public function ownerType(): string;

    /**
     * @return User|MorphTo|BelongsTo
     */
    public function user();

    /**
     * @return UserEntity|BelongsTo
     */
    public function userEntity();

    /**
     * @return User|MorphTo|BelongsTo
     */
    public function owner();

    /**
     * @return UserEntity|BelongsTo
     */
    public function ownerEntity();

    /**
     * Get indicate item handle privacy logic.
     *
     * @return self
     */
    public function reactItem();

    /**
     * Get indicate item handle privacy logic.
     *
     * @return ?self
     */
    public function privacyItem();

    /**
     * @return bool
     */
    public function isDraft();

    /**
     * @return bool
     */
    public function isPublished();

    /**
     * @return bool
     */
    public function isApproved();

    /**
     * @return bool
     */
    public function isOwnerPending(): bool;

    /**
     * @return string|null
     */
    public function getOwnerPendingMessage(): ?string;
}
