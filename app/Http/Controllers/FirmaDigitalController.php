<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SignatureController extends Controller
{
    public function sign(Request $request)
    {
        $path = storage_path('app/private/fwAuthorization.json');

        if (!file_exists($path)) {
            return response()->json(['error' => 'Archivo fwAuthorization.json no encontrado'], 404);
        }

        $authorizationData = json_decode(file_get_contents($path), true);

        $clientId = $authorizationData['client_id'] ?? null;
        $clientSecret = $authorizationData['client_secret'] ?? null;
        $tokenUrl = $authorizationData['token_url'] ?? null;

        if (!$clientId || !$clientSecret || !$tokenUrl) {
            return response()->json(['error' => 'Datos de autorización incompletos'], 400);
        }

        $response = Http::asForm()->post($tokenUrl, [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Error al obtener el token'], 500);
        }

        $token = $response->body();

        $data = [
            "signatureFormat" => "PAdES",
            "signatureLevel" => 'B',
            "signaturePackaging" => "enveloped",
            "documentToSign" => url('test-firma-peru/test.pdf'),
            "certificateFilter" => ".*",
            "webTsa" => "",
            "userTsa" => "",
            "passwordTsa" => "",
            "theme" => "claro",
            "visiblePosition" => true,
            "contactInfo" => "",
            "signatureReason" => "Soy el autor de este documento",
            "bachtOperation" => false,
            "oneByOne" => true,
            "signatureStyle" => 1,
            "imageToStamp" => url('test-firma-peru/stamp.png'),
            "stampTextSize" => 14,
            "stampWordWrap" => 37,
            "role" => 'Analista de Servicios',
            "stampPage" => 1,
            "positionx" => 20,
            "positiony" => 20,
            "uploadDocumentSigned" => url('test-firma-peru/upload.php'),
            'certificationSignature' => false,
            'token' => $token
        ];

        $base64 = base64_encode(json_encode($data));

        return response($base64)->header('Content-Type', 'text/plain');
    }
}
