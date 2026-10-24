# omniscient/omnigame

A game's presence on the stores, read under any PHP host: its store record (texts, dates, studios,
platforms, price in a country, pictures, scores, its identifiers on other stores), a search by
title, a player's library and whether a player owns a game. Read only: nothing is bought, granted
or written. One set of models and contracts; each store or catalogue is a package of its own.

```php
use Omnigame\Model\{GameRef, Query};
use Omnigame\Registry;
use Omnigame\Steam\SteamFactory;
use Omnigame\Gog\GogFactory;
use Symfony\Component\HttpClient\HttpClient;

$http = HttpClient::create();
$stores = new Registry([new SteamFactory($http), new GogFactory($http)], [
    'steam' => ['factory' => 'steam', 'options' => ['key' => getenv('STEAM_WEB_API_KEY')]],
    'gog' => ['factory' => 'gog'],
]);

$portal = $stores->catalogue('steam')->game(GameRef::steam(620), 'fr', 'FR');
$found = $stores->search('gog')->search(new Query('witcher', 'fr', 'FR'));
$owns = $stores->ownership('steam')->owns('76561197960287930', GameRef::steam(620));
```

Plain PHP: no container, no framework. The core requires only `symfony/http-client-contracts`
(interfaces); a provider requires `symfony/http-client` and takes the application's client.

## The shape

- **Models** (`Omnigame\Model`): `Game` (title, `Identifiers`, page, summary, release date, coming
  soon, developers, publishers, genres, tags, `Platform`s, `Price`, `Image`s, `Rating`s),
  `GameRef` (a `Scheme` - steam, gog, itch, epic, igdb, rawg - and an identifier), `Query`,
  `OwnedGame`, `Platform::fromLabels()` folding a store's names ("PC (Microsoft Windows)",
  "osx", "PlayStation 5") into families. See [docs/models.md](docs/models.md).
- **Contracts by capability**: `SearchInterface`, `CatalogueInterface`, `LibraryInterface`,
  `OwnershipInterface`; `ProviderInterface::capabilities()` says which a provider has, the
  identifiers it finds a game by, what a player is to it, what its terms ask to be shown.
- **Registry, Config, ProviderFactory** as in the other omni families; `Registry::game()` asks, in
  order, each provider that resolves the identifier.
- **Http\JsonClient**, **Http\Data**, **Testing** (`FixedProvider`, `RecordedHttpClient`,
  `BareTestCase`) as in omnifilm.

## Providers

| Package | Search | Catalogue by | Library / ownership | Key |
|---|---|---|---|---|
| [omnigame/steam](https://github.com/glitchr-studio/omnigame-steam) | yes | steam | yes / yes (Web API key) | none for the store |
| [omnigame/gog](https://github.com/glitchr-studio/omnigame-gog) | yes | gog | - | none |
| [omnigame/itch](https://github.com/glitchr-studio/omnigame-itch) | - (no search API) | itch page | the key's own / yes (API key) | none for pages |
| [omnigame/igdb](https://github.com/glitchr-studio/omnigame-igdb) | yes | igdb, steam, gog | - | Twitch client credentials |
| [omnigame/rawg](https://github.com/glitchr-studio/omnigame-rawg) | yes | rawg | - | API key |

**Epic Games Store: not written.** It publishes no catalogue API; a player's entitlements are read
through Epic Online Services by the game's own product, with that product's credentials and the
player's sign-in - something for the first studio with an EOS product to ask for. IGDB and RAWG
give a game's Epic identifier meanwhile (`Scheme::Epic`).

Writing one: [docs/providers.md](docs/providers.md). The harness: [docs/harness.md](docs/harness.md).
The Symfony and Laravel wiring will live in omnibridge when a site needs it.

## Verified, and not

- Verified (the harness, PHP 8.4, 2026-10-24): the core's tests and the bare script (a registry,
  a search, a game, a library, ownership, a JSON call answered, a 404, an error without its key)
  loading nothing but the package and the HTTP client contracts (`BareTest`); every provider's
  tests and `BareTest`; PHPStan at its highest level on every package, the baseline empty.
- Verified live (`docker compose run --rm omnigame live`, no key): Steam's store (Portal 2's record
  and price, a search), GOG's catalogue (a product, its price, a search), an itch.io page.
- Not verified live: Steam's library and ownership, itch.io's ownership and library, IGDB, RAWG -
  no key was used. Their tests run on answers shaped after each API's documentation and written by
  hand.

## Responsibility

`omniscient/omnigame` and its `omnigame/*` providers are independent, unofficial clients: none is
affiliated with, endorsed by or sponsored by the stores and services it reaches. Each keeps its own
terms (accounts and keys, rate limits, commercial use, trademarks, players' data), named in the
README of each provider. The MIT licence covers this code only and grants no right to any service
or to its data.

Whoever installs and runs these packages is responsible for complying with those terms, for
obtaining whatever they require, and for any use made of what they return. The authors publish them
free of charge, make no commercial use of them, and accept no liability for how they are used (see
[LICENSE](LICENSE)).

## Licence

MIT.
