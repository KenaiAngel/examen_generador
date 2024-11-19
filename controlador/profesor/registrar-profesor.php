<?php
use EXAMEN\MYAPI\Profesor as Profesor;

include_once __DIR__ . '/../myapi/Profesor.php';


// Crear una instancia de la clase Profesor y usar sus métodos
$resgistrarProfesor = new Profesor('examenes');
$resgistrarProfesor->registrarProfesor(json_decode(json_encode($_POST))); 
echo $resgistrarProfesor->getData();
?>
