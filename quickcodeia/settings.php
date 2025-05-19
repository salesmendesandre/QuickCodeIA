<?php
// ============================================================================
//  QuickCode IA – Configuración del plugin desde administración del sitio
//  mod/quickcodeia/settings.php
// ============================================================================

defined('MOODLE_INTERNAL') || die();

// Mostrar configuración solo si el usuario tiene permisos de administración del sitio
if ($hassiteconfig) {
    // Crear una nueva página de ajustes del plugin
    $settings = new admin_settingpage(
        'mod_quickcodeia_settings',
        new lang_string('pluginname', 'mod_quickcodeia')
    );

    // Agregar elementos solo si se está mostrando el árbol completo de configuración
    if ($ADMIN->fulltree) {
        // TO-DO: Aquí puedes definir los ajustes del plugin, por ejemplo:
        // $settings->add(new admin_setting_configtext(...));
        // Más información: https://docs.moodle.org/dev/Admin_settings
    }
}
