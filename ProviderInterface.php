<?php

namespace Omnigame;

/**
 * One store or catalogue of games, configured: Steam, GOG, IGDB... Its
 * Capabilities say what it can be asked - search by title
 * (SearchInterface), a game's store record (CatalogueInterface), a player's
 * games (LibraryInterface), whether a player owns a game
 * (OwnershipInterface) - and what its terms ask of whoever shows its
 * records. Everything is read: nothing is bought, granted or written.
 */
interface ProviderInterface
{
    /** The factory's name: "steam", "igdb"... */
    public function getName(): string;

    public function getTitle(): string;

    public function capabilities(): Capabilities;
}
