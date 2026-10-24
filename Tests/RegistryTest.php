<?php

namespace Omnigame\Tests;

use Omnigame\Capabilities;
use Omnigame\Config;
use Omnigame\Exception\InvalidConfigException;
use Omnigame\Exception\NotSupportedException;
use Omnigame\Model\Game;
use Omnigame\Model\GameRef;
use Omnigame\Model\Identifiers;
use Omnigame\Model\Scheme;
use Omnigame\ProviderFactory;
use Omnigame\ProviderInterface;
use Omnigame\Registry;
use Omnigame\Testing\FixedProviderFactory;
use PHPUnit\Framework\TestCase;

final class RegistryTest extends TestCase
{
    public function testProvidersAreBuiltOnceFromTheirFactoryAndOptions(): void
    {
        $registry = new Registry([new FixedProviderFactory(), self::bare()], ['store' => ['factory' => 'fixed'], 'shop' => ['factory' => 'bare', 'options' => ['key' => 'not-a-real-key']]]);

        self::assertSame($registry->get('store'), $registry->get('store'));
        self::assertSame(['store', 'shop'], $registry->names());
        self::assertSame(['fixed', 'bare'], $registry->factories());
        self::assertSame(['key' => 'not-a-real-key'], $registry->options('shop'));
        self::assertFalse($registry->has('other'));
        self::assertSame([], $registry->library('store')->library('nobody'));
    }

    public function testACapabilityTheProviderLacksIsSaid(): void
    {
        $registry = new Registry([self::bare()], ['shop' => ['factory' => 'bare', 'options' => ['key' => 'x']]]);

        $this->expectException(NotSupportedException::class);
        $this->expectExceptionMessage('does not list a player\'s games');
        $registry->library('shop');
    }

    public function testAMissingOptionAndAnUnknownFactoryAreNamed(): void
    {
        try {
            (new Registry([self::bare()], ['shop' => ['factory' => 'bare']]))->get('shop');
            self::fail('No key.');
        } catch (InvalidConfigException $e) {
            self::assertSame('The "bare" provider needs: key.', $e->getMessage());
        }
        $this->expectExceptionMessage('No "steam" factory for the "store" game provider; installed: fixed.');
        (new Registry([new FixedProviderFactory()], ['store' => ['factory' => 'steam']]))->get('store');
    }

    public function testAGameIsAskedOfTheFirstProviderThatResolvesItsIdentifier(): void
    {
        $portal = new Game('Portal 2', Identifiers::of(['steam' => 620]), 'fixed');
        $registry = new Registry([self::bare(), new FixedProviderFactory([$portal])], ['shop' => ['factory' => 'bare', 'options' => ['key' => 'x']], 'store' => ['factory' => 'fixed']]);

        self::assertSame($portal, $registry->game(GameRef::steam(620)));
        self::assertNull($registry->game(GameRef::steam(400)));
        self::assertNull($registry->game(GameRef::steam(620), null, null, 'shop'));
    }

    private static function bare(): ProviderFactory
    {
        return new class extends ProviderFactory {
            protected function populate(Config $c): void
            {
                $c->defaults(['omnigame.factory_name' => 'bare', 'omnigame.required_options' => ['key']]);
            }

            protected function build(Config $c): ProviderInterface
            {
                return new class implements ProviderInterface {
                    public function getName(): string
                    {
                        return 'bare';
                    }

                    public function getTitle(): string
                    {
                        return 'Bare';
                    }

                    public function capabilities(): Capabilities
                    {
                        return new Capabilities(lookupBy: [Scheme::Steam]);
                    }
                };
            }
        };
    }
}
