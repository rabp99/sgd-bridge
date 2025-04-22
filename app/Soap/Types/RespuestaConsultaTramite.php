<?php

namespace App\Soap\Types;

class RespuestaConsultaTramite
{
    public $return;

    public function __construct()
    {
        $this->return = new class {
            public $vcodres;
            public $vdesres;
            public $vcuo;
            public $vcuoref;
            public $vnumregstd;
            public $vanioregstd;
            public $vuniorgstd;
            public $dfecregstd;
            public $vusuregstd;
            public $bcarstd;
            public $vobs;
            public $cflgest;
        };
    }
}
