<?php

namespace App\Http\Controllers\REST;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\WordService;
use Sabre\DAV\Client;

class SistramdocDocumentController extends Controller
{
    protected WordService $wordService;

    public function __construct(WordService $wordService)
    {
        $this->wordService = $wordService;
    }

    public function create2(Request $request) {}

    public function create(Request $request)
    {
        $documentContent = $this->wordService->create('INFORME');

        $webdavSettings = [
            'baseUri'  => 'http://tmt-web/webdav/',
            // Añade autenticación si la tienes configurada en Apache:
            // 'userName' => 'tu_usuario',
            // 'password' => 'tu_contraseña',
        ];
        $client = new Client($webdavSettings);
        $remoteFilename = 'documento_generado_' . date('Ymd_His') . '.docx';

        try {
            // El método request('PUT', ...) envía el contenido binario (documentContent)
            $response = $client->request('PUT', $remoteFilename, $documentContent);

            // Los códigos 201 (Created) o 204 (No Content/Updated) indican éxito
            if ($response['statusCode'] === 201 || $response['statusCode'] === 204) {
                echo "✅ Documento '" . $remoteFilename . "' subido con éxito a WebDAV.\n";
            } else {
                echo "❌ Error al subir el documento. Código de estado: " . $response['statusCode'] . "\n";
                // Opcional: imprimir el cuerpo de la respuesta para depuración
                // echo "Cuerpo de la respuesta: " . $response['body'] . "\n";
            }
        } catch (\Exception $e) {
            echo "❌ Error de conexión/WebDAV: " . $e->getMessage() . "\n";
        }
    }
}
