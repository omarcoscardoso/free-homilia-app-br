<?php

namespace App\Services;

use App\Contracts\PdfExportServiceInterface;
use Spatie\LaravelMarkdown\MarkdownRenderer;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class DomPdfExportService implements PdfExportServiceInterface
{
    protected MarkdownRenderer $markdownRenderer;

    /**
     * Injeção de dependência do MarkdownRenderer.
     */
    public function __construct(MarkdownRenderer $markdownRenderer)
    {
        $this->markdownRenderer = $markdownRenderer;
    }

    /**
     * Exporta o conteúdo Markdown de um sermão para um arquivo PDF seguro e estilizado.
     */
    public function export(string $markdownContent): Response
    {
        $cleanedMarkdown = $this->cleanMarkdown($markdownContent);

        // Converte o Markdown limpo para HTML utilizando a biblioteca padrão configurada
        $htmlContent = $this->markdownRenderer->toHtml($cleanedMarkdown);

        // Constrói a estrutura HTML final com estilos limpos em Vanilla CSS
        $fullHtml = $this->buildFullHtml($htmlContent);

        // Carrega o HTML na fachada do DomPDF
        $pdf = Pdf::loadHtml($fullHtml);
        $pdf->setPaper('A4', 'portrait');

        // Configuração de segurança programática do DomPDF para evitar LFI/SSRF
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->set_option('isPhpEnabled', false);
        $pdf->getDomPDF()->set_option('isRemoteEnabled', false);

        $filename = 'esboco_homilIA_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Limpa e formata o markdown removendo metadados desnecessários do início.
     */
    protected function cleanMarkdown(string $markdownContent): string
    {
        $lines = explode("\n", $markdownContent);
        $cleanedLines = [];
        $foundFirstUsefulLine = false;

        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            if (!$foundFirstUsefulLine && (empty($trimmedLine) 
                || str_starts_with($trimmedLine, '*gerado em:') 
                || str_starts_with($trimmedLine, 'data:') 
                || str_starts_with($trimmedLine, '```'))) {
                continue;
            }
            $foundFirstUsefulLine = true;
            $cleanedLines[] = $line;
        }

        if (!empty($cleanedLines) && str_ends_with(trim(end($cleanedLines)), '```')) {
            array_pop($cleanedLines);
        }

        while (!empty($cleanedLines) && trim($cleanedLines[0]) === '') {
            array_shift($cleanedLines);
        }

        return implode("\n", $cleanedLines);
    }

    /**
     * Envolve o HTML em uma estrutura completa estilizada em Vanilla CSS para o DomPDF.
     */
    protected function buildFullHtml(string $htmlContent): string
    {
        return "<!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Esboço do Sermão</title>
            <style>
            body {
                font-family: Arial, sans-serif;
                margin: 15mm;
                color: #1f2937;
                line-height: 1.6;
            }
            h1 {
                font-size: 24px;
                font-weight: 800;
                margin-top: 0px;
                margin-bottom: 12px;
                color: #111827;
                line-height: 1.25;
            }
            h2 {
                font-size: 20px;
                font-weight: 700;
                margin-top: 28px;
                margin-bottom: 14px;
                color: #1d4ed8;
                border-bottom: 2px solid #bfdbfe;
                padding-bottom: 6px;
            }
            h3 {
                font-size: 16px;
                font-weight: 600;
                margin-top: 20px;
                margin-bottom: 10px;
                color: #2563eb;
            }
            p {
                margin-bottom: 12px;
                color: #374151;
            }
            ul {
                list-style-type: disc;
                padding-left: 24px;
                margin-bottom: 14px;
            }
            li {
                margin-bottom: 6px;
                color: #4b5563;
            }
            h2 + p, h3 + p, h2 + ul, h3 + ul {
                page-break-before: auto !important;
            }
            .no-break {
                page-break-inside: avoid !important;
            }
            strong {
                font-weight: bold;
                color: #111827;
            }
            em {
                font-size: 14px;
                color: #4b5563;
            }
            blockquote {
                border-left: 4px solid #60a5fa;
                padding-left: 12px;
                margin: 16px 0;
                font-style: italic;
                color: #4b5563;
            }
            code {
                background-color: #f3f4f6;
                color: #8b5cf6;
                padding: 2px 4px;
                border-radius: 4px;
                font-family: monospace;
                font-size: 13px;
            }
            a {
                color: #2563eb;
                text-decoration: underline;
            }
            </style>
        </head>
        <body>
            " . $htmlContent . "
        </body>
        </html>";
    }
}
