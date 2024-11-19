$(document).ready(function () {
    $('#contenedor-examen').hide();
    const urlParams = new URLSearchParams(window.location.search);

    // Obtener el valor del parámetro 'correo'
    const correo = urlParams.get('correo');

    if (correo) {
        // Mostrar el correo en la página
        $('#correoUsuario').text(correo);
        $('#correo_id').hide(); // Ocultar elemento con jQuery

        $('#empezarExamen').click(function () {
            $('#historial').hide();
            $('#contenedor-examen').show();
            // Llamar a crear-examen.php para iniciar el examen
            
            $.post('../controlador/estudiante/crear-examen.php', function (response) {
                console.log(response);
                if (response.status === 'success') {
                    const idExamen = response.idExamen;
                    console.log(response);
                    $.ajax({
                        url: '../controlador/estudiante/obtener-examen.php',
                        data: { idExamen },
                        type: 'GET',
                        success: function (response) {
                            // Convertir el response a JSON si es necesario
                            let examen = JSON.parse(response);
                    
                            // Seleccionar el contenedor donde irá el examen
                            let contenedorExamen = $('#contenedor-examen');
                            contenedorExamen.empty(); // Limpiar contenido previo
                    
                            // Iterar sobre las preguntas
                            examen.preguntas.forEach(pregunta => {
                                // Crear el contenedor de la pregunta
                                let divPregunta = $('<div>').addClass('pregunta').attr('data-id-pregunta', pregunta.id_pregunta);
                                let tituloPregunta = $('<h2>').text(pregunta.base_reactivo);
                                divPregunta.append(tituloPregunta);
                    
                                // Agregar argumentación (oculto inicialmente)
                                let justificacion = $('<h3>')
                                    .text("Argumentación")
                                    .css('display', 'none'); // Ocultar inicialmente
                                let argumen = $('<p>')
                                    .text(pregunta.argumentacion)
                                    .css('display', 'none'); // Ocultar inicialmente
                                divPregunta.append(justificacion);
                                divPregunta.append(argumen);
                    
                                // Iterar sobre las respuestas de la pregunta
                                pregunta.respuestas.forEach(respuesta => {
                                    let opcionRespuesta = $('<div>').addClass('respuesta');
                                    let input = $('<input>')
                                        .attr('type', 'radio')
                                        .attr('name', `pregunta_${pregunta.id_pregunta}`)
                                        .attr('value', respuesta.id_respuesta)
                                        .data('es-correcta', respuesta.es_correcta === "1"); // Guardar si es correcta
                                    let label = $('<label>').text(respuesta.descripcion);
                    
                                    // Agregar el input y el label al contenedor de la respuesta
                                    opcionRespuesta.append(input).append(label);
                                    divPregunta.append(opcionRespuesta);
                                });
                    
                                // Agregar la pregunta completa al contenedor del examen
                                contenedorExamen.append(divPregunta);
                            });
                    
                            // Agregar el botón de enviar examen
                            let botonEnviar = $('<button>')
                                .attr('id', 'btn-enviar')
                                .text('Enviar Examen')
                                .css({ display: 'none' }) // Ocultarlo inicialmente
                                .on('click', function () {
                                    verificarRespuestas(examen, botonEnviar);
                                });
                            contenedorExamen.append(botonEnviar);
                    
                            // Mostrar el botón si todas las preguntas están respondidas
                            contenedorExamen.on('change', 'input[type="radio"]', function () {
                                let totalPreguntas = examen.preguntas.length;
                                let respondidas = contenedorExamen.find('input[type="radio"]:checked').length;
                                if (respondidas === totalPreguntas) {
                                    botonEnviar.css({ display: 'block' });
                                } else {
                                    botonEnviar.css({ display: 'none' });
                                }
                            });
                        },
                        error: function (error) {
                            console.error("Error al obtener el examen:", error);
                        }
                    });
                    
                    // Función para verificar las respuestas
                    function verificarRespuestas(examen, botonEnviar) {
                        let totalCorrectas = 0;
                        let totalIncorrectas = 0;
                    
                        $('#contenedor-examen .pregunta').each(function () {
                            let idPregunta = $(this).attr('data-id-pregunta');
                            let respuestaSeleccionada = $(this).find('input[type="radio"]:checked');
                            let esCorrecta = respuestaSeleccionada.data('es-correcta');
                    
                            if (esCorrecta) {
                                totalCorrectas++;
                                $(this).css('background-color', '#d4edda'); // Verde claro para correcto
                            } else {
                                totalIncorrectas++;
                                $(this).css('background-color', '#f8d7da'); // Rojo claro para incorrecto
                                $(this).find('h3, p').css('display', 'block'); // Mostrar justificación
                            }
                        });
                    
                        // Cambiar el botón a "Regresar"
                        botonEnviar
                            .text('Regresar')
                            .off('click') // Quitar el evento actual
                            .on('click', function () {
                                $('#historial').show();
                                $('#contenedor-examen').hide();
                            });
                    
                        // Mostrar resultados
                        alert(`Resultados:${(totalCorrectas/5)*100}\nCorrectas: ${totalCorrectas}\nIncorrectas: ${totalIncorrectas}`);
                    }
                    
                } else {
                    alert('Error al crear el examen: ' + response.message);
                }
            }, 'json');
        });
    } else {
        console.log('No se recibió ningún correo.');
        alert('No se recibió el correo. Por favor, inicia sesión nuevamente.');
        window.location.href = 'login.html' ;
    }
});
