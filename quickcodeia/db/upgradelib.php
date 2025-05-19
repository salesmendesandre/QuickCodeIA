<?php
// ============================================================================
//  QuickCode IA – Funciones auxiliares para actualizaciones del plugin
//  mod/quickcodeia/db/upgradelib.php
// ============================================================================

/**
 * Funciones auxiliares que pueden ser llamadas desde upgrade.php.
 *
 * @package     mod_quickcodeia
 * @category    upgrade
 * @copyright   QuickCodeIA
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 o later
 */

/**
 * Ejemplo de función auxiliar que se puede invocar durante una actualización.
 * 
 * ⚠️ Importante: Solo se deben usar llamadas de bajo nivel a la base de datos aquí.
 * No uses APIs de Moodle que dependan del estado completo del sistema.
 * 
 * Más información: https://docs.moodle.org/dev/Upgrade_API
 */
function mod_quickcodeia_helper_function() {
    global $DB;

    // Aquí puedes escribir código que sea reutilizable por múltiples pasos de upgrade.
    // Por ejemplo: migraciones, normalizaciones, valores por defecto, etc.

    // Ejemplo de acceso directo a datos (no utilizar API de Moodle aquí):
    // $DB->execute("UPDATE {quickcodeia_state} SET hints_used = 0 WHERE hints_used IS NULL");
}
