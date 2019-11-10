<?php

//Variables iniciales
$characters = [0,1,2];
$variant = [2,3,4];
$x = [0,1,2];
$twos = [0,1,2];
$r = 1;
$n = 42;
$temperature = 2;
$cold_constant = 0.95;


$full_code = generateFullCode($characters);
//echo toStringCode($full_code);die;

$conditioned_code = generateConditionedCode($full_code, $variant, $x, $twos);

$start_time = time();

ob_start();

$initial_covering_code = generateCoveringCode($n, $r, $full_code);
//echo toStringCoveringCode($initial_covering_code);die;

$covering_code = simulatedAnnealing($initial_covering_code, $full_code, $conditioned_code, $r, $temperature, $cold_constant);

ob_end_flush();

$end_time = time();

echo "----- SOLUCIÓN -----".PHP_EOL;
echo toStringCoveringCode($covering_code, $conditioned_code);
echo "Conditioned Uncovering: ". sizeof(getUncoveringWords($covering_code, $conditioned_code)).PHP_EOL;
echo "Total Uncovering: ". sizeof(getUncoveringWords($covering_code, $full_code)).PHP_EOL;

echo "Total Covered: ". sizeof(getCoveredWords($covering_code)).PHP_EOL;

echo 'START TIME:'.$start_time.PHP_EOL;
echo 'END TIME:'.$end_time.PHP_EOL;
echo 'TOTAL TIME:'.($end_time-$start_time).'s'.PHP_EOL;

//Algoritmo de recocido simulado para la obtención de un código de recubrimiento óptimo
function simulatedAnnealing($covering_code, $code, $conditioned_code, $r, $temperature, $cold_constant)
{

    //Obtenemos el número de apuestas del espacio condicionado que ha quedado sin recubrir con el código inicial
    $n = sizeof(getUncoveringWords($covering_code, $conditioned_code));

    //Se repite el algoritmo mientras n sea mayor de 0
    while ($n > 0)
    {
        //Recorremos todas las bolas
        foreach ($covering_code as $it_group => $group)
        {
            //Vamos a repetir el proceso 5 veces por bola
            for ($i=0; $i<5; $i++)
            {

                //Obtenemos una apuesta aleatoria del conjunto que no sea ya centro de otra bola
                do {
                    $random_word_it = array_rand($group['words']);
                    $random_word = $group['words'][$random_word_it];

                    $is_center = false;
                    foreach ($covering_code as $g)
                    {
                        if ($g['center'] == $random_word)
                        {
                            $is_center = true;
                            break;
                        }
                    }

                } while ($is_center);

                //Volvemos a generar el contenido de la bola con este nuevo centro
                $new_covering_code = regenerateGroupWords($covering_code, $it_group, $random_word, $r, $code);

                //Volvemos a comprobar n para el nuevo conjunto sin recubrir
                $n_partial = sizeof(getUncoveringWords($new_covering_code, $conditioned_code));

                //Se acepta el resultado si el nuevo n es menor o igual que el anterior o si se cumple la condición de probabilidad con la temperatura
                $acepta = 0;
                if ($n_partial <= $n)
                    $acepta = 1;
                else
                {
                    $probability = exp((-1*($n_partial-$n))/$temperature);
                    $random = rand(0, 10) / 10;
                    if ($random<$probability)
                        $acepta = 2;
                }

                if ($acepta > 0)
                {
                    $n = $n_partial;
                    $group = $new_covering_code[$it_group];
                    $covering_code[$it_group] = $new_covering_code[$it_group];
                }

                echo 'BOLA '.$it_group.' It: '.$i.' --- '.(($acepta > 0) ? '(ACEPTA SOLUCIÓN '.(($acepta == 2) ? 'PROBABILIDAD' : '').')' : '').PHP_EOL.
                    'Old:'.$n.PHP_EOL.
                    'New:'.$n_partial.PHP_EOL;
                ob_flush();
            }
        }

        //Reducimos la temperatura con la constante de frío
         $temperature = $temperature * $cold_constant;
    }

    return $covering_code;
}

//Función que regenera el contenido de una bola con su nuevo centro
function regenerateGroupWords($covering_code, $it_group, $new_center_word, $r, $code)
{
    //Asignamos el nuevo centro al grupo y eliminamos sus palabras
    $covering_code[$it_group]['center'] = $new_center_word;
    $covering_code[$it_group]['words'] = [];

    //Regeneramos el contenido de la bola con el nuevo centro
    $new_group = fillGroupWords([$covering_code[$it_group]], $code, $r);
    $covering_code[$it_group] = reset($new_group);

    return $covering_code;
}

//Función que obtiene el número de apuestas de un conjunto que han quedado sin recubrir
function getUncoveringWords($covering_code, $code)
{
    $uncovering_words = [];
    foreach ($code as $word)
    {
        $exists = false;
        foreach ($covering_code as $covering_center)
        {
            if (in_array($word, $covering_center['words']) || $word == $covering_center['center'])
            {
                $exists = true;
                break;
            }
        }

        if (!$exists && !in_array($word, $uncovering_words))
            $uncovering_words[] = $word;
    }

    return $uncovering_words;
}

function getCoveredWords($covering_code)
{
    $covered_words = [];
    foreach ($covering_code as $group)
    {
        if (!in_array($group['center'], $covered_words))
            $covered_words[] = $group['center'];

        foreach ($group['words'] as $word)
        {
            if (!in_array($word, $covered_words))
                $covered_words[] = $word;
        }

    }
    return $covered_words;
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
    $groups = [];

    //Seleccionamos n centros aleatorios
    for ($i=0;$i<$n;$i++)
    {
        $random_int = array_rand($code);
        $center = $code[$random_int];
        $groups[] = ['center' => $center, 'words' => []];
    }

    //Rellenamos las bolas
    $groups = fillGroupWords($groups, $code, $r);

    //echo sizeof($code);
    return $groups;
}

//Función que rellena las bolas con las palabras a distancia r de cada centro
function fillGroupWords($groups, $code, $r)
{

    foreach ($groups as $it_group => $group)
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

            if ($distance != 0 && $distance <= $r)
                $groups[$it_group]['words'][] = $word;
        }
    }

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
function toStringCoveringCode($code, $conditioned_code)
{
    $str = '';
    $str .= '---- COVERING ----'.PHP_EOL;
    foreach ($code as $it => $data)
    {
        $str .= 'Group '.($it+1).' ('.(sizeof($data['words'])).')'.PHP_EOL;
        $str .= '---- Center: '.toStringWord($data['center']).PHP_EOL;
        $str .= '---- Words: '.toStringCode($data['words']).PHP_EOL;
    }

    return $str.PHP_EOL;
}

//Convierte a cadena de texto una palabra
function toStringWord($word)
{
    $str = '{'.implode(', ', $word).'}';
    return $str;
}

