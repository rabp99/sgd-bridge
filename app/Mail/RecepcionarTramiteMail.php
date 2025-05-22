<?php

namespace App\Mail;

use App\Http\Services\DocumentoService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class RecepcionarTramiteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    protected DocumentoService $documentoService;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->documentoService = app()->make(DocumentoService::class);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $ccodtipdocValue = 'Carta';

        $vcuo = $this->data['vcuo'];
        $vrucentrem = $this->data['vrucentrem'];
        $vnomentemi = $this->data['vnomentemi'];
        $vuniorgrem = $this->data['vuniorgrem'];
        $ccodtipdocValue =  $this->documentoService->getTipo($this->data['ccodtipdoc']);
        $vnumdoc = $this->data['vnumdoc'];
        $dfecdoc = $this->data['dfecdoc'];
        $vasu = $this->data['vasu'];

        return $this->subject("Se recibió un documento | CUO: $vcuo")
            ->view('mails.recepcionar-tramite', compact(
                'vcuo',
                'vrucentrem',
                'vnomentemi',
                'vuniorgrem',
                'ccodtipdocValue',
                'vnumdoc',
                'dfecdoc',
                'vasu',
            ));
    }
}
