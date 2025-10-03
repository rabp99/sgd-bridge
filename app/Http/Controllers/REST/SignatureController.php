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
        $isExternal = $request->isExternal;
        $signatureReason = $request->signatureReason;
        $role = $request->role;

        $queryParams = "id_registro=$idRegistro&area_id_doc=$areaIdDoc&isExternal=$isExternal";

        $documentTosignURL = env('SGD_CLIENT_PDF_GENERATOR_URL') . '?' . $queryParams;
        $uploadURL = env('SGD_CLIENT_PDF_UPLOADER_URL') . '?' . $queryParams;
        $imageToStampURL = env('SGD_CLIENT_IMAGE_TO_STAMP');

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
            "stampTextSize" => 15,
            "stampWordWrap" => 44,
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

        $id = $request->id;
        $signatureReason = $request->signatureReason;
        $role = $request->role;

        $queryParams = "id=$id";

        $documentTosignURL = env('SGD_CLIENT_PDF_CARGO_GENERATOR_URL') . '?' . $queryParams;
        $uploadURL = env('SGD_CLIENT_PDF_CARGO_UPLOADER_URL') . '?' . $queryParams;
        $imageToStampURL = env('SGD_CLIENT_IMAGE_TO_STAMP');

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
            "stampTextSize" => 15,
            "stampWordWrap" => 44,
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

    public function signCargoInterno(Request $request) 
    {
        logger('test');
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

        $id_RegDestino = $request->id_RegDestino;
        $id_registro = $request->id_registro;
        $signatureReason = $request->signatureReason;
        $role = $request->role;

        $queryParams = "id_RegDestino=$id_RegDestino&id_registro=$id_registro&signatureReason=" . urlencode($signatureReason) . "&role=" . urlencode($role) . "&isExternal=false";

        $documentTosignURL = env('SGD_CLIENT_PDF_INTERNO_URL') . '?' . $queryParams;
        $uploadURL = env('SGD_CLIENT_PDF_UPLOADER_CARGO_INTERNO_URL') . '?' . $queryParams;
        $imageToStampURL = env('SGD_CLIENT_IMAGE_TO_STAMP');

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
            "stampTextSize" => 15,
            "stampWordWrap" => 44,
            "role" => $role, // 'Analista de Servicios', // here
            "stampPage" => 1,
            "positionx" => 20,
            "positiony" => 20,
            "uploadDocumentSigned" => $uploadURL,
            'certificationSignature' => false,
            'token' => $token
        ];

        $base64 = base64_encode(json_encode($data));

        logger('test 2222');
        return response($base64)->header('Content-Type', 'text/plain');
    }
}
