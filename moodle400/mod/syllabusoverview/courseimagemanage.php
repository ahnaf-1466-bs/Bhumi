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

global $DB, $PAGE, $OUTPUT, $CFG;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/mod/syllabusoverview/lib.php');

require_login();
$context = context_system::instance();
$courseid = optional_param('id', 0, PARAM_INT);
$cmid = optional_param('cmid', 0, PARAM_INT);

$PAGE->set_url(new moodle_url('/mod/syllabusoverview/courseimage.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('manage_courseimage', 'mod_syllabusoverview'));
$PAGE->set_heading(get_string('manage_courseimage', 'mod_syllabusoverview'));
$PAGE->requires->css('/mod/syllabusoverview/styles.css');

echo $OUTPUT->header();
$html = '';
$messages = get_url_for_courseimage($courseid);
if (!$messages) {
    $html .= '<div class="alert alert-warning fade show" role="alert">';
    $html .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
    $html .= '<span aria-hidden="true">&times;</span>';
    $html .= '</button>';
    $html .= '<strong>No Image Found!</strong>';
    $html .= '</div>';
}

$record = $DB->get_record('syllabusoverview', ['course' => $courseid]);
$addcourseimage = get_string('addcourseimagenew', 'mod_syllabusoverview');
$back = get_string('goback', 'mod_syllabusoverview');
$upload = get_string('uploadbutton', 'mod_syllabusoverview');
$backtoview = get_string('backtoview', 'mod_syllabusoverview');
$editcourseimage = get_string('editcourseimage', 'mod_syllabusoverview');
$delete = get_string('delete', 'mod_syllabusoverview');

$templatecontext = (object)[
    'messages' => array_values($messages),
    'courseimgsection' => new moodle_url('/mod/syllabusoverview/courseimage.php?id='.$courseid),
    'adddescription' => $addcourseimage,
    'backtoview' => $backtoview,
    'upload' => $upload,
    'backurl' => new moodle_url('/mod/syllabusoverview/view.php?id='.$record->coursemodule),
    'editdescription' => $editcourseimage,
    'del' => $delete,
    'cmid' => $cmid,
    'html' => $html
];

echo $OUTPUT->render_from_template('mod_syllabusoverview/courseimage', $templatecontext);

echo $OUTPUT->footer();
