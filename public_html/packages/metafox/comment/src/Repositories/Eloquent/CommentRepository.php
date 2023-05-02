<?php

namespace MetaFox\Comment\Repositories\Eloquent;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use MetaFox\Comment\Models\Comment;
use MetaFox\Comment\Models\CommentAttachment;
use MetaFox\Comment\Models\CommentHide;
use MetaFox\Comment\Models\CommentHistory;
use MetaFox\Comment\Policies\CommentPolicy;
use MetaFox\Comment\Repositories\CommentHistoryRepositoryInterface;
use MetaFox\Comment\Repositories\CommentRepositoryInterface;
use MetaFox\Comment\Support\Helper;
use MetaFox\Core\Constants;
use MetaFox\Platform\Contracts\ActivityFeedSource;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasTotalComment;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\PackageManager;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Scopes\SortScope;
use MetaFox\Platform\Support\Helper\Pagination;
use MetaFox\User\Support\Browse\Scopes\User\BlockedScope;

/**
 * Class CommentRepository.
 * @method Comment getModel()
 * @method Comment find($id, $columns = ['*'])
 * @SuppressWarnings(PHPMD)
 */
class CommentRepository extends AbstractRepository implements CommentRepositoryInterface
{
    public const REGEX_LINK = '/(http[s]?:\/\/(www\.)?|ftp:\/\/(www\.)?|www\.){1}([0-9A-Za-z-\-\.@:%_\+~#=]+)+((\.[a-zA-Z])*)(\/([0-9A-Za-z-\-\.@:%_\+~#=\?])*)*/';

    public const NEWEST_SORT_OPERATOR = '>';

    public const OLDEST_SORT_OPERATOR = '<';

    public function model(): string
    {
        return Comment::class;
    }

    protected function commentHistoryRepository(): CommentHistoryRepositoryInterface
    {
        return resolve(CommentHistoryRepositoryInterface::class);
    }

    public function viewComments(User $context, array $attributes): Collection
    {
        policy_authorize(CommentPolicy::class, 'viewAny', $context);

        $sort = $attributes['sort'] ?? 'created_at';

        $sortType = $attributes['sort_type'] ?? 'desc';

        $limit = $attributes['limit'] ?? Pagination::DEFAULT_ITEM_PER_PAGE;

        $itemType = $attributes['item_type'] ?? null;

        $itemId = $attributes['item_id'];

        $comment = new Comment([
            'item_id'   => $itemId,
            'item_type' => $itemType,
        ]);

        $sortScope = new SortScope();

        $sortScope
            ->setSort($sort)
            ->setSortType($sortType);

        $item = $comment->item;

        if (null == $item) {
            throw (new ModelNotFoundException())->setModel($itemType);
        }

        $query = $this->buildCommentsQuery($context, $attributes);

        $blockedScope = new BlockedScope();
        $blockedScope->setContextId($context->entityId())
            ->setPrimaryKey('user_id')
            ->setTable('comments');

        $query->with(['userEntity', 'commentAttachment', 'userHidden'])
            ->addScope($sortScope);

        return $query
            ->addScope($blockedScope)
            ->orderByRaw(DB::raw(
                "
                CASE
                    WHEN fr.id IS NOT NULL THEN 1
                    WHEN comments.user_id <> {$context->entityId()} THEN 2
                    ELSE 3
                END ASC"
            ))
            ->limit($limit)
            ->get(['comments.*']);
    }

    public function viewComment(User $context, int $id): Comment
    {
        $comment = $this->with(['userEntity', 'commentAttachment'])->find($id);

        policy_authorize(CommentPolicy::class, 'view', $context, $comment);

        return $comment;
    }

    public function checkSpam(User $user, int $commentId, string $content, int $stickerId, bool $hasPhotoId): bool
    {
        $checkComment      = Settings::get('comment.enable_hash_check');
        $totalCommentCheck = Settings::get('comment.comments_to_check');
        $totalMinutes      = Settings::get('comment.total_minutes_to_wait_for_comments', 0);

        if ($checkComment && $totalCommentCheck && $totalMinutes) {
            $date = Carbon::now('UTC')->subMinutes($totalMinutes)->toDateTimeString();

            $query = $this->getModel()->newQuery()
                ->with(['commentAttachment'])
                ->where('user_id', '=', $user->entityId())
                ->where('user_type', '=', $user->entityType())
                ->where('updated_at', '>=', $date)
                ->orderByDesc('updated_at')
                ->limit($totalCommentCheck);

            if ($commentId > 0) {
                $query->where('id', '<>', $commentId);
            }

            $comments = $query->get();

            foreach ($comments as $comment) {
                /** @var Comment $comment */
                $commentAttachment = $comment->commentAttachment;
                if ($stickerId > 0) {
                    if (null != $commentAttachment && $commentAttachment->item_type == CommentAttachment::TYPE_STICKER) {
                        if ($content == $comment->text_parsed && $stickerId == $commentAttachment->item_id) {
                            return true;
                        }
                    }
                }

                if ($stickerId == 0) {
                    if ((null == $commentAttachment && !$hasPhotoId)
                        || (null != $commentAttachment && $commentAttachment->item_type == CommentAttachment::TYPE_LINK)) {
                        if ($content == $comment->text_parsed) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    public function createComment(User $context, array $attributes): Comment
    {
        $comment       = (new Comment())->fill($attributes);
        $item          = $comment->item;
        $taggedFriends = Arr::get($attributes, 'tagged_friends');
        $stickerId     = $attributes['sticker_id'] ?? 0;
        $hasPhotoId    = !empty($attributes['photo_id']);
        $enableThread  = Settings::get('comment.enable_thread');
        if (null == $item) {
            abort(404, __p('core::phrase.this_post_is_no_longer_available'));
        }

        policy_authorize(CommentPolicy::class, 'create', $context, $item);

        $textParsed = parse_output()->parse($attributes['text']);

        $isSpam = $this->checkSpam($context, 0, $textParsed, $stickerId, $hasPhotoId);

        if ($isSpam) {
            abort(400, __p('core::phrase.you_have_already_added_this_recently_try_adding_something_else'));
        }

        if (array_key_exists('parent_id', $attributes) && $attributes['parent_id'] > 0) {
            $checkParentExists = Comment::query()->where([
                'item_id'   => $item->entityId(),
                'item_type' => $item->entityType(),
                'id'        => $attributes['parent_id'],
            ])->exists();

            if (!$checkParentExists) {
                abort(400, __p('comment::phrase.this_comment_has_been_deleted'));
            }

            if (!$enableThread) {
                abort(400, __p('comment::phrase.this_may_because_technical_error'));
            }
        }

        $autoApprove = (int) $context->hasPermissionTo('comment.auto_approved');

        $comment->fill([
            'user_id'         => $context->entityId(),
            'user_type'       => $context->entityType(),
            'owner_id'        => $item->userId(),
            'owner_type'      => $item->userType(),
            'is_approved'     => $autoApprove,
            'text_parsed'     => $textParsed,
            'tagged_user_ids' => collect($taggedFriends)->keys(),
        ])->save();

        $isCheckLink = true;

        $canCreateSticker = true;

        if (Arr::has($attributes, 'photo_id') && $attributes['photo_id'] > 0) {
            $isCheckLink      = false;
            $canCreateSticker = false;
            $this->handleCreateAttachment($comment, CommentAttachment::TYPE_FILE, $attributes['photo_id']);
        }

        if (Arr::has($attributes, 'sticker_id') && $attributes['sticker_id'] > 0 && $canCreateSticker) {
            $isCheckLink = false;
            $this->handleCreateAttachment($comment, CommentAttachment::TYPE_STICKER, $attributes['sticker_id']);

            // send signal to sticker to update sticker recent
            app('events')->dispatch('sticker.create_sticker_recent', [$context, $attributes['sticker_id']]);
        }

        if ($isCheckLink) {
            $this->handleCreateAttachment($comment, CommentAttachment::TYPE_LINK);
        }

        if (is_array($taggedFriends) && count($taggedFriends)) {
            app('events')->dispatch(
                'friend.create_tag_friends',
                [$context, $comment, $taggedFriends, $comment->entityType()],
                true
            );
        }

        app('events')->dispatch('hashtag.create_hashtag', [$context, $comment, $comment->text_parsed], true);

        return $comment->refresh();
    }

    public function updateComment(User $context, int $id, array $attributes): Comment
    {
        $comment = $this->with(['commentAttachment', 'tagData'])->find($id);

        policy_authorize(CommentPolicy::class, 'update', $context, $comment);

        if (array_key_exists('text', $attributes)) {
            $this->validateText($comment, $attributes);

            $attributes['text_parsed'] = $attributes['text'] != '' ? $comment->text_parsed : '';

            if ($attributes['text'] != $comment->text) {
                $attributes['text_parsed'] = parse_output()->parse($attributes['text']);

                if (!$this->commentHistoryRepository()->checkExists($comment)) {
                    $this->commentHistoryRepository()->createHistory($comment->user, $comment);
                }
            }

            $stickerId         = !empty($attributes['sticker_id']) ? $attributes['sticker_id'] : 0;
            $hasPhotoId        = !empty($attributes['photo_id']);
            $commentAttachment = $comment->commentAttachment;

            if (null != $commentAttachment) {
                if (!$stickerId && !array_key_exists('sticker_id', $attributes)
                    && $commentAttachment->item_type == CommentAttachment::TYPE_STICKER) {
                    $stickerId = $commentAttachment->item_id;
                }

                if (!$hasPhotoId && !array_key_exists('photo_id', $attributes)
                    && $commentAttachment->item_type == CommentAttachment::TYPE_FILE) {
                    $hasPhotoId = true;
                }
            }

            $isSpam = $this->checkSpam(
                $context,
                $comment->entityId(),
                $attributes['text_parsed'],
                $stickerId,
                $hasPhotoId
            );

            if ($isSpam) {
                abort(400, __p('core::phrase.you_have_already_added_this_recently_try_adding_something_else'));
            }
        }

        $attributes['tagged_user_ids'] = [];

        $taggedFriends = Arr::get($attributes, 'tagged_friends', []);

        if (is_array($taggedFriends) && count($taggedFriends)) {
            $attributes['tagged_user_ids'] = collect($taggedFriends)->keys();
        }

        $oldText    = $comment->text;
        $oldHashTag = implode(',', parse_output()->getHashtags($comment->text_parsed));
        $comment->fill($attributes)->update();
        $comment->refresh();

        $canUpdateSticker = true;
        $canDeletePhoto   = !(isset($attributes['sticker_id']) && $attributes['sticker_id'] > 0); //for update Attachment. Not delete record

        $phrase = null;
        if (isset($attributes['photo_id'])) {
            if ($attributes['photo_id'] > 0) {
                $canUpdateSticker = false;
                $this->handleCreateAttachment($comment, CommentAttachment::TYPE_FILE, $attributes['photo_id']);
                $phrase = CommentHistory::PHRASE_COLUMNS_ADDED;
            }

            if ($attributes['photo_id'] == 0 && $canDeletePhoto) {
                $this->handleDeleteAttachment($comment, CommentAttachment::TYPE_FILE);
                $phrase = CommentHistory::PHRASE_COLUMNS_DELETED;
            }
        }

        if (isset($attributes['sticker_id'])) {
            if ($attributes['sticker_id'] > 0 && $canUpdateSticker) {
                $this->handleCreateAttachment($comment, CommentAttachment::TYPE_STICKER, $attributes['sticker_id']);
            }

            if ($attributes['sticker_id'] == 0) {
                $this->handleDeleteAttachment($comment, CommentAttachment::TYPE_STICKER);
            }
        }

        $comment->refresh();

        if (null == $comment->commentAttachment) {
            $this->handleCreateAttachment($comment, CommentAttachment::TYPE_LINK);
        }

        if (null != $comment->commentAttachment) {
            if ($comment->commentAttachment->item_type == CommentAttachment::TYPE_LINK) {
                if (!preg_match(self::REGEX_LINK, $comment->text)) {
                    $this->handleDeleteAttachment($comment, CommentAttachment::TYPE_LINK);
                }

                $this->handleCreateAttachment($comment, CommentAttachment::TYPE_LINK, 0, $oldText);
            }
        }

        if (isset($attributes['tagged_friends'])) {
            app('events')->dispatch(
                'friend.update_tag_friends',
                [$context, $comment, $attributes['tagged_friends'], $comment->entityType()],
                true
            );
        }

        $newHashTag = implode(',', parse_output()->getHashtags($comment->text_parsed));
        if (!empty($newHashTag)) {
            if ($newHashTag != $oldHashTag) {
                app('events')->dispatch('hashtag.create_hashtag', [$context, $comment, $comment->text_parsed], true);
            }
        }

        if (empty($newHashTag) && !empty($oldHashTag)) {
            $comment->tagData()->sync([]);
        }

        if ($this->commentHistoryRepository()->checkExists($comment)
            && $oldText != $comment->text || $phrase != null) {
            $this->commentHistoryRepository()->createHistory($context, $comment, $phrase);
        }

        return $comment->refresh();
    }

    /**
     * @param Comment $comment
     * @param string  $itemType
     * @param int     $itemId
     * @param string  $oldText
     */
    private function handleCreateAttachment(
        Comment $comment,
        string $itemType,
        int $itemId = 0,
        string $oldText = ''
    ): void {
        $attachmentData    = [];
        $commentAttachment = $comment->commentAttachment;

        // single comment should morph to other comment instead of create new comment attachment.
        switch ($itemType) {
            case CommentAttachment::TYPE_FILE:
                $tempFile = upload()->getFile($itemId);

                $attachmentData = [
                    'item_id'   => $tempFile->id,
                    'item_type' => $tempFile->entityType(),
                    'params'    => null,
                ];

                $tempFile->rollUp();
                break;
            case CommentAttachment::TYPE_STICKER:
                $attachmentData = [
                    'item_id'   => $itemId,
                    'item_type' => CommentAttachment::TYPE_STICKER,
                    'params'    => null,
                ];
                break;
            case CommentAttachment::TYPE_LINK:
                if ($oldText != $comment->text) {
                    if (preg_match(self::REGEX_LINK, $comment->text, $matches)) {
                        if (null != $commentAttachment && CommentAttachment::TYPE_LINK == $commentAttachment->item_type) {
                            if (preg_match(self::REGEX_LINK, $oldText, $oldMatches)) {
                                if ($matches[0] == $oldMatches[0]) {
                                    break;
                                }
                            }
                        }

                        try {
                            $data = app('events')->dispatch('core.parse_url', [$matches[0]], true);
                            unset($data['resource_name']);
                            $data['actual_link'] = $data['link'] ?? null;
                            $attachmentData      = [
                                'item_id'    => 0,
                                'item_type'  => CommentAttachment::TYPE_LINK,
                                'image_path' => null,
                                'server_id'  => 'public',
                                'params'     => json_encode($data),
                            ];
                        } catch (Exception $e) {
                        }
                    }
                }

                break;
        }

        if (null != $commentAttachment) {
            if ($commentAttachment->item_type == CommentAttachment::TYPE_FILE) {
                // check to destroy external resource
            }

            if (!empty($attachmentData)) {
                $commentAttachment->update(Arr::except($attachmentData, ['id']));
            }

            return;
        }

        if (!empty($attachmentData)) {
            // fix may be insert id to pgsql prevent sequence nextval
            $comment->commentAttachment()->create(Arr::except($attachmentData, ['id']));
        }
    }

    /**
     * @param Comment $comment
     * @param string  $itemType
     */
    private function handleDeleteAttachment(Comment $comment, string $itemType): void
    {
        $commentAttachment = $comment->commentAttachment;

        if (null != $commentAttachment && $commentAttachment->item_type == $itemType) {
            $commentAttachment->delete();

            if ($itemType == CommentAttachment::TYPE_FILE) {
                app('storage')->deleteAll($commentAttachment->item_id);
            }
        }
    }

    /**
     * @param User $context
     * @param int  $id
     *
     * @return array<string,          mixed>
     * @throws AuthorizationException
     */
    public function deleteCommentById(User $context, int $id): array
    {
        $comment = $this->find($id);

        policy_authorize(CommentPolicy::class, 'delete', $context, $comment);

        $item = $comment->item;
        $comment->delete();
        $feedId = null;

        if (null !== $item) {
            $item->refresh();
        }

        if ($item instanceof ActivityFeedSource) {
            try {
                $feedAction = $item->toActivityFeed();
                $typeId     = $feedAction?->getTypeId();
                /** @var Content $feed */
                $feed   = app('events')->dispatch('activity.get_feed', [$context, $item, $typeId], true);
                $feedId = $feed->entityId();
            } catch (Exception $e) {
                //Do nothing
            }
        }

        [, , , $packageId] = app('core.drivers')->loadDriver(Constants::DRIVER_TYPE_ENTITY, $item->entityType());

        $data = [
            'id'             => $comment->entityId(),
            'feed_id'        => $feedId,
            'item_module_id' => PackageManager::getAlias($packageId),
            'item_id'        => $item->entityId(),
            'item_type'      => $item->entityType(),
            'statistic'      => [
                'total_comment' => $item instanceof HasTotalComment ? $item->total_comment : 0,
            ],
        ];

        $extra = app('events')->dispatch('core.proxy_item', [$item], true);

        if (is_array($extra)) {
            $data = array_merge($data, $extra);
        }

        return $data;
    }

    public function deleteCommentByParentId(int $parentId): bool
    {
        return $this->getModel()->where('parent_id', $parentId)->each(function (Comment $comment) {
            $comment->delete();
        });
    }

    public function hideComment(User $context, int $id, bool $isHidden): bool
    {
        $comment = $this->find($id);

        policy_authorize(CommentPolicy::class, 'hide', $context, $comment);

        return $this->processContextHidden($context, $id, $isHidden, Helper::HIDE_OWN);
    }

    public function hideCommentGlobal(User $context, int $id, bool $isHidden): bool
    {
        $comment = $this->with(['user', 'owner'])
            ->find($id);

        policy_authorize(CommentPolicy::class, 'hideGlobal', $context, $comment);

        $owner = $comment->owner;

        $user = $comment->user;

        $isOwner = null !== $owner && $context->entityId() == $owner->entityId();

        // Push view permission for owner of comment
        if (null !== $user) {
            $this->processRelatedHidden($comment->user, $id, $isHidden);
        }

        // Push view permission when context is owner of post
        if ($isOwner) {
            return $this->processContextHidden(
                $owner,
                $id,
                $isHidden,
                $isHidden ? Helper::HIDE_GLOBAL : Helper::HIDE_OWN
            );
        }

        // Push view permission for owner of post
        $this->processRelatedHidden($owner, $id, $isHidden);

        // Push hidden view for context in case context has moderate permission
        return $this->processContextHidden($context, $id, $isHidden, Helper::HIDE_OWN);
    }

    /**
     * In case owner of item and owner of comment.
     * @param  User $user
     * @param  int  $id
     * @param  bool $isHidden
     * @return bool
     */
    protected function processRelatedHidden(User $user, int $id, bool $isHidden): bool
    {
        $model = CommentHide::query()
            ->where([
                'user_id'   => $user->entityId(),
                'user_type' => $user->entityType(),
                'item_id'   => $id,
            ])
            ->first();

        $type = $isHidden ? Helper::HIDE_GLOBAL : Helper::HIDE_OWN;

        if ($model instanceof CommentHide) {
            if ($model->type != $type) {
                return $model->update(['type' => $type]);
            }

            return true;
        }

        $model = new CommentHide([
            'user_id'   => $user->entityId(),
            'user_type' => $user->entityType(),
            'item_id'   => $id,
            'type'      => $type,
            'is_hidden' => false,
        ]);

        return $model->save();
    }

    /**
     * In case context is executed action hide.
     * @param  User   $user
     * @param  int    $id
     * @param  bool   $isHidden
     * @param  string $type
     * @return bool
     */
    protected function processContextHidden(User $user, int $id, bool $isHidden, string $type): bool
    {
        $model = CommentHide::query()
            ->where([
                'user_id'   => $user->entityId(),
                'user_type' => $user->entityType(),
                'item_id'   => $id,
            ])
            ->first();

        if ($model instanceof CommentHide) {
            $update = ['is_hidden' => $isHidden];

            if ($type != $model->type) {
                Arr::set($update, 'type', $type);
            }

            return $model->update($update);
        }

        $model = new CommentHide([
            'user_id'   => $user->entityId(),
            'user_type' => $user->entityType(),
            'item_id'   => $id,
            'type'      => $type,
            'is_hidden' => $isHidden,
        ]);

        return $model->save();
    }

    public function getRelatedCommentsByType(
        User $context,
        string $itemType,
        int $itemId,
        array $attributes = []
    ): Collection {
        $numberOfCommentOnFeed = Settings::get('comment.prefetch_comments_on_feed');

        $enableThread = Settings::get('comment.enable_thread');

        $showReply = Settings::get('comment.show_reply');

        $numberOfReplyOnFeed = Settings::get('comment.prefetch_replies_on_feed');

        $isShowReply = $enableThread && $showReply && $numberOfReplyOnFeed;

        $sortType = Arr::get(
            $attributes,
            'sort_type',
            Helper::getSortType(Settings::get('comment.sort_by', Helper::SORT_ALL))
        );

        $excludedIds = Arr::get($attributes, 'excluded_ids');

        $orderedId = Arr::get($attributes, 'ordered_id');

        $relations = [
            'userEntity',
            'commentAttachment',
            'parentComment',
            'parentComment.commentAttachment',
            'parentComment.userEntity',
            'tagData',
            'userHidden',
        ];

        $query = $this->getModel()->newQuery()
            ->with($relations)
            ->select('comments.*');

        if (!$context->hasPermissionTo('comment.moderate')) {
            $query->leftJoin('comment_hidden', function (JoinClause $joinClause) {
                $joinClause->on('comment_hidden.item_id', '=', 'comments.id')
                    ->where('comment_hidden.type', '=', Helper::HIDE_GLOBAL);
            })
                ->where(function (Builder $builder) use ($context) {
                    $builder->whereNull('comment_hidden.id')
                        ->orWhere('comment_hidden.user_id', '=', $context->entityId());
                });
        }

        if (is_array($excludedIds) && count($excludedIds)) {
            $query->whereNotIn('comments.id', $excludedIds);
        }

        if (is_numeric($orderedId) && $orderedId > 0) {
            $query->orderByRaw("
                CASE
                    WHEN comments.id = {$orderedId} THEN 1
                    ELSE 2
                END ASC
            ");
        }

        $blockedScope = new BlockedScope();
        $blockedScope->setContextId($context->entityId())
            ->setPrimaryKey('user_id')
            ->setTable('comments');

        $comments = $query
            ->addScope($blockedScope)
            ->where([
                'comments.item_id'   => $itemId,
                'comments.item_type' => $itemType,
                'comments.parent_id' => 0,
            ])
            ->orderBy('comments.id', $sortType)
            ->leftJoin('friends AS fr', function (JoinClause $join) use ($context) {
                $join->on('fr.user_id', '=', 'comments.user_id');
                $join->where('fr.owner_id', $context->entityId());
            })
            ->orderByRaw("
            CASE
                WHEN fr.id IS NOT NULL THEN 1
                WHEN comments.user_id <> {$context->entityId()} THEN 2
                ELSE 3
            END ASC")
            ->limit($numberOfCommentOnFeed)
            ->get();

        if (!$comments->count()) {
            return $comments;
        }

        if (!$isShowReply) {
            return $comments;
        }

        //TODO: Please improve later by using UNION ALL with one query
        return $comments->map(function ($comment) use ($numberOfReplyOnFeed) {
            $children = $comment->children()
                ->with(['userEntity', 'commentAttachment'])
                ->orderByDesc('id')
                ->limit($numberOfReplyOnFeed)
                ->get();

            $comment->setRelation('children', $children);

            return $comment;
        });
    }

    public function getRelatedComments(User $context, HasTotalComment $content): Collection
    {
        $itemId = $content->entityId();

        $itemType = $content->entityType();

        return $this->getRelatedCommentsByType($context, $itemType, $itemId);
    }

    public function getRelatedCommentsForItemDetail(User $context, HasTotalComment $content, int $limit = 6): Collection
    {
        $itemId       = $content->entityId();
        $itemType     = $content->entityType();
        $enableThread = Settings::get('comment.enable_thread');
        $showReply    = Settings::get('comment.show_reply');
        $limit        = Settings::get('comment.prefetch_comments_on_item_detail', 6);
        $limitReplies = Settings::get('comment.prefetch_replies_on_item_detail');
        $isShowReply  = $enableThread && $showReply && $limitReplies;

        $query = $this->getModel()->newQuery()
            ->with(['commentAttachment', 'userHidden'])
            ->select('comments.*')
            ->where([
                'comments.parent_id' => 0,
                'comments.item_id'   => $itemId,
                'comments.item_type' => $itemType,
            ]);

        if (!$context->hasPermissionTo('comment.moderate')) {
            $query->leftJoin('comment_hidden', function (JoinClause $joinClause) {
                $joinClause->on('comment_hidden.item_id', '=', 'comments.id')
                    ->where('comment_hidden.type', '=', Helper::HIDE_GLOBAL);
            })
                ->where(function (Builder $builder) use ($context) {
                    $builder->whereNull('comment_hidden.id')
                        ->orWhere('comment_hidden.user_id', '=', $context->entityId());
                });
        }

        $sortType = Helper::getSortType(Settings::get('comment.sort_by', Helper::SORT_ALL));

        $query->leftJoin('friends AS fr', function (JoinClause $join) use ($context) {
            $join->on('fr.user_id', '=', 'comments.user_id');
            $join->where('fr.owner_id', $context->entityId());
        })
            ->orderBy('comments.id', $sortType)
            ->orderByRaw("CASE
                WHEN fr.id IS NOT NULL THEN 1
                WHEN comments.user_id <> {$context->entityId()} THEN 2
                ELSE 3
            END ASC")
            ->limit($limit)
            ->orderByDesc('comments.updated_at');

        if ($isShowReply) {
            $query->with([
                'children' => function (HasMany $q) use ($limitReplies) {
                    $q->orderByDesc('created_at')->limit($limitReplies);
                },
            ]);
        }

        /** @var Collection|Comment[] $comments */
        $comments = $query->get();

        if (!$comments instanceof Collection) {
            return new Collection([]);
        }

        return $comments;
    }

    /**
     * @param Comment              $comment
     * @param array<string, mixed> $attributes
     *
     * @throws ValidationException
     */
    private function validateText(Comment $comment, array $attributes): void
    {
        if ($attributes['text'] == '') {
            if (empty($attributes['sticker_id']) && empty($attributes['photo_id'])) {
                $isMissText = false;
                if (null == $comment->commentAttachment) {
                    $isMissText = true;
                }

                if (null != $comment->commentAttachment
                    && (array_key_exists('photo_id', $attributes) || array_key_exists('sticker_id', $attributes))) {
                    $isMissText = true;
                }

                if ($isMissText) {
                    $errorMessage = __p('validation.required_without', ['attribute' => 'text', 'values' => 'photo_id']);
                    if (app_active('metafox/sticker')) {
                        $errorMessage = __p('validation.required_without_all', [
                            'attribute' => 'text',
                            'values'    => implode(' / ', ['photo_id', 'sticker_id']),
                        ]);
                    }

                    throw ValidationException::withMessages([
                        'text' => $errorMessage,
                    ]);
                }
            }
        }
    }

    /**
     * @param  array<string, mixed> $attributes
     * @return Builder
     */
    private function buildCommentsQuery(User $context, array $attributes): Builder
    {
        $itemId = $attributes['item_id'];

        $itemType = $attributes['item_type'];

        $parentId = Arr::get($attributes, 'parent_id', 0);

        $excludes = Arr::get($attributes, 'excludes', []);

        $lastId = Arr::get($attributes, 'last_id', 0);

        $sortType = Arr::get($attributes, 'sort_type', Browse::SORT_TYPE_DESC);

        $query = $this->getModel()->newQuery()
            ->leftJoin('friends AS fr', function (JoinClause $join) use ($context) {
                $join->on('fr.user_id', '=', 'comments.user_id');
                $join->where('fr.owner_id', $context->entityId());
            })
            ->where([
                'comments.item_id'     => $itemId,
                'comments.item_type'   => $itemType,
                'comments.parent_id'   => $parentId,
                'comments.is_approved' => 1,
            ]);

        if (!$context->hasPermissionTo('comment.moderate')) {
            $query->leftJoin('comment_hidden', function (JoinClause $joinClause) {
                $joinClause->on('comment_hidden.item_id', '=', 'comments.id')
                    ->where('comment_hidden.type', '=', Helper::HIDE_GLOBAL);
            })
                ->where(function (Builder $builder) use ($context) {
                    $builder->whereNull('comment_hidden.id')
                        ->orWhere('comment_hidden.user_id', '=', $context->entityId());
                });
        }

        if (is_array($excludes) && count($excludes)) {
            $query->whereNotIn('comments.id', $excludes);
        }

        if ($lastId > 0) {
            $operator = $sortType == Browse::SORT_TYPE_DESC ? self::OLDEST_SORT_OPERATOR : self::NEWEST_SORT_OPERATOR;

            $query->where('comments.id', $operator, $lastId);
        }

        return $query;
    }

    /**
     * @inheritDoc
     */
    public function getUsersCommentByItem(User $context, array $attributes): array
    {
        $limit = $attributes['limit'];

        $itemType = $attributes['item_type'];

        $itemId = $attributes['item_id'];

        $comment = new Comment([
            'item_id'   => $itemId,
            'item_type' => $itemType,
        ]);

        $item = $comment->item;

        if (null == $item) {
            throw (new ModelNotFoundException())->setModel($itemType);
        }

        $query = $this->getModel()
            ->newModelQuery()
            ->distinct('user_id')
            ->where('item_id', $itemId)
            ->where('item_type', $itemType)
            ->where('is_approved', 1);

        $relations = ['userEntity'];

        $total = $query->count();

        $collection = new Collection();

        if ($total > 0) {
            $collection = $query
                ->with($relations)
                ->limit($limit)
                ->get();

            $collection = $collection->map(function ($item) {
                return $item->userEntity;
            });
        }

        return [$total, $collection];
    }

    public function getRelevantCommentsById(User $context, int $id, ?Entity $content = null): ?Collection
    {
        $where = [
            'id' => $id,
        ];

        if (null !== $content) {
            $where = array_merge($where, [
                'item_type' => $content->entityType(),
                'item_id'   => $content->entityId(),
            ]);
        }

        $relevantComment = $this
            ->getModel()
            ->newQuery()
            ->with(['parentComment'])
            ->where($where)
            ->first();

        if (null === $relevantComment) {
            return null;
        }

        if ($relevantComment->parent_id > 0) {
            if (null === $relevantComment) {
                return null;
            }

            $relevantComment = $relevantComment->parentComment;
        }

        return $this->getRelatedCommentsByType($context, $relevantComment->itemType(), $relevantComment->itemId(), [
            'ordered_id' => $relevantComment->entityId(),
            'sort_type'  => Browse::SORT_TYPE_ASC,
        ]);
    }

    public function getTotalHidden(User $context, HasTotalComment $item, int $parentId = 0): int
    {
        if ($context->hasPermissionTo('comment.moderate')) {
            return 0;
        }

        if ($context->entityId() == $item->userId()) {
            return 0;
        }

        $total = $this->getModel()->newQuery()
            ->join('comment_hidden', function (JoinClause $joinClause) {
                $joinClause->on('comment_hidden.item_id', '=', 'comments.id')
                    ->where('comment_hidden.type', '=', Helper::HIDE_GLOBAL);
            })
            ->where([
                'comments.item_id'     => $item->entityId(),
                'comments.item_type'   => $item->entityType(),
                'comments.parent_id'   => $parentId,
                'comments.is_approved' => 1,
            ])
            ->where('comments.user_id', '<>', $context->entityId())
            ->count();

        // divide 2 because when inserting global scope hidden view, we will insert owner of comment and owner of item
        return $total / 2;
    }

    public function removeLinkPreview(Comment $comment): bool
    {
        if (null === $comment->commentAttachment) {
            return false;
        }

        $params = json_decode($comment->commentAttachment->params, true);

        if (!is_array($params)) {
            $params = [];
        }

        if (Arr::get($params, 'is_hidden')) {
            return false;
        }

        Arr::set($params, 'is_hidden', true);

        $comment->commentAttachment->update(['params' => json_encode($params)]);

        return true;
    }
}
