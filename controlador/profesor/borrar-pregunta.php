<?php

    use EXAMEN\MYAPI\Profesor as Profesor;

    include_once __DIR__ . '/../myapi/Profesor.php';

    $borrarPregunta = new Profesor('examenes');
    if( isset($_POST['id']) ) {
        $id = $_POST['id'];
        $borrarPregunta->borrarPregunta($id);
        echo $borrarPregunta->getData();
    }
    else{
        $data = array(
            'status'  => 'error',
            'message' => 'EL dato ingresado es incorrecto'
        );
        echo json_encode($data, JSON_PRETTY_PRINT);
    }


?>