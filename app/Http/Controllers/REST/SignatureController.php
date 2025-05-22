<?php

namespace App\Http\Controllers\REST;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

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

        $idRegistro = $request->id_registro;
        $areaIdDoc = $request->area_id_doc;

        $queryParams = "id_registro=$idRegistro&area_id_doc=$areaIdDoc";

        // $documentTosignURL = 'http://localhost:8080/SISTRAMDOC/generate-pdf.php?id_registro=' . $idRegistro . '&area_id_doc=' . $areaIdDoc;
        $documentTosignURL = env('SGD_CLIENT_PDF_GENERATOR_URL') . '?' . $queryParams;
        // $uploadURL = 'http://localhost:8080/SISTRAMDOC/upload-pdf.php?id_registro=' . $idRegistro;
        $uploadURL = env('SGD_CLIENT_PDF_UPLOADER_URL') . '?' . $queryParams;
        $imageToStampURL = env('SGD_CLIENT_IMAGE_TO_STAMP');

        $signatureReason = $request->signatureReason;
        $role = $request->role;

        $data = [
            "signatureFormat" => "PAdES",
            "signatureLevel" => 'B',
            "signaturePackaging" => "enveloped",
            "documentToSign" => $documentTosignURL,
            "certificateFilter" => ".*",
            "webTsa" => "",
            "userTsa" => "",
            "passwordTsa" => "",
            "theme" => "claro",
            "visiblePosition" => true,
            "contactInfo" => "",
            "signatureReason" => $signatureReason, // "Soy el autor de este documento", // here
            "bachtOperation" => false,
            "oneByOne" => true,
            "signatureStyle" => 1,
            "imageToStamp" => $imageToStampURL, // 'top-secret-stamp.png' here
            "stampTextSize" => 14,
            "stampWordWrap" => 37,
            "role" => $role, // 'Analista de Servicios', // here
            "stampPage" => 1,
            "positionx" => 20,
            "positiony" => 20,
            "uploadDocumentSigned" => $uploadURL,
            'certificationSignature' => false,
            'token' => $token
        ];

        $base64 = base64_encode(json_encode($data));

        return response($base64)->header('Content-Type', 'text/plain');
    }

    public function signCargo(Request $request)
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

        // $idRegistro = $request->id_registro;
        // $areaIdDoc = $request->area_id_doc;

        $id = $request->id;
        $queryParams = "id=$id";

        // $documentTosignURL = 'http://localhost:8080/SISTRAMDOC/generate-pdf.php?id_registro=' . $idRegistro . '&area_id_doc=' . $areaIdDoc;
        $documentTosignURL = env('SGD_CLIENT_PDF_CARGO_GENERATOR_URL') . '?' . $queryParams;
        // $uploadURL = 'http://localhost:8080/SISTRAMDOC/upload-pdf.php?id_registro=' . $idRegistro;
        $uploadURL = env('SGD_CLIENT_PDF_CARGO_UPLOADER_URL') . '?' . $queryParams;
        $imageToStampURL = env('SGD_CLIENT_IMAGE_TO_STAMP');

        $signatureReason = $request->signatureReason;
        $role = $request->role;

        $data = [
            "signatureFormat" => "PAdES",
            "signatureLevel" => 'B',
            "signaturePackaging" => "enveloped",
            "documentToSign" => $documentTosignURL,
            "certificateFilter" => ".*",
            "webTsa" => "",
            "userTsa" => "",
            "passwordTsa" => "",
            "theme" => "claro",
            "visiblePosition" => true,
            "contactInfo" => "",
            "signatureReason" => $signatureReason, // "Soy el autor de este documento", // here
            "bachtOperation" => false,
            "oneByOne" => true,
            "signatureStyle" => 1,
            "imageToStamp" => $imageToStampURL, // 'top-secret-stamp.png' here
            "stampTextSize" => 14,
            "stampWordWrap" => 37,
            "role" => $role, // 'Analista de Servicios', // here
            "stampPage" => 1,
            "positionx" => 20,
            "positiony" => 20,
            "uploadDocumentSigned" => $uploadURL,
            'certificationSignature' => false,
            'token' => $token
        ];

        $base64 = base64_encode(json_encode($data));

        return response($base64)->header('Content-Type', 'text/plain');
    }
}
