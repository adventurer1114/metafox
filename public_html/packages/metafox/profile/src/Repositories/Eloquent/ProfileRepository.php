<?php

namespace MetaFox\Profile\Repositories\Eloquent;

use ArrayObject;
use Illuminate\Support\Arr;
use MetaFox\Form\AbstractForm;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Profile\Models\Field;
use MetaFox\Profile\Models\Profile;
use MetaFox\Profile\Models\Section;
use MetaFox\Profile\Models\Value;
use MetaFox\Profile\Repositories\ProfileRepositoryInterface;

/**
 * stub: /packages/repositories/eloquent_repository.stub.
 */

/**
 * class ProfileRepository.
 */
class ProfileRepository extends AbstractRepository implements ProfileRepositoryInterface
{
    public function model()
    {
        return Profile::class;
    }

    public function loadEditFields(AbstractForm $form, $user, ?string $resolution = null): void
    {
        /** @var Section[] $sections */
        $sections = Section::query()
            ->where('is_active', 1)
            ->orderBy('ordering', 'asc')
            ->get();

        foreach ($sections as $item) {
            $section = $form->addSection($item->name)
                ->label($item->label)
                ->description($item->description);

            $this->loadFieldInSection($section, $item->id, $resolution);
        }
    }

    private function loadFieldInSection($section, int $id, ?string $resolution = null): void
    {
        /** @var Field[] $fields */
        $fields = Field::query()
            ->where('section_id', $id)
            ->where('is_active', 1)
            ->get();

        foreach ($fields as $field) {
            $section->addField($field->toEditField($resolution));
        }
    }

    public function loadEditRules(ArrayObject $rules)
    {
        /** @var Field[] $fields */
        $fields = Field::query()
            ->where('is_active', '=', 1)
            ->get();

        foreach ($fields as $field) {
            $rules[$field->field_name] = $field->toRule();
        }
    }

    /**
     * @return string[]
     */
    public function getFieldNames(): array
    {
        $result = [];

        /** @var Field[] $fields */
        $fields = Field::query()->newQuery()
            ->where('is_active', '=', 1)
            ->get();

        foreach ($fields as $field) {
            $result[] = $field->field_name;
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getFieldMaps(): array
    {
        $result = [];

        /** @var Field[] $fields */
        $fields = Field::query()
            ->where('is_active', '=', 1)
            ->get();

        foreach ($fields as $field) {
            $result[$field->field_name] = $field->id;
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getStructure(): array
    {
        $byIds = [];

        /** @var Field[] $fields */
        $fields = Field::query()
            ->where('is_active', '=', 1)
            ->get();

        foreach ($fields as $field) {
            $byIds['#' . $field->id] = $field->field_name;
        }

        return [
            'byIds' => $byIds,
        ];
    }

    public function saveValues($user, array $input): void
    {
        foreach ($this->getFieldMaps() as $name => $id) {
            $value = Arr::get($input, $name);

            if (null === $value) {
                continue;
            }

            $item = Value::query()->firstOrNew([
                'user_id'   => $user->id,
                'user_type' => $user->entityType(),
                'field_id'  => $id,
            ], [
                'field_value_text' => $value,
            ]);

            $item->fill([
                'field_value_text' => $value,
            ]);

            $item->saveQuietly();
        }
    }

    public function denormalize($user): array
    {
        $result = [];

        $structure = $this->getStructure();

        $customValue = Value::query()->where('user_id', '=', $user->id)
            ->get(['field_id', 'field_value_text', 'ordering']);

        foreach ($customValue as $value) {
            $id    = $value['field_id'];
            $value = $value['field_value_text'];
            $name  = Arr::get($structure, 'byIds.#' . $id);

            if (!$name) {
                continue;
            }
            Arr::set($result, $name, $value);
        }

        return $result;
    }

    public function viewSections($user, ArrayObject $response): void
    {
        $custom = $this->denormalize($user);

        /** @var Section[] $items */
        $items = Section::query()->where('is_active', 1)
            ->orderBy('ordering', 'asc')
            ->get();

        foreach ($items as $item) {
            $fields = $this->viewFields($item->id, $user, $custom);

            $empty = count(array_values($fields));

            if (!$empty) {
                continue;
            }

            $response[$item->name] = [
                'label'       => $item->label,
                'description' => $item->description,
                'fields'      => $fields,
            ];
        }
    }

    public function viewFields($section, $user, array &$data): array
    {
        $response = [];

        /** @var Field[] $fields */
        $fields = Field::query()->where('section_id', $section)
            ->where('is_active', 1)
            ->orderBy('ordering', 'asc')
            ->get();

        foreach ($fields as $field) {
            $value = $data[$field->name] ?? null;
            if (null === $value) {
                continue;
            }

            $response[$field->name] = [
                'label'       => $field->label,
                'description' => $field->description,
                'value'       => $value,
            ];
        }

        return $response;
    }
}
