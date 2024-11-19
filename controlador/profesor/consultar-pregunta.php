<?php

use EXAMEN\MYAPI\Profesor as Profesor;

include_once __DIR__ . '/../myapi/Profesor.php';

if (isset($_GET['id'])) {
    $idPregunta = $_GET['id']; // Obtener el id de la pregunta desde la URL

    // Crear una instancia de la clase Profesor
    $consultaPregunta = new Profesor('examenes');

    // Consultar los datos de la pregunta usando el id
    $consultaPregunta->consultarPregunta($idPregunta);

    // Devolver los datos en formato JSON
    echo $consultaPregunta->getData();
} else {
    // Si no se pasa el ID, retornar un error
    echo json_encode([
        'status' => 'error',
        'message' => 'Falta el parámetro de ID de la pregunta.'
    ]);
}

?>
