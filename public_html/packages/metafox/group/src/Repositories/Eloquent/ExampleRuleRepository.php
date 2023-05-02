<?php

namespace MetaFox\Group\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\ValidationException;
use MetaFox\Group\Models\ExampleRule;
use MetaFox\Group\Policies\ExampleRulePolicy;
use MetaFox\Group\Repositories\ExampleRuleRepositoryInterface;
use MetaFox\Localize\Models\Phrase;
use MetaFox\Localize\Repositories\PhraseRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;

/**
 * Class ExampleRuleRepository.
 * @method ExampleRule find($id, $columns = ['*'])
 * @method ExampleRule getModel()
 * @ignore
 */
class ExampleRuleRepository extends AbstractRepository implements ExampleRuleRepositoryInterface
{
    public function model(): string
    {
        return ExampleRule::class;
    }

    public function getPhraseRepository(): PhraseRepositoryInterface
    {
        return resolve(PhraseRepositoryInterface::class);
    }

    /**
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function createRuleExample(User $context, array $attributes): ExampleRule
    {
        policy_authorize(ExampleRulePolicy::class, 'create', $context);
        $phraseParams = [
            'locale'     => Arr::get($attributes, 'locale', 'en'),
            'package_id' => Arr::get($attributes, 'package_id'),
            'group'      => Arr::get($attributes, 'group'),
        ];

        $phraseTitle = $this->getPhraseRepository()->createPhrase(array_merge($phraseParams, [
            'name' => Arr::get($attributes, 'title_phrase'),
            'text' => Arr::get($attributes, 'title'),
        ]));

        $phraseDescription = $this->getPhraseRepository()->createPhrase(array_merge($phraseParams, [
            'name' => Arr::get($attributes, 'description_phrase'),
            'text' => Arr::get($attributes, 'description'),
        ]));

        $exampleRuleParams = [
            'title'       => $phraseTitle->key,
            'is_active'   => Arr::get($attributes, 'is_active'),
            'description' => $phraseDescription->key,
        ];

        $exampleRule = new ExampleRule();
        $exampleRule->fill($exampleRuleParams)->save();

        return $exampleRule->refresh();
    }

    /**
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function updateRuleExample(User $context, int $id, array $attributes): ExampleRule
    {
        policy_authorize(ExampleRulePolicy::class, 'update', $context);
        $title       = Arr::get($attributes, 'title', '');
        $description = Arr::get($attributes, 'description', '');
        $isActive    = Arr::get($attributes, 'is_active', 1);

        $rule = $this->find($id);

        $this->handleUpdatePhrase($rule->title, $title);
        $this->handleUpdatePhrase($rule->description, $description);

        Artisan::call('cache:reset');
        $rule->update(['is_active' => $isActive]);

        return $rule->refresh();
    }

    /**
     * @throws ValidationException
     */
    protected function handleUpdatePhrase(string $key, string $text): void
    {
        $phrase = $this->getPhraseRepository()->findWhere([
            ['locale', '=', 'en'],
            ['key', '=', $key],
        ])->first();

        if (!$phrase instanceof Phrase) {
            throw (new ModelNotFoundException())->setModel(Phrase::class);
        }

        $this->getPhraseRepository()->updatePhrase($phrase->entityId(), ['text' => $text]);
    }

    public function viewRuleExamples(User $context, array $attributes): Paginator
    {
        policy_authorize(ExampleRulePolicy::class, 'viewAny', $context);
        $limit = $attributes['limit'];

        return $this->getModel()->newQuery()
            ->orderBy('ordering')
            ->simplePaginate($limit);
    }

    public function deleteRuleExample(User $context, int $id): bool
    {
        policy_authorize(ExampleRulePolicy::class, 'delete', $context);
        $rule = $this->find($id);
        if (!$rule instanceof ExampleRule) {
            abort(401, __p('phrase.permission_deny'));
        }

        $this->getPhraseRepository()->deleteWhere(['key' => $rule->title]);
        $this->getPhraseRepository()->deleteWhere(['key' => $rule->description]);

        return (bool) $rule->delete();
    }

    public function orderRuleExamples(User $context, array $orders): bool
    {
        policy_authorize(ExampleRulePolicy::class, 'update', $context);

        foreach ($orders as $id => $order) {
            ExampleRule::query()->where('id', $id)->update(['ordering' => $order]);
        }

        return true;
    }

    public function updateActive(User $context, int $id, int $isActive): bool
    {
        policy_authorize(ExampleRulePolicy::class, 'update', $context);
        $rule = $this->find($id);

        return $rule->update(['is_active' => $isActive]);
    }

    public function getAllActiveRuleExamples(User $context): Collection
    {
        policy_authorize(ExampleRulePolicy::class, 'viewAny', $context);

        return $this->getModel()->newQuery()
            ->where('is_active', '=', ExampleRule::IS_ACTIVE)
            ->orderBy('ordering')
            ->get();
    }

    public function getAllActiveRuleExsForForm(User $context): array
    {
        return $this->getAllActiveRuleExamples($context)->map(function (ExampleRule $ruleExample) {
            return [
                'title'       => __p($ruleExample->title),
                'description' => __p($ruleExample->description),
            ];
        })->toArray();
    }
}
