<?php
// This file is part of Moodle Course Rollover Plugin
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

use mod_syllabusoverview\form\addprogramdate;
use mod_syllabusoverview\manager;

global $DB, $COURSE, $CFG, $PAGE, $OUTPUT;
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/mod/syllabusoverview/lib.php');
require_once($CFG->dirroot.'/mod/syllabusoverview/classes/form/addprogramdate.php');

require_login();
$context = context_system::instance();
$messageid = optional_param('programid', 0, PARAM_INT);
$courseid = required_param('id', PARAM_INT);
$delete = optional_param('del', 0, PARAM_INT);

$PAGE->set_url(new moodle_url('/mod/syllabusoverview/addprogramdate.php?id='.$courseid));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Add Program');

// Delete the record
if($delete == 1) {
    $DB->delete_records('syllabusoverview_prog_name', ['id' => $messageid]);
    $DB->delete_records('syllabusoverview_program', ['prog_id' => $messageid]);
    redirect($CFG->wwwroot . '/mod/syllabusoverview/programmanage.php?id='.$courseid, get_string('valuedeleted', 'mod_syllabusoverview'));
}

if ($messageid != NULL) {
    $mform = new addprogramdate($CFG->wwwroot . '/mod/syllabusoverview/addprogramdate.php?id='.$courseid.'&programid='.$messageid);
} else {
    $mform = new addprogramdate($CFG->wwwroot . '/mod/syllabusoverview/addprogramdate.php?id='.$courseid);
}
if ($mform->is_cancelled()) {
    // Go back to programmanage.php page
    redirect($CFG->wwwroot . '/mod/syllabusoverview/programmanage.php?id='.$courseid, get_string('cancelled_form', 'mod_syllabusoverview'));

} else if ($fromform = $mform->get_data()) {
    $manager = new manager();

    if($messageid != NULL) {
        // We are updating an existing record.
        update_record_programname($courseid, $fromform, $messageid);
        redirect($CFG->wwwroot . '/mod/syllabusoverview/programmanage.php?id='.$courseid, get_string('updated_form', 'mod_syllabusoverview'));
    }

    // Insert.
    try {
        insert_record_programname($courseid, $fromform);

    }   catch  (dml_exception $e) {
        return false;
    }

    // Go back to programmanage.php page
    redirect($CFG->wwwroot . '/mod/syllabusoverview/programmanage.php?id='.$courseid, get_string('created_form', 'mod_syllabusoverview'));
}

if ($messageid) {
    // Add extra data to the form.
    global $DB;
    $manager = new manager();
    $message = $manager->get_programname($messageid);
    if (!$message) {
        throw new invalid_parameter_exception('Message not found');
    }
    $mform->set_data($message);
}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();