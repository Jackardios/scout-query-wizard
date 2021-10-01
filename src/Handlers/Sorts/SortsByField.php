<?php

namespace Jackardios\ScoutQueryWizard\Handlers\Sorts;

class SortsByField extends AbstractScoutSort
{
    public function handle($queryHandler, $query, string $direction): void
    {
        $query->orderBy($this->getPropertyName(), $direction);
    }
}
