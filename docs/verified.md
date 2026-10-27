---
title: Verified, and not
order: 90
---

# Verified, and not

## What the tests prove

Run on 2026-10-27 in the [harness](harness.md), every package from its checkout, PHP 8.4,
PHPUnit 11.5: 44 tests, 245 assertions, green; PHPStan at its highest level on the six packages, no
error.

- The core: identifiers, references, the query's limits, a game's refs, pictures and platforms,
  `Platform::fromLabels()`, capabilities and `resolves()`, the registry (providers built once, a
  capability lacking, a missing option, `game()` asking the first provider that resolves), `Data`
  and `JsonClient`; a `BareTest` loading nothing but Omnigame and the HTTP client contracts.
- Each provider: its calls and its reading of the answers, unknown as `null`, a refusal as an
  exception, and a `BareTest`.

| Provider | Answers its tests use |
|---|---|
| steam | the store's answers recorded on 2026-10-24 (Portal 2 in French, an unknown app, a search); the library on an answer shaped after the Web API's documentation |
| gog | recorded on 2026-10-24 (The Witcher in French with its French price, an unknown product, a search) |
| itch | X-Moon's page recorded on 2026-10-24; ownership and library shaped after the server-side API's documentation |
| igdb | shaped after IGDB's documentation, written by hand |
| rawg | shaped after RAWG's documentation, written by hand |

There is no conformance suite shared by the providers.

## What was run live

- `docker compose run --rm omnigame live`, 2026-10-24 (README): Steam's store (Portal 2's record
  and price, a search), GOG's catalogue (a product, its price, a search), an itch.io page.
- For this manual, 2026-10-27, 06:17-06:24 UTC: GOG's product and price (The Witcher in France, two
  calls, twice) and itch.io's page (X-Moon, one call) answered; Steam's store could not be reached from
  the harness's network (the TLS connection cut, twice): its record in this manual is the recorded
  one.

## What is not verified

- Steam's library and ownership, itch.io's ownership and library, IGDB and RAWG: no key was used.
- IGDB's move of external games from `category` to `external_game_source`: both are read, the lookup
  asks the latter - not seen live.
- How itch.io answers "no download key found" (its status): read as "not owned" whatever the status.
- The [Symfony](symfony.md) and [Laravel](laravel.md) pages were written from the classes'
  constructors, not run.

## Known defects

Listed where the behaviour is described, and in the family's defects list:

- A network error's message holds the whole URL: Steam's Web API key, RAWG's key, IGDB's client
  secret ([Errors](errors.md#the-messages)).
- Steam and itch.io implement library and ownership without a key; the registry's getters do not
  refuse them ([Models](models.md#capabilities)).
- itch.io's prices: JPY read with two decimals, no thousands separator
  ([Store records and prices](usage-catalogue.md#prices)).
- `ProviderException` calls `mb_substr()` without `ext-mbstring` required
  ([Installation](installation.md#requirements)).

## Responsibility

`omnisense/omnigame` and its `omnigame/*` providers are independent, unofficial clients: none is
affiliated with, endorsed by or sponsored by the stores and services it reaches. Each keeps its own
terms (accounts and keys, rate limits, commercial use, trademarks, players' data), named in the
README and the manual of each provider. The MIT licence covers this code only and grants no right
to any service or to its data; whoever runs these packages complies with those terms.
