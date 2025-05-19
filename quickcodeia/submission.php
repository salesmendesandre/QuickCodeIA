<?php
// ============================================================================
//  QuickCode IA – Visualización de una entrega individual (modo profesor)
//  mod/quickcodeia/submission.php
// ============================================================================

require(__DIR__ . '/../../config.php');

// ---------------------------------------------------------------------------
// Parámetros obligatorios
// ---------------------------------------------------------------------------
$id     = required_param('id',     PARAM_INT);  // ID del módulo (cmid)
$userid = required_param('userid', PARAM_INT);  // ID del usuario (alumno)

// ---------------------------------------------------------------------------
// Obtención de registros y contexto
// ---------------------------------------------------------------------------
$cm      = get_coursemodule_from_id('quickcodeia', $id, 0, false, MUST_EXIST);
$module  = $DB->get_record('quickcodeia', ['id' => $cm->instance], '*', MUST_EXIST);
$course  = $DB->get_record('course',     ['id' => $cm->course],   '*', MUST_EXIST);
$context = context_module::instance($cm->id);
$user    = $DB->get_record('user', ['id' => $userid, 'deleted' => 0], '*', MUST_EXIST);

// ---------------------------------------------------------------------------
// Control de acceso
// ---------------------------------------------------------------------------
require_login($course, false, $cm);
require_capability('mod/quickcodeia:viewsubmissions', $context);

// ---------------------------------------------------------------------------
// Configuración de la página
// ---------------------------------------------------------------------------
$PAGE->set_url('/mod/quickcodeia/submission.php', ['id' => $cm->id, 'userid' => $userid]);
$PAGE->set_context($context);
$PAGE->set_title(fullname($user) . ' – ' . format_string($module->name));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();
echo $OUTPUT->heading(fullname($user), 3);

// ---------------------------------------------------------------------------
// Mostrar iframe (con permiso para pantalla completa)
// ---------------------------------------------------------------------------
//$iframeurl = new moodle_url('http://localhost:5173/');
$iframeurl = new moodle_url('/mod/quickcodeia/iframe/index.html'); // Producción

echo html_writer::tag('iframe', '', [
    'src'             => $iframeurl,
    'width'           => '100%',
    'height'          => '700',
    'id'              => 'quickcodeframe',
    'style'           => 'border: none; overflow:hidden',
    'allowfullscreen' => 'true',
    'allow'           => 'fullscreen',
    'scrolling'       => 'no',
]);

// ---------------------------------------------------------------------------
// Payload para el iframe (solo config – el estado se carga vía load_state.php)
// ---------------------------------------------------------------------------
$payload = [
    'event'         => 'init',
    'viewMode'      => 'teacher',
    'userId'        => $userid,
    'cmId'          => $cm->id,
    'statement'     => $module->statement,
    'solutionCode'  => $module->solutioncode,
    'language'      => $module->language,
    'dueDate'       => $module->duedate,
    'maxHints'      => $module->maxhints,
    'enableHints'   => $module->enablehints,
    'enableConsole' => $module->enableconsole,
];

echo '<script>const moodleQuickcodePayload = ' .
     json_encode($payload, JSON_UNESCAPED_UNICODE) .
     ';</script>';

// Cargar el frontend común
echo '<script src="' . $CFG->wwwroot . '/mod/quickcodeia/view.js"></script>';

echo $OUTPUT->footer();
