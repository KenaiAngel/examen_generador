
$(document).ready(function() {
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
    
    const urlParams = new URLSearchParams(window.location.search);

    // Obtener el valor del parámetro 'correo'
    const correo = urlParams.get('correo');

    if (correo) {
        // Mostrar el correo en la página
        $('#correoUsuario').text(correo);
        $('#correo_id').hide(); // Ocultar elemento con jQuery

 
        $('#question-form').submit(e => {
            e.preventDefault(); // Previene el envío por defecto del formulario.
            
            // Obtenemos los valores del formulario
            const area = $('#area').val();
            const definicion = $('#definicion').val();
            const base = $('#base').val();
            const argumentacion = $('#argumentacion').val();
            const respuestaTipo = $('#respuestaTipo').val(); // Opción múltiple o Verdadero/Falso
            let tipoRespuesta = respuestaTipo === "opcion-multiple" ? 0 : 1; // 0: Opción Múltiple, 1: Verdadero/Falso
            
            // Recuperar las respuestas y cuál es la correcta
            const respuestas = [];
            let respuestaCorrecta = null;
            
            $('#respuesta-container input[type="text"]').each((index, input) => {
                // Recuperar el texto de la respuesta
                const respuestaTexto = $(input).val().trim();
                if (respuestaTexto) {
                    // Si el input tiene algún valor
                    const esCorrecta = input.style.backgroundColor === 'rgb(200, 230, 201)'; // Verifica si el fondo es verde
                    respuestas.push({
                        texto: respuestaTexto,
                        correcta: esCorrecta
                    });
            
                    if (esCorrecta) {
                        respuestaCorrecta = respuestaTexto; // Guarda la respuesta correcta
                    }
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
        
            // Mostrar los datos en consola (o en otro lugar según sea necesario)
            const pregunta = {
                email: correo, // Suponiendo que tienes un campo de correo
                area,
                definicion,
                base,
                argumentacion,
                tipoRespuesta, // 0 o 1 según sea Opción Múltiple o Verdadero/Falso
                respuestas, // Las respuestas con su validez
                respuestaCorrecta // La respuesta correcta
            };

            console.log(pregunta);

            $.ajax({
                url: '../controlador/profesor/agregar-pregunta.php' ,
                type: 'POST',
                data: {
                    email: pregunta.email, // Suponiendo que tienes un campo de correo
                    area: pregunta.area,
                    definicion: pregunta.definicion,
                    base: pregunta.base,
                    argumentacion: pregunta.argumentacion,
                    tipoRespuesta: pregunta.tipoRespuesta, // 0 o 1 según sea Opción Múltiple o Verdadero/Falso
                    respuestas: pregunta.respuestas, // Las respuestas con su validez
                },
                success: function (response) {
                    //manejar la respuesta del servidor.
                    console.log(response);
                    const msj = JSON.parse(response);
                    alert(msj.message);
                    //Redirigir a otra página.
                    // window.location.href = 'dashboard.php';
                },
                error: function () {
                    alert('Hubo un error al procesar el registro.');
                }
            });

        
            // Limpiar el formulario
            $('#question-form')[0].reset(); // Limpiar el formulario
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
            
            // Reestablecer el contador de respuestas
            answerCount = 2;
            updateDeleteButtons(); 
        });
        
        

        console.log('Correo recibido:', correo);

    } else {
        console.log('No se recibió ningún correo.');
        alert('No se recibió el correo. Por favor, inicia sesión nuevamente.');
    }
});
