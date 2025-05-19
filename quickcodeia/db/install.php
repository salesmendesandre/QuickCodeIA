<?php
// ============================================================================
//  QuickCode IA – Código que se ejecuta tras la instalación inicial del plugin
//  Guardar como: mod/quickcodeia/db/install.php
// ============================================================================

/**
 * Esta función se ejecuta una vez después de que se instala el esquema de base de datos
 * del plugin. Aquí puedes añadir lógica personalizada si es necesario (ej. valores por defecto,
 * migraciones iniciales, etc.).
 *
 * @package     mod_quickcodeia
 * @category    upgrade
 * @copyright   QuickCodeIA
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Función que se ejecuta tras la instalación inicial del plugin.
 *
 * @return bool Devuelve true si no hay errores.
 */
function xmldb_quickcodeia_install() {
    // Código personalizado opcional después de la instalación.
    // Por ahora no se requiere ninguna acción especial.

    return true;
}
