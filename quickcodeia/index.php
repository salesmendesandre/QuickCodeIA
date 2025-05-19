<?php
// ============================================================================
//  QuickCode IA – Listado de todas las instancias del módulo en un curso
//  Guardar como:  mod/quickcodeia/index.php
// ============================================================================

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

// ---------------------------------------------------------------------------
// Parámetro obligatorio: ID del curso
// ---------------------------------------------------------------------------
$id = required_param('id', PARAM_INT); // ID del curso

// ---------------------------------------------------------------------------
// Cargar curso y verificar acceso
// ---------------------------------------------------------------------------
$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);
require_course_login($course);

$coursecontext = context_course::instance($course->id);

// ---------------------------------------------------------------------------
// Registrar evento de visualización del listado de instancias del módulo
// ---------------------------------------------------------------------------
$event = \mod_quickcodeia\event\course_module_instance_list_viewed::create([
    'context' => $coursecontext, // ← corregido: era $modulecontext (no definido)
]);
$event->add_record_snapshot('course', $course);
$event->trigger();

// ---------------------------------------------------------------------------
// Configuración de la página
// ---------------------------------------------------------------------------
$PAGE->set_url('/mod/quickcodeia/index.php', ['id' => $id]);
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($coursecontext);

echo $OUTPUT->header();

// ---------------------------------------------------------------------------
// Título principal
// ---------------------------------------------------------------------------
$modulenameplural = get_string('modulenameplural', 'mod_quickcodeia');
echo $OUTPUT->heading($modulenameplural);

// ---------------------------------------------------------------------------
// Obtener todas las instancias del módulo en el curso
// ---------------------------------------------------------------------------
$quickcodeias = get_all_instances_in_course('quickcodeia', $course);

if (empty($quickcodeias)) {
    notice(
        get_string('noquickcodeiainstances', 'mod_quickcodeia'),
        new moodle_url('/course/view.php', ['id' => $course->id])
    );
}

// ---------------------------------------------------------------------------
// Crear tabla para mostrar las instancias
// ---------------------------------------------------------------------------
$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

// Cabecera según el formato del curso
if ($course->format === 'weeks') {
    $table->head  = [get_string('week'), get_string('name')];
    $table->align = ['center', 'left'];
} else if ($course->format === 'topics') {
    $table->head  = [get_string('topic'), get_string('name')];
    $table->align = ['center', 'left'];
} else {
    $table->head  = [get_string('name')];
    $table->align = ['left'];
}

// Añadir filas a la tabla
foreach ($quickcodeias as $quickcodeia) {
    $link = html_writer::link(
        new moodle_url('/mod/quickcodeia/view.php', ['id' => $quickcodeia->coursemodule]),
        format_string($quickcodeia->name, true),
        $quickcodeia->visible ? [] : ['class' => 'dimmed']
    );

    if ($course->format === 'weeks' || $course->format === 'topics') {
        $table->data[] = [$quickcodeia->section, $link];
    } else {
        $table->data[] = [$link];
    }
}

// ---------------------------------------------------------------------------
// Mostrar la tabla y pie de página
// ---------------------------------------------------------------------------
echo html_writer::table($table);
echo $OUTPUT->footer();
