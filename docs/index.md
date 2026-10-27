# omnisense/omnigame

Omnigame is one PHP contract for a game's presence on the stores: its store record (texts, release
date, studios, platforms, price in a country, pictures, scores, its identifiers on other stores), a
search by title, a player's library and whether a player owns a game. Everything is read: nothing
is bought, granted or written. An application asks every store or catalogue the same questions and
gets the same `Game` back; each is a package of its own (`omnigame/steam`, `omnigame/igdb`...) that
turns the questions into its API calls.

```php
$stores->search('gog')->search(new Query('witcher', 'fr', 'FR'));                   // Game[], best first
$stores->catalogue('steam')->game(GameRef::steam(620), 'fr', 'FR');                 // ?Game, with its French price
$stores->game(GameRef::steam(620));                                                 // the first provider that finds it
$stores->library('steam')->library('76561197960287930');                            // OwnedGame[]: playtime, last played
$stores->ownership('itch')->owns('12345', GameRef::itch('leafo/x-moon'));           // bool
```

The design is the one of the other Omnisense families: a provider's **factory** fills a
configuration with its name, its required options and the defaults of the others, then builds the
**provider**; a **registry** holds the application's providers by name. A provider says what it can
be asked in its **capabilities** - which of the four contracts it answers, the identifiers it finds
a game by, what a player is to it, what its terms ask to be shown - and an identifier it cannot
resolve is refused before any call. The core needs no framework: it requires
`symfony/http-client-contracts` and nothing else.

Published as `omnisense/omnigame` (it `replace`s the former name `omniscient/omnigame`). The
namespace is `Omnigame\`; providers keep the `omnigame/` vendor and the `Omnigame\<Provider>\`
namespace.

## When to use it

- A studio's or a blog's page that shows a game with its price on Steam and GOG, kept current.
- A catalogue page fed by IGDB or RAWG, which also give a game's Steam, GOG, itch.io or Epic
  identifiers, leading to the store pages (`Registry::game()` asks each provider in turn).
- A community site that shows a player's Steam library and playtime, or a studio that checks
  whether a player owns its itch.io game before granting a role or a download.

It sells nothing and grants nothing: no purchase, no key handed out, nothing written to a store. It
stores nothing either: keeping records and prices (each store's terms say for how long) is the
application's job.

## Providers

Five packages. The capabilities are each provider's `capabilities()`; library and ownership are
offered only with the key they need.

| Package | Factory name | Search | Catalogue by | Library | Ownership | Prices | Credentials |
|---|---|---|---|---|---|---|---|
| [`omnigame/steam`](https://github.com/glitchr-omnisense/omnigame-steam) | `steam` | yes | steam | with `key` (a 64-bit Steam ID) | with `key` | yes | none for the store; a Steam Web API key for players |
| [`omnigame/gog`](https://github.com/glitchr-omnisense/omnigame-gog) | `gog` | yes | gog | - | - | yes | none |
| [`omnigame/itch`](https://github.com/glitchr-omnisense/omnigame-itch) | `itch` | - (no search API) | itch (a page, `creator/game`) | with `key`: the key's own account | with `key` (user id, e-mail, download key) | yes | none for pages; a server-side API key |
| [`omnigame/igdb`](https://github.com/glitchr-omnisense/omnigame-igdb) | `igdb` | yes | igdb, steam, gog | - | - | - | a Twitch application's client id and secret |
| [`omnigame/rawg`](https://github.com/glitchr-omnisense/omnigame-rawg) | `rawg` | yes | rawg (number or slug) | - | - | - | an API key |

Cross identifiers: IGDB gives a game's Steam, GOG, Epic and itch.io identifiers; RAWG its Steam,
itch.io and Epic ones (read from the links of the stores that sell it). The core's test provider,
`Omnigame\Testing\FixedProvider`, answers all four contracts ([Testing](testing.md)).

**Epic Games Store: not written.** It publishes no catalogue API; a player's entitlements are read
through Epic Online Services by the game's own product, with that product's credentials and the
player's sign-in. IGDB and RAWG give a game's Epic identifier meanwhile (`Scheme::Epic`), which no
provider resolves.

## How this manual is organised

- [Installation](installation.md): requirements, a first script in plain PHP, the stores' APIs.
- [Symfony](symfony.md) and [Laravel](laravel.md): wiring the registry in a framework (no bridge
  yet: a service definition of the application's).
- [Models](models.md): providers, capabilities, factories, the registry, every model and enum.
- Usage: [search](usage-search.md), [store records and prices](usage-catalogue.md),
  [players: libraries and ownership](usage-players.md).
- [Configuration reference](configuration.md): the registry's configuration and every provider's
  options.
- [Errors](errors.md): every exception and when it is thrown.
- [Testing](testing.md): the fixed provider, the recorded HTTP client, the bare test case.
- [The Docker harness](harness.md).
- [Writing a provider](providers.md), and the providers' terms.
- [Verified, and not](verified.md).
