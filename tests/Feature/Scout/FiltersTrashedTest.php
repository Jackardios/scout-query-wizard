<?php

namespace Jackardios\ScoutQueryWizard\Tests\Feature\Scout;

use Jackardios\ScoutQueryWizard\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Jackardios\ScoutQueryWizard\ScoutQueryWizard;
use Jackardios\ScoutQueryWizard\Handlers\Filters\TrashedFilter;
use Jackardios\ScoutQueryWizard\Tests\App\Models\SoftDeleteModel;

/**
 * @group scout
 * @group filter
 * @group scout-filter
 */
class FiltersTrashedTest extends TestCase
{
    /** @var \Illuminate\Support\Collection */
    protected $models;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('scout.soft_delete', true);

        $this->models = factory(SoftDeleteModel::class, 2)->create()
            ->merge(factory(SoftDeleteModel::class, 1)->create(['deleted_at' => now()]));
    }

    /** @test */
    public function it_should_filter_not_trashed_by_default()
    {
        $models = $this
            ->createQueryFromFilterRequest([
                'trashed' => '',
            ])
            ->setAllowedFilters(new TrashedFilter())
            ->build()
            ->get();

        $this->assertCount(2, $models);
    }

    /** @test */
    public function it_can_filter_only_trashed()
    {
        $models = $this
            ->createQueryFromFilterRequest([
                'trashed' => 'only',
            ])
            ->setAllowedFilters(new TrashedFilter())
            ->build()
            ->get();

        $this->assertCount(1, $models);
    }

    /** @test */
    public function it_can_filter_with_trashed()
    {
        $models = $this
            ->createQueryFromFilterRequest([
                'trashed' => 'with',
            ])
            ->setAllowedFilters(new TrashedFilter())
            ->build()
            ->get();

        $this->assertCount(3, $models);
    }

    protected function createQueryFromFilterRequest(array $filters): ScoutQueryWizard
    {
        $request = new Request([
            'filter' => $filters,
        ]);

        return ScoutQueryWizard::for(SoftDeleteModel::search(), $request);
    }
}
