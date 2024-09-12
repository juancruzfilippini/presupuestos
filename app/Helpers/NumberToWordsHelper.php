<?php

namespace App\Helpers;

use NumberToWords\NumberToWords;

class NumberToWordsHelper
{
    public static function convertir($numero)
    {
        // Crea una instancia de NumberToWords
        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('es'); // El idioma puede ser 'es' para español

        // Asegúrate de que el número tenga dos decimales
        $numero = number_format($numero, 2, '.', '');

        // Divide el número en parte entera y decimal
        list($entero, $decimal) = explode('.', $numero);

        // Convierte la parte entera a palabras
        $parteEntera = $numberTransformer->toWords($entero);

        // Convierte la parte decimal a palabras (asumiendo que es una parte de 100)
        $parteDecimal = $decimal ? $numberTransformer->toWords($decimal) . ' centavos' : '';

        // Concatenar ambas partes
        return ucfirst($parteEntera . ' pesos con ' . $parteDecimal);
    }

}
