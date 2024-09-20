<?php
namespace App\Http\Controllers\Admin;

use App\Models\Prestacion; // Importamos el modelo correcto
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PrestacionController extends Controller
{

    public function getPrestaciones($convenioId)
    {
        try {
            $prestaciones = DB::connection('db2')->select("
            SELECT 
                p.id AS prestacionid,
                p.codigo AS prestacioncodigo,
                p.nombre AS prestacionnombre,
                c.nombre AS convenionombre,
                p.root AS preroot
            FROM convenio c
            INNER JOIN nomenclador n ON c.id=n.convenio_id
            INNER JOIN prestacion p ON n.prestacion_raiz_id=p.root
            WHERE p.lvl = 3 AND p.activo = 1 AND c.id = :convenioId
            ORDER BY p.id", ['convenioId' => $convenioId]);

            return response()->json($prestaciones);
        } catch (\Exception $e) {
            // Registrar el error en el log
            \Log::error('Error en getPrestaciones: ' . $e->getMessage());
            // Retornar un mensaje de error
            return response()->json(['error' => 'Error al obtener las prestaciones'], 500);
        }
    }


    public function obtenerPrecio($convenioId, $codigoPrestacion)
    {
        $sql = "SELECT
    t.convenio AS CONVENIO,
    t.nomenclador AS NOMENCLADOR,
    t.plan AS PLAN,
    t.codigo AS CODIGO,
    t.prestacion AS PRESTACION,
    t.id,
    SUM(COALESCE(t.total, 0)) AS PRECIO,
    (SELECT COALESCE(MAX(cop.monto), 0)
     FROM prestacion AS pre3
     LEFT JOIN nomenclable nomen3 ON nomen3.id = pre3.id
     LEFT JOIN regla reg3 ON reg3.nomenclable_id = nomen3.id
     LEFT JOIN atributo atri3 ON atri3.regla_id = reg3.id
     LEFT JOIN copago cop ON cop.id = atri3.id
     LEFT JOIN nomenclador ndor3 ON ndor3.id = reg3.nomenclador_id
     LEFT JOIN convenio conv3 ON conv3.id = ndor3.convenio_id
     WHERE pre3.id=t.id AND conv3.id=t.convenioid
    ) AS COPAGO,
    (SELECT nconve.noconvenida
     FROM prestacion AS pre4
     INNER JOIN nomenclable nomen4 ON nomen4.id = pre4.id
     INNER JOIN regla reg4 ON reg4.nomenclable_id = nomen4.id
     INNER JOIN atributo atri4 ON atri4.regla_id = reg4.id
     INNER JOIN noconvenida nconve ON nconve.id = atri4.id
     INNER JOIN nomenclador ndor4 ON ndor4.id = reg4.nomenclador_id
     INNER JOIN convenio conv4 ON conv4.id = ndor4.convenio_id
     WHERE pre4.id=t.id AND conv4.id=t.convenioid AND atri4.dtype = 3
     GROUP BY pre4.id, nconve.noconvenida
    ) AS CONVENIDA
FROM (
    SELECT
        pre.nombre AS prestacion,
        pre.codigo AS codigo,
        pre.id,
        nom.nombre AS nomenclador,
        ara.nombre AS arancel,
        SUM(COALESCE(prec.monto, 0)) * SUM(COALESCE(multi.porciento, 1)) * COALESCE(multi2.porciento, 1) AS total,
        conve.nombre AS convenio,
        conve.id AS convenioid,
        pl.nombre AS plan
    FROM prestacion AS pre
    INNER JOIN prestacion AS nom ON nom.id = pre.root
    INNER JOIN prestacion_arancel AS pa ON pa.prestacion_id = pre.id
    INNER JOIN arancel AS ara ON ara.id = pa.arancel_id
    INNER JOIN nomenclable nomen ON nomen.id = pa.id
    INNER JOIN regla reg ON reg.nomenclable_id = nomen.id
    INNER JOIN atributo atri ON atri.regla_id = reg.id
    LEFT JOIN precio prec ON prec.id = atri.id
    LEFT JOIN multiplicador multi ON multi.id = atri.id
    INNER JOIN nomenclador ndor ON ndor.id = reg.nomenclador_id
    INNER JOIN convenio conve ON conve.id = ndor.convenio_id
    INNER JOIN regla AS reg2 ON reg2.nomenclable_id = ara.id
    INNER JOIN nomenclador ndor2 ON ndor2.id = reg2.nomenclador_id
    INNER JOIN convenio conv2 ON conv2.id = ndor2.convenio_id
    INNER JOIN atributo atri2 ON atri2.regla_id = reg2.id
    LEFT JOIN multiplicador multi2 ON multi2.id = atri2.id
    INNER JOIN convenio_plan AS conv_p ON conve.id = conv_p.convenio_id
    INNER JOIN plan AS pl ON pl.id = conv_p.plan_institucion_id
    WHERE pre.lvl = 3 AND conv_p.borrado_logico = 0 AND reg.activo = 1 AND conve.id = ? AND conv2.id = ?
    GROUP BY pre.nombre, pre.codigo, pre.id, nom.nombre, ara.nombre, conve.nombre, conve.id, pl.nombre, pa.id, multi.porciento, multi2.porciento
    ORDER BY pre.root, pre.codigo, ara.nombre, monto
) AS t
WHERE t.codigo LIKE ?
GROUP BY t.id, t.convenio, t.nomenclador, t.plan, t.codigo, t.prestacion, t.convenioid
HAVING CONVENIDA = 0 OR ISNULL(CONVENIDA);";

        $query = DB::connection('db2')->select($sql, [$convenioId, $convenioId, $codigoPrestacion]);

        return $query;
    }


}
