<?php

namespace Jackardios\ScoutQueryWizard\Tests\Feature\Scout;

use Jackardios\ScoutQueryWizard\Tests\TestCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Jackardios\QueryWizard\Exceptions\InvalidFilterQuery;
use Jackardios\ScoutQueryWizard\Handlers\Filters\AbstractScoutFilter;
use Jackardios\ScoutQueryWizard\ScoutQueryWizard;
use Jackardios\ScoutQueryWizard\Handlers\Filters\ExactFilter;
use Jackardios\ScoutQueryWizard\Handlers\Filters\ScopeFilter;
use Jackardios\ScoutQueryWizard\Tests\App\Models\TestModel;

/**
 * @group scout
 * @group filter
 * @group scout-filter
 */
class FilterTest extends TestCase
{
    /** @var Collection */
    protected $models;

    public function setUp(): void
    {
        parent::setUp();

        $this->models = factory(TestModel::class, 5)->create();
    }

    /** @test */
    public function it_can_filter_models_by_exact_property_by_default(): void
    {
        $expectedModel = $this->models->random();
        $modelsResult = $this
            ->createQueryFromFilterRequest([
                'name' => $expectedModel->name,
            ])
            ->setAllowedFilters('name')
            ->build()
            ->get();

        $this->assertCount(1, $modelsResult);
        $this->assertEquals($expectedModel->name, $modelsResult->first()->name);
    }

    /** @test */
    public function it_can_filter_models_by_an_array_as_filter_value(): void
    {
        $expectedModel = $this->models->random();
        $modelsResult = $this
            ->createQueryFromFilterRequest([
                'name' => ['first' => $expectedModel->name],
            ])
            ->setAllowedFilters('name')
            ->build()
            ->get();

        $this->assertCount(1, $modelsResult);
        $this->assertEquals($expectedModel->name, $modelsResult->first()->name);
    }

    /** @test */
    public function it_can_filter_partially_and_case_insensitive(): void
    {
        $expectedModel = $this->models->random();
        $modelsResult = $this
            ->createQueryFromFilterRequest([
                'name' => strtoupper($expectedModel->name),
            ])
            ->setAllowedFilters('name')
            ->build()
            ->get();

        $this->assertCount(1, $modelsResult);
        $this->assertEquals($expectedModel->name, $modelsResult->first()->name);
    }

    /** @test */
    public function it_can_filter_models_and_return_an_empty_collection(): void
    {
        $modelsResult = $this
            ->createQueryFromFilterRequest([
                'name' => 'None existing first name',
            ])
            ->setAllowedFilters('name')
            ->build()
            ->get();

        $this->assertCount(0, $modelsResult);
    }

    /** @test */
    public function it_can_filter_a_custom_base_query_with_select(): void
    {
        $expectedModel = TestModel::query()
            ->select(['id', 'name'])
            ->find($this->models->random()->id);

        $request = new Request([
            'filter' => ['name' => $expectedModel->name],
        ]);

        $resultModel = ScoutQueryWizard::for(TestModel::search(), $request)
            ->query(function(Builder $query) {
                return $query->select('id', 'name');
            })
            ->setAllowedFilters('name', 'id')
            ->build()
            ->first();

        $this->assertModelsAttributesEqual($expectedModel, $resultModel);
    }

    /** @test */
    public function it_can_filter_results_based_on_the_existence_of_a_property_in_an_array(): void
    {
        $results = $this
            ->createQueryFromFilterRequest([
                'id' => '1,2',
            ])
            ->setAllowedFilters('id')
            ->build()
            ->get();

        $this->assertCount(2, $results);
        $this->assertEqualsCanonicalizing([1, 2], $results->pluck('id')->all());
    }

    /** @test */
    public function it_can_filter_and_match_results_by_exact_property(): void
    {
        $expectedModel = $this->models->random();

        $modelsResult = $this
            ->createQueryFromFilterRequest([
                'id' => $expectedModel->id,
            ])
            ->setAllowedFilters(new ExactFilter('id'))
            ->build()
            ->get();

        $this->assertCount(1, $modelsResult);
        $this->assertEquals($expectedModel->id, $modelsResult->first()->id);
    }

    /** @test */
    public function it_can_filter_and_reject_results_by_exact_property(): void
    {
        TestModel::create(['name' => 'John Testing Doe']);

        $modelsResult = $this
            ->createQueryFromFilterRequest([
                'name' => ' Testing ',
            ])
            ->setAllowedFilters(new ExactFilter('name'))
            ->build()
            ->get();

        $this->assertCount(0, $modelsResult);
    }

    /** @test */
    public function it_can_filter_results_by_scope(): void
    {
        $expectedModel = TestModel::create(['name' => 'John Testing Doe']);

        $modelsResult = $this
            ->createQueryFromFilterRequest(['named' => 'John Testing Doe'])
            ->setAllowedFilters(new ScopeFilter('named'))
            ->build()
            ->get();

        $this->assertCount(1, $modelsResult);
        $this->assertEquals($expectedModel->id, $modelsResult->first()->id);
    }

    /** @test */
    public function it_can_filter_results_by_nested_relation_scope(): void
    {
        $expectedModel = TestModel::create(['name' => 'John Testing Doe 234234']);
        $expectedModel->relatedModels()->create(['name' => 'John\'s Post']);

        $modelsResult = $this
            ->createQueryFromFilterRequest(['relatedModels.named' => 'John\'s Post'])
            ->setAllowedFilters(new ScopeFilter('relatedModels.named'))
            ->build()
            ->get();

        $this->assertCount(1, $modelsResult);
        $this->assertEquals($expectedModel->id, $modelsResult->first()->id);
    }

    /** @test */
    public function it_can_filter_results_by_type_hinted_scope(): void
    {
        TestModel::create(['name' => 'John Testing Doe']);

        $modelsResult = $this
            ->createQueryFromFilterRequest(['user' => 1])
            ->setAllowedFilters(new ScopeFilter('user'))
            ->build()
            ->get();

        $this->assertCount(1, $modelsResult);
    }

    /** @test */
    public function it_can_filter_results_by_regular_and_type_hinted_scope(): void
    {
        $expectedModel = TestModel::create(['id' => 1000, 'name' => 'John Testing Doe']);

        $modelsResult = $this
            ->createQueryFromFilterRequest(['user_info' => ['id' => '1000', 'name' => 'John Testing Doe']])
            ->setAllowedFilters(new ScopeFilter('user_info'))
            ->build()
            ->get();

        $this->assertCount(1, $modelsResult);
        $this->assertEquals($expectedModel->id, $modelsResult->first()->id);
    }

    /** @test */
    public function it_can_filter_results_by_scope_with_multiple_parameters(): void
    {
        Carbon::setTestNow(Carbon::parse('2016-05-05'));

        $expectedModel = TestModel::create(['name' => 'John Testing Doe']);

        $modelsResult = $this
            ->createQueryFromFilterRequest(['created_between' => '2016-01-01,2017-01-01'])
            ->setAllowedFilters(new ScopeFilter('created_between'))
            ->build()
            ->get();

        $this->assertCount(1, $modelsResult);
        $this->assertEquals($expectedModel->id, $modelsResult->first()->id);
    }

    /** @test */
    public function it_can_filter_results_by_scope_with_multiple_parameters_in_an_associative_array(): void
    {
        Carbon::setTestNow(Carbon::parse('2016-05-05'));

        $expectedModel = TestModel::create(['name' => 'John Testing Doe']);

        $modelsResult = $this
            ->createQueryFromFilterRequest(['created_between' => ['start' => '2016-01-01', 'end' => '2017-01-01']])
            ->setAllowedFilters(new ScopeFilter('created_between'))
            ->build()
            ->get();

        $this->assertCount(1, $modelsResult);
        $this->assertEquals($expectedModel->id, $modelsResult->first()->id);
    }

    /** @test */
    public function it_can_filter_results_by_a_custom_filter_class(): void
    {
        $expectedModel = $this->models->random();

        $filterClass = new class('custom_name') extends AbstractScoutFilter {
            public function handle($queryHandler, $queryBuilder, $value): void
            {
                $queryBuilder->where('name', $value);
            }
        };

        $modelsResult = $this
            ->createQueryFromFilterRequest([
                'custom_name' => $expectedModel->name,
            ])
            ->setAllowedFilters($filterClass)
            ->build()
            ->get();

        $this->assertCount(1, $modelsResult);
        $this->assertEquals($expectedModel->id, $modelsResult->first()->id);
    }

    /** @test */
    public function it_can_allow_multiple_filters(): void
    {
        $model1 = TestModel::create(['name' => 'abcdef']);
        $model2 = TestModel::create(['name' => 'abcdef']);

        $results = $this
            ->createQueryFromFilterRequest([
                'name' => 'abcdef',
            ])
            ->setAllowedFilters(new ExactFilter('name'), 'id')
            ->build()
            ->get();

        $this->assertCount(2, $results);
        $this->assertEqualsCanonicalizing([$model1->id, $model2->id], $results->pluck('id')->all());
    }

    /** @test */
    public function it_can_allow_multiple_filters_as_an_array(): void
    {
        $model1 = TestModel::create(['name' => 'abcdef']);
        $model2 = TestModel::create(['name' => 'abcdef']);

        $results = $this
            ->createQueryFromFilterRequest([
                'name' => 'abcdef',
            ])
            ->setAllowedFilters([new ExactFilter('name'), 'id'])
            ->build()
            ->get();

        $this->assertCount(2, $results);
        $this->assertEqualsCanonicalizing([$model1->id, $model2->id], $results->pluck('id')->all());
    }

    /** @test */
    public function it_can_filter_by_multiple_filters(): void
    {
        $model1 = TestModel::create(['name' => 'abcdef']);
        $model2 = TestModel::create(['name' => 'abcdef']);

        $results = $this
            ->createQueryFromFilterRequest([
                'name' => 'abcdef',
                'id' => "1,{$model1->id}",
            ])
            ->setAllowedFilters(new ExactFilter('name'), 'id')
            ->build()
            ->get();

        $this->assertCount(1, $results);
        $this->assertEquals([$model1->id], $results->pluck('id')->all());
    }

    /** @test */
    public function it_guards_against_invalid_filters(): void
    {
        $this->expectException(InvalidFilterQuery::class);

        $this
            ->createQueryFromFilterRequest(['name' => 'John'])
            ->setAllowedFilters('id')
            ->build();
    }

    /** @test */
    public function it_can_create_a_custom_filter_with_an_instantiated_filter(): void
    {
        $customFilter = new class('*') extends AbstractScoutFilter {
            public function handle($queryHandler, $queryBuilder, $value): void
            {
                //
            }
        };

        TestModel::create(['name' => 'abcdef']);

        $results = $this
            ->createQueryFromFilterRequest([
                '*' => '*',
            ])
            ->setAllowedFilters('name', $customFilter)
            ->build()
            ->get();

        $this->assertNotEmpty($results);
    }

    /** @test */
    public function an_invalid_filter_query_exception_contains_the_unknown_and_allowed_filters(): void
    {
        $exception = new InvalidFilterQuery(collect(['unknown filter']), collect(['allowed filter']));

        $this->assertEquals(['unknown filter'], $exception->unknownFilters->all());
        $this->assertEquals(['allowed filter'], $exception->allowedFilters->all());
    }

    /** @test */
    public function it_sets_property_column_name_to_property_name_by_default(): void
    {
        $filter = new ExactFilter('property_name');

        $this->assertEquals($filter->getName(), $filter->getPropertyName());
    }

    /** @test */
    public function it_resolves_queries_using_property_column_name(): void
    {
        $filter = new ExactFilter('name', 'nickname');

        TestModel::create(['name' => 'abcdef']);

        $models = $this
            ->createQueryFromFilterRequest([
                'nickname' => 'abcdef',
            ])
            ->setAllowedFilters($filter)
            ->build()
            ->get();

        $this->assertCount(1, $models);
    }

    /** @test */
    public function it_can_filter_using_boolean_flags(): void
    {
        TestModel::query()->update(['is_visible' => true]);
        $filter = new ExactFilter('is_visible');

        $models = $this
            ->createQueryFromFilterRequest(['is_visible' => 'false'])
            ->setAllowedFilters($filter)
            ->build()
            ->get();

        $this->assertCount(0, $models);
        $this->assertGreaterThan(0, TestModel::all()->count());
    }

    /** @test */
    public function it_should_apply_a_default_filter_value_if_nothing_in_request(): void
    {
        $model1 = TestModel::create(['name' => 'UniqueJohn Doe']);
        $model2 = TestModel::create(['name' => 'UniqueJohn Deer']);

        $filter = (new ExactFilter('name'))->default('UniqueJohn Doe');

        $models = $this
            ->createQueryFromFilterRequest([])
            ->setAllowedFilters($filter)
            ->build()
            ->get();

        $this->assertEquals(1, $models->count());
        $this->assertEquals($models[0]->name, $model1->name);
    }

    /** @test */
    public function it_does_not_apply_default_filter_when_filter_exists_and_default_is_set(): void
    {
        $model1 = TestModel::create(['name' => 'UniqueJohn UniqueDoe']);
        $model2 = TestModel::create(['name' => 'UniqueJohn Deer']);

        $filter = (new ExactFilter('name'))->default('UniqueJohn Deer');

        $models = $this
            ->createQueryFromFilterRequest([
                'name' => 'UniqueJohn UniqueDoe',
            ])
            ->setAllowedFilters($filter)
            ->build()
            ->get();

        $this->assertEquals(1, $models->count());
        $this->assertEquals($models[0]->name, $model1->name);
    }

    protected function createQueryFromFilterRequest(array $filters): ScoutQueryWizard
    {
        $request = new Request([
            'filter' => $filters,
        ]);

        return ScoutQueryWizard::for(TestModel::search(), $request);
    }
}
