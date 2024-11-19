# examen_generador
Instrucciones sql para phpmyadmin
CREATE TABLE estudiante (
    id_estudiante INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(25) NOT NULL,
    correo VARCHAR(50) NOT NULL UNIQUE, 
    clave VARCHAR(15) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE profesor (
    id_profesor INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(25) NOT NULL,
    correo VARCHAR(50) NOT NULL UNIQUE, 
    clave VARCHAR(15) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE area (
    id_area INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(70) NOT NULL,
    definicion_operacional VARCHAR(150) NOT NULL
);

CREATE TABLE pregunta (
    id_pregunta INT AUTO_INCREMENT PRIMARY KEY,
    definicion_operacional VARCHAR(200) NOT NULL,
    base_reactivo VARCHAR(200) NOT NULL,
    argumentacion VARCHAR(200) NOT NULL,
    id_profesor INT NOT NULL,
    id_area INT,
    CONSTRAINT fk_profesor FOREIGN KEY (id_profesor) REFERENCES profesor(id_profesor)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_area FOREIGN KEY (id_area) REFERENCES area(id_area)
        ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE TABLE respuesta (
    id_respuesta INT AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(200) NOT NULL,
    es_correcta BOOLEAN NOT NULL, # de tipo  BOOLEAN 0 es incorrecto y 1 correcto
    id_pregunta INT NOT NULL,
    CONSTRAINT fk_pregunta FOREIGN KEY (id_pregunta) REFERENCES pregunta(id_pregunta)
        ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE TABLE examen (
    id_examen INT AUTO_INCREMENT PRIMARY KEY,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE examen_pregunta (
    id_examen INT NOT NULL,
    id_pregunta INT NOT NULL,
    PRIMARY KEY (id_examen, id_pregunta),
    CONSTRAINT fk_examen FOREIGN KEY (id_examen) REFERENCES examen(id_examen)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_pregunta_intermedia FOREIGN KEY (id_pregunta) REFERENCES pregunta(id_pregunta)
        ON DELETE CASCADE ON UPDATE CASCADE
);



CREATE TABLE historial_examen (
    id_historial INT AUTO_INCREMENT PRIMARY KEY,
    id_examen INT NOT NULL,
    puntaje_obtenido DECIMAL(5,2) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_estudiante INT NOT NULL,
    CONSTRAINT fk_historial_examen FOREIGN KEY (id_examen) REFERENCES examen(id_examen)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_historial_estudiante FOREIGN KEY (id_estudiante) REFERENCES estudiante(id_estudiante)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE VIEW correos_combinados AS
SELECT correo FROM estudiante
UNION ALL
SELECT correo FROM profesor;

ALTER TABLE pregunta ADD tipo_respuesta BOOLEAN NOT NULL DEFAULT 0; # de tipo  BOOLEAN de respuesta 0 es de opción múltiple y 1 verdadero o falso
