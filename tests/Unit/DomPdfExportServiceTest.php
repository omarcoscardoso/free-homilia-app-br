<?php

namespace Tests\Unit;

use App\Services\DomPdfExportService;
use Spatie\LaravelMarkdown\MarkdownRenderer;
use Tests\TestCase;
use Mockery;

class DomPdfExportServiceTest extends TestCase
{
    /**
     * Testa se o DomPdfExportService limpa o markdown, converte em HTML e exporta a resposta PDF correspondente.
     */
    public function test_it_exports_pdf_correctly()
    {
        $mockRenderer = Mockery::mock(MarkdownRenderer::class);
        $mockRenderer->shouldReceive('toHtml')
            ->once()
            ->with("<h1>Sermon</h1>") // O markdown é limpo antes de ir ao renderer
            ->andReturn('<h1>Sermon</h1>');

        $service = new DomPdfExportService($mockRenderer);
        
        // Simula o markdown com cabeçalhos extras que devem ser limpos
        $markdown = "*gerado em: 30/07/2025*\n```\n<h1>Sermon</h1>\n```";
        
        $response = $service->export($markdown);
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment; filename=esboco_homilIA_', $response->headers->get('Content-Disposition'));
    }
}
