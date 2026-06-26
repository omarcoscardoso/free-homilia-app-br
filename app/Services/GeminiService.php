<?php

namespace App\Services;

use App\Contracts\SermonGeneratorInterface;
use Gemini\Laravel\Facades\Gemini;

class GeminiService implements SermonGeneratorInterface
{
    protected SermonPromptBuilder $promptBuilder;

    /**
     * Injeção de dependência do SermonPromptBuilder.
     */
    public function __construct(SermonPromptBuilder $promptBuilder)
    {
        $this->promptBuilder = $promptBuilder;
    }

    /**
     * Gera o esboço do sermão/homilia via Gemini API.
     */
    public function generate(string $textHomilia, ?string $versoHomilia, ?int $qtDivisaoHomilia): string
    {
        $prompt = $this->promptBuilder->build($textHomilia, $versoHomilia, $qtDivisaoHomilia);

        $result = Gemini::generativeModel(model: 'gemini-2.5-flash')->generateContent($prompt);
        
        return $result->text();
    }

    /**
     * Método legado mantido para fins de retrocompatibilidade direta.
     */
    public function analyzeHomilia(string $textHomilia, ?string $versoHomilia, ?int $qtDivisaoHomilia): string
    {
        return $this->generate($textHomilia, $versoHomilia, $qtDivisaoHomilia);
    }
}