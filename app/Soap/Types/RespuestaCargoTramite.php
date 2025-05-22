<?php

namespace App\Soap\Types;

class RespuestaCargoTramite
{
    public $return;

    public function __construct()
    {
        $this->return = new class {
            public $vcodres;
            public $vdesres;
        };
    }
}
