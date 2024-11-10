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

$PAGE->set_url(new moodle_url('/mod/syllabusoverview/syllabusmanage.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('manage_syllabus', 'mod_syllabusoverview'));
$PAGE->set_heading(get_string('manage_syllabus', 'mod_syllabusoverview'));
$PAGE->requires->css('/mod/syllabusoverview/styles.css');

echo $OUTPUT->header();

$messages = $DB->get_records('syllabusoverview_syllabus', ['course' => $courseid]);
$record = $DB->get_record('syllabusoverview', ['course' => $courseid]);
$val = get_string('newfeature', 'mod_syllabusoverview');
$back = get_string('goback', 'mod_syllabusoverview');
$editsyllabus = get_string('editsyllabus', 'mod_syllabusoverview');
$deletesyllabus = get_string('deletesyllabus', 'mod_syllabusoverview');
$addpdf = get_string('addpdf', 'mod_syllabusoverview');
$addsyllabusdeatils = get_string('addsyllabusdeatils', 'mod_syllabusoverview');

$templatecontext = (object)[
    'messages' => array_values($messages),
    'featureurl' => new moodle_url('/mod/syllabusoverview/syllabus.php?id='.$courseid),
    'val' => $val,
    'back' => $back,
    'backurl' => new moodle_url('/mod/syllabusoverview/view.php?id='.$record->coursemodule),
    'pdfurl' => new moodle_url('/mod/syllabusoverview/syllabuspdfmanage.php?id='.$courseid),
    'addpdf' => $addpdf,
    'addsyllabusdeatils' => $addsyllabusdeatils,
    'editsyllabus' => $editsyllabus,
    'deletesyllabus' => $deletesyllabus
];

echo $OUTPUT->render_from_template('mod_syllabusoverview/syllabusmanage', $templatecontext);

echo $OUTPUT->footer();
