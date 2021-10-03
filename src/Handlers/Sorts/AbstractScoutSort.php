<?php

namespace Jackardios\ScoutQueryWizard\Handlers\Sorts;

use Laravel\Scout\Builder;
use Jackardios\QueryWizard\Abstracts\Handlers\Sorts\AbstractSort;
use Jackardios\ScoutQueryWizard\Handlers\ScoutQueryHandler;

abstract class AbstractScoutSort extends AbstractSort
{
    /**
     * @param ScoutQueryHandler $queryHandler
     * @param Builder $queryBuilder
     * @param string $direction
     */
    abstract public function handle($queryHandler, $queryBuilder, string $direction): void;
}
