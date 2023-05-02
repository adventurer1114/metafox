<?php

namespace MetaFox\Forum\Http\Requests\v1\ForumPost;

use Illuminate\Foundation\Http\FormRequest;

class PosterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'thread_id' => ['required', 'exists:forum_threads,id'],
        ];
    }
}
