<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Presupuesto;

class mailPresupuesto extends Mailable
{
    use Queueable, SerializesModels;

    public $presupuesto;
    public $data1;
    public $pdfPath;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data1, $pdfPath)
    {
        $this->data1 = $data1;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        
        return $this->view('presupuestos.email_presupuesto') 
                    ->attach($this->pdfPath, [
                        'as' => 'Presupuesto_'. $this->data1['presupuesto']['paciente'] .'.pdf',
                        'mime' => 'application/pdf',
                    ])
                    ->subject('PRESUPUESTO HOSPITAL UNIVERSITARIO');
    }
}