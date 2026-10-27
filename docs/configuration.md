---
title: Configuration reference
order: 40
---

# Configuration reference

## The registry's configuration

`new Registry($factories, $config)` takes the providers by name, each with its factory and options:

```php
[
    '<provider name>' => [
        'factory' => '<factory name>',     // required: a factory's getName()
        'options' => [/* ... */],          // optional, default []: handed to the factory's create()
    ],
]
```

The order matters to `Registry::game()` called without names. Options are checked when the provider
is first asked for. Every factory takes an optional HTTP client in its constructor and makes its own
(`HttpClient::create(['timeout' => 20])`) when given none. An empty string is the same as an option
not given.

## omnigame/steam

Factory `Omnigame\Steam\SteamFactory`, name `steam`.

| Option | Type | Default | Required | |
|---|---|---|---|---|
| `key` | string | `null` | no | a Steam Web API key: only for players' libraries and ownership; sent in the query string (`key=`) |
| `language` | string | `english` | no | the store's language: Steam's name (`french`) or an ISO code (`fr`, `pt-BR`, `zh-TW`: 17 mapped) |
| `country` | string | `US` | no | the prices' country (`cc`) |
| `user_agent` | string | `omnigame/1.0 (+https://github.com/glitchr-omnisense/omnigame)` | no | sent with every request |
| `store` | string | `https://store.steampowered.com` | no | the storefront |
| `api` | string | `https://api.steampowered.com` | no | the Web API |

## omnigame/gog

Factory `Omnigame\Gog\GogFactory`, name `gog`.

| Option | Type | Default | Required | |
|---|---|---|---|---|
| `locale` | string | `en-US` | no | the texts' locale; a two-letter code is widened (`en` -> `en-US`, `fr` -> `fr-FR`) |
| `country` | string | `US` | no | the prices' country |
| `currency` | string | `USD` | no | the search's prices; a record's price is in the country's own currency |
| `user_agent` | string | `omnigame/1.0 (+https://github.com/glitchr-omnisense/omnigame)` | no | |
| `api` | string | `https://api.gog.com` | no | products and prices |
| `catalog` | string | `https://catalog.gog.com` | no | the search |

## omnigame/itch

Factory `Omnigame\Itch\ItchFactory`, name `itch`.

| Option | Type | Default | Required | |
|---|---|---|---|---|
| `key` | string | `null` | no | a server-side API key (account settings, or OAuth): only for ownership and library; sent as `Authorization: Bearer` |
| `user_agent` | string | `omnigame/1.0 (+https://github.com/glitchr-omnisense/omnigame)` | no | |
| `api` | string | `https://api.itch.io` | no | the server-side API (pages are read from `https://{creator}.itch.io`) |

## omnigame/igdb

Factory `Omnigame\Igdb\IgdbFactory`, name `igdb`.

| Option | Type | Default | Required | |
|---|---|---|---|---|
| `client_id` | string | - | yes | a Twitch application's client id, sent as `Client-ID` |
| `client_secret` | string | - | yes | its secret, for the client-credentials token |
| `api` | string | `https://api.igdb.com/v4` | no | |
| `token_url` | string | `https://id.twitch.tv/oauth2/token` | no | |
| `images` | string | `https://images.igdb.com/igdb/image/upload` | no | covers and screenshots |

## omnigame/rawg

Factory `Omnigame\Rawg\RawgFactory`, name `rawg`.

| Option | Type | Default | Required | |
|---|---|---|---|---|
| `api_key` | string | - | yes | sent in the query string (`key=`) |
| `user_agent` | string | `omnigame/1.0 (+https://github.com/glitchr-omnisense/omnigame)` | no | |
| `api` | string | `https://api.rawg.io/api` | no | |

## Per call

A call's `language` and `country` replace the provider's options for that call; the `Query` carries
them for a search ([Models](models.md#query)).

## The fixed provider (testing)

`Omnigame\Testing\FixedProviderFactory`, name `fixed`, takes its games and libraries in its
constructor (`new FixedProviderFactory($games, $libraries)`) and no option.
