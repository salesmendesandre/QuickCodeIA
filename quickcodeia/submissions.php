<?php
// ============================================================================
//  QuickCode IA – listado de entregas por alumno
//  mod/quickcodeia/submissions.php
// ============================================================================

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->libdir . '/gradelib.php');

// ---------------------------------------------------------------------------
// Parámetros
// ---------------------------------------------------------------------------
$id = required_param('id', PARAM_INT); // Course‑module ID (CMID).

// ---------------------------------------------------------------------------
// Contexto, curso y módulo
// ---------------------------------------------------------------------------
$cm      = get_coursemodule_from_id('quickcodeia', $id, 0, false, MUST_EXIST);
$module  = $DB->get_record('quickcodeia', ['id' => $cm->instance], '*', MUST_EXIST);
$course  = $DB->get_record('course',     ['id' => $cm->course],   '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, false, $cm);
require_capability('mod/quickcodeia:viewsubmissions', $context);

// ---------------------------------------------------------------------------
// Preparar la página
// ---------------------------------------------------------------------------
$PAGE->set_url('/mod/quickcodeia/submissions.php', ['id' => $cm->id]);
$PAGE->set_context($context);
$PAGE->set_title(format_string($module->name) . ' — ' . get_string('viewsubmissions', 'quickcodeia'));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('viewsubmissions', 'quickcodeia'), 2);

// ---------------------------------------------------------------------------
// Usuarios con permiso de enviar la actividad
// ---------------------------------------------------------------------------
$users = get_enrolled_users($context, 'mod/quickcodeia:submit');
if (!$users) {
    echo $OUTPUT->notification(get_string('nousers', 'quickcodeia'), 'notifymessage');
    echo $OUTPUT->footer();
    exit;
}

// ---------------------------------------------------------------------------
// Estado guardado (quickcodeia_state) por usuario
// ---------------------------------------------------------------------------
list($insql, $params) = $DB->get_in_or_equal(array_keys($users));
$params[] = $cm->id;

$rows = $DB->get_records_sql("
    SELECT s.*
      FROM {quickcodeia_state} s
     WHERE s.userid $insql
       AND s.cmid = ?
", $params);

$records = [];
foreach ($rows as $row) {
    $records[$row->userid] = $row;
}

// ---------------------------------------------------------------------------
// Calificaciones finales del gradebook
// ---------------------------------------------------------------------------
$grades = grade_get_grades(
    $course->id,
    'mod',
    'quickcodeia',
    $module->id,
    array_keys($users)
);

// ---------------------------------------------------------------------------
// Tabla flexible
// ---------------------------------------------------------------------------
$table = new flexible_table('quickcodeia_subs_' . $cm->id);
$table->define_baseurl($PAGE->url);

$table->define_columns(['fullname', 'timemodified', 'grade', 'current_status', 'action']);
$table->define_headers([
    get_string('fullname'),
    get_string('lastmodified', 'quickcodeia'),
    get_string('score', 'quickcodeia'),
    get_string('currentstatus', 'quickcodeia'),
    get_string('view')
]);

$table->sortable(true, 'lastname');
$table->set_attribute('class', 'generaltable generalbox');
$table->setup();

// ---------------------------------------------------------------------------
// Añadir filas
// ---------------------------------------------------------------------------
foreach ($users as $uid => $user) {
    // Fecha de última modificación
    if (isset($records[$uid])) {
        $lastmodified = userdate($records[$uid]->timemodified, '%d/%m/%Y, %H:%M:%S');
    } else if (!empty($grades->items[0]->grades[$uid]->dategraded)) {
        $lastmodified = userdate($grades->items[0]->grades[$uid]->dategraded, '%d/%m/%Y, %H:%M:%S');
    } else {
        $lastmodified = '-';
    }

    // Nota final
    $grade = '-';
    if (!empty($grades->items[0]->grades[$uid])) {
        $grade = format_float($grades->items[0]->grades[$uid]->grade, 2);
    }

    // Estado actual
    $status = '-';
    if (!empty($records[$uid]) && isset($records[$uid]->current_status)) {
        $status = get_string(format_string($records[$uid]->current_status), 'quickcodeia');
    }

    // Acciones
    $viewurl = new moodle_url('/mod/quickcodeia/submission.php', [
        'id' => $cm->id,
        'userid' => $uid
    ]);
    $actions = html_writer::link($viewurl, get_string('view'));

    if (isset($records[$uid])) {
        $deleteurl = new moodle_url('/mod/quickcodeia/delete_submission.php', [
            'id' => $cm->id,
            'userid' => $uid
        ]);
        $actions .= ' | ' . html_writer::link(
            $deleteurl,
            get_string('deletesubmission', 'quickcodeia'),
            ['onclick' => "return confirm('¿Estás seguro de que deseas borrar esta entrega?');"]
        );
    }

    // Solo mostrar si hay estado
    if ($status !== '-') {
        $table->add_data([
            fullname($user),
            $lastmodified,
            $grade,
            $status,
            $actions
        ]);
    }
}

// ---------------------------------------------------------------------------
// Mostrar la tabla y pie de página
// ---------------------------------------------------------------------------
$table->finish_output();
echo $OUTPUT->footer();
