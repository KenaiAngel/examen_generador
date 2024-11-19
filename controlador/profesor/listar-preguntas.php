<?php

    use EXAMEN\MYAPI\Profesor as Profesor;

    include_once __DIR__ . '/../myapi/Profesor.php';

    if( isset($_GET['correo']) ) {

        $listarPreguntas = new Profesor('examenes');
        
        $correo = $_GET['correo'];

        $listarPreguntas->listarPreguntas($correo);
        echo $listarPreguntas->getData();
    }

    
?>