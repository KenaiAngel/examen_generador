$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);

    // Obtener el valor del parámetro 'correo'
    const correo = urlParams.get('correo');

    if (correo) {
        // Mostrar el correo en la página
        $('#correoUsuario').text(correo);
        $('#correo_id').hide(); // Ocultar elemento con jQuery

        function listarProfesor() {
            $.ajax({
                url: '../controlador/profesor/listar-preguntas.php',
                data: { correo: correo }, 
                type: 'GET',
                success: function(response) {
                   console.log(response);
                    // Convertir la respuesta JSON
                    const preguntas = JSON.parse(response);
                    
                    // Verificar si hay preguntas
                    if (preguntas.length > 0) {
                        let template = '';

                        preguntas.forEach(pregunta => {
                            // Crear cada fila de la tabla
                            template += `
                                <tr pregunta="${pregunta.id_pregunta}">
                                    <td>${pregunta.id_pregunta}</td>
                                    <td>${pregunta.nombre_area}</td>
                                    <td>${pregunta.definicion_operacional}</td>
                                    <td>${pregunta.base_reactivo}</td>
                                    <td>${pregunta.argumentacion}</td>
                                    <td>${pregunta.tipo_respuesta === "0" ? 'Opción múltiple' : 'Verdadero o falso'}</td>
                                    <td>
                                        <button class="btn editar">Editar</button>
                                        <button class="btn eliminar">Eliminar</button>
                                    </td>
                                </tr>
                            `;
                        });

                        // Insertar filas en la tabla
                        $('#preguntas_profesores').html(template);
                    } else {
                        $('#preguntas_profesores').html('<tr><td colspan="6">No hay preguntas disponibles</td></tr>');
                    }
  
                },
                error: function() {
                    console.error('Error al listar las preguntas.');
                    alert('No se pudieron cargar las preguntas. Intenta de nuevo.');
                }
            });
        }

        listarProfesor(); 

        $('#question-form').hide();

        console.log('Correo recibido:', correo);

        
        const correoUsuario = document.getElementById('correoUsuario');

        // Evento al hacer clic en "Agregar Preguntas"
        document.querySelector('a[href="#agregar-pregunta"]').addEventListener('click', function(event) {
            event.preventDefault(); 
            const correo = correoUsuario.textContent.trim(); // Obtener el correo

            if (correo) {
                // Redirigir a preguntas.html con el correo en la URL
                window.location.href = 'preguntas.html?correo=' + encodeURIComponent(correo);
            } else {
                alert("El correo no está disponible.");
            }
        });

        document.querySelector('a[href="profesor.html"]').addEventListener('click', function(event) {
            event.preventDefault(); 
            const correo = correoUsuario.textContent.trim(); // Obtener el correo

            if (correo) {
                // Redirigir a preguntas.html con el correo en la URL
                window.location.href = 'profesor.html?correo=' + encodeURIComponent(correo);
            } else {
                alert("El correo no está disponible.");
            }
        });

        $(document).on('click', '.eliminar', function(e) { 
            if (confirm('¿Realmente deseas eliminar la pregunta?')) {
                // Seleccionar la fila actual desde el botón clicado
                const element = $(this).closest('tr'); // Encuentra el <tr> más cercano al botón
                const id = element.attr('pregunta');  // Obtener el atributo 'pregunta' del <tr>
                
                // Realizar la petición AJAX para eliminar
                $.post('../controlador/profesor/borrar-pregunta.php', {id}, (response) => {
                    console.log("Delete");
                    console.log(response);
        
                    // Refrescar la lista de profesores
                    listarProfesor();
                });
            }
        });
        function consultarPregunta(idPregunta) {
            $('#question-form').show();
            $.ajax({
                url: '../controlador/profesor/consultar-pregunta.php', // Cambiar a la URL correcta
                data: { id: idPregunta },
                type: 'GET',
                success: function(response) {
                    console.log(response);
        
                    // Convertir la respuesta JSON
                    const data = JSON.parse(response);
        
                    if (data.status === "success") {
                        const pregunta = data.pregunta;
                        const respuestas = data.respuestas;
        
                        // Llenar los campos del formulario con los datos de la pregunta
                        $('#area').val(pregunta.nombre_area);
                        $('#definicion').val(pregunta.definicion_operacional);
                        $('#base').val(pregunta.base_reactivo);
                        $('#argumentacion').val(pregunta.argumentacion);
                        $('#respuestaTipo').val(pregunta.tipo_respuesta === "0" ? 'opcion-multiple' : 'verdadero-falso');
        
                        // Limpiar el contenedor de respuestas
                        $('#respuesta-container').empty();
        
                        // Insertar las respuestas en el contenedor
                        respuestas.forEach((respuesta, index) => {
                            const isCorrect = respuesta.es_correcta === "1" ? 'correcta' : '';
                            $('#respuesta-container').append(`
                                <div id="respuesta${index + 1}" class="response-options">
                                    <input type="text" id="respuesta${index + 1}-input" value="${respuesta.descripcion}" placeholder="Respuesta ${index + 1}">
                                    <button type="button" class="button marcar ${isCorrect}" onclick="setCorrectAnswer(${index + 1})">
                                        Marcar como correcta
                                    </button>
                                    <button type="button" class="button eliminar" onclick="removeAnswer(${index + 1})">
                                        Eliminar
                                    </button>
                                </div>
                            `);
                        });
                    } else {
                        alert(data.message || 'No se encontró la pregunta.');
                    }
                },
                error: function() {
                    console.error('Error al consultar la pregunta.');
                    alert('No se pudo cargar la pregunta. Intenta de nuevo.');
                }
            });
        }
        let idPreguntaGlobal = null; 

        // Evento para cargar la pregunta al formulario cuando se haga clic en "Editar"
        $(document).on('click', '.editar', function() {
            idPreguntaGlobal = $(this).closest('tr').attr('pregunta'); // Obtener ID de la pregunta

            consultarPregunta(idPreguntaGlobal);
        });

        $('#question-form').submit(e => {
            e.preventDefault(); // Previene el envío por defecto del formulario.
        
            // Obtenemos los valores del formulario
            const idPregunta = idPreguntaGlobal; // Usamos la variable global
            const area = $('#area').val();
            const definicion = $('#definicion').val();
            const base = $('#base').val();
            const argumentacion = $('#argumentacion').val();
            const respuestaTipo = $('#respuestaTipo').val(); // Opción múltiple o Verdadero/Falso
            const tipoRespuesta = respuestaTipo === "opcion-multiple" ? 0 : 1; // 0: Opción Múltiple, 1: Verdadero/Falso
        
            // Recuperar las respuestas y cuál es la correcta
            const respuestas = [];
            $('#respuesta-container input[type="text"]').each((index, input) => {
                const respuestaTexto = $(input).val().trim();
                if (respuestaTexto) {
                    const esCorrecta = input.style.backgroundColor === 'rgb(200, 230, 201)'; // Verifica si el fondo es verde
                    respuestas.push({
                        texto: respuestaTexto,
                        correcta: esCorrecta
                    });
                }
            });
        
            // Validar para opción múltiple
            if (respuestaTipo === "opcion-multiple" && respuestas.length > 1) {
                const correctAnswers = respuestas.filter(respuesta => respuesta.correcta);
                if (correctAnswers.length === respuestas.length) {
                    alert('Debe haber al menos una respuesta incorrecta.');
                    return;
                }
            }
        
            // Construimos el objeto de la pregunta
            const pregunta = {
                id: idPregunta, // ID de la pregunta a actualizar
                area,
                definicion,
                base,
                argumentacion,
                tipoRespuesta, // 0 o 1 según sea Opción Múltiple o Verdadero/Falso
                respuestas // Las respuestas con su validez
            };
        
            console.log(pregunta); // Ver datos antes de enviar
        
            // Realizar la llamada AJAX para actualizar
            $.ajax({
                url: '../controlador/profesor/actualizar-pregunta.php', // Cambiar a la ruta del archivo de actualización
                type: 'POST',
                data: {
                    id: pregunta.id,
                    area: pregunta.area,
                    definicion: pregunta.definicion,
                    base: pregunta.base,
                    argumentacion: pregunta.argumentacion,
                    tipoRespuesta: pregunta.tipoRespuesta,
                    respuestas: pregunta.respuestas, // Pasar las respuestas

                },
                success: function (response) {
                    console.log(response); // Manejar la respuesta del servidor
                    const msj = JSON.parse(response);
                    alert(msj.message);
        
                    if (msj.status === "success") {
                        // Opcional: Redirigir o actualizar la lista de preguntas
                        // window.location.href = 'dashboard.php';
                        $('#question-form')[0].reset(); // Limpia el formulario
                        $('#respuesta-container').html(`
                            <div id="respuesta1">
                                <input type="text" id="respuesta1-input" placeholder="Respuesta 1">
                                <button type="button" class="button" onclick="setCorrectAnswer(1)">Marcar como correcta</button>
                                <button type="button" class="button eliminar" onclick="removeAnswer(1)">Eliminar</button>
                            </div>
                            <div id="respuesta2">
                                <input type="text" id="respuesta2-input" placeholder="Respuesta 2">
                                <button type="button" class="button" onclick="setCorrectAnswer(2)">Marcar como correcta</button>
                                <button type="button" class="button eliminar" onclick="removeAnswer(2)">Eliminar</button>
                            </div>
                        `);
                        currentQuestionId = null; // Reinicia el ID de la pregunta
                    }
                    listarProfesor();
                    $('#question-form').hide(); // Ocultar elemento con jQuery
                },
                error: function () {
                    alert('Hubo un error al procesar la actualización.');
                }
            });
        });

        
    } else {
        console.log('No se recibió ningún correo.');
        alert('No se recibió el correo. Por favor, inicia sesión nuevamente.');
        window.location.href = 'login.html' ;
    }
});
