<?php
    namespace EXAMEN\MYAPI;
    use EXAMEN\MYAPI\BaseDatos as BaseDatos;
    require_once __DIR__. '/BaseDatos.php';

    class Estudiante extends BaseDatos {
        private $data;

        public function __construct($db, $user = 'root', $pass = 'sapo123'){
            //$this->conexion = new DataBase($user, $pass, $db);
            $this->data = array();
            parent::__construct($db, $user, $pass);
        }

        public function getData(){
            // SE HACE LA CONVERSIÓN DE ARRAY A JSON
            // SE HACE LA CONVERSIÓN DE ARRAY A JSON
            $jsonData = json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $this->data = NULL;
            return $jsonData;
            
        }

        public function resgistrarEstudiante ($estudiante){
            $this->data = array(
                'status'  => 'error',
                'message' => 'Ya existe un alumno con ese correo'
            );
            $existeEstudiante = $this->conexion->query("SELECT EXISTS(SELECT 1 FROM correos_combinados WHERE correo = '$estudiante->email') AS existe");
        
            // Verificar si la consulta devuelve un resultado
            if ($existeEstudiante) {
                $row = $existeEstudiante->fetch_assoc();
                $exists = $row['existe']; // Esto será 1 si existe o 0 si no
        
                $this->conexion->set_charset("utf8");
        
                // Si el producto no existe, proceder con la inserción
                if (!$exists) {
                    // Preparar y ejecutar la consulta de inserción
                    $sql = "INSERT INTO estudiante (nombre, correo, clave) 
                            VALUES ('{$estudiante->username}', '{$estudiante->email}', '{$estudiante->password}')";
        
                    if ($this->conexion->query($sql)) {
                        $this->data['status'] = "success";
                        $this->data['message'] = "Alumno agregado";
                    } else {
                        $this->data['message'] = "ERROR: No se ejecutó $sql. " . mysqli_error($this->conexion);
                    }
                } 
            }
        
            // Cerrar la conexión
            $this->conexion->close();
        
        }

        public function crearExamen() {
            $this->data = array(
                'status'  => 'error',
                'message' => 'No se pudo crear el examen'
            );
        
            // Insertar el examen
            $fecha = date('Y-m-d'); // Fecha actual
            $sqlInsertExamen = "INSERT INTO examen(fecha) VALUES ('$fecha')";
            if ($this->conexion->query($sqlInsertExamen)) {
                $idExamen = $this->conexion->insert_id; // Obtener el ID del examen insertado
        
                // Seleccionar 5 preguntas aleatorias de la vista
                $sqlSelectPreguntas = "SELECT DISTINCT id_pregunta 
                                       FROM vista_pregunta_respuesta 
                                       ORDER BY RAND() 
                                       LIMIT 5";
                $resultado = $this->conexion->query($sqlSelectPreguntas);
        
                if ($resultado->num_rows > 0) {
                    // Insertar cada pregunta en la tabla examen_pregunta
                    while ($fila = $resultado->fetch_assoc()) {
                        $idPregunta = $fila['id_pregunta'];
                        $sqlInsertExamenPregunta = "INSERT INTO examen_pregunta(id_examen, id_pregunta) 
                                                    VALUES ($idExamen, $idPregunta)";
                        $this->conexion->query($sqlInsertExamenPregunta);
                    }
        
                    // Cambiar el estado a éxito
                    $this->data = array(
                        'status'  => 'success',
                        'message' => 'Examen creado con éxito',
                        'idExamen' => $idExamen
                    );
                }
            }
        
            // Cerrar la conexión
            $this->conexion->close();
        }

        public function obtenerPreguntasConRespuestas($idExamen) {
            $this->data = array(
                'status'  => 'error',
                'message' => 'No se pudieron obtener las preguntas y respuestas'
            );
        
            $this->conexion->set_charset('utf8mb4');
        
            // Consultar las preguntas asociadas al examen
            $sqlPreguntas = "SELECT id_pregunta 
                             FROM examen_pregunta 
                             WHERE id_examen = $idExamen";
        
            $resultadoPreguntas = $this->conexion->query($sqlPreguntas);
        
            if ($resultadoPreguntas && $resultadoPreguntas->num_rows > 0) {
                $preguntas = array();
        
                // Recorrer las preguntas asociadas
                while ($filaPregunta = $resultadoPreguntas->fetch_assoc()) {
                    $idPregunta = $filaPregunta['id_pregunta'];
        
                    // Consultar las respuestas asociadas a la pregunta
                    $sqlRespuestas = "SELECT id_pregunta, base_reactivo, tipo_respuesta, id_respuesta, descripcion, es_correcta, argumentacion
                                      FROM vista_pregunta_respuesta 
                                      WHERE id_pregunta = $idPregunta";
        
                    $resultadoRespuestas = $this->conexion->query($sqlRespuestas);
        
                    if ($resultadoRespuestas && $resultadoRespuestas->num_rows > 0) {
                        $respuestas = array();
                        $base_reactivo = null;
                        $tipo_respuesta = null;
        
                        while ($filaRespuesta = $resultadoRespuestas->fetch_assoc()) {
                            // Guardar base_reactivo y tipo_respuesta solo una vez
                            if ($base_reactivo === null && $tipo_respuesta === null) {
                                $base_reactivo = $filaRespuesta['base_reactivo'];
                                $argumentacion = $filaRespuesta['argumentacion'];
                                $tipo_respuesta = $filaRespuesta['tipo_respuesta'];
                            }
        
                            $respuestas[] = array(
                                'id_respuesta'  => $filaRespuesta['id_respuesta'],
                                'descripcion'   => $filaRespuesta['descripcion'],
                                'es_correcta'   => $filaRespuesta['es_correcta']
                            );
                        }
        
                        // Agregar la pregunta con sus respuestas
                        $preguntas[] = array(
                            'id_pregunta'    => $idPregunta,
                            'base_reactivo'  => $base_reactivo,
                            'argumentacion' => $argumentacion,
                            'tipo_respuesta' => $tipo_respuesta,
                            'respuestas'     => $respuestas
                        );
                    }
                }
        
                // Cambiar el estado a éxito
                $this->data = array(
                    'status'  => 'success',
                    'message' => 'Preguntas y respuestas obtenidas con éxito',
                    'preguntas' => $preguntas
                );
            }
        
            // Cerrar la conexión
            $this->conexion->close();
        }
        
        
        
    
    }
?>