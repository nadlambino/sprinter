<?php

namespace App\QueryBuilders\Task;

use App\Models\Task;
use App\QueryBuilders\Builder as QueryBuildersBuilder;
use App\QueryBuilders\Filters\TrashedFilter;
use App\QueryBuilders\Task\Filters\PublishedFilter;
use App\QueryBuilders\Task\Filters\SearchFilter;
use App\QueryBuilders\Task\Filters\StatusFilter;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use NadLambino\QueryBuilder\AllowedFilter;
use NadLambino\QueryBuilder\QueryBuilder;

class TaskBuilder
{
    use QueryBuildersBuilder;

    public function of(Authenticatable|Model $owner): static
    {
        $this->builder = $owner->tasks();

        return $this;
    }

    public function build(): QueryBuilder
    {
        $builder = isset($this->builder) ? $this->builder : Task::query();

        return QueryBuilder::for($builder, $this->getSource())
            ->selectRaw('tasks.*, (TIMESTAMPDIFF(MINUTE, tasks.started_at, tasks.ended_at) / 60) as time_spent')
            ->whereHas('status')
            ->allowedFilters([
                AllowedFilter::exact('parent_id'),
                AllowedFilter::exact('status_id'),
                AllowedFilter::custom('search', new SearchFilter),
                AllowedFilter::custom('status', new StatusFilter),
                AllowedFilter::custom('published', new PublishedFilter),
                AllowedFilter::custom('trashed', new TrashedFilter)
            ])
            ->allowedIncludes(['status', 'images', 'parent', 'children'])
            ->defaultSort('created_at')
            ->allowedSorts(['title', 'created_at']);
    }
}
