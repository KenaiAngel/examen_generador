<?php
use EXAMEN\MYAPI\Profesor as Profesor;

include_once __DIR__ . '/../myapi/Profesor.php';


$resgistrarPregunta = new Profesor('examenes');
$resgistrarPregunta->modificarPregunta(json_decode(json_encode($_POST), false)); // false indica que se debe decodificar como objeto.
 
echo $resgistrarPregunta->getData();
?>
