<?php
// ============================================================================
//  QuickCode IA – Código que se ejecuta antes de desinstalar el plugin
//  Guardar como: mod/quickcodeia/db/uninstall.php
// ============================================================================

/**
 * Esta función se ejecuta antes de que las tablas del plugin sean eliminadas.
 * Puedes usarla para limpiar datos adicionales, archivos o configuraciones
 * personalizadas relacionadas con el módulo.
 *
 * @package     mod_quickcodeia
 * @category    upgrade
 * @copyright   QuickCodeIA
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Procedimiento personalizado de desinstalación del plugin.
 *
 * @return bool Devuelve true si la desinstalación puede continuar.
 */
function xmldb_quickcodeia_uninstall() {
    // Aquí podrías eliminar configuraciones personalizadas o archivos extra.
    // Actualmente, no se requiere ninguna acción adicional.

    return true;
}
