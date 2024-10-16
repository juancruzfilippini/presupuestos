<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail; // Añadir esta línea
use App\Models\Presupuesto; // Importamos el modelo correcto
use App\Models\Prestaciones; // Importamos el modelo correcto
use App\Models\Prestacion; // Importamos el modelo correcto
use App\Models\ObraSocial; // Importamos el modelo correcto
use App\Models\Firmas; // Importamos el modelo correcto
use App\Models\Paciente; // Importamos el modelo correcto
use App\Models\Anestesia_p; // Importamos el modelo correcto
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Archivo;
use App\Models\Convenio;
use App\Models\Exportar;
use App\Mail\mailPresupuesto; // Importar la clase de correo

use PDF;


class ExportarController extends Controller
{

    public function exportarDatos($id)
    {
        // Crear una instancia del modelo Exportar
        $exportar = new Exportar();

        // Obtener el presupuesto
        $presupuesto = $exportar->getPresupuesto($id);

        // Inicializar variables
        $os = '';
        $conv = '';

        // Establecer la anestesia según el id

        // Obtener obra social y convenio
        if (is_numeric($presupuesto->obra_social)) {
            $os = ObraSocial::getObraSocialById($presupuesto->obra_social);
        } else {
            $os = $presupuesto->obra_social;
        }

        if (is_numeric($presupuesto->convenio)) {
            $conv = Convenio::getConvenioById($presupuesto->convenio);
        } else {
            $conv = $presupuesto->convenio;
        }

        // Crear array con los datos
        $data = [
            'id' => $presupuesto->id,
            'fecha' => $presupuesto->fecha,
            'estado' => $presupuesto->estado,
            'medico_tratante' => $presupuesto->medico_tratante,
            'medico_solicitante' => $presupuesto->medico_solicitante,
            'paciente_salutte_id' => $presupuesto->paciente_salutte_id,
            'paciente' => $presupuesto->paciente,
            'edad' => $presupuesto->edad,
            'obra_social' => $os,
            'convenio' => $conv,
            'especialidad' => $presupuesto->especialidad,
            'total_presupuesto' => $presupuesto->total_presupuesto,
            'email' => $presupuesto->email,
            'telefono' => $presupuesto->telefono,
            'nro_afiliado' => $presupuesto->nro_afiliado,
            'condicion' => $presupuesto->condicion,
            'incluye' => $presupuesto->incluye,
            'excluye' => $presupuesto->excluye,
            'adicionales' => $presupuesto->adicionales,
            'detalle' => $presupuesto->detalle,
            'fcomer' => $presupuesto->fcomer,
            'faudi' => $presupuesto->faudi,
            'fdirec' => $presupuesto->fdirec,
        ];

        // Obtener las prestaciones asociadas al presupuesto
        $prestaciones = Prestaciones::where('presupuesto_id', $id)->get();
        $firmas = Firmas::where('presupuesto_id', $id)->first();
        $anestesias = Anestesia_p::where('presupuesto_id', $id)->get();
        $paciente = Paciente::findById($presupuesto->paciente_salutte_id);
        $today = date('Y-m-d');

        // Empaquetar ambas variables en un solo array
        $data1 = [
            'presupuesto' => $data,
            'prestaciones' => $prestaciones,
            'paciente' => $paciente,
            'anestesias' => $anestesias,
            'firmas' => $firmas,
            'today' => $today
        ];

        //dd($data);
        // Generar el PDF utilizando la vista y los datos
        $pdf = PDF::loadView('presupuestos.export_presupuesto', $data1);
        $pdf->setPaper('A4', 'portrait');

        // Descargar el PDF directamente sin almacenarlo en el servidor
        return $pdf->stream('presupuesto.pdf');
    }


    public function enviarDatosPorCorreo(Request $request, $id)
    {
        $exportar = new Exportar();
        $presupuesto = $exportar->getPresupuesto($id);

        $os = '';
        $conv = '';

        // Establecer la anestesia según el id

        // Obtener obra social y convenio
        if (is_numeric($presupuesto->obra_social)) {
            $os = ObraSocial::getObraSocialById($presupuesto->obra_social);
        } else {
            $os = $presupuesto->obra_social;
        }

        if (is_numeric($presupuesto->convenio)) {
            $conv = Convenio::getConvenioById($presupuesto->convenio);
        } else {
            $conv = $presupuesto->convenio;
        }

        // Crear array con los datos
        $data = [
            'id' => $presupuesto->id,
            'fecha' => $presupuesto->fecha,
            'estado' => $presupuesto->estado,
            'medico_tratante' => $presupuesto->medico_tratante,
            'medico_solicitante' => $presupuesto->medico_solicitante,
            'paciente_salutte_id' => $presupuesto->paciente_salutte_id,
            'paciente' => $presupuesto->paciente,
            'obra_social' => $os,
            'convenio' => $conv,
            'especialidad' => $presupuesto->especialidad,
            'total_presupuesto' => $presupuesto->total_presupuesto,
            'email' => $presupuesto->email,
            'telefono' => $presupuesto->telefono,
            'nro_afiliado' => $presupuesto->nro_afiliado,
            'condicion' => $presupuesto->condicion,
            'incluye' => $presupuesto->incluye,
            'excluye' => $presupuesto->excluye,
            'adicionales' => $presupuesto->adicionales,
            'detalle' => $presupuesto->detalle,
            'fcomer' => $presupuesto->fcomer,
            'faudi' => $presupuesto->faudi,
            'fdirec' => $presupuesto->fdirec,
        ];

        // Obtener las prestaciones asociadas al presupuesto
        $prestaciones = Prestaciones::where('presupuesto_id', $id)->get();
        $firmas = Firmas::where('presupuesto_id', $id)->first();
        $anestesias = Anestesia_p::where('presupuesto_id', $id)->get();
        $paciente = Paciente::findById($presupuesto->paciente_salutte_id);
        $today = date('Y-m-d');

        // Empaquetar ambas variables en un solo array
        $data1 = [
            'presupuesto' => $data,
            'prestaciones' => $prestaciones,
            'paciente' => $paciente,
            'anestesias' => $anestesias,
            'firmas' => $firmas,
            'today' => $today
        ];
        // Verificar si el contacto es nulo o si el email es nulo
        if ($presupuesto->email) {
            // Generar el PDF
            $pdf = PDF::loadView('presupuestos.export_presupuesto', $data1);


            // Definir el nombre del archivo
            $pdfFilename = 'Presupuesto HU. ' . $presupuesto->paciente . '.pdf';
            $pdfPath = storage_path('app/public/' . $pdfFilename);
            $pdf->save($pdfPath);

            $autoEmail = 'no-reply@hospital.uncu.edu.ar';

            // Enviar el PDF por correo al paciente y asi mismo
            Mail::to([$presupuesto->email, $autoEmail])->send(new mailPresupuesto($data1, $pdfPath));

            // Actualizar el estado del estudio solo si el email es válido y el envío fue exitoso
            Presupuesto::where('id', $id)->update(['enviado' => 1]);

            // Eliminar el archivo temporal después de enviar
            unlink($pdfPath);
            //Obtener la posicion mediante una consulta para redireccionar

            return redirect()->route('presupuestos.index')->with('success', 'Presupuesto enviado por correo con éxito');
        } else {
            // Redirigir con un mensaje de error si no hay email
            return redirect()->route('estudios.index')->with('error', 'El paciente no cuenta con un email registrado');
        }
    }
}