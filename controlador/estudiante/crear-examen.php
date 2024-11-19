<?php
use EXAMEN\MYAPI\Estudiante as Estudiante;


include_once __DIR__.'/../myapi/Estudiante.php';


$examenEstudiante = new Estudiante('examenes');
$examenEstudiante->crearExamen();
echo $examenEstudiante->getData();
?>
