<?php

use EXAMEN\MYAPI\Profesor as Profesor;


include_once __DIR__ . '/myapi/Profesor.php';


$ingresarSistema = new Profesor('examenes');
$ingresarSistema->loginGeneral(json_decode(json_encode($_POST))); 
echo $ingresarSistema->getData();
?>
