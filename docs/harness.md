---
title: The Docker harness
order: 70
---

# The Docker harness

`docker/` runs the core with every provider in PHP 8.4 (Alpine), nothing installed on the host, in
the image `omnisense/omnigame-harness`:

```sh
cd docker
echo 'OMNIGAME_PLUGINS=../..' > .env      # the providers' checkouts beside the core
docker compose run --rm omnigame test     # every package's tests, recorded answers, no network
docker compose run --rm omnigame bare     # the bare script: what omnigame does and what PHP loaded
docker compose run --rm omnigame live     # the checks that need no key (Steam's store, GOG, itch.io pages)
docker compose run --rm omnigame phpstan  # PHPStan, highest level, each package
docker compose down -v                    # the harness's volume gone
```

## What it installs

At each start, `docker/harness/setup.php` writes the harness's `composer.json` and `phpunit.xml` in
the volume (`/harness`); the entrypoint runs `composer install` the first time and `composer
update` when the `composer.json` changed:

- the core from its checkout (`/omnigame/core`, a path repository, symlinked);
- each provider of `docker/harness/plugins.php` (steam, gog, itch, igdb, rawg) from a checkout
  under `OMNIGAME_PLUGINS` when there is one - `docker/.env`, git-ignored - else from GitHub
  (`https://github.com/glitchr-omnisense/omnigame-<slug>.git`, branch 1.x);
- PHPUnit 11, PHPStan 2.1 and `symfony/http-client` for the tests; one suite per package.

The service uses Docker's default bridge network and one named volume, `omnigame-harness_harness`.

## The commands

| Command | Runs |
|---|---|
| `test` (the default) | `vendor/bin/phpunit --exclude-group live` |
| `live` | `vendor/bin/phpunit --group live`: Steam's store (Portal 2's record and price, a search), GOG's catalogue (a product, a search), an itch.io page |
| `bare` | `docker/harness/bin/bare`: a registry with the fixed provider (capabilities, a search, a game, platforms folded, a library, ownership), then `JsonClient` with no network (a record, a 404, a 500); the results and every class and file PHP loaded, as JSON |
| `phpstan` | PHPStan on the core and each provider |
| `composer ...`, `php ...`, `sh` | as they are, in `/harness` |

```
$ docker compose run --rm omnigame test
OK (44 tests, 245 assertions)
```

(2026-10-27, every provider from its checkout; `phpstan` the same day: no error in the six
packages). Some of what `bare` printed:

```
search ["Portal 2"]
platforms ["windows", "mac", "linux", "playstation"]
owns [true, false]
json-404 null
json-error "Example answered 500 for GET /games/3. Internal error"
```

## Running a script of your own

```sh
docker compose run --rm -v "$PWD/scratch:/scratch" omnigame php /scratch/game.php
```

with `require '/harness/vendor/autoload.php';` in the script. With no network at all:

```sh
docker run --rm --network none \
    -v omnigame-harness_harness:/harness:ro -v "$PWD/..:/omnigame/core:ro" -v "$PWD/../..:/omnigame/plugins:ro" \
    -v "$PWD/scratch:/scratch:ro" --entrypoint php omnisense/omnigame-harness /scratch/game.php
```
