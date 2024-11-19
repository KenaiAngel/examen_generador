<?php
use EXAMEN\MYAPI\Profesor as Profesor;

include_once __DIR__ . '/../myapi/Profesor.php';


$resgistrarPregunta = new Profesor('examenes');
$resgistrarPregunta-> registrarPregunta(json_decode(json_encode($_POST))); 
echo $resgistrarPregunta->getData();
?>
