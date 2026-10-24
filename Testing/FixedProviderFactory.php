<?php

namespace Omnigame\Testing;

use Omnigame\Config;
use Omnigame\Model\Game;
use Omnigame\Model\OwnedGame;
use Omnigame\ProviderFactory;
use Omnigame\ProviderInterface;

/** FixedProvider's factory: "fixed", answering from the games and libraries it holds. */
final class FixedProviderFactory extends ProviderFactory
{
    /**
     * @param list<Game>                     $games
     * @param array<string, list<OwnedGame>> $libraries
     */
    public function __construct(private readonly array $games = [], private readonly array $libraries = [])
    {
    }

    protected function populate(Config $c): void
    {
        $c->defaults(['omnigame.factory_name' => 'fixed', 'omnigame.factory_title' => 'Fixed games', 'omnigame.required_options' => []]);
    }

    protected function build(Config $c): ProviderInterface
    {
        return new FixedProvider($this->games, $this->libraries);
    }
}
