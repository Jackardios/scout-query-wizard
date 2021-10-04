<?php

namespace Jackardios\ScoutQueryWizard\Handlers\Filters;

class FiltersTrashed extends AbstractScoutFilter
{
    public function __construct(string $propertyName = "trashed", ?string $alias = null, $default = null)
    {
        parent::__construct($propertyName, $alias, $default);
    }

    public function handle($queryHandler, $queryBuilder, $value): void
    {
        if ($value === 'with') {
            $queryBuilder->withTrashed();

            return;
        }

        if ($value === 'only') {
            $queryBuilder->onlyTrashed();

            return;
        }

        $queryBuilder->wheres['__soft_deleted'] = 0;
    }
}
