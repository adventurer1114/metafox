<?php

namespace MetaFox\User\Support\Browse\Scopes\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\HasFeature;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Scopes\BaseScope;

class ViewScope extends BaseScope
{
    public const VIEW_DEFAULT = Browse::VIEW_ALL;
    public const VIEW_RECOMMEND = 'recommend';
    public const VIEW_FEATURED = 'featured';
    public const VIEW_RECENT = 'recent';

    /**
     * @return array<string>
     */
    public static function getAllowView(): array
    {
        return [
            self::VIEW_DEFAULT,
            self::VIEW_RECOMMEND,
            self::VIEW_FEATURED,
            self::VIEW_RECENT,
        ];
    }

    /**
     * @var string
     */
    private string $view = self::VIEW_DEFAULT;

    /**
     * @param  string    $view
     * @return ViewScope
     */
    public function setView(string $view): self
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @return string
     */
    public function getView(): string
    {
        return $this->view;
    }

    /**
     * @param Builder $builder
     * @param Model   $model
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function apply(Builder $builder, Model $model)
    {
        $view = $this->getView();
        $table = $model->getTable();

        switch ($view) {
            case self::VIEW_FEATURED:
                $builder->where($this->alias($table, 'is_featured'), HasFeature::IS_FEATURED);
                break;
            default:
                break;
        }
    }
}
