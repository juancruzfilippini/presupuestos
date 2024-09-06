<?php

namespace App\Helpers;

use NumberToWords\NumberToWords;

class NumberToWordsHelper
{
    public static function convertir($numero)
    {
        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('es'); // El idioma puede ser 'es' para español
        return ucfirst($numberTransformer->toWords($numero));
    }
}
