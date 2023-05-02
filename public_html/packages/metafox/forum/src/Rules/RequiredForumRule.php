<?php

namespace MetaFox\Forum\Rules;

use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;
use MetaFox\Forum\Repositories\ForumRepositoryInterface;

class RequiredForumRule implements
    Rule,
    DataAwareRule,
    ValidatorAwareRule,
    ImplicitRule
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var Validator
     */
    protected $validator;

    public function passes($attribute, $value)
    {
        $data = $this->data;

        $isWiki = Arr::has($data, 'is_wiki') && $data['is_wiki'] == 1;

        if (!$isWiki) {
            return $this->hasExists($attribute, $value);
        }

        return true;
    }

    protected function isValidNumeric($attribute, $value): bool
    {
        return $this->validator->validateNumeric($attribute, $value) && $value > 0;
    }

    protected function hasExists($attribute, $value): bool
    {
        if (!$this->isValidNumeric($attribute, $value)) {
            return false;
        }

        if (!$this->validator->validateExists($attribute, $value, ['forums', 'id'])) {
            return false;
        }

        $forum = resolve(ForumRepositoryInterface::class)->find($value);

        if ($forum->is_closed) {
            return false;
        }

        return true;
    }

    public function message()
    {
        return __p('forum::validation.forum_id.required');
    }

    public function setData($data): void
    {
        $this->data = $data;
    }

    public function setValidator($validator): void
    {
        $this->validator = $validator;
    }
}
