<?php

namespace Omnigame;

use Omnigame\Exception\InvalidConfigException;
use Omnigame\Exception\NotSupportedException;
use Omnigame\Model\Game;
use Omnigame\Model\GameRef;

/**
 * The application's providers by name, each built once from its factory and
 * options:
 *
 *   $registry = new Registry([new SteamFactory($http), new IgdbFactory($http)], [
 *       'steam' => ['factory' => 'steam', 'options' => ['key' => '...']],
 *       'igdb' => ['factory' => 'igdb', 'options' => ['client_id' => '...', 'client_secret' => '...']],
 *   ]);
 *
 *   $registry->catalogue('steam')->game(GameRef::steam(620), 'french', 'FR');
 *   $registry->game(GameRef::gog(1207658924));   // the first provider that finds it
 */
final class Registry
{
    /** @var array<string, ProviderFactoryInterface> */
    private array $factories = [];

    /** @var array<string, ProviderInterface> */
    private array $providers = [];

    /**
     * @param iterable<ProviderFactoryInterface>                                     $factories
     * @param array<string, array{factory: string, options?: array<string, mixed>}> $config
     */
    public function __construct(iterable $factories, private readonly array $config)
    {
        foreach ($factories as $factory) {
            $this->factories[$factory->getName()] = $factory;
        }
    }

    public function get(string $name): ProviderInterface
    {
        return $this->providers[$name] ??= $this->factory($name)->create($this->config[$name]['options'] ?? []);
    }

    public function has(string $name): bool
    {
        return isset($this->config[$name]);
    }

    public function search(string $name): SearchInterface
    {
        $provider = $this->get($name);

        return $provider instanceof SearchInterface ? $provider : throw NotSupportedException::capability($provider, $name, 'search');
    }

    public function catalogue(string $name): CatalogueInterface
    {
        $provider = $this->get($name);

        return $provider instanceof CatalogueInterface ? $provider : throw NotSupportedException::capability($provider, $name, 'give a game\'s record');
    }

    public function library(string $name): LibraryInterface
    {
        $provider = $this->get($name);

        // Offered only when it can be given: a library that needs a key the provider was not configured with is not.
        return $provider instanceof LibraryInterface && $provider->capabilities()->library ? $provider : throw NotSupportedException::capability($provider, $name, 'list a player\'s games');
    }

    public function ownership(string $name): OwnershipInterface
    {
        $provider = $this->get($name);

        return $provider instanceof OwnershipInterface && $provider->capabilities()->ownership ? $provider : throw NotSupportedException::capability($provider, $name, 'say whether a player owns a game');
    }

    /**
     * The game from the first provider, in the configuration's order (or the
     * order named), that finds it by this identifier: a Steam app id read
     * from IGDB asked of Steam's store.
     */
    public function game(GameRef $ref, ?string $language = null, ?string $country = null, string ...$names): ?Game
    {
        foreach ($names ?: $this->names() as $name) {
            $provider = $this->get($name);
            if ($provider instanceof CatalogueInterface && $provider->capabilities()->resolves($ref) && null !== $game = $provider->game($ref, $language, $country)) {
                return $game;
            }
        }

        return null;
    }

    /** @return array<string, mixed> the options a provider is configured with */
    public function options(string $name): array
    {
        return $this->config[$name]['options'] ?? [];
    }

    /** @return list<string> the configured providers' names */
    public function names(): array
    {
        return array_keys($this->config);
    }

    /** @return list<string> the factories installed: what `factory:` may name */
    public function factories(): array
    {
        return array_keys($this->factories);
    }

    private function factory(string $name): ProviderFactoryInterface
    {
        $provider = $this->config[$name] ?? throw new InvalidConfigException(\sprintf('No "%s" game provider; configured: %s.', $name, implode(', ', array_keys($this->config)) ?: 'none'));

        return $this->factories[$provider['factory']] ?? throw new InvalidConfigException(\sprintf('No "%s" factory for the "%s" game provider; installed: %s.', $provider['factory'], $name, implode(', ', array_keys($this->factories)) ?: 'none'));
    }
}
