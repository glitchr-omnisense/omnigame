# Changelog

## Unreleased (1.x)

### Fixed
- An `UnreachableException` no longer quotes the URL called: as its docblock promised, the message
  names the method and the path, then the HTTP client's reason without the address it quotes.
  Symfony's transport messages end with the whole URL, so Steam's and RAWG's `key=` and IGDB's
  Twitch `client_secret=` stood in the message.

### Added
- `Omnigame\Http\JsonClient::reason()`: an HTTP client's message without the addresses it quotes.
