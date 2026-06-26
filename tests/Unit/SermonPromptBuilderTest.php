<?php

namespace Tests\Unit;

use App\Services\SermonPromptBuilder;
use Tests\TestCase;

class SermonPromptBuilderTest extends TestCase
{
    /**
     * Testa se o prompt é construído com a estrutura correta e os valores informados.
     */
    public function test_it_builds_prompt_with_correct_structure_and_inputs()
    {
        $builder = new SermonPromptBuilder();
        $prompt = $builder->build('Texto do meu sermão', 'João 3:16', 3);

        $this->assertStringContainsString('Texto do meu sermão', $prompt);
        $this->assertStringContainsString('João 3:16', $prompt);
        $this->assertStringContainsString('3 divisões principais', $prompt);
        $this->assertStringContainsString('Igreja Presbiteriana Renovada do Brasil', $prompt);
    }

    /**
     * Testa se o prompt oculta campos opcionais não fornecidos.
     */
    public function test_it_omits_optional_fields_when_null()
    {
        $builder = new SermonPromptBuilder();
        $prompt = $builder->build('Texto do meu sermão', null, null);

        $this->assertStringContainsString('Texto do meu sermão', $prompt);
        $this->assertStringNotContainsString('texto bíblico de:', $prompt);
        $this->assertStringNotContainsString('divisões principais', $prompt);
    }
}
