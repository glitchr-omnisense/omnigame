<?php

namespace Omnigame\Tests;

use Omnigame\Http\Data;
use Omnigame\Registry;
use Omnigame\Testing\BareTestCase;

/**
 * omnisense/omnigame with no framework: the harness's bare script searches,
 * reads a game, a library and ownership, and makes a provider's JSON call,
 * in a PHP process of its own - nothing may load from outside the package
 * but Symfony's HTTP client contracts (interfaces only).
 */
final class BareTest extends BareTestCase
{
    protected static function entryClass(): string
    {
        return Registry::class;
    }

    protected static function requiredPackages(): array
    {
        return ['symfony/http-client-contracts'];
    }

    public function testGamesReadByHandLoadNothingButOmnigameAndWhatItRequires(): void
    {
        $report = Data::of(self::php([__DIR__.'/../docker/harness/bin/bare']));

        self::assertSame(['search', 'catalogue', 'library', 'ownership'], $report->get('capabilities.contracts'));
        self::assertSame(['Portal 2'], $report->get('search'));
        self::assertSame('The Witcher: Enhanced Edition', $report->get('game.title'));
        self::assertSame(['amount' => 119, 'currency' => 'EUR', 'initial' => 799, 'discount' => 85], $report->get('game.price'));
        self::assertSame(['windows', 'mac', 'linux', 'playstation'], $report->get('platforms'));
        self::assertSame([['identifiers' => ['steam' => '620'], 'title' => 'Portal 2', 'playtime' => 1250, 'lastPlayed' => null]], $report->get('library'));
        self::assertSame([true, false], $report->get('owns'));
        self::assertSame(['Portal 2', '2011', ['Valve'], 'omnigame-bare/1.0'], $report->get('json'));
        self::assertNull($report->get('json-404'));
        self::assertSame('Example answered 500 for GET /games/3. Internal error', $report->get('json-error'));

        $symbols = $report->strings('loaded.symbols');
        self::assertContains('Omnigame\Testing\FixedProvider', $symbols);
        self::assertSame([], self::foreign($symbols), 'no class of a framework, an HTTP client or an application');
        self::assertSame([], self::outside($report->strings('loaded.files'), $report->strings('loaded.eager')), 'no file but the package and what it requires');
    }
}
