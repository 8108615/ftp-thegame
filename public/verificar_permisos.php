<?php
// Definimos la ruta de la carpeta que queremos probar
$path = __DIR__ . '/../storage/app/uploads/';

// Intentamos crear la carpeta si no existe
if (!is_dir($path)) {
    if (mkdir($path, 0777, true)) {
        echo "Carpeta creada correctamente.<br>";
    } else {
        echo "ERROR: No se pudo crear la carpeta. Revisa los permisos de la carpeta 'storage/app'.<br>";
    }
}

// Comprobamos si podemos escribir en ella
if (is_writable($path)) {
    echo "¡ÉXITO! La carpeta tiene permisos de escritura y todo está listo para la subida.";
} else {
    echo "¡ERROR! La carpeta NO tiene permisos de escritura.<br>";
    echo "Ruta intentada: " . $path . "<br>";
    echo "Por favor, ve a la carpeta 'storage/app/uploads' en Windows, haz clic derecho -> Propiedades -> Seguridad, y da 'Control Total' al usuario.";
}
?>
