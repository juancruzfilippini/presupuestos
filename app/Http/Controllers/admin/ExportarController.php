<?php

use Illuminate\Support\Facades\Auth;
use App\Models\Presupuesto; // Importamos el modelo correcto
use App\Models\Prestacion; // Importamos el modelo correcto
use App\Models\ObraSocial; // Importamos el modelo correcto
use App\Models\Firmas; // Importamos el modelo correcto
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Models\Archivo;
use App\Models\Convenio;
use App\Models\Exportar;
use App\Models\Prestaciones;
use Illuminate\Support\Facades\DB;
use PDF;


class ExportarController extends Controller
{

    public function exportarDatos($id)
    {
        // Crear una instancia del modelo Exportar
        $exportar = new Exportar();

        $presupuesto = $exportar->getPresupuesto($id);

        $os = '';
        $conv = '';

        switch ($presupuesto->anestesia_id) {
            case 0:
                $anestesia = 'Sin anestesia';
                break;
            case 1:
                $anestesia = 'Local';
                break;
            case 2:
                $anestesia = 'Periférica';
                break;
            case 3:
                $anestesia = 'Central';
                break;
            case 4:
                $anestesia = 'Total';
                break;
            default:
                $anestesia = 'No especificado';
                break;
        }

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
            'anestesia' => $anestesia,
            'detalle_anestesia' => $presupuesto->detalle_anestesia,
            'complejidad' => $presupuesto->complejidad,
            'precio_anestesia' => $presupuesto->precio_anestesia,
            'total_presupuesto' => $presupuesto->total_presupuesto,
            'condicion' => $presupuesto->condicion,
            'incluye' => $presupuesto->incluye,
            'excluye' => $presupuesto->excluye,
            'adicionales' => $presupuesto->adicionales,
            'detalle' => $presupuesto->detalle,
            'fcomer' => $presupuesto->fcomer,
            'faudi' => $presupuesto->faudi,
            'fdirec' => $presupuesto->fdirec,
        ];


        // Generar el PDF utilizando la vista y los datos
        $pdf = PDF::loadView('presupuesto.export_presupuesto', $data);

        // Descargar el PDF directamente sin almacenarlo en el servidor
        return $pdf->download('informe.pdf');
    }
}