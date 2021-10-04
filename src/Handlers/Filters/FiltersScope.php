<?php

namespace Jackardios\ScoutQueryWizard\Handlers\Filters;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Jackardios\QueryWizard\Handlers\Eloquent\Filters\FiltersScope as EloquentFiltersScope;

class FiltersScope extends AbstractScoutFilter
{
    public function handle($queryHandler, $queryBuilder, $value): void
    {
        $eloquentFilter = EloquentFiltersScope::makeFromOther($this);
        $queryHandler->addEloquentQueryCallback(function(EloquentBuilder $queryBuilder) use ($eloquentFilter, $queryHandler, $value) {
            $eloquentFilter->handle($queryHandler, $queryBuilder, $value);
        });
    }
}
