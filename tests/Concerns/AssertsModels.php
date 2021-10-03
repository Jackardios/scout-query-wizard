<?php

namespace Jackardios\ScoutQueryWizard\Tests\Concerns;

use Illuminate\Database\Eloquent\Model;
use Jackardios\ScoutQueryWizard\Tests\TestCase;

/**
 * @mixin TestCase
 */
trait AssertsModels
{
    protected function assertModelsAttributesEqual(Model $firstModel, Model $secondModel): void
    {
        $this->assertEquals($firstModel->getAttributes(), $secondModel->getAttributes());
    }
}
