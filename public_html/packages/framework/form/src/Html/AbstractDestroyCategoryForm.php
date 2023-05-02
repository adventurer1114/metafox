<?php

namespace MetaFox\Form\Html;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\Section;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Repositories\Contracts\CategoryRepositoryInterface;
use MetaFox\Yup\Yup;

/**
 * Class AbstractDestroyCategoryForm.
 */
abstract class AbstractDestroyCategoryForm extends AbstractForm
{
    /**
     * @var CategoryRepositoryInterface
     *                                  warn: unitest might not assign mock there.
     */
    protected $repository;

    protected function prepare(): void
    {
        $this->title(__p('core::phrase.delete_category'))
            ->action($this->getActionUrl())
            ->asDelete()
            ->setValue([
                'migrate_items' => 0,
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic([]);
        $this->handleConfirm($basic);

        $this->addFooter()
            ->addFields(
                Builder::submit()->label(__p('core::phrase.delete')),
                Builder::cancelButton(),
            );
    }

    /**
     * @return array<string,mixed>
     */
    protected function getParentCategoryOptions(): array
    {
        $query = $this->repository->getCategoriesForForm();
        $query = collect($query)
            ->where('value', '<>', $this->resource->entityId())
            ->where('level', '<=', $this->resource->level);

        if (count($this->resource?->subCategories) > 0) {
            $query->where('level', '<', MetaFoxConstant::MAX_CATEGORY_LEVEL);
        }

        return $query->toArray();
    }

    /**
     * @return array
     */
    protected function getDeleteOptions(): array
    {
        $categoryOptions = $this->getParentCategoryOptions();

        $options[] = [
            'label' => __p('core::phrase.delete_category_option_delete_all_items', [
                'type' => $this->getPluralizationItemType(),
            ]),
            'value' => 0,
        ];

        if ($categoryOptions) {
            array_push($options, [
                'label' => __p('core::phrase.delete_category_option_move_all_items', [
                    'type' => $this->getPluralizationItemType(),
                ]),
                'value' => 1,
            ]);
        }

        return $options;
    }

    /**
     * @return string
     */
    abstract protected function getActionUrl(): string;

    /**
     * @return string
     */
    abstract protected function getPluralizationItemType(): string;

    protected function handleConfirm(Section $basic): Section
    {
        $basic           = $this->addBasic([]);
        $categoryOptions = $this->getParentCategoryOptions();
        $deleteOptions   = $this->getDeleteOptions();

        $totalItem = $this->resource->totalItem;
        if ($totalItem == 0 && $this->resource->subCategories()->exists()) {
            return $basic->addFields(
                Builder::typography('delete_confirm')
                    ->tagName('strong')
                    ->plainText(__p('core::phrase.delete_category_confirm', ['name' => $this->resource->name]))
            );
        }
        $basic->addFields(
            Builder::typography('delete_confirm')
                ->tagName('strong')
                ->plainText(__p('core::phrase.delete_category_confirm', ['name' => $this->resource->name])),
            Builder::description('delete_notice')
                ->label(__p('core::phrase.action_cant_be_undone')),
            Builder::radioGroup('migrate_items')
                ->label(__p('core::phrase.delete_category_option_label', [
                    'type' => $this->getPluralizationItemType(),
                ]))
                ->options($deleteOptions)
                ->yup(Yup::string()->required()),
        );

        if ($categoryOptions) {
            $basic->addField(Builder::choice('new_category_id')
                ->label(__p('core::phrase.category'))
                ->requiredWhen(['eq', 'migrate_items', 1])
                ->options($categoryOptions)
                ->yup(
                    Yup::number()
                        ->positive()
                        ->nullable(true)
                ));
        }

        return $basic;
    }
}
