<?php

namespace MetaFox\Forum\Http\Resources\v1\ForumThread;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Arr;
use MetaFox\Captcha\Support\Facades\Captcha;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\Section;
use MetaFox\Forum\Models\ForumThread;
use MetaFox\Forum\Policies\ForumThreadPolicy;
use MetaFox\Forum\Repositories\ForumRepositoryInterface;
use MetaFox\Forum\Support\Facades\ForumThread as ForumThreadFacade;
use MetaFox\Forum\Support\ForumSupport;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Yup\Yup;

/**
 * Class CreateForm.
 * @property ForumThread $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CreateForm extends AbstractForm
{
    /**
     * @var User
     */
    protected User $user;

    /**
     * @var User
     */
    protected User $owner;

    /**
     * @param  User       $user
     * @return CreateForm
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param  User $user
     * @return self
     */
    public function setOwner(User $user): self
    {
        $this->owner = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * @throws AuthenticationException
     */
    protected function prepare(): void
    {
        $context = user();

        $this
            ->title(__p('forum::menu.create_new_thread'))
            ->action('forum-thread')
            ->asPost()
            ->setBackProps(__p('forum::phrase.forums'))
            ->setValue([
                'is_subscribed' => (int) $this->canSubscribe(),
                'is_wiki'       => 0,
                'owner_id'      => $this->owner->entityId(),
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $context = user();

        $maxTitleLength = Settings::get('forum.maximum_name_length', MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH);
        $minTitleLength = Settings::get('forum.minimum_name_length', MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH);
        $canUsingWiki   = !$this->owner instanceof HasPrivacyMember && ForumThreadFacade::canDisplayOnWiki($context);
        $yup            = match ($canUsingWiki) {
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
            Builder::hidden('owner_id'),
            Builder::choice('forum_id')
                ->required()
                ->alwaysShow()
                ->label(__p('forum::phrase.forum'))
                ->options($this->getForums())
                ->yup($yup)
                ->showWhen(['falsy', 'is_wiki']),
            Builder::text('title')
                ->required()
                ->returnKeyType('next')
                ->margin('normal')
                ->label(__p('core::phrase.title'))
                ->placeholder(__p('forum::form.fill_in_a_title_for_your_thread'))
                ->description(__p('core::phrase.maximum_length_of_characters', ['length' => $maxTitleLength]))
                ->maxLength($maxTitleLength)
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
            Builder::attachment()
                ->placeholder(__p('core::phrase.attach_files'))
                ->itemType(ForumThread::ENTITY_TYPE)
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

        $basic->addField(
            Captcha::getFormField('forum.' . ForumSupport::CAPTCHA_RULE_CREATE_THREAD)
        );

        $this->setHiddenFieldById($basic);

        $this->addDefaultFooter();
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

        if (!$this->resource) {
            return;
        }

        if (null === $this->resource->item_type) {
            return;
        }

        $this->setItemComponent($basic);
    }

    protected function getIntegratedComponent(): ?array
    {
        $user = $this->getUser();

        $owner = $this->getOwner();

        $entity = null;

        $resource = $this->resource;

        if (null !== $resource && null !== $resource->item_type) {
            $entity = $resource->item;
        }

        return ForumThreadFacade::getIntegratedItem($user, $owner, $entity);
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

        if ($this->resource instanceof ForumThread) {
            return;
        }

        if (!Arr::has($item, 'values')) {
            return;
        }

        $values = $this->getValue();

        if (!is_array($values)) {
            $values = [];
        }

        $values = array_merge($values, $item['values']);

        $this->setValue($values);
    }

    protected function setHiddenFieldById(Section $basic): void
    {
    }

    /**
     * @return array
     * @throws AuthenticationException
     */
    protected function getForums(): array
    {
        $context = user();

        return resolve(ForumRepositoryInterface::class)->getForumsForForm($context);
    }
}
