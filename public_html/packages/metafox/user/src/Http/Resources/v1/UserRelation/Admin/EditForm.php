<?php

namespace MetaFox\User\Http\Resources\v1\UserRelation\Admin;

use MetaFox\Localize\Repositories\PhraseRepositoryInterface;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class EditForm.
 */
class EditForm extends CreateForm
{
    protected function prepare(): void
    {
        $file    = null;
        $imageId = $this->resource->image_file_id;

        if ($imageId) {
            $file = [
                'id'        => $imageId,
                'temp_file' => $imageId,
                'status'    => 'keep',
            ];
        }
        $phrase = resolve(PhraseRepositoryInterface::class)->getPhrasesByKey($this->resource->phrase_var);
        $this->title(__p('user::phrase.edit_relation'))
            ->action("/admincp/user/relation/{$this->resource->id}")
            ->asPut()
            ->setValue([
                'locale'     => $phrase->locale,
                'is_active'  => $this->resource->is_active,
                'phrase_var' => $this->resource->phrase_var,
                'title'      => __p($this->resource->phrase_var),
                'file'       => $file,
            ]);
    }

    protected function isEdit()
    {
        return true;
    }
}
