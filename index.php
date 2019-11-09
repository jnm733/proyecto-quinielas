<?php

//Variables iniciales
$characters = [0,1,2];
$variant = [2,3,4];
$x = [0,1,2];
$twos = [0,1,2];

$full_code = generateFullCode($characters);
$conditioned_code = generateConditionedCode($full_code, $variant, $x, $twos);

var_dump(sizeof($full_code), sizeof($conditioned_code));

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
function generateCodeGroups($n, $r, $code)
{
    $groups = [];

    for ($i=0;$i<$n;$i++)
    {

    }

    return $groups;
}
