<?php

//Variables iniciales
$characters = [0,1,2];
$variant = [2,3,4];
$x = [0,1,2];
$twos = [0,1,2];

$full_code = generateFullCode($characters);
//echo toStringCode($full_code);die;

$conditioned_code = generateConditionedCode($full_code, $variant, $x, $twos);

$initial_covering_code = generateCoveringCode(73, 1, $full_code);
//echo toStringCoveringCode($initial_covering_code);die;

$covering_code = simulatedAnnealing($initial_covering_code, $conditioned_code);

//Algoritmo de recocido simulado para la obtención de un código de recubrimiento óptimo
function simulatedAnnealing($covering_code, $conditioned_code)
{
    //Obtenemos el número de apuestas del espacio condicionado que ha quedado sin recubrir con el código inicial
    $n = 0;
    $uncovering = $covering_code['uncovering'];
    $covering = $covering_code['covering'];
    foreach ($conditioned_code as $word)
    {
        if (in_array($word, $uncovering))
            $n ++;
    }

    //Se repite el algoritmo mientras n sea mayor de 0
    while ($n > 0)
    {
        foreach ($covering as $group)
        {
            var_dump($group);die;
        }
    }
}

//Función que genera el código del conjunto completo de 6 triples
function generateFullCode($characters)
{
    $a1 = 0;
    $a2 = 0;
    $a3 = 0;
    $a4 = 0;
    $a5 = 0;
    $a6 = 0;

    $code = [];

    while($a1 != sizeof($characters))
    {
        while ($a2 != sizeof($characters))
        {
            while ($a3 != sizeof($characters))
            {
                while ($a4 != sizeof($characters))
                {
                    while ($a5 != sizeof($characters))
                    {
                        while ($a6 != sizeof($characters))
                        {
                            $code[]=[$characters[$a1],$characters[$a2],$characters[$a3],$characters[$a4],$characters[$a5],$characters[$a6]];
                            $a6+=1;
                        }
                        $a6=0;
                        $a5+=1;
                    }
                    $a5=0;
                    $a4+=1;
                }
                $a4=0;
                $a3+=1;
            }
            $a3=0;
            $a2+=1;
        }
        $a2=0;
        $a1+=1;
    }
    return $code;
}


//Genera el código del conjunto condicionado de apuestas
function generateConditionedCode($code, $variant, $x, $twos)
{
    $conditioned_code = [];
    foreach ($code as $word)
    {
        $count_values = array_count_values($word);

        $variant_count = $count_values[1]+$count_values[2];
        $x_count = $count_values[1];
        $twos_count = $count_values[2];

        if (in_array($variant_count, $variant) && in_array($x_count, $x) && in_array($twos_count, $twos))
            $conditioned_code[] = $word;
    }

    return $conditioned_code;
}

//Genera aleatoriamente n bolas de radio r con palabras de code
function generateCoveringCode($n, $r, $code)
{
    //echo sizeof($code).'---';
    $groups = ['covering' => [], 'unconvering' => []];

    //Seleccionamos n centros aleatorios
    for ($i=0;$i<$n;$i++)
    {
        $random_int = array_rand($code);
        $center = $code[$random_int];
        unset($code[$random_int]);
        $groups['covering'][] = ['center' => $center, 'words' => []];
    }

    //Agrupamos las palabras a distancia r de cada centro
    foreach ($groups['covering'] as &$group)
    {
        foreach ($code as $it_code => $word)
        {
            $distance = 0;
            foreach ($word as $it => $bit)
            {
                if ($group['center'][$it] != $bit)
                {
                    $distance ++;
                    if ($distance > $r)
                        break;
                }
            }

            if ($distance <= $r)
            {
                $group['words'][] = $word;
                unset($code[$it_code]);
            }
        }
    }

    //Agrupamos las palabras que quedan sin recubrir
    $groups['uncovering'] = $code;

    //echo sizeof($code);
    return $groups;
}

//Convierte a cadena de texto la estructura de datos de un código
function toStringCode($code)
{
    $str = '';
    foreach ($code as $it => $word)
        $str .= (($it != 0) ? ', ' : '').'{'.implode(', ', $word).'}';
    return $str;
}

//Convierte a cadena de texto la estructura de datos del código de recubrimiento
function toStringCoveringCode($code)
{
    $str = '';
    $str .= '---- COVERING ----'.PHP_EOL;
    foreach ($code['covering'] as $it => $data)
    {
        $str .= 'Group '.($it+1).PHP_EOL;
        $str .= '---- Center: '.toStringWord($data['center']).PHP_EOL;
        $str .= '---- Words: '.toStringCode($data['words']).PHP_EOL;
    }

    $str .= '---- UNCOVERING ('.sizeof($code['uncovering']).') ----'.PHP_EOL;
    $str .= toStringCode($code['uncovering']);

    return $str;
}

//Convierte a cadena de texto una palabra
function toStringWord($word)
{
    $str = '{'.implode(', ', $word).'}';
    return $str;
}

