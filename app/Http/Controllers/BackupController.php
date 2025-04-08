<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use ZipArchive;

class BackupController extends Controller
{
    // ✅ Crear backup y devolver link de descarga
    public function create()
    {
        Artisan::call('backup:run');

        $files = Storage::disk('local')->files('Laravel');
        $lastBackup = collect($files)->sortDesc()->first();

        if ($lastBackup && Storage::disk('local')->exists($lastBackup)) {
            $url = route('backup.download', ['file' => basename($lastBackup)]);
            return response()->json([
                'message' => 'Backup creado exitosamente.',
                'download_url' => $url
            ]);
        }

        return response()->json(['error' => 'No se pudo crear el backup.'], 500);
    }

    // ✅ Descargar backup
    public function download($file)
    {
        $path = 'Laravel/' . $file;

        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->download($path);
        }

        return abort(404);
    }

    // ✅ Mostrar formulario para restaurar backup
    public function showRestoreForm()
    {
        return view('admin.restore');
    }

    // ✅ Procesar archivo ZIP y restaurar
    public function processRestore(Request $request)
    {
        $request->validate([
            'backup_zip' => 'required|file|mimes:zip',
        ]);

        $zipFile = $request->file('backup_zip');
        $fileName = 'restore_' . time() . '.zip';
        $path = storage_path('app/restore');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $zipFile->move($path, $fileName);

        // Extraer ZIP
        $zip = new ZipArchive;
        $res = $zip->open($path . '/' . $fileName);

        if ($res === TRUE) {
            $zip->extractTo($path . '/unzipped');
            $zip->close();

            // Restaurar base de datos si hay archivo .sql
            $sqlFile = $path . '/unzipped/database.sql';
            if (file_exists($sqlFile)) {
                $command = sprintf(
                    'mysql -u%s -p%s %s < %s',
                    env('DB_USERNAME'),
                    env('DB_PASSWORD'),
                    env('DB_DATABASE'),
                    $sqlFile
                );
                exec($command);
            }

            // Restaurar archivos
            $storagePath = $path . '/unzipped/storage';
            if (File::exists($storagePath)) {
                File::copyDirectory($storagePath, storage_path('app'));
            }

            $publicPath = $path . '/unzipped/public';
            if (File::exists($publicPath)) {
                File::copyDirectory($publicPath, public_path());
            }

            return redirect()->back()->with('success', 'Copia restaurada con éxito.');
        } else {
            return redirect()->back()->with('error', 'Error al descomprimir el ZIP.');
        }
    }
}
