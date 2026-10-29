# Changelog

## Unreleased (1.x)

### Fixed
- `Registry::library()` and `::ownership()` give a provider only when its `capabilities()` says it
  can: Steam and itch.io without a `key` are a `NotSupportedException` there, where the registry
  returned them and their `library()` then threw `InvalidConfigException`.
- An `UnreachableException` no longer quotes the URL called: as its docblock promised, the message
  names the method and the path, then the HTTP client's reason without the address it quotes.
  Symfony's transport messages end with the whole URL, so Steam's and RAWG's `key=` and IGDB's
  Twitch `client_secret=` stood in the message.

### Added
- `ext-mbstring` in `composer.json`'s requirements: `ProviderException` shortens error bodies with
  `mb_substr()`, which a PHP built without mbstring did not have (no test: a requirement).
- `Omnigame\Http\JsonClient::reason()`: an HTTP client's message without the addresses it quotes.
