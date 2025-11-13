<?php

namespace App\Http\Controllers\REST;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\WebDavService;
use App\Services\WordService;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;

class SistramdocDocumentController extends Controller
{
    protected WordService $wordService;

    protected WebDavService $webDavService;

    public function __construct(
        WordService $wordService,
        WebDavService $webDavService
    ) {
        $this->wordService = $wordService;
        $this->webDavService = $webDavService;
    }

    public function create(Request $request)
    {
        $documentContent = $this->wordService->create(
            $request->params,
            $request->recipients_mult,
            $request->refs
        );

        if ($this->webDavService->upload($request->params['document_id'], $documentContent)) {
            return response()->json([
                'message' => 'The file was successfully uploaded to the webdav server.'
            ]);
        } else {
            return response()->json(['error' => 'It was not possible to upload the file to the webdav server.'], 500);
        }
    }

    public function convert2(Request $request)
    {
        try {
            $path = $request->input('path');

            $responseBody = $this->webDavService->get($path);

            $tempWord = tempnam(sys_get_temp_dir(), 'word_') . '.docx';
            file_put_contents($tempWord, $responseBody);

            $phpWord = IOFactory::load($tempWord);

            Settings::setPdfRendererName('TCPDF');
            Settings::setPdfRendererPath(base_path('vendor/tecnickcom/tcpdf'));

            $pdfTemp = tempnam(sys_get_temp_dir(), 'pdf_') . '.pdf';
            $writer = IOFactory::createWriter($phpWord, 'PDF');
            $writer->save($pdfTemp);

            @unlink($tempWord);

            return response()->file($pdfTemp, [
                'Content-Type' => 'application/pdf',
            ])->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            logger($e);
            throw $e;
        }
    }

    public function convert(Request $request)
    {
        try {
            $path = $request->input('path');

            $responseBody = $this->webDavService->get($path);

            $tempWord = tempnam(storage_path('conversion_shared'), 'word_');
            file_put_contents($tempWord, $responseBody);

            $pdfTemp = tempnam(storage_path('conversion_shared'), 'pdf_');

            @unlink($pdfTemp);
            $conversionCommand = "soffice --headless --convert-to pdf:writer_pdf_Export --outdir /app/conversion_shared/" . basename($pdfTemp) . " /app/conversion_shared/" . basename($tempWord);
            logger($conversionCommand);
            $dockerCommand = "/bin/bash -c '/usr/bin/docker exec sgd-bridge-converter {$conversionCommand}'";
            logger($dockerCommand);
            $output = shell_exec($dockerCommand);
            logger($output);

            if (file_exists(storage_path('conversion_shared') . $pdfTemp)) {
                @unlink($tempWord);

                return response()->file($pdfTemp, [
                    'Content-Type' => 'application/pdf',
                ])->deleteFileAfterSend(true);
            }
        } catch (\Throwable $e) {
            logger($e);
            throw $e;
        }
    }
}
