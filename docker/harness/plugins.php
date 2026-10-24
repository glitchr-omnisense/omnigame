<?php

/*
 * The omnigame/* providers the harness installs beside the core: their tests'
 * namespace, and what their tests need besides (require-dev).
 */
return [
    'steam' => ['Omnigame\\Steam\\Tests\\'],
    'gog' => ['Omnigame\\Gog\\Tests\\'],
    'itch' => ['Omnigame\\Itch\\Tests\\'],
    'igdb' => ['Omnigame\\Igdb\\Tests\\'],
    'rawg' => ['Omnigame\\Rawg\\Tests\\'],
];
