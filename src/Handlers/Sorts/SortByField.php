<?php

namespace Jackardios\ScoutQueryWizard\Handlers\Sorts;

class SortByField extends AbstractScoutSort
{
    public function handle($queryHandler, $queryBuilder, string $direction): void
    {
        $queryBuilder->orderBy($this->getPropertyName(), $direction);
    }
}
