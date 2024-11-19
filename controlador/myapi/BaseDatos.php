<?php
    namespace EXAMEN\MYAPI;

    abstract class BaseDatos{
        protected $conexion;

        public function __construct($db, $user, $pass)
        {
            $this->conexion = @mysqli_connect(
                'localhost',
                $user,
                $pass,
                $db,
            );

            if(!$this->conexion) {
                die('¡Base de datos NO conextada!');
            }
        }
    }


?>