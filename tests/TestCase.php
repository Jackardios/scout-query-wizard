<?php

namespace Jackardios\ScoutQueryWizard\Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Jackardios\QueryWizard\QueryWizardServiceProvider;
use Jackardios\ScoutQueryWizard\Tests\Concerns\AssertsModels;
use Jackardios\ScoutQueryWizard\Tests\Concerns\AssertsQueryLog;
use Laravel\Scout\ScoutServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use DatabaseMigrations;
    use AssertsQueryLog;
    use AssertsModels;

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            QueryWizardServiceProvider::class,
            ScoutServiceProvider::class
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/App/data/migrations');
        $this->withFactories(__DIR__ . '/App/data/factories');
    }
}
