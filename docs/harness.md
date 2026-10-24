# The harness

`docker/` runs the core with every provider in PHP 8.4 (Alpine), nothing installed on the host:

    cd docker
    echo 'OMNIFILM_PLUGINS=../..' > .env      # the providers' checkouts beside the core
    docker compose run --rm omnigame test     # every package's tests, recorded answers, no network
    docker compose run --rm omnigame bare     # the bare script: what omnigame does and what PHP loaded
    docker compose run --rm omnigame live     # the checks that need no key (Steam's store, GOG, itch.io pages)
    docker compose run --rm omnigame phpstan  # PHPStan, highest level, each package
    docker compose down -v

`harness/setup.php` writes the harness's `composer.json` and `phpunit.xml`: the core from its
checkout, each provider from a checkout under `OMNIFILM_PLUGINS` when there is one, from GitHub
(branch 1.x) otherwise. `harness/plugins.php` lists the providers.
