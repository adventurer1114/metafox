<?php

namespace MetaFox\Group\Http\Resources\v1\Request;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Group\Models\Question;
use MetaFox\Group\Models\QuestionField;
use MetaFox\Group\Models\Request as Model;
use MetaFox\Group\Repositories\QuestionRepositoryInterface;
use MetaFox\User\Http\Resources\v1\User\UserItem;

/**
 * Class RequestItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class RequestItem extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     * @throws AuthenticationException
     */
    public function toArray($request): array
    {
        $answers = resolve(QuestionRepositoryInterface::class)
            ->getAnswersByRequestId(user(), $this->resource);
        $answers = $this->transformValue($answers);

        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => 'group',
            'resource_name' => $this->resource->entityType(),
            'user'          => new UserItem($this->resource->user),
            'group_id'      => $this->resource->group_id,
            'answers'       => $answers,
        ];
    }

    private function transformValue(Collection $collection): array
    {
        foreach ($collection as $key => $value) {
            $answers = $value['answers'];

            foreach ($answers as $keyAnswer => $answer) {
                /** @var Question $question */
                $question = Question::query()
                    ->with(['questionFields'])
                    ->where('id', $answer->question_id)->first();

                if ($question->type_id == Question::TYPE_TEXT) {
                    continue;
                }

                /** @var QuestionField $questionField */
                $questionField                = $question->questionFields->where('id', $answer->value)->first();
                $answers[$keyAnswer]['value'] = $questionField->title;
            }

            $collection[$key]['answers'] = $answers;
        }

        return $collection->toArray();
    }
}
