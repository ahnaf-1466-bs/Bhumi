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

$PAGE->set_url(new moodle_url('/mod/syllabusoverview/programpdfmanage.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('manage_programdetails', 'mod_syllabusoverview'));
$PAGE->set_heading(get_string('manage_programdetails', 'mod_syllabusoverview'));
$PAGE->requires->css('/mod/syllabusoverview/styles.css');

echo $OUTPUT->header();
$messages = get_program_title_and_data($courseid);
$pdfdata = get_pdfurl_for_program($courseid);
//echo "<pre>";var_dump($pdfdata); die();
$record = $DB->get_record('syllabusoverview', ['course' => $courseid]);
$newprogrampdf = get_string('newprogrampdf', 'mod_syllabusoverview');
$addpdf = get_string('addpdffile', 'mod_syllabusoverview');
$editprogrampdf = get_string('editprogrampdf', 'mod_syllabusoverview');
$back = get_string('goback', 'mod_syllabusoverview');
$programstructure_desc = get_string('programstructure_desc', 'mod_syllabusoverview');
$deadline_desc = get_string('deadline_desc', 'mod_syllabusoverview');
$length_desc = get_string('length_desc', 'mod_syllabusoverview');
$fee_desc = get_string('fee_desc', 'mod_syllabusoverview');

$templatecontext = (object)[
    'messages' => array_values($messages),
    'featureurl' => new moodle_url('/mod/syllabusoverview/programadd.php?id='.$courseid),
    'pdfurl' => new moodle_url('/mod/syllabusoverview/addpdf.php?id='.$courseid),
    'newprogrampdf' => $newprogrampdf,
    'addpdf' => $addpdf,
    'editprogrampdf' => $editprogrampdf,
    'back' => $back,
    'programstructure_desc' => $programstructure_desc,
    'deadline_desc' => $deadline_desc,
    'length_desc' => $length_desc,
    'fee_desc' => $fee_desc,
    'backurl' => new moodle_url('/mod/syllabusoverview/programmanage.php?id='.$courseid),
    'pdfdata' => $pdfdata
];

echo $OUTPUT->render_from_template('mod_syllabusoverview/programpdfmanage', $templatecontext);

echo $OUTPUT->footer();
