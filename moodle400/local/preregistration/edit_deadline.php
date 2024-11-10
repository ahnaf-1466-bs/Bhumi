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

use local_preregistration\form\programdeadline_form;

require('../../config.php');
require_once('./lib.php');
require_once($CFG->dirroot.'/local/preregistration/classes/form/programdeadline_form.php');
try {
    require_login();
} catch (Exception $exception) {
    print_r($exception);
}

global $CFG, $PAGE, $DB, $USER;

$batchid = required_param('batchid', PARAM_INT);
$dataid = optional_param('dataid', 0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);

$PAGE->set_url('/local/preregistration/edit_deadline.php');
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('edit_deadline', 'local_preregistration'));
$PAGE->set_heading(get_string('edit_deadline', 'local_preregistration'));

$batchinfo = $DB->get_record('local_preregistration_batch', array('id' => $batchid));

if($dataid && $delete) {
    // Delete the pre registration data by id.
    local_preregistration_delete_batch_data($dataid, $batchid);

    //Back to view.php
    redirect(new moodle_url('/local/preregistration/batch_details.php?batchid=' . $batchid), get_string('deleted', 'local_preregistration'));
}


if ($dataid) {
    $actionurl = new moodle_url('/local/preregistration/edit_deadline.php?batchid=' . $batchid . '&dataid=' . $dataid);
    $mform = new programdeadline_form($actionurl);
    
} else {
    $actionurl = new moodle_url('/local/preregistration/edit_deadline.php?batchid=' . $batchid);
    $mform = new programdeadline_form($actionurl);
}

if ($mform->is_cancelled()) {
    //Back to view.php
    redirect(new moodle_url('/local/preregistration/batch_details.php?batchid=' . $batchid), get_string('cancelled', 'local_preregistration'));
} else if ($fromform = $mform->get_data()) {
    
    if ($dataid) {
       
        // // Update the record.
        
        $record = new stdClass();
        $record = $DB->get_record('local_preregistration_data', array('id' => $dataid));
        $record->id = $dataid;
        $record->batchid = $batchid;
        $record->courseid = $batchinfo->courseid;
        $record->type = 'deadline';
        $record->value = $fromform->value;
        $record->timemodified = time();

        $DB->update_record('local_preregistration_data', $record);
        // Go back to view page.
        redirect(new moodle_url('/local/preregistration/batch_details.php?batchid=' . $batchid), get_string('update_message', 'local_preregistration'));
    
    } else {
        // Insert the record.
        
        $record = new stdClass();
        
        $record->batchid = $batchid;
        $record->courseid = $batchinfo->courseid;
        $record->type = 'deadline';
        $record->value = $fromform->value;
        $record->timecreated = time();
        $record->timemodified = time();

        $DB->insert_record('local_preregistration_data', $record);
        // Go back to view page.
        redirect(new moodle_url('/local/preregistration/batch_details.php?batchid=' . $batchid), get_string('insert_message', 'local_preregistration'));
    }
}

if($dataid) {
    $deadline = $DB->get_record('local_preregistration_data', array('id' => $dataid));
    $mform->set_data($deadline);
}

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();