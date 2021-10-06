<?php

namespace Jackardios\ScoutQueryWizard\Handlers\Sorts;

class FieldSort extends AbstractScoutSort
{
    public function handle($queryHandler, $queryBuilder, string $direction): void
    {
        $queryBuilder->orderBy($this->getPropertyName(), $direction);
    }
}
