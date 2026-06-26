<?php

namespace App\Contracts;

use Symfony\Component\HttpFoundation\Response;

interface PdfExportServiceInterface
{
    /**
     * Exporta o conteúdo do sermão (Markdown) para uma resposta HTTP contendo o PDF.
     *
     * @param string $markdownContent
     * @return Response
     */
    public function export(string $markdownContent): Response;
}
