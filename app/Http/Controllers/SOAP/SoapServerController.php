<?php

namespace App\Http\Controllers\SOAP;

use App\Http\Services\TramiteService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SoapServerController extends Controller
{
    public function handle(Request $request)
    {
        $nginxIp = trim(shell_exec("getent hosts sgd-bridge-nginx | awk '{ print $1 }'"));

        $wsdl = response()
            ->view('soap.wsdl', ['host' => $nginxIp])
            ->header('Content-Type', 'text/xml');

        if ($request->has('wsdl')) {
            return $wsdl;
        }

        $url = 'http://' . $nginxIp . '/wsiotramite/Tramite?wsdl';
        $server = new \SoapServer($url, [
            'uri' => 'http://' . $nginxIp,
        ]);

        $server->setObject(new TramiteService());

        ob_start();
        $server->handle();
        $response = ob_get_clean();

        return response($response, 200)->header('Content-Type', 'text/xml');
    }
}
