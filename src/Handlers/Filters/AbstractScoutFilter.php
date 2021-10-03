<?php

namespace Jackardios\ScoutQueryWizard\Handlers\Filters;

use Laravel\Scout\Builder;
use Jackardios\QueryWizard\Abstracts\Handlers\Filters\AbstractFilter;
use Jackardios\ScoutQueryWizard\Handlers\ScoutQueryHandler;

abstract class AbstractScoutFilter extends AbstractFilter
{
    /**
     * @param ScoutQueryHandler $queryHandler
     * @param Builder $queryBuilder
     * @param mixed $value
     */
    abstract public function handle($queryHandler, $queryBuilder, $value): void;
}
