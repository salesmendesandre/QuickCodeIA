<?php
// ============================================================================
//  QuickCode IA – Definición de capacidades (permisos) del módulo
//  mod/quickcodeia/db/access.php
// ============================================================================

defined('MOODLE_INTERNAL') || die();

$capabilities = [

    // ① Ver la actividad (acceso básico)
    'mod/quickcodeia:view' => [
        'captype'      => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes'   => [
            'student'        => CAP_ALLOW,
            'teacher'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW,
        ],
    ],

    // ② Enviar o interactuar con el ejercicio (escribir solución)
    'mod/quickcodeia:submit' => [
        'captype'      => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes'   => [
            'student' => CAP_ALLOW,
        ],
    ],

    // ③ Añadir una nueva instancia de la actividad al curso
    'mod/quickcodeia:addinstance' => [
        'riskbitmask'  => RISK_SPAM | RISK_XSS, // potenciales riesgos
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => [
            'teacher'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW,
        ],
    ],

    // ④ Ver las entregas de los alumnos
    'mod/quickcodeia:viewsubmissions' => [
        'captype'      => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes'   => [
            'teacher'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW,
        ],
    ],

    // ⑤ Evaluar (calificar) ejercicios
    'mod/quickcodeia:grade' => [
        'captype'      => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes'   => [
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW,
        ],
    ],

    // ⑥ Eliminar entregas de estudiantes
    'mod/quickcodeia:managesubmissions' => [
        'captype'      => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes'   => [
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW,
        ],
    ],
];
