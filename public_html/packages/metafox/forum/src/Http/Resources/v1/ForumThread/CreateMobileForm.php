<?php

namespace MetaFox\Forum\Http\Resources\v1\ForumThread;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Arr;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Form\Mobile\MobileForm as AbstractForm;
use MetaFox\Form\Section;
use MetaFox\Forum\Http\Requests\v1\ForumThread\CreateFormRequest;
use MetaFox\Forum\Models\ForumThread;
use MetaFox\Forum\Models\ForumThread as Model;
use MetaFox\Forum\Policies\ForumThreadPolicy;
use MetaFox\Forum\Repositories\ForumRepositoryInterface;
use MetaFox\Forum\Repositories\ForumThreadRepositoryInterface;
use MetaFox\Forum\Support\Facades\ForumThread as ForumThreadFacade;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\Yup\Yup;

/**
 * Class CreateMobileForm.
 * @property Model $resource
 */
class CreateMobileForm extends AbstractForm
{
    /**
     * @var int|null
     */
    protected ?int $ownerId = null;
    /**
     * @var User
     */
    protected User $owner;

    /**
     * @param  CreateFormRequest              $request
     * @param  ForumThreadRepositoryInterface $repository
     * @param  int|null                       $id
     * @return void
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function boot(CreateFormRequest $request, ForumThreadRepositoryInterface $repository, ?int $id = null): void
    {
        $data        = $request->validated();
        $this->owner = $context = user();
        $ownerId     = Arr::get($data, 'owner_id', 0);

        if ($ownerId > 0) {
            $this->owner = UserEntity::getById($ownerId)->detail;
        }

        $this->resource = new Model($data);

        policy_authorize(ForumThreadPolicy::class, 'create', $context, $this->owner);

        $this->ownerId = $ownerId;
    }

    protected function prepare(): void
    {
        $context = user();

        $this
            ->title(__p('forum::phrase.forum_phrase_menu_create_new_thread'))
            ->action('forum-thread')
            ->asPost()
            ->setBackProps(__p('forum::phrase.forums'))
            ->setValue([
                'is_subscribed' => (int) $this->canSubscribe(),
                'is_wiki'       => 0,
                'tags'          => [],
                'owner_id'      => $this->ownerId ?? 0,
            ]);
    }

    /**
     * @throws AuthenticationException
     */
    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $context = user();

        $canUsingWiki   = !$this->owner instanceof HasPrivacyMember && ForumThreadFacade::canDisplayOnWiki($context);
        $maxTitleLength = Settings::get('forum.maximum_name_length', MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH);
        $minTitleLength = Settings::get('forum.minimum_name_length', MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH);

        $yup = match ($canUsingWiki) {
            true => Yup::number()
                ->when(
                    Yup::when('is_wiki')
                        ->is(0)
                        ->then(
                            Yup::number()
                                ->required()
                                ->setError('typeError', __p('forum::validation.forum_id.required'))
                        )
                ),
            default => Yup::number()
                ->required()
                ->setError('typeError', __p('forum::validation.forum_id.required')),
        };

        $basic->addFields(
            Builder::choice('forum_id')
                ->required()
                ->label(__p('forum::phrase.forum'))
                ->options($this->getForums())
                ->yup($yup)
                ->showWhen(['falsy', 'is_wiki']),
            Builder::text('title')
                ->required()
                ->maxLength($maxTitleLength)
                ->returnKeyType('next')
                ->label(__p('core::phrase.title'))
                ->description(__p('core::phrase.maximum_length_of_characters', ['length' => $maxTitleLength]))
                ->placeholder(__p('forum::form.fill_in_a_title_for_your_thread'))
                ->yup(
                    Yup::string()
                        ->required()
                        ->nullable(false)
                        ->minLength(
                            $minTitleLength,
                            __p(
                                'core::validation.title_minimum_length_of_characters',
                                ['number' => $minTitleLength]
                            )
                        )
                        ->maxLength(
                            $maxTitleLength,
                            __p('core::validation.title_maximum_length_of_characters', [
                                'min' => $minTitleLength,
                                'max' => $maxTitleLength,
                            ])
                        )
                ),
            Builder::richTextEditor('text')
                ->required()
                ->returnKeyType('default')
                ->label(__p('forum::form.content'))
                ->placeholder(__p('forum::form.add_some_content_to_your_thread'))
                ->yup(
                    Yup::string()
                        ->required()
                        ->nullable(false)
                ),
        );

        $this->attachItem($basic);

        $canSubscribe = $this->canSubscribe();

        $subscribeField = match ($canSubscribe) {
            true => Builder::switch('is_subscribed')
                ->label(__p('forum::menu.subscribe')),
            false => Builder::hidden('is_subscribed'),
        };

        $basic->addFields(
            Builder::tags('tags')
                ->label(__p('core::phrase.tags'))
                ->placeholder(__p('core::phrase.keywords')),
            $subscribeField
        );

        switch ($canUsingWiki) {
            case true:
                $basic->addField(
                    Builder::switch('is_wiki')
                        ->label(__p('forum::phrase.display_on_wiki'))
                        ->description(__p('forum::phrase.display_on_wiki_description')),
                );
                break;
            default:
                $basic->addField(
                    Builder::hidden('is_wiki'),
                );
                break;
        }
    }

    protected function canSubscribe(): bool
    {
        $context = user();

        if ($this->resource instanceof ForumThread && $this->resource?->id) {
            return policy_check(ForumThreadPolicy::class, 'subscribe', $context, $this->resource);
        }

        return $context->hasPermissionTo('forum_thread.auto_approved') && $context->hasPermissionTo('forum_thread.subscribe');
    }

    protected function attachItem(Section $basic): void
    {
        $context = user();

        if (policy_check(ForumThreadPolicy::class, 'attachPoll', $context)) {
            $this->setItemComponent($basic);

            return;
        }

        if (!$this->resource->id) {
            return;
        }

        if (null === $this->resource->item_type) {
            return;
        }

        $this->setItemComponent($basic);
    }

    /**
     * @return array<string, mixed>
     * @throws AuthenticationException
     */
    protected function getForums(): array
    {
        $context = user();

        return resolve(ForumRepositoryInterface::class)->getForumsForForm($context);
    }

    /**
     * @return array|null
     * @throws AuthenticationException
     */
    protected function getIntegratedComponent(): ?array
    {
        $user = user();

        $owner = user();

        $entity = null;

        $resource = $this->resource;

        if (null !== $resource && null !== $resource->item_type) {
            $entity = $resource->item;
        }

        return ForumThreadFacade::getIntegratedItem($user, $owner, $entity, 'mobile');
    }

    protected function setItemComponent(Section $basic): void
    {
        $item = $this->getIntegratedComponent();

        if (!is_array($item) || 0 === count($item)) {
            return;
        }

        $basic->addFields(
            $item['item_component'],
            Builder::hidden('item_type')
                ->setValue($item['item_type'])
        );

        $value = $this->getValue();

        if (!is_array($value)) {
            $value = [];
        }

        $value = array_merge($value, ['item_type' => $item['item_type']]);

        if (!$this->resource->id) {
            if (Arr::has($item, 'values')) {
                $value = array_merge($value, $item['values']);
            }
        }

        $this->setValue($value);
    }
}
