<?php

namespace Omnigame\Tests;

use Omnigame\Capabilities;
use Omnigame\Exception\InvalidArgumentException;
use Omnigame\Model\Game;
use Omnigame\Model\GameRef;
use Omnigame\Model\Identifiers;
use Omnigame\Model\Image;
use Omnigame\Model\ImageKind;
use Omnigame\Model\Platform;
use Omnigame\Model\Price;
use Omnigame\Model\Query;
use Omnigame\Model\Scheme;
use PHPUnit\Framework\TestCase;

final class ModelTest extends TestCase
{
    public function testPlatformsAreFoldedIntoFamilies(): void
    {
        self::assertSame([Platform::Windows, Platform::Mac, Platform::Linux, Platform::PlayStation, Platform::Switch, Platform::Web, Platform::Ios], Platform::fromLabels(['PC (Microsoft Windows)', 'osx', 'Linux', 'PlayStation 5', 'PS4', 'Nintendo Switch 2', 'HTML5', 'iOS', 'windows', 'Amiga']));
        self::assertNull(Platform::fromLabel('Sega Saturn'));
    }

    public function testAGameGivesItsRefsPicturesAndPlatforms(): void
    {
        $game = new Game('Portal 2', Identifiers::of(['steam' => 620, 'igdb' => 72]), 'test', images: [new Image(ImageKind::Header, 'https://example.org/h.jpg'), new Image(ImageKind::Cover, 'https://example.org/c.jpg')], platforms: [Platform::Windows], price: new Price(0, 'EUR'));

        self::assertSame('steam:620', (string) $game->ref(Scheme::Steam));
        self::assertNull($game->ref(Scheme::Gog));
        self::assertSame('https://example.org/c.jpg', $game->image()?->url);
        self::assertNull($game->image(ImageKind::Logo));
        self::assertTrue($game->runsOn(Platform::Windows));
        self::assertFalse($game->runsOn(Platform::Mac));
        self::assertTrue($game->price?->isFree());
        self::assertSame(['igdb' => '72', 'steam' => '620'], $game->toArray()['identifiers']);
    }

    public function testRefsAndQueriesRefuseEmptiness(): void
    {
        self::assertSame('itch:leafo/x-moon', (string) GameRef::itch('leafo/x-moon'));
        try {
            new Query(' ');
            self::fail('No words.');
        } catch (InvalidArgumentException) {
            self::addToAssertionCount(1);
        }
        $this->expectException(InvalidArgumentException::class);
        GameRef::steam(' ');
    }

    public function testCapabilitiesSayWhatResolves(): void
    {
        $capabilities = new Capabilities(search: true, catalogue: true, lookupBy: [Scheme::Steam], player: 'a 64-bit Steam ID');

        self::assertTrue($capabilities->resolves(GameRef::steam(620)));
        self::assertFalse($capabilities->resolves(GameRef::gog(1)));
        self::assertSame(['search', 'catalogue'], $capabilities->contracts());
        self::assertSame(['steam'], $capabilities->toArray()['lookupBy']);
        self::assertFalse((new Capabilities(lookupBy: [Scheme::Steam]))->resolves(GameRef::steam(620)));
    }
}
