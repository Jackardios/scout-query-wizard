<?php

namespace Jackardios\ScoutQueryWizard;

use Illuminate\Support\Str;
use Jackardios\QueryWizard\Abstracts\AbstractQueryWizard;
use Jackardios\QueryWizard\Concerns\HandlesAppends;
use Jackardios\QueryWizard\Concerns\HandlesFields;
use Jackardios\QueryWizard\Concerns\HandlesFilters;
use Jackardios\QueryWizard\Concerns\HandlesIncludes;
use Jackardios\QueryWizard\Concerns\HandlesSorts;
use Jackardios\QueryWizard\Handlers\Eloquent\Includes\AbstractEloquentInclude;
use Jackardios\QueryWizard\Handlers\Eloquent\Includes\IncludedCount;
use Jackardios\QueryWizard\Handlers\Eloquent\Includes\IncludedRelationship;
use Jackardios\ScoutQueryWizard\Handlers\Filters\ExactFilter;
use Jackardios\ScoutQueryWizard\Handlers\Sorts\SortByField;
use Jackardios\ScoutQueryWizard\Handlers\ScoutQueryHandler;
use Laravel\Scout\Builder;

/**
 * @mixin Builder
 * @property ScoutQueryHandler $queryHandler
 * @method static ScoutQueryWizard for(Builder $subject, \Illuminate\Http\Request|null $request = null)
 */
class ScoutQueryWizard extends AbstractQueryWizard
{
    use HandlesAppends;
    use HandlesFields;
    use HandlesFilters;
    use HandlesIncludes;
    use HandlesSorts;

    protected string $queryHandlerClass = ScoutQueryHandler::class;

    protected function defaultFieldsKey(): string
    {
        return $this->queryHandler->getSubject()->model->getTable();
    }

    /**
     * Set the callback that should have an opportunity to modify the database query.
     * This method overrides the Scout Query Builder method
     *
     * @param  callable  $callback
     * @return $this
     */
    public function query(callable $callback): self
    {
        $this->queryHandler->addEloquentQueryCallback($callback);

        return $this;
    }

    public function makeDefaultFilterHandler(string $filterName): ExactFilter
    {
        return new ExactFilter($filterName);
    }

    /**
     * @param string $includeName
     * @return IncludedRelationship|IncludedCount
     */
    public function makeDefaultIncludeHandler(string $includeName): AbstractEloquentInclude
    {
        $countSuffix = config('query-wizard.count_suffix');
        if (Str::endsWith($includeName, $countSuffix)) {
            $relation = Str::before($includeName, $countSuffix);
            return new IncludedCount($relation, $includeName);
        }
        return new IncludedRelationship($includeName);
    }

    public function makeDefaultSortHandler(string $sortName): SortByField
    {
        return new SortByField($sortName);
    }
}
