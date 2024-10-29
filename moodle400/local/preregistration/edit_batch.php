<?php
// This file is part of Moodle - http://moodle.org/
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
 * Create or edit a next batch for a course.
 *
 * @package    local_preregistration
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_preregistration\form\edit_batch_form;

require('../../config.php');
require_once('./lib.php');
require_once($CFG->dirroot.'/local/preregistration/classes/form/edit_batch_form.php');
try {
    require_login();
} catch (Exception $exception) {
    print_r($exception);
}

global $CFG, $PAGE, $DB, $USER;

$id = optional_param('id', 0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);

$PAGE->set_url('/local/preregistration/edit_batch.php');
$PAGE->set_context(\context_system::instance());

if($id) {
    $PAGE->set_title(get_string('edit', 'local_preregistration'));
    $PAGE->set_heading(get_string('edit', 'local_preregistration'));
} else {
    $PAGE->set_title(get_string('create', 'local_preregistration'));
    $PAGE->set_heading(get_string('create', 'local_preregistration'));
}

if($id && $delete) {
    // Delete the pre registration data by id.
    local_preregistration_delete_batch_by_id($id);

    //Back to view.php
    redirect(new moodle_url('/local/preregistration/view.php'), get_string('deleted', 'local_preregistration'));
}


if ($id) {
    $actionurl = new moodle_url('/local/preregistration/edit_batch.php?id=' . $id);
    $mform = new edit_batch_form($actionurl);
    
} else {
    $actionurl = new moodle_url('/local/preregistration/edit_batch.php');
    $mform = new edit_batch_form($actionurl);
}

if ($mform->is_cancelled()) {
    //Back to view.php
    redirect(new moodle_url('/local/preregistration/view.php'), get_string('cancelled', 'local_preregistration'));
} else if ($fromform = $mform->get_data()) {
    
    if ($id) {
       
        // Update the record.
        $record = new stdClass();
        $record = $DB->get_record('local_preregistration_batch', array('id' => $id));

        $record->id = $id;
        $record->courseid = $fromform->course_id;
        $record->name = $fromform->name;
        $record->description = $fromform->description;
        $record->startdate = $fromform->startdate;
        $record->enddate = $fromform->enddate;
        $record->active = $fromform->active;
        $record->timemodified = time();
        
        $DB->update_record('local_preregistration_batch', $record);
        
        // Go back to view page.
        redirect(new moodle_url('/local/preregistration/view.php'), get_string('update_message', 'local_preregistration'));  

    } else {
        // Insert the record.
        
        $record = new stdClass();
        
        $record->courseid = $fromform->course_id;
        $record->name = $fromform->name;
        $record->description = $fromform->description;
        $record->startdate = $fromform->startdate;
        $record->enddate = $fromform->enddate;
        $record->active = $fromform->active;
        $record->timecreated = time();
        $record->timemodified = time();

        $DB->insert_record('local_preregistration_batch', $record);
        // Go back to view page.
        redirect(new moodle_url('/local/preregistration/view.php'), get_string('insert_message', 'local_preregistration'));
    }
}

if($id) {
    $batch = $DB->get_record('local_preregistration_batch', array('id' => $id));
    $mform->set_data($batch);
}

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();