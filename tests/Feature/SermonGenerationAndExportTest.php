<?php

namespace Tests\Feature;

use App\Contracts\SermonGeneratorInterface;
use App\Contracts\PdfExportServiceInterface;
use Livewire\Livewire;
use App\Livewire\GenerateHomilia;
use Tests\TestCase;
use Mockery;

class SermonGenerationAndExportTest extends TestCase
{
    /**
     * Testa se a página inicial carrega com o componente Livewire correto.
     */
    public function test_home_page_renders_generate_homilia_component()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSeeLivewire(GenerateHomilia::class);
    }

    /**
     * Testa se o componente Livewire gera o sermão chamando o serviço corretamente.
     */
    public function test_livewire_component_generates_sermon()
    {
        $mockGenerator = Mockery::mock(SermonGeneratorInterface::class);
        $mockGenerator->shouldReceive('generate')
            ->once()
            ->with('Texto base para o sermao', 'João 3:16', 3)
            ->andReturn('# Esboço do Sermão de Teste');

        $this->app->instance(SermonGeneratorInterface::class, $mockGenerator);

        Livewire::test(GenerateHomilia::class)
            ->set('text_homilia', 'Texto base para o sermao')
            ->set('verso_homilia', 'João 3:16')
            ->set('qt_divisao_homilia', 3)
            ->call('generateHomilia')
            ->assertSet('message', '# Esboço do Sermão de Teste')
            ->assertSet('error', null);
    }

    /**
     * Testa se a rota de exportar PDF valida a ausência de conteúdo do sermão.
     */
    public function test_export_pdf_requires_sermon_content()
    {
        $response = $this->post(route('gemini-homilia.export-pdf'), []);
        $response->assertSessionHasErrors(['sermon_content']);
    }

    /**
     * Testa se a rota de exportação gera e retorna a resposta PDF correta.
     */
    public function test_export_pdf_generates_pdf_response()
    {
        $mockPdfService = Mockery::mock(PdfExportServiceInterface::class);
        $mockPdfService->shouldReceive('export')
            ->once()
            ->with('# Esboço do Sermão de Teste')
            ->andReturn(response('PDF_CONTENT', 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="test.pdf"',
            ]));

        $this->app->instance(PdfExportServiceInterface::class, $mockPdfService);

        $response = $this->post(route('gemini-homilia.export-pdf'), [
            'sermon_content' => '# Esboço do Sermão de Teste',
        ]);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertEquals('PDF_CONTENT', $response->getContent());
    }
}
