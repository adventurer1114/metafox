<?php

namespace MetaFox\Like\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

Route::resource('reaction', ReactionController::class)->except(['delete']);
