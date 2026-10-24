<?php

namespace Omnigame;

use Omnigame\Model\Game;
use Omnigame\Model\Query;

interface SearchInterface
{
    /**
     * Games whose title matches, best first, as short as the provider's
     * search answers them (ask the catalogue for a whole record).
     *
     * @return list<Game>
     */
    public function search(Query $query): array;
}
