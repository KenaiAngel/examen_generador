<?php
    use EXAMEN\MYAPI\Estudiante as Estudiante;

    include_once __DIR__.'/../myapi/Estudiante.php';

    $registrarEstudiante = new Estudiante('examenes');
    $registrarEstudiante ->resgistrarEstudiante(json_decode(json_encode($_POST)));
    echo $registrarEstudiante->getData();
?>
