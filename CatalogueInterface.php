<?php

namespace Omnigame;

use Omnigame\Exception\NotSupportedException;
use Omnigame\Model\Game;
use Omnigame\Model\GameRef;

interface CatalogueInterface
{
    /**
     * The game's whole record, null when the provider does not know it.
     *
     * @param ?string $language the language of the texts, where the store translates
     * @param ?string $country  the country whose price is answered (ISO 3166-1 alpha-2), where the store has prices
     *
     * @throws NotSupportedException when the provider does not find a game by this identifier (Capabilities::resolves())
     */
    public function game(GameRef $ref, ?string $language = null, ?string $country = null): ?Game;
}
