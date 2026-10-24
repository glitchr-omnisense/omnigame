<?php

namespace Omnigame\Testing;

use Omnigame\Capabilities;
use Omnigame\CatalogueInterface;
use Omnigame\Exception\NotSupportedException;
use Omnigame\LibraryInterface;
use Omnigame\Model\Game;
use Omnigame\Model\GameRef;
use Omnigame\Model\OwnedGame;
use Omnigame\Model\Query;
use Omnigame\Model\Scheme;
use Omnigame\OwnershipInterface;
use Omnigame\ProviderInterface;
use Omnigame\SearchInterface;

/**
 * A provider that answers from the games and libraries it was given, with no
 * network: an application's tests, the harness's bare script. It finds a
 * game by any identifier the game carries, searches titles by their words,
 * and owns what a player's library holds.
 */
final class FixedProvider implements ProviderInterface, SearchInterface, CatalogueInterface, LibraryInterface, OwnershipInterface
{
    /**
     * @param list<Game>                   $games
     * @param array<string, list<OwnedGame>> $libraries by player
     */
    public function __construct(private readonly array $games = [], private readonly array $libraries = [], private readonly string $name = 'fixed')
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return 'Fixed games';
    }

    public function capabilities(): Capabilities
    {
        return new Capabilities(search: true, catalogue: true, library: true, ownership: true, lookupBy: Scheme::cases(), prices: true, player: 'any name', keyless: true);
    }

    public function search(Query $query): array
    {
        $words = preg_split('~\W+~u', mb_strtolower($query->text), -1, \PREG_SPLIT_NO_EMPTY) ?: [];
        $found = array_filter($this->games, static fn (Game $game) => [] === array_filter($words, static fn (string $word) => !str_contains(mb_strtolower($game->title), $word)));

        return \array_slice(array_values($found), 0, $query->limit);
    }

    public function game(GameRef $ref, ?string $language = null, ?string $country = null): ?Game
    {
        if (!$this->capabilities()->resolves($ref)) {
            throw NotSupportedException::ref($this->getTitle(), $ref);
        }
        foreach ($this->games as $game) {
            if ($ref->id === $game->identifiers->get($ref->scheme)) {
                return $game;
            }
        }

        return null;
    }

    public function library(string $player): array
    {
        return $this->libraries[$player] ?? [];
    }

    public function owns(string $player, GameRef $game): bool
    {
        foreach ($this->library($player) as $owned) {
            if ($game->id === $owned->identifiers->get($game->scheme)) {
                return true;
            }
        }

        return false;
    }
}
