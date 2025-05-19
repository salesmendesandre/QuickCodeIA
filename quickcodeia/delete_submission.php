<?php
// ============================================================================
//  QuickCode IA – Eliminación de la entrega y calificación de un alumno
//  mod/quickcodeia/delete_submission.php
// ============================================================================

require_once(__DIR__ . '/../../config.php');

// ---------------------------------------------------------------------------
// Parámetros obligatorios
// ---------------------------------------------------------------------------
$id     = required_param('id', PARAM_INT);      // ID del módulo (cmid)
$userid = required_param('userid', PARAM_INT);  // ID del alumno

// ---------------------------------------------------------------------------
// Obtener registros y contexto
// ---------------------------------------------------------------------------
$cm      = get_coursemodule_from_id('quickcodeia', $id, 0, false, MUST_EXIST);
$course  = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$module  = $DB->get_record('quickcodeia', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

// ---------------------------------------------------------------------------
// Verificar permisos
// ---------------------------------------------------------------------------
require_login($course, false, $cm);
require_capability('mod/quickcodeia:managesubmissions', $context);

// ---------------------------------------------------------------------------
// Configurar página
// ---------------------------------------------------------------------------
$PAGE->set_url(new moodle_url('/mod/quickcodeia/delete_submission.php', [
    'id'     => $id,
    'userid' => $userid
]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('deletesubmission', 'quickcodeia'));
$PAGE->set_heading($course->fullname);

// ---------------------------------------------------------------------------
// Buscar y eliminar el estado guardado
// ---------------------------------------------------------------------------
$record = $DB->get_record('quickcodeia_state', ['cmid' => $id, 'userid' => $userid]);

if ($record) {
    // Eliminar el registro del estado
    $DB->delete_records('quickcodeia_state', ['id' => $record->id]);

    require_once($CFG->libdir . '/gradelib.php');

    // 1. Notificar al sistema de calificaciones
    grade_update(
        'mod/quickcodeia',
        $course->id,
        'mod',
        'quickcodeia',
        $module->id,
        $userid,
        null,
        ['deleted' => 1]
    );

    // 2. Forzar eliminación de la nota desde la tabla grade_grades
    $grade_item = grade_item::fetch([
        'itemtype'     => 'mod',
        'itemmodule'   => 'quickcodeia',
        'iteminstance' => $module->id,
        'courseid'     => $course->id
    ]);

    if ($grade_item) {
        // Eliminar cualquier calificación para este usuario
        $DB->delete_records('grade_grades', [
            'itemid' => $grade_item->id,
            'userid' => $userid
        ]);
    }

    // Redirigir con mensaje de éxito
    redirect(new moodle_url('/mod/quickcodeia/submissions.php', ['id' => $id]),
             get_string('submissiondeleted', 'quickcodeia'), 2);

} else {
    // Mostrar notificación si no existe entrega
    echo $OUTPUT->header();
    echo $OUTPUT->notification('No se encontró la solución para borrar.', 'notifyproblem');
    echo $OUTPUT->footer();
    exit;
}
