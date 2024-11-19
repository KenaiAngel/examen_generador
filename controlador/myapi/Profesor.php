<?php
    namespace EXAMEN\MYAPI;
    use EXAMEN\MYAPI\BaseDatos as BaseDatos;
    require_once __DIR__. '/BaseDatos.php';

    class Profesor extends BaseDatos {
        private $data;

        public function __construct($db, $user = 'root', $pass = 'sapo123'){
            //$this->conexion = new DataBase($user, $pass, $db);
            $this->data = array();
            parent::__construct($db, $user, $pass);
        }

        public function getData(){
            // SE HACE LA CONVERSIÓN DE ARRAY A JSON
            $jsonData = json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $this->data = NULL;
            return $jsonData;
        }
        

        public function registrarProfesor ($profesor){
            $this->data = array(
                'status'  => 'error',
                'message' => 'Ya existe un profesor con ese correo'
            );
            $existeProfesor = $this->conexion->query("SELECT EXISTS(SELECT 1 FROM correos_combinados WHERE correo = '$profesor->email') AS existe");
        
            // Verificar si la consulta devuelve un resultado
            if ($existeProfesor) {
                $row = $existeProfesor->fetch_assoc();
                $exists = $row['existe']; // Esto será 1 si existe o 0 si no
        
                $this->conexion->set_charset('utf8mb4');
                
                
                // Si el producto no existe, proceder con la inserción
                if (!$exists) {
                    // Preparar y ejecutar la consulta de inserción
                    $sql = "INSERT INTO profesor (nombre, correo, clave) 
                            VALUES ('{$profesor->username}', '{$profesor->email}', '{$profesor->password}')";
        
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

        public function loginGeneral($usuario) {
            $this->data = array(
                'status'  => 'error',
                'message' => 'El usuario no está registrado'
            );
        
            // Consulta para verificar si el correo existe 
            $existeUsuario = $this->conexion->query("SELECT EXISTS(SELECT 1 FROM correos_combinados WHERE correo = '$usuario->email') AS existe");
        
    
            if ($existeUsuario) {
                $row = $existeUsuario->fetch_assoc();
                $exists = $row['existe']; // Esto será 1 si existe o 0 si no
        
                $this->conexion->set_charset('utf8mb4');
        
                if ($exists) {
                    // Verificar la contraseña en las tablas "estudiante" y "profesor"
                    // Intentamos primero en la tabla "estudiante"
                    $sql = "SELECT clave FROM estudiante WHERE correo = '$usuario->email'";
                    $resultEstudiante = $this->conexion->query($sql);
        
                    if ($resultEstudiante && $resultEstudiante->num_rows > 0) {
                        $row = $resultEstudiante->fetch_assoc();
                        $storedPassword = $row['clave'];
        
                        if ($usuario->password == $storedPassword) {
                            $this->data['status'] = "success";
                            $this->data['message'] = "Acceso Autorizado";
                        } else {
                            $this->data['message'] = "Acceso Denegado, clave incorrecta";
                        }

                    } else {
                        // Si no se encuentra en "estudiante", intentar en "profesor"
                        $sql = "SELECT clave FROM profesor WHERE correo = '$usuario->email'";
                        $resultProfesor = $this->conexion->query($sql);
        
                        if ($resultProfesor && $resultProfesor->num_rows > 0) {
                            $row = $resultProfesor->fetch_assoc();
                            $storedPassword = $row['clave'];
        
                            if ($usuario->password == $storedPassword) {
                                $this->data['status'] = "success";
                                $this->data['message'] = "Acceso Autorizado";
                            } else {
                                $this->data['message'] = "Acceso Denegado, clave incorrecta";
                            }
                        } else {
                            // Si el correo no existe ni en "estudiante" ni en "profesor"
                            $this->data['message'] = "Correo no registrado";
                        }
                    }
                } else {
                    $this->data['message'] = "Acceso Denegado, correo no registrado";
                }
            }
        
            // Cerrar la conexión
            $this->conexion->close();
        }

        public function getProfesorID($correo){
            $this->data = array(
                'status'  => 'error',
                'message' => 'Hubo un error inesperado'
            );
        

            $resultado = $this->conexion->query("SELECT id_profesor FROM profesor WHERE correo = '$correo'");
        
            // Verificar si se obtuvo un resultado
            if ($resultado && $resultado->num_rows > 0) {
                $fila = $resultado->fetch_assoc();
                $idProfesor = (int)$fila['id_profesor']; // Convertir a número
                $this->data = array(
                    'status'  => 'success',
                    'message' => 'ID encontrado',
                    //'id'      => $idProfesor
                );
                return $idProfesor;
            } else {
                $this->data['message'] = 'No se encontró ningún profesor con ese correo';
                return null;
            }
        
            // Cerrar la conexión
            $this->conexion->close();
        }
        

        public function listarPreguntas($correo) {
            $idProfesor = new Profesor('examenes');
            $id_profesor = $idProfesor->getProfesorID($correo);
            
            $this->conexion->set_charset('utf8mb4');

            if ($id_profesor) {
                // Consulta con JOIN para obtener el nombre del área junto con las preguntas
                $query = "
                    SELECT p.*, a.nombre AS nombre_area
                    FROM pregunta p
                    LEFT JOIN area a ON p.id_area = a.id_area
                    WHERE p.id_profesor = '$id_profesor'
                ";
        
                if ($result = $this->conexion->query($query)) {
                    // SE OBTIENEN LOS RESULTADOS
                    $rows = $result->fetch_all(MYSQLI_ASSOC);
        
                    if (!is_null($rows)) {
                        // SE CODIFICAN A UTF-8 LOS DATOS Y SE MAPEAN AL ARREGLO DE RESPUESTA
                        foreach ($rows as $num => $row) {
                            foreach ($row as $key => $value) {
                                $this->data[$num][$key] = ($value);
                            }
                        }
                    }
                    $result->free();
                } else {
                    die('Query Error: ' . mysqli_error($this->conexion));
                }
            }
        
            $this->conexion->close();
        }

        /****************************************** */

        public function registrarPregunta($pregunta) {
            $this->data = array(
                'status'  => 'error',
                'message' => 'Error inesperado'
            );
            $error = false;
        
            // Obtener el ID del profesor
            $idProfesor = new Profesor('examenes');
            $id_profesor = $idProfesor->getProfesorID($pregunta->email);
        
            // Verificar si el área existe (ignorando mayúsculas y minúsculas)
            $existeArea = $this->conexion->query("SELECT id_area FROM area WHERE LOWER(nombre) = LOWER('$pregunta->area')");
            $id_area = null;
        
            if ($existeArea && $existeArea->num_rows > 0) {
                // Si el área ya existe, obtener su ID
                $row = $existeArea->fetch_assoc();
                $id_area = $row['id_area'];
            } else {
                // Si el área no existe, insertarla
                $sqlInsertArea = "INSERT INTO area (nombre, definicion_operacional) 
                                  VALUES ('{$pregunta->area}', '{$pregunta->definicion}')";
        
                if ($this->conexion->query($sqlInsertArea)) {
                    $id_area = $this->conexion->insert_id; // Obtener el ID del área recién creada
                } else {
                    $this->data['message'] = "ERROR: No se pudo insertar el área. " . $this->conexion->error;
                    $error = true;
                }
            }
        
            if (!$error && $id_area) {
                // Insertar la pregunta
                $sqlInsertPregunta = "INSERT INTO pregunta (definicion_operacional, base_reactivo, argumentacion, id_profesor, id_area, tipo_respuesta) 
                                      VALUES ('{$pregunta->definicion}', '{$pregunta->base}', '{$pregunta->argumentacion}', '$id_profesor', '$id_area', '{$pregunta->tipoRespuesta}')";
        
                if ($this->conexion->query($sqlInsertPregunta)) {
                    $id_pregunta = $this->conexion->insert_id; // Obtener el ID de la pregunta recién creada
        
                    // Insertar las respuestas

                    
                    foreach ($pregunta->respuestas as $respuesta) {
                        $descripcion = $respuesta->texto; // Acceso con -> porque es un objeto
                        $es_correcta = filter_var($respuesta->correcta, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

                    
                        $sqlInsertRespuesta = "INSERT INTO respuesta (descripcion, es_correcta, id_pregunta) 
                                               VALUES ('$descripcion', '$es_correcta', '$id_pregunta')";
                    
                        if (!$this->conexion->query($sqlInsertRespuesta)) {
                            $this->data['message'] = "ERROR: No se pudo insertar una respuesta. " . $this->conexion->error;
                            return $this->data;
                        }
                    }
                    


                    if (!$error) {
                        $this->data['status'] = "success";
                        $this->data['message'] = "Pregunta y respuestas registradas con éxito.";
                    }
                } else {
                    $this->data['message'] = "ERROR: No se pudo insertar la pregunta. " . $this->conexion->error;
                    $error = true;
                }
            }
            // Cerrar la conexión
            $this->conexion->close();
        
        }
        public function modificarPregunta($pregunta) {
            $this->data = array(
                'status'  => 'error',
                'message' => 'Error inesperado'
            );
            $error = false;
        
            // Verificar si el área existe (ignorando mayúsculas y minúsculas)
            $existeArea = $this->conexion->query("SELECT id_area FROM area WHERE LOWER(nombre) = LOWER('$pregunta->area')");
            $id_area = null;
        
            if ($existeArea && $existeArea->num_rows > 0) {
                // Si el área ya existe, obtener su ID
                $row = $existeArea->fetch_assoc();
                $id_area = $row['id_area'];
            } else {
                // Si el área no existe, insertarla
                $sqlInsertArea = "INSERT INTO area (nombre, definicion_operacional) 
                                  VALUES ('{$pregunta->area}', '{$pregunta->definicion}')";
        
                if ($this->conexion->query($sqlInsertArea)) {
                    $id_area = $this->conexion->insert_id; // Obtener el ID del área recién creada
                } else {
                    $this->data['message'] = "ERROR: No se pudo insertar el área. " . $this->conexion->error;
                    $error = true;
                }
            }
        
            if (!$error && $id_area) {
                // Actualizar la pregunta existente
                $sqlUpdatePregunta = "UPDATE pregunta 
                                      SET definicion_operacional = '{$pregunta->definicion}', 
                                          base_reactivo = '{$pregunta->base}', 
                                          argumentacion = '{$pregunta->argumentacion}', 
                                          id_area = '$id_area', 
                                          tipo_respuesta = '{$pregunta->tipoRespuesta}' 
                                      WHERE id_pregunta = '{$pregunta->id}'";
        
                if ($this->conexion->query($sqlUpdatePregunta)) {
                    // Eliminar las respuestas antiguas
                    $sqlDeleteRespuestas = "DELETE FROM respuesta WHERE id_pregunta = '{$pregunta->id}'";
                    if ($this->conexion->query($sqlDeleteRespuestas)) {
                        // Insertar las nuevas respuestas
                        foreach ($pregunta->respuestas as $respuesta) {
                            $descripcion = $respuesta->texto; // Acceso con -> porque es un objeto
                            $es_correcta = filter_var($respuesta->correcta, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        
                            $sqlInsertRespuesta = "INSERT INTO respuesta (descripcion, es_correcta, id_pregunta) 
                                                   VALUES ('$descripcion', '$es_correcta', '{$pregunta->id}')";
        
                            if (!$this->conexion->query($sqlInsertRespuesta)) {
                                $this->data['message'] = "ERROR: No se pudo insertar una respuesta. " . $this->conexion->error;
                                return $this->data;
                            }
                        }
        
                        $this->data['status'] = "success";
                        $this->data['message'] = "Pregunta y respuestas actualizadas con éxito.";
                    } else {
                        $this->data['message'] = "ERROR: No se pudo eliminar las respuestas antiguas. " . $this->conexion->error;
                    }
                } else {
                    $this->data['message'] = "ERROR: No se pudo actualizar la pregunta. " . $this->conexion->error;
                }
            }
        
            // Cerrar la conexión
            $this->conexion->close();
        
        }
        

        public function borrarPregunta ($id){
            $this->data = array(
                'status'  => 'error',
                'message' => 'La consulta falló'
            );

            // SE REALIZA LA QUERY DE BÚSQUEDA Y AL MISMO TIEMPO SE VALIDA SI HUBO RESULTADOS
            $sql = "DELETE FROM pregunta WHERE id_pregunta = '{$id}' ";
            if ( $this->conexion->query($sql) ) {
                $this->data['status'] =  "success";
                $this->data['message'] =  "Pregunta eliminado";
            } else {
                $data['message'] = "ERROR: No se ejecuto $sql. " . mysqli_error($this->conexion);
            }
            $this->conexion->close();
        }

        public function consultarPregunta($id_pregunta) {
            $this->data = array(
                'status' => 'error',
                'message' => 'No se encontró la pregunta con el ID proporcionado.'
            );
        
            // Validar el ID de la pregunta
            if (empty($id_pregunta) || !is_numeric($id_pregunta)) {
                return $this->data;
            }
        
            $this->conexion->set_charset('utf8mb4');
        
            // Consultar los datos de la pregunta y el área
            $sqlPregunta = "
                SELECT 
                    p.id_pregunta,
                    p.definicion_operacional,
                    p.base_reactivo,
                    p.argumentacion,
                    p.tipo_respuesta,
                    a.nombre AS nombre_area
                FROM 
                    pregunta p
                INNER JOIN 
                    area a ON p.id_area = a.id_area
                WHERE 
                    p.id_pregunta = $id_pregunta
            ";
        
            $resultadoPregunta = $this->conexion->query($sqlPregunta);
        
            if ($resultadoPregunta && $resultadoPregunta->num_rows > 0) {
                $pregunta = $resultadoPregunta->fetch_assoc();
        
                // Consultar las respuestas asociadas
                $sqlRespuestas = "
                    SELECT 
                        id_respuesta,
                        descripcion,
                        es_correcta
                    FROM 
                        respuesta
                    WHERE 
                        id_pregunta = $id_pregunta
                ";
        
                $resultadoRespuestas = $this->conexion->query($sqlRespuestas);
        
                $respuestas = [];
                if ($resultadoRespuestas && $resultadoRespuestas->num_rows > 0) {
                    while ($respuesta = $resultadoRespuestas->fetch_assoc()) {
                        $respuestas[] = $respuesta;
                    }
                }
        
                // Consolidar los datos
                $this->data['status'] = "success";
                $this->data['pregunta'] = $pregunta;
                $this->data['respuestas'] = $respuestas;
        
            } else {
                // Si no se encontró la pregunta, mantener el status y mensaje de error predeterminado
                $this->data['message'] = 'No se encontró la pregunta con el ID proporcionado.';
            }
        
            // Cerrar la conexión
            $this->conexion->close();
        
        }
        


    }

?>