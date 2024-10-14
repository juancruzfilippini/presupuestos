@php
    use App\Models\PersonaAlephooModel;
    use App\Models\PersonaLocalModel;
    use App\Models\EstadoModel;
    use App\Models\DatoPersonaModel;
    use App\Models\EspecialidadModel;
    use App\Models\TipoModel;
    use Carbon\Carbon;

    if($alert->is_in_alephoo == 1){
        $persona = PersonaAlephooModel::find($alert->persona_id);
    }else{
        $persona = PersonaLocalModel::find($alert->persona_id);
    }
@endphp

<!DOCTYPE html>
<html>
<head>
    <title>Hospital Universitario</title>
</head>
<body>
    <p><strong>Estimado {{ $persona->apellidos." ".$persona->nombres }}</strong></p>
    <h1>Le informamos que tiene agendada una alerta temprana este mes</h1>
    <p><strong>{{ $alert->especialidad }}</strong></p>
    <p>Detalle del informe:</p>
    <p>{{ $alert->detalle }}</p>
    <p>Por favor, si no es contactado por el Hospital Universitario debe contactarse personalmente y solicitar turno para control de su salud.</p>
    <p>Saludos cordiales,</p>
    <p>El equipo de alertas tempranas</p>
</body>
</html>