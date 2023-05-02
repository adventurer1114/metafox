<?php

namespace MetaFox\Platform\Traits\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait OrderCategoryTrait
{
    public function order(Request $request): JsonResponse
    {
        $orderIds = $request->get('order_ids');

        $this->repository->orderCategories($orderIds);

        return $this->success([], [], __p('core::phrase.categories_successfully_ordered'));
    }
}
