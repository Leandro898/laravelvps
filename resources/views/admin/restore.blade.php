<h2>Restaurar copia de seguridad</h2>

<form action="/admin/restore" method="POST" enctype="multipart/form-data">
    @csrf
    <label for="backup_zip">Selecciona archivo .zip de respaldo:</label><br><br>
    <input type="file" name="backup_zip" accept=".zip" required><br><br>
    <button type="submit">Restaurar</button>
</form>

@if (session('success'))
    <p style="color:green;">✅ {{ session('success') }}</p>
@endif

@if (session('error'))
    <p style="color:red;">❌ {{ session('error') }}</p>
@endif
