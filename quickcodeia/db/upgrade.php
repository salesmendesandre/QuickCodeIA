<?php
// ============================================================================
//  QuickCode IA – Pasos de actualización del plugin
//  mod/quickcodeia/db/upgrade.php
// ============================================================================

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/upgradelib.php');

/**
 * Ejecuta los pasos de actualización del plugin desde una versión anterior.
 *
 * @param int $oldversion Versión anterior instalada en el sitio
 * @return bool Devuelve true si se completó correctamente
 */
function xmldb_quickcodeia_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Aquí puedes añadir bloques como:
    //
    // if ($oldversion < 2025051900) {
    //     // Realizar cambios (crear tabla, añadir campo, etc.)
    //     // upgrade_main_savepoint(true, 2025051900, 'mod_quickcodeia');
    // }

    // Más información:
    // https://docs.moodle.org/dev/Upgrade_API
    // https://docs.moodle.org/dev/XMLDB_editor

    return true;
}
