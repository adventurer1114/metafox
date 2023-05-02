<?php

namespace MetaFox\Quiz\Models;

use Illuminate\Database\Eloquent\Model;

class QuizPrivacyStream extends Model
{
    protected $table = 'quiz_privacy_streams';

    public $timestamps = false;

    protected $fillable = [
        'privacy_id',
        'item_id',
    ];
}

// end
