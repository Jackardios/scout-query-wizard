<?php

use Faker\Generator as Faker;
use Jackardios\ScoutQueryWizard\Tests\TestClasses\Models\SoftDeleteModel;

$factory->define(SoftDeleteModel::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
