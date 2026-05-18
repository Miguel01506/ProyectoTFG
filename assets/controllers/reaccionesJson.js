document.addEventListener('click', function (e) {
    // el target es el elemento que se ha clicado
    // con closest buscamos el enlace más cercano, tipo si cklicamos en el icono
    // o texto cogerá el elemento de .enlace-reaccion, no el elemento pulsado literalmente
    const boton = e.target.closest('.enlace-reaccion');
    if (!boton) return;

    e.preventDefault();

    const url = boton.href; // pillamos la URL del enlace, que está en el atributo href
    const contenedor = boton.closest('.bloque-reacciones');  // aquí lo mismo con el contenedor, buscamos el bloque de reacciones

    // esto ya es la petición fetch
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // esto actualiza en número de likes y dislikes en tiempo real
                contenedor.querySelector('.likes-count').textContent = data.totalLikes;
                contenedor.querySelector('.dislikes-count').textContent = data.totalDislikes;
            } else {
                console.error('Error en la reacción: ', data.error);
                alert('Ha habido un error al procesar tu reacción. Intentalo de nuevio');
            }
        })
        .catch(error => {
            console.error('Error en la petición: ', error);
            alert('Ha habido un error de conexión. Intentalo de nuevo');
        });
});