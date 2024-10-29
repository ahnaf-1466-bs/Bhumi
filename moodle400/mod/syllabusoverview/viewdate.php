<?php
//This file is part of Moodle Course Rollover Plugin
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package     mod_syllabusoverview
 * @author      Brain Station 23 Ltd.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/mod/syllabusoverview/lib.php');
global $DB, $PAGE, $OUTPUT;

require_login();
$context = context_system::instance();
$courseid = optional_param('id', 0, PARAM_INT);
$programid = optional_param('programid', 0, PARAM_INT);

$PAGE->set_url(new moodle_url('/mod/syllabusoverview/viewdate.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Program Date and Time');
$PAGE->set_heading('Program Date and Time');
$PAGE->requires->css('/mod/syllabusoverview/styles.css');

echo $OUTPUT->header();

$messages = $DB->get_records('syllabusoverview_program', ['course' => $courseid, 'prog_id' => $programid]);
//echo "<pre>"; var_dump($messages); die();
$record = $DB->get_record('syllabusoverview', ['course' => $courseid]);
$back = get_string('goback', 'mod_syllabusoverview');
$addprogramdetails = get_string('addprogramdetails', 'mod_syllabusoverview');

$templatecontext = (object)[
    'messages' => array_values($messages),
//    'featureurl' => new moodle_url('/mod/syllabusoverview/addprogram.php?id='.$courseid),
    'datetimeurl' => new moodle_url('/mod/syllabusoverview/program.php?id='.$courseid),
    'backurl' => new moodle_url('/mod/syllabusoverview/programmanage.php?id='.$courseid),
    'back' => $back,
    'addprogramdetails' => $addprogramdetails,
    'programid' => $programid
//    'pdfurl' => new moodle_url('/mod/syllabusoverview/programpdfmanage.php?id='.$courseid),
//    'detailsurl' => new moodle_url('/mod/syllabusoverview/programdetails.php?id='.$courseid),
//    'viewdatetime' => new moodle_url('/mod/syllabusoverview/viewdate.php?id='.$courseid),
];

echo $OUTPUT->render_from_template('mod_syllabusoverview/viewdate', $templatecontext);

echo $OUTPUT->footer();
