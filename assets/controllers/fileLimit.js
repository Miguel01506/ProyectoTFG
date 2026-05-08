document.getElementById('filesInput').addEventListener('change', function () {
    const limite = 5;
    const mensaje = document.getElementById('mensaje');
    const btnPublicar = document.getElementById('btnPublicar');

    if (this.files.length > limite) {
        mensaje.innerHTML = "<b style='color:red;'>Has seleccionado " + this.files.length + " archivos. El máximo son 5.</b>";
        this.value = "";
        btnPublicar.disabled = true;
    } else {
        mensaje.style.color = "black";
        mensaje.innerText = "Has seleccionado " + this.files.length + " archivos.";
        btnPublicar.disabled = false;
    }
});