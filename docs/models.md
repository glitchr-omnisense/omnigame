# Models

All are immutable (`readonly`) and plain PHP; each has `toArray()`.

- **`Game`** - `title`, `identifiers`, `source` (the provider's name), `url` (the page at the store
  or catalogue), `summary` (plain text), `released` (a day), `comingSoon`, `developers`,
  `publishers`, `genres` and `tags` (the provider's words, in the language asked where it
  translates), `platforms`, `price`, `images`, `ratings`. Helpers: `ref(Scheme)`,
  `image(ImageKind)`, `runsOn(Platform)`.
- **`GameRef`** - a game named by one identifier: `GameRef::steam(620)`, `GameRef::gog(1207658924)`,
  `GameRef::itch('leafo/x-moon')` (the page: creator and game), `GameRef::igdb(72)`,
  `GameRef::rawg(4200)`.
- **`Identifiers`** - `{scheme: id}`, empty values dropped; IGDB and RAWG fill a game's Steam,
  GOG, itch.io and Epic identifiers.
- **`Platform`** - windows, mac, linux, android, ios, web, switch, playstation, xbox;
  `fromLabel()`/`fromLabels()` fold a store's names into them, unknown ones left out.
- **`Price`** - `amount` in minor units, `currency` (ISO 4217; "XXX" for a free game whose store
  names no currency), `initial` before a discount, `discount` in percent; `isFree()`.
- **`Image`** - `ImageKind` (cover, header, screenshot, background, logo, icon), URL at the store.
- **`Rating`** - source, value, scale, votes: `metacritic` 95/100, `igdb` 92.3/100, `gog` 4.6/5.
- **`OwnedGame`** - identifiers, title, playtime in minutes, last played.
- **`Query`** - words, language, country (prices), limit (1 to 50).

Exceptions all implement `Omnigame\Exception\OmnigameException`.
