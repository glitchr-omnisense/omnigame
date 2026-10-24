<?php

namespace Omnigame\Exception;

use Omnigame\Model\GameRef;

/** A provider asked for what it does not do: a library from a catalogue, a game by a store it does not know. */
final class NotSupportedException extends \LogicException implements OmnigameException
{
    public static function capability(object $provider, string $name, string $capability): self
    {
        return new self(\sprintf('The "%s" provider (%s) does not %s.', $name, $provider::class, $capability));
    }

    public static function ref(string $provider, GameRef $ref): self
    {
        return new self(\sprintf('%s does not find a game by its %s identifier.', $provider, $ref->scheme->value));
    }
}
