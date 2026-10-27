---
title: Errors
order: 50
---

# Errors

Every exception Omnigame throws implements `Omnigame\Exception\OmnigameException` (a `\Throwable`):

| Exception | Extends | When |
|---|---|---|
| `InvalidArgumentException` | `\InvalidArgumentException` | a search without words, a limit outside 1-50, an empty identifier, a register name that is not one |
| `InvalidConfigException` | `\InvalidArgumentException` | a provider not configured, a factory not installed, a required option missing; Steam's or itch.io's players without a `key`; a Steam player that is not a 64-bit Steam ID; itch.io's library of anyone but `me` |
| `NotSupportedException` | `\LogicException` | a contract the provider lacks (`Registry::search('itch')`), an identifier it does not resolve. Thrown before any call |
| `ProviderException` | `\RuntimeException` | a status of 400 or more but 404, a body that is not JSON, Twitch giving no token |
| `UnreachableException` | `\RuntimeException` | a network error, a timeout |

Not an exception: a game the store does not know - a 404 included - is `null`; a search with no
match `[]`; a player who does not own the game `false`.

## The messages

Seen in the [harness](harness.md) on 2026-10-27, the network ones in a container with no network:

```
steam library    UnreachableException: Steam could not be reached for GET /IPlayerService/GetOwnedGames/v1/: Could not resolve host: api.steampowered.com (Could not contact DNS servers) for "https://api.steampowered.com/IPlayerService/GetOwnedGames/v1/?key=SECRET-KEY&steamid=76561197960287930&include_appinfo=1&include_played_free_games=1&format=json".
rawg network     UnreachableException: RAWG could not be reached for GET /api/games/portal-2: Could not resolve host: api.rawg.io (Could not contact DNS servers) for "https://api.rawg.io/api/games/portal-2?key=SECRET-KEY".
igdb network     UnreachableException: IGDB could not be reached for POST /oauth2/token: Could not resolve host: id.twitch.tv (Could not contact DNS servers) for "https://id.twitch.tv/oauth2/token?client_id=ID&client_secret=SECRET&grant_type=client_credentials".
steam no key     InvalidConfigException: A player's Steam library needs the "key" option (a Steam Web API key).
steam player     InvalidConfigException: "gabe" is not a 64-bit Steam ID.
steam gog ref    NotSupportedException: Steam does not find a game by its gog identifier.
steam unknown    NULL
steam 429        ProviderException: Steam answered 429 for GET /api/appdetails. Too Many Requests
itch library     InvalidConfigException: itch.io lets a key read the library of its own account only: ask for "me".
itch search      NotSupportedException: The "i" provider (Omnigame\Itch\ItchProvider) does not search.
igdb token       ProviderException: IGDB answered 400 for POST /oauth2/token. {"status":400,"message":"invalid client secret"}
rawg no key      InvalidConfigException: The "rawg" provider needs: api_key.
rawg 401         ProviderException: RAWG answered 401 for GET /api/games. {"error":"The key parameter is not provided"}
query limit      InvalidArgumentException: A search answers 1 to 50 games.
ref empty        InvalidArgumentException: A game is named by a non-empty identifier.
```

A `ProviderException` names the provider, the status, the method and the path - never the query -
then the answer's text (tags stripped, 300 characters at most); its code is 0.

> [!WARNING]
> Known defect: an `UnreachableException` quotes the HTTP client's message, which names the whole
> URL: Steam's Web API key, RAWG's key and IGDB's Twitch client secret appear in it (above), where
> `JsonClient`'s docblock promises messages that never name the query, where a key may sit. Keep
> these messages out of pages and shared logs.

## What to catch where

| Where | Catch |
|---|---|
| a page showing a store's record | `OmnigameException`: show what the application kept, log the message privately |
| a check before granting access (ownership) | `UnreachableException` and `ProviderException`: do not grant on an error; ask again later |
| a batch refresh of prices | a `ProviderException` for a 429 or 5xx: wait and retry, at a gentler pace |
| building the registry, a deploy | `InvalidConfigException`: a configuration error, to fix |
| a feature a provider may lack | check `capabilities()->contracts()` and `resolves()` first |
