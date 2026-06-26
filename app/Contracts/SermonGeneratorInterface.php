<?php

namespace App\Contracts;

interface SermonGeneratorInterface
{
    /**
     * Gera o esboço do sermão/homilia.
     *
     * @param string $textHomilia
     * @param string|null $versoHomilia
     * @param int|null $qtDivisaoHomilia
     * @return string
     */
    public function generate(string $textHomilia, ?string $versoHomilia, ?int $qtDivisaoHomilia): string;
}
