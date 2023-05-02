<?php

namespace MetaFox\Group\Support;

use Exception;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use MetaFox\Group\Contracts\SupportContract;
use MetaFox\Group\Models\Group;
use MetaFox\Group\Repositories\GroupRepositoryInterface;
use MetaFox\Group\Repositories\QuestionRepositoryInterface;
use MetaFox\Menu\Repositories\MenuItemRepositoryInterface;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Contracts\User as ContractUser;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxPrivacy;

class Support implements SupportContract
{
    private GroupRepositoryInterface $groupRepository;

    private QuestionRepositoryInterface $questionRepository;

    public const MENTION_REGEX = '^\[group=(.*?)\]^';

    public const SHARED_TYPE = 'group';

    public function __construct(
        GroupRepositoryInterface $groupRepository,
        QuestionRepositoryInterface $questionRepository
    ) {
        $this->groupRepository    = $groupRepository;
        $this->questionRepository = $questionRepository;
    }

    public function getGroup(int $id): ?Group
    {
        return $this->groupRepository->getGroup($id);
    }

    public function mustAnswerMembershipQuestion(Group $group): bool
    {
        return $this->groupRepository->hasGroupQuestions($group) && $group->is_answer_membership_question;
    }

    public function mustAcceptGroupRule(Group $group): bool
    {
        return $this->groupRepository->hasGroupRule($group) && $group->is_rule_confirmation;
    }

    public function getQuestions(Group $group): ?Collection
    {
        return $this->questionRepository->getQuestionsForForm($group->entityId());
    }

    public function getMaximumMembershipQuestion(): int
    {
        return (int) Settings::get('group.maximum_membership_question', 3);
    }

    public function getMaximumNumberMembershipQuestionOption(): int
    {
        return (int) Settings::get('group.maximum_membership_question_option', 5);
    }

    /**
     * getListTypes.
     *
     * @return array<mixed>
     */
    public function getListTypes(): array
    {
        return Cache::rememberForever('groups_list_types', function () {
            $resourceName = 'feed_type';

            $integrationTypes = $this->getDefaultListTypes($resourceName);

            $menuItems = resolve(MenuItemRepositoryInterface::class)
                ->getMenuItemByMenuName(
                    'group.group.profileMenu',
                    'web',
                    true
                );

            if ($menuItems->count()) {
                foreach ($menuItems as $menuItem) {
                    if (is_string($menuItem->name)) {
                        $model = Relation::getMorphedModel($menuItem->name);

                        if (null !== $model) {
                            $model = resolve($model);
                        }

                        if ($model instanceof Content) {
                            $integrationTypes[] = [
                                'id'            => $model->entityType(),
                                'resource_name' => $resourceName,
                                'name'          => __p($menuItem->label),
                            ];
                        }
                    }
                }
            }

            return $integrationTypes;
        });
    }

    /**
     * getDefaultListTypes.
     *
     * @param  string       $resourceName
     * @return array<mixed>
     */
    protected function getDefaultListTypes(string $resourceName): array
    {
        if (!app_active('metafox/activity')) {
            return [];
        }

        $types[] = [
            'id'            => 'all',
            'resource_name' => $resourceName,
            'name'          => __p('core::phrase.all'),
        ];

        $postModel = Relation::getMorphedModel('activity_post');

        $linkModel = Relation::getMorphedModel('link');

        if (null !== $postModel) {
            $postModel = resolve($postModel);

            $types[] = [
                'id'            => $postModel->entityType(),
                'resource_name' => $resourceName,
                'name'          => __p('activity::phrase.posts'),
            ];
        }

        if (null !== $linkModel) {
            $linkModel = resolve($linkModel);

            $types[] = [
                'id'            => $linkModel->entityType(),
                'resource_name' => $resourceName,
                'name'          => __p('core::phrase.links'),
            ];
        }

        return $types;
    }

    public function getPrivacyList(): array
    {
        return [
            // Member only
            [
                'privacy'         => MetaFoxPrivacy::FRIENDS,
                'privacy_type'    => Group::GROUP_MEMBERS,
                'privacy_icon'    => 'ico-user-two-men',
                'privacy_tooltip' => [
                    'var_name' => 'group::phrase.member_of_group_name',
                    'params'   => [
                        'name' => 'ownerEntity',
                    ],
                ],
            ],
            // Admin only.
            [
                'privacy'      => MetaFoxPrivacy::CUSTOM,
                'privacy_type' => Group::GROUP_ADMINS,
            ],
            // Moderator only.
            [
                'privacy'      => MetaFoxPrivacy::CUSTOM,
                'privacy_type' => Group::GROUP_MODERATORS,
            ],
        ];
    }

    public function getMentions(string $content): array
    {
        $userIds = [];

        try {
            preg_match_all(self::MENTION_REGEX, $content, $matches);
            $userIds = array_unique($matches[1]);
        } catch (Exception $e) {
            // Silent.
        }

        return $userIds;
    }

    public function getGroupsForMention(array $ids): Collection
    {
        $collection = $this->groupRepository->getModel()->newModelQuery()
            ->whereIn('id', $ids)
            ->get();

        return $collection->mapWithKeys(function ($group) {
            return [$group->entityId() => $group];
        });
    }

    public function getGroupBuilder(User $user): Builder
    {
        return $this->groupRepository->getGroupBuilder($user);
    }

    /**
     * @inheritDoc
     */
    public function getMaximumNumberGroupRule(): int
    {
        return (int) Settings::get('group.maximum_number_group_rule', 3);
    }

    /**
     * @inheritDoc
     */
    public function isFollowing(ContractUser $context, ContractUser $user): bool
    {
        if (!app('events')->dispatch('follow.is_follow', [$context, $user], true)) {
            return false;
        }

        return true;
    }
}
