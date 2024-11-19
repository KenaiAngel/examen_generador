$('#login-form').submit(e => {
    e.preventDefault(); // Previene el envío por defecto del formulario.

    const user = {
        email: document.getElementById('email').value.trim(),
        password: document.getElementById('password').value.trim()
    };


    if (user.email === '') {
        alert('El correo electrónico es obligatorio.');
        return;
    }

    // Validar formato del correo electrónico.
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if (!emailPattern.test(user.email)) {
        alert('Por favor ingresa un correo electrónico válido.');
        return;
    }

    if (user.password === '') {
        alert('La contraseña es obligatoria.');
        return;
    }

    if (user.password.length < 6) {
        alert('La contraseña debe tener al menos 6 caracteres.');
        return;
    }

    const url =  '../controlador/login.php';

    // Si todas las validaciones pasan, enviar el formulario por AJAX.
    $.ajax({
        url: url,
        type: 'POST',
        data: {
            email: user.email,
            password: user.password,
        },
        success: function (response) {
            //manejar la respuesta del servidor.
            console.log(response);
            const msj = JSON.parse(response);
            //Acceso Denegado, clave incorrecta
            //Correo no registrado
            
            if(msj.message==="Acceso Autorizado"){
                window.location.href = 'profesor.html?correo=' + encodeURIComponent(user.email);
            }
            else{
                alert(msj.message);
            }

        },
        error: function () {
            alert('Hubo un error al iniciar seccion.');
        }
    });
});
