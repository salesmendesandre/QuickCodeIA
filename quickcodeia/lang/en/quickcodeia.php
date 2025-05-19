<?php
// ============================================================================
//  QuickCode IA — English language strings
//  Location: mod/quickcodeia/lang/en/quickcodeia.php
// ============================================================================

defined('MOODLE_INTERNAL') || die();

// ---------------------------------------------------------------------------
// Plugin meta
// ---------------------------------------------------------------------------
$string['pluginname']               = 'QuickCode IA';
$string['pluginadministration']     = 'QuickCode IA administration';

// ---------------------------------------------------------------------------
// Capabilities (as shown in role editor)
// ---------------------------------------------------------------------------
$string['quickcodeia:addinstance']      = 'Add a new QuickCode IA activity';
$string['quickcodeia:view']             = 'View QuickCode IA activity';
$string['quickcodeia:viewsubmissions']  = 'View QuickCode IA submissions';

// ---------------------------------------------------------------------------
// Module names and help
// ---------------------------------------------------------------------------
$string['modulename']        = 'QuickCode IA';
$string['modulenameplural']  = 'QuickCode IA activities';
$string['modulename_help']   = 'This module allows teachers to create coding exercises enhanced with AI hints and an interactive console.';

// ---------------------------------------------------------------------------
// Form fields and configuration
// ---------------------------------------------------------------------------
$string['quickcodeianame']        = 'Exercise name';
$string['quickcodeianame_help']   = 'The name of this coding exercise instance.';

$string['quickcodeiasettings']    = 'QuickCode IA settings';
$string['quickcodeiafieldset']    = 'Exercise configuration';

$string['statement']       = 'Exercise statement';
$string['statement_help']  = 'The main description or instructions for the coding exercise shown to students.';

$string['solutioncode']       = 'Solution code';
$string['solutioncode_help']  = 'Sample solution code for the exercise, hidden from students.';

$string['language']        = 'Programming language';
$string['language_help']   = 'Select the programming language for the exercise.';

$string['duedate']         = 'Due date';
$string['duedate_help']    = 'Date by which the student must complete the exercise.';

$string['enablehints']      = 'Enable AI hints';
$string['enablehints_help'] = 'If enabled, students can request AI‑powered hints while solving the exercise.';

$string['enableconsole']      = 'Enable AI console help';
$string['enableconsole_help'] = 'If enabled, students will have access to an AI assistant console for this exercise.';

$string['maxhints']        = 'Maximum number of hints';
$string['maxhints_help']   = 'The maximum number of hints a student can use for this exercise.';

// ---------------------------------------------------------------------------
// Miscellaneous
// ---------------------------------------------------------------------------
$string['openaihelp'] = 'Open AI console help';
$string['event:course_module_viewed'] = 'QuickCode IA viewed';

// ---------------------------------------------------------------------------
// Submissions (teacher view)
// ---------------------------------------------------------------------------
$string['viewsubmissions']     = 'View submissions';
$string['lastmodified']        = 'Last modified';
$string['score']               = 'Score';
$string['view']                = 'View';
$string['nousers']             = 'No enrolled users can submit this activity.';
$string['currentstatus']       = 'Current status';

$string['corrected']           = 'Corrected';
$string['submitted']           = 'Submitted';
$string['solving']             = 'Solving';

$string['submissiondeleted']   = 'Submission successfully deleted.';
$string['deletesubmission']    = 'Delete';
