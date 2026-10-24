<?php

namespace Omnigame;

/** Builds a provider from its options (a key, a client id, a User-Agent...). */
interface ProviderFactoryInterface
{
    /** The name providers are configured with: "steam", "igdb"... */
    public function getName(): string;

    /** @param array<string, mixed> $options */
    public function create(array $options = []): ProviderInterface;
}
