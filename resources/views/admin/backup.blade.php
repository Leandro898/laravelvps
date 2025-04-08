<!-- resources/views/admin/backup.blade.php -->
<button onclick="generarBackup()">Generar Backup</button>
<div id="mensaje" style="margin-top: 1em;"></div>

<script>
function generarBackup() {
    const mensaje = document.getElementById('mensaje');
    mensaje.innerHTML = 'Generando backup... ⏳';

    fetch('/backup/create')
        .then(res => res.json())
        .then(data => {
            if (data.download_url) {
                mensaje.innerHTML = `
                    <p>✅ Backup generado correctamente.</p>
                    <a href="${data.download_url}" class="btn-descargar" download>
                        Descargar Backup
                    </a>
                `;
            } else {
                mensaje.innerText = '❌ Error al generar el backup.';
            }
        })
        .catch(err => {
            mensaje.innerText = '❌ Error al conectarse con el servidor.';
            console.error(err);
        });
}
</script>

<style>
.btn-descargar {
    display: inline-block;
    margin-top: 10px;
    padding: 10px 15px;
    background-color: #4CAF50;
    color: white;
    text-decoration: none;
    font-weight: bold;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}
.btn-descargar:hover {
    background-color: #45a049;
}
</style>
