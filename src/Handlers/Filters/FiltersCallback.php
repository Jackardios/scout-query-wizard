<?php

namespace Jackardios\ScoutQueryWizard\Handlers\Filters;

class FiltersCallback extends AbstractScoutFilter
{
    /**
     * @var callable a PHP callback of the following signature:
     * `function (\Jackardios\ScoutQueryWizard\Handlers\ScoutQueryHandler $queryHandler, \Laravel\Scout\Builder $builder, mixed $value)`
     */
    private $callback;

    /**
     * @param string $propertyName
     * @param callable $callback a PHP callback of the following signature:
     * `function (\Jackardios\ScoutQueryWizard\Handlers\ScoutQueryHandler $queryHandler, \Laravel\Scout\Builder $builder, mixed $value)`
     * @param string|null $alias
     * @param mixed $default
     */
    public function __construct(string $propertyName, callable $callback, ?string $alias = null, $default = null)
    {
        parent::__construct($propertyName, $alias, $default);

        $this->callback = $callback;
    }

    public function handle($queryHandler, $query, $value): void
    {
        call_user_func($this->callback, $queryHandler, $query, $value);
    }
}
