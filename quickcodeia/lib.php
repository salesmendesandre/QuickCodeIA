<?php
// ============================================================================
//  QuickCode IA – Librería principal del módulo
//  mod/quickcodeia/lib.php
// ============================================================================

defined('MOODLE_INTERNAL') || die();

/**
 * Informa si el plugin soporta ciertas características estándar de Moodle.
 *
 * @param string $feature Constante que representa la característica.
 * @return true|null Devuelve true si se soporta, null en caso contrario.
 */
function quickcodeia_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
        case FEATURE_BACKUP_MOODLE2:
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        default:
            return null;
    }
}

/**
 * Crea una nueva instancia del módulo en la base de datos.
 *
 * @param object $moduleinstance Datos del formulario.
 * @param mod_quickcodeia_mod_form|null $mform Formulario (opcional).
 * @return int ID de la nueva instancia.
 */
function quickcodeia_add_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timecreated = time();
    $moduleinstance->timemodified = $moduleinstance->timecreated;

    if (isset($moduleinstance->statement_editor)) {
        $moduleinstance->statement = $moduleinstance->statement_editor['text'];
    }

    $id = $DB->insert_record('quickcodeia', $moduleinstance);

    $moduleinstance->id = $id;
    quickcodeia_grade_item_update($moduleinstance);

    return $id;
}

/**
 * Actualiza una instancia existente del módulo.
 *
 * @param object $moduleinstance Datos del formulario.
 * @param mod_quickcodeia_mod_form|null $mform Formulario (opcional).
 * @return bool True si fue exitoso.
 */
function quickcodeia_update_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;

    if (isset($moduleinstance->statement_editor)) {
        $moduleinstance->statement = $moduleinstance->statement_editor['text'];
    }

    $success = $DB->update_record('quickcodeia', $moduleinstance);

    if ($success) {
        quickcodeia_grade_item_update($moduleinstance);
    }

    return $success;
}

/**
 * Elimina una instancia del módulo.
 *
 * @param int $id ID de la instancia.
 * @return bool True si fue exitoso.
 */
function quickcodeia_delete_instance($id) {
    global $DB;

    $exists = $DB->get_record('quickcodeia', ['id' => $id]);
    if (!$exists) {
        return false;
    }

    $DB->delete_records('quickcodeia', ['id' => $id]);

    return true;
}

/**
 * Retorna las áreas de archivos navegables para el módulo.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return string[] Lista vacía (no hay áreas de archivos propias).
 */
function quickcodeia_get_file_areas($course, $cm, $context) {
    return [];
}

/**
 * Navegación de archivos para áreas definidas (no implementado).
 */
function quickcodeia_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    return null;
}

/**
 * Sirve archivos del módulo (no implementado).
 */
function quickcodeia_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, $options = []) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);
    send_file_not_found();
}

/**
 * Actualiza la calificación de un alumno.
 *
 * @param stdClass $quickcodeia Instancia del módulo.
 * @param int $userid ID del usuario.
 * @param float|null $score Puntuación a guardar.
 */
function quickcodeia_update_grades($quickcodeia, $userid = 0, $score = null) {
    require_once($GLOBALS['CFG']->libdir . '/gradelib.php');

    $grades = [
        'userid'   => $userid,
        'rawgrade' => $score
    ];

    grade_update('mod/quickcodeia', $quickcodeia->course, 'mod', 'quickcodeia',
                 $quickcodeia->id, 0, $grades);
}

/**
 * Crea o actualiza el ítem de calificación del módulo.
 *
 * @param stdClass $quickcodeia Instancia del módulo.
 * @return int 0 si no hay cambios, 1 si se actualizó.
 */
function quickcodeia_grade_item_update($quickcodeia) {
    require_once(__DIR__ . '/../../lib/gradelib.php');

    $item = [
        'itemname'  => $quickcodeia->name,
        'gradetype' => GRADE_TYPE_VALUE,
        'grademax'  => 100,
        'grademin'  => 0,
    ];

    return grade_update('mod/quickcodeia', $quickcodeia->course, 'mod', 'quickcodeia',
                        $quickcodeia->id, 0, null, $item);
}

/**
 * Añade un enlace "Ver entregas" en el menú de navegación de ajustes del módulo.
 *
 * @param settings_navigation $settings Árbol de navegación.
 * @param navigation_node|null $node Nodo raíz del módulo (por defecto).
 */
function quickcodeia_extend_settings_navigation(settings_navigation $settings, navigation_node $node = null) {
    global $PAGE;

    if (!has_capability('mod/quickcodeia:viewsubmissions', $PAGE->context)) {
        return;
    }

    $target = $node ?? $settings;

    $url = new moodle_url('/mod/quickcodeia/submissions.php', [
        'id' => $PAGE->cm->id
    ]);

    $target->add(
        get_string('viewsubmissions', 'quickcodeia'),
        $url,
        navigation_node::TYPE_SETTING,
        null,
        'quickcodeia_submissions'
    );
}
