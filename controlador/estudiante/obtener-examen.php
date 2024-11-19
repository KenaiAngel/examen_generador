

<?php

    use EXAMEN\MYAPI\Estudiante as Estudiante;

    include_once __DIR__ . '/../myapi/Estudiante.php';

    if( isset($_GET['idExamen']) ) {

        $examenEstudiante = new Estudiante('examenes');
        
        $idExamen = $_GET['idExamen'];

        $examenEstudiante->obtenerPreguntasConRespuestas($idExamen);
        echo $examenEstudiante->getData();
    }

    
?>
