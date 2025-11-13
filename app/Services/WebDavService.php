<?php

namespace App\Services;

use Sabre\DAV\Client;

class WebDavService
{
    public function upload($documentId, $documentContent)
    {
        $webdavSettings = [
            'baseUri'  => config('services.web-dav.url'),
        ];

        $client = new Client($webdavSettings);
        $remoteFilename = $documentId . '.docx';

        try {
            $response = $client->request('PUT', $remoteFilename, $documentContent);

            if ($response['statusCode'] === 201 || $response['statusCode'] === 204) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            logger($ex);
            return false;
        }
    }

    public function get($remotePath)
    {
        $client = new Client([
            'baseUri'  => config('services.web-dav.url'),
        ]);

        try {
            $response = $client->request('GET', $remotePath);

            if ($response['statusCode'] !== 200) {
                return response()->json(['error' => 'No se pudo obtener el archivo desde WebDAV'], 404);
            }

            return $response['body'];
        } catch (\Throwable $e) {
            logger($e);
            throw $e;
        }
    }
}
