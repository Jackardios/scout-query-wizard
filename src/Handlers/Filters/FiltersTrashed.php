<?php

namespace Jackardios\ScoutQueryWizard\Handlers\Filters;

class FiltersTrashed extends AbstractScoutFilter
{
    public function __construct(string $propertyName = "trashed", ?string $alias = null, $default = null)
    {
        parent::__construct($propertyName, $alias, $default);
    }

    public function handle($queryHandler, $query, $value): void
    {
        if ($value === 'with') {
            $query->withTrashed();

            return;
        }

        if ($value === 'only') {
            $query->onlyTrashed();

            return;
        }

        $query->wheres['__soft_deleted'] = 0;
    }
}
