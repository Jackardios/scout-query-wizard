<?php

namespace Jackardios\ScoutQueryWizard\Handlers\Filters;

use Jackardios\ScoutQueryWizard\Handlers\ScoutQueryHandler;
use Laravel\Scout\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

class FiltersExact extends AbstractScoutFilter
{
    protected bool $withRelationConstraint = true;

    public function __construct(
        string $propertyName,
        ?string $alias = null,
        $default = null,
        $withRelationConstraint = true
    )
    {
        parent::__construct($propertyName, $alias, $default);
        $this->withRelationConstraint = $withRelationConstraint;
    }

    public function withRelationConstraint(bool $value = true): void
    {
        $this->withRelationConstraint = $value;
    }

    public function handle($queryHandler, $queryBuilder, $value): void
    {
        $propertyName = $this->getPropertyName();

        if ($this->withRelationConstraint && $this->isRelationProperty($queryBuilder, $propertyName)) {
            $this->addRelationConstraint($queryHandler, $value, $propertyName);

            return;
        }

        if (is_array($value)) {
            $queryBuilder->whereIn($propertyName, $value);

            return;
        }

        $queryBuilder->where($propertyName, $value);
    }

    protected function isRelationProperty(Builder $queryBuilder, string $propertyName): bool
    {
        if (! Str::contains($propertyName, '.')) {
            return false;
        }

        $firstRelationship = explode('.', $propertyName)[0];

        if (! method_exists($queryBuilder->model, $firstRelationship)) {
            return false;
        }

        return is_a($queryBuilder->model->{$firstRelationship}(), Relation::class);
    }

    protected function addRelationConstraint(ScoutQueryHandler $queryHandler, $value, string $propertyName): void
    {
        $relation = Str::beforeLast($propertyName, '.');
        $propertyName = Str::afterLast($propertyName, '.');

        $queryHandler->addEloquentQueryCallback(function(EloquentBuilder $query) use ($relation, $propertyName, $value) {
            $query->whereHas($relation, function (EloquentBuilder $query) use ($propertyName, $value) {
                if (is_array($value)) {
                    $query->whereIn($query->qualifyColumn($propertyName), $value);

                    return;
                }

                $query->where($query->qualifyColumn($propertyName), '=', $value);
            });
        });
    }
}
