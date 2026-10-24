<?php

namespace Omnigame;

use Omnigame\Model\GameRef;
use Omnigame\Model\Scheme;

/**
 * What a provider is, said before it is asked anything: what it can read,
 * the identifiers it finds a game by, what a player is to it, and what its
 * terms ask of whoever shows its records.
 */
final readonly class Capabilities
{
    /**
     * @param bool         $search     it searches by title (SearchInterface)
     * @param bool         $catalogue  it gives a game's record (CatalogueInterface)
     * @param bool         $library    it lists a player's games (LibraryInterface)
     * @param bool         $ownership  it says whether a player owns a game (OwnershipInterface)
     * @param list<Scheme> $lookupBy   the identifiers CatalogueInterface finds a game by
     * @param bool         $prices     its records carry a price
     * @param ?string      $player     what a player is to it ("a 64-bit Steam ID"), for library and ownership
     * @param ?string      $attribution what its terms ask to be shown beside its records
     * @param list<string> $terms      the terms whoever runs it must follow (URLs)
     * @param bool         $keyless    its catalogue needs no account nor key
     */
    public function __construct(
        public bool $search = false,
        public bool $catalogue = false,
        public bool $library = false,
        public bool $ownership = false,
        public array $lookupBy = [],
        public bool $prices = false,
        public ?string $player = null,
        public ?string $attribution = null,
        public array $terms = [],
        public bool $keyless = false,
    ) {
    }

    /** Whether CatalogueInterface::game() finds this game by this identifier. */
    public function resolves(GameRef $ref): bool
    {
        return $this->catalogue && \in_array($ref->scheme, $this->lookupBy, true);
    }

    /** @return list<string> what it can be asked, by contract: search, catalogue, library, ownership */
    public function contracts(): array
    {
        return array_keys(array_filter(['search' => $this->search, 'catalogue' => $this->catalogue, 'library' => $this->library, 'ownership' => $this->ownership]));
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'contracts' => $this->contracts(),
            'lookupBy' => array_map(static fn (Scheme $s) => $s->value, $this->lookupBy),
            'prices' => $this->prices,
            'player' => $this->player,
            'attribution' => $this->attribution,
            'terms' => $this->terms,
            'keyless' => $this->keyless,
        ];
    }
}
