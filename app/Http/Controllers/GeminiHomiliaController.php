<?php

namespace App\Http\Controllers;

use App\Contracts\PdfExportServiceInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GeminiHomiliaController extends Controller
{
    protected PdfExportServiceInterface $pdfService;

    /**
     * Injeção de dependência do serviço de exportação de PDF (SOLID DIP/SRP).
     */
    public function __construct(PdfExportServiceInterface $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Endpoint para exportar o esboço de sermão para PDF.
     */
    public function exportPdf(Request $request): Response
    {
        $request->validate([
            'sermon_content' => 'required|string|max:50000',
        ], [
            'sermon_content.required' => 'Não há conteúdo de sermão para exportar.',
            'sermon_content.string' => 'O conteúdo do sermão deve ser um texto válido.',
            'sermon_content.max' => 'O conteúdo do sermão excede o tamanho limite permitido para exportação.',
        ]);

        return $this->pdfService->export($request->input('sermon_content'));
    }
}