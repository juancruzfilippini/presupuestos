<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Mail\mailPresupuesto;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Presupuesto; // Importamos el modelo correcto


class MailController extends Controller
{
    public function sendMail($id){

        $email = Presupuesto::where('id', $id)->email;
        Mail::to($email)->send(new mailPresupuesto($alert));
    }
}