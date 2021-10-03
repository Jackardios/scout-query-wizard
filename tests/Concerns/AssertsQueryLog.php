<?php

namespace Jackardios\ScoutQueryWizard\Tests\Concerns;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Jackardios\ScoutQueryWizard\Tests\TestCase;

/**
 * @mixin TestCase
 */
trait AssertsQueryLog
{
    protected function assertQueryLogContains(string $partialSql): void
    {
        $queryLog = collect(DB::getQueryLog())->pluck('query')->implode('|');

        // Could've used `assertStringContainsString` but we want to support L5.5 with PHPUnit 6.0
        $this->assertTrue(Str::contains($queryLog, $partialSql));
    }

    protected function assertQueryLogDoesntContain(string $partialSql): void
    {
        $queryLog = collect(DB::getQueryLog())->pluck('query')->implode('|');

        // Could've used `assertStringContainsString` but we want to support L5.5 with PHPUnit 6.0
        $this->assertFalse(Str::contains($queryLog, $partialSql), "Query log contained partial SQL: `{$partialSql}`");
    }
}
