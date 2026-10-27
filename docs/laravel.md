---
title: Laravel
order: 16
---

# Laravel

omnibridge/laravel, the bridge that hosts omnibase in Laravel, has no Omnigame provider yet. The
registry is plain PHP: a service provider of the application's builds it once, from a config file,
with a `symfony/http-client` client (every provider requires that package; Laravel's own HTTP
client is Guzzle's, which the factories do not take).

```php
// config/omnigame.php
return [
    'providers' => [
        'steam' => ['factory' => 'steam', 'options' => [
            'key' => env('STEAM_WEB_API_KEY'),       // null: the store only
            'language' => 'fr',
            'country' => 'FR',
        ]],
        'rawg' => ['factory' => 'rawg', 'options' => ['api_key' => env('RAWG_KEY')]],
    ],
];
```

```php
// app/Providers/OmnigameServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Omnigame\Rawg\RawgFactory;
use Omnigame\Registry;
use Omnigame\Steam\SteamFactory;
use Symfony\Component\HttpClient\HttpClient;

final class OmnigameServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Registry::class, function (): Registry {
            $http = HttpClient::create(['timeout' => 20]);

            return new Registry([
                new SteamFactory($http),
                new RawgFactory($http),
            ], config('omnigame.providers'));
        });
    }
}
```

Register the provider (`bootstrap/providers.php` on Laravel 11 and later), then inject the
registry where it is needed:

```php
use Illuminate\Support\Facades\Cache;
use Omnigame\Model\GameRef;
use Omnigame\Registry;

final class GameController
{
    public function show(Registry $stores, int $app)
    {
        $game = Cache::remember('steam.'.$app, now()->addHours(6), fn () => $stores->catalogue('steam')->game(GameRef::steam($app))?->toArray());

        return view('games.show', ['game' => $game]);
    }
}
```

List in the provider every factory the application uses: a hand-built registry knows only the
factories it is given. Configuration caching (`php artisan config:cache`) keeps working: the
options are plain arrays read through `config()`. This page was written from the registry's API;
it was not run in a Laravel application.
