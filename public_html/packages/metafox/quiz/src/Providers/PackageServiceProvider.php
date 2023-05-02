<?php

namespace MetaFox\Quiz\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use MetaFox\Platform\Support\EloquentModelObserver;
use MetaFox\Quiz\Models\Question;
use MetaFox\Quiz\Models\Quiz;
use MetaFox\Quiz\Models\QuizText;
use MetaFox\Quiz\Models\Result;
use MetaFox\Quiz\Observers\QuestionObserver;
use MetaFox\Quiz\Observers\QuizObserver;
use MetaFox\Quiz\Observers\ResultObserver;
use MetaFox\Quiz\Repositories\Eloquent\QuestionRepository;
use MetaFox\Quiz\Repositories\Eloquent\QuizRepository;
use MetaFox\Quiz\Repositories\Eloquent\ResultRepository;
use MetaFox\Quiz\Repositories\QuestionRepositoryInterface;
use MetaFox\Quiz\Repositories\QuizRepositoryInterface;
use MetaFox\Quiz\Repositories\ResultRepositoryInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PackageServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            Quiz::ENTITY_TYPE => Quiz::class,
        ]);

        Quiz::observe([EloquentModelObserver::class, QuizObserver::class]);
        QuizText::observe([EloquentModelObserver::class]);
        Question::observe([QuestionObserver::class]);
        Result::observe([ResultObserver::class, EloquentModelObserver::class]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(QuizRepositoryInterface::class, QuizRepository::class);
        $this->app->bind(ResultRepositoryInterface::class, ResultRepository::class);
        $this->app->bind(QuestionRepositoryInterface::class, QuestionRepository::class);
    }
}
