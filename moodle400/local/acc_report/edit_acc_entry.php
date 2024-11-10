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
 * Create a new entry for income/expense.
 *
 * @package    local_acc_report
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_acc_report\form\edit_acc_entry_form;


require('../../config.php');
require_once('./lib.php');
require_once($CFG->dirroot.'/local/acc_report/classes/form/edit_acc_entry_form.php');
try {
    require_login();
} catch (Exception $exception) {
    print_r($exception);
}

global $CFG, $PAGE, $DB, $USER;

if(!is_siteadmin($USER)) {
    return redirect(new moodle_url('/'), 'Unauthorized', null, \core\output\notification::NOTIFY_ERROR);
}

$id = optional_param('id', 0, PARAM_INT);
$type = optional_param('type', '', PARAM_TEXT);
$delete = optional_param('delete', 0, PARAM_INT);

$PAGE->set_url('/local/acc_report/edit_coupon.php');
$PAGE->set_context(\context_system::instance());
if($id) {
    $PAGE->set_title(get_string('edit', 'local_acc_report'));
    $PAGE->set_heading(get_string('edit', 'local_acc_report'));
} else {
    $PAGE->set_title(get_string('create', 'local_acc_report'));
    $PAGE->set_heading(get_string('create', 'local_acc_report'));
}

if ($id) {
    $actionurl = new moodle_url('/local/acc_report/edit_acc_entry.php?id=' . $id);
    $mform = new edit_acc_entry_form($actionurl);
    
} else {
    $actionurl = new moodle_url('/local/acc_report/edit_acc_entry.php');
    $mform = new edit_acc_entry_form($actionurl);
}

// Delete the entry and redirect to view page.
if($delete) {
    $DB->delete_records('local_acc_report_data', array('id' => $id));
    //Back to view.php
    redirect(new moodle_url('/local/acc_report/view.php'), get_string('deleted_message', 'local_acc_report'));

}

if ($mform->is_cancelled()) {
    //Back to view.php
    redirect(new moodle_url('/local/acc_report/view.php'), get_string('cancelled_message', 'local_acc_report'));
} else if ($fromform = $mform->get_data()) {
    
    if ($id) {
       
        // Update the record.
        $record = new stdClass();
        $record = $DB->get_record('local_acc_report_data', array('id' => $id));
        
        $record->id = $id;
        $record->courseid = $fromform->courseid;
        $record->type = $fromform->type;
        $record->amount = $fromform->amount;
        $record->currency = $fromform->currency;
        $record->createdby = $USER->id;
        $record->comment = $fromform->comment;
        $record->timemodified = time();

        $DB->update_record('local_acc_report_data', $record);
        // Go back to view page.
        redirect(new moodle_url('/local/acc_report/view.php'), get_string('update_message', 'local_acc_report'));  

    } else {
        // Insert the record.
        
        $record = new stdClass();
        
        $record->courseid = $fromform->courseid;
        $record->type = $fromform->type;
        $record->amount = $fromform->amount;
        $record->currency = $fromform->currency;
        $record->createdby = $USER->id;
        $record->comment = $fromform->comment;
        $record->timecreated = time();
        $record->timemodified = time();

        $DB->insert_record('local_acc_report_data', $record);
        // Go back to view page.
        redirect(new moodle_url('/local/acc_report/view.php'), get_string('insert_message', 'local_acc_report'));
    }
}

if($id) {
    $entry = $DB->get_record('local_acc_report_data', array('id' => $id));
    $mform->set_data($entry);
}
else if($type) {
    $entry = new stdClass();
    $entry->type = $type;

    $mform->set_data($entry);
} 

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();