<?php

use Faker\Generator as Faker;
use Jackardios\ScoutQueryWizard\Tests\App\Models\TestModel;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(TestModel::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
