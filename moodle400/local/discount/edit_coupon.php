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
 * Create a new coupon.
 *
 * @package    local_discount
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_discount\form\edit_coupon_form;

require('../../config.php');
require_once('./lib.php');
require_once($CFG->dirroot.'/local/discount/classes/form/edit_coupon_form.php');

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
$active = optional_param('active', -1, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);

$PAGE->set_url('/local/discount/edit_coupon.php');
$PAGE->set_context(\context_system::instance());
if($id) {
    $PAGE->set_title(get_string('edit', 'local_discount'));
} else {
    $PAGE->set_title(get_string('create', 'local_discount'));
}

if ($id) {
    $actionurl = new moodle_url('/local/discount/edit_coupon.php?id=' . $id);
    $mform = new edit_coupon_form($actionurl);
    
} else {
    $actionurl = new moodle_url('/local/discount/edit_coupon.php');
    $mform = new edit_coupon_form($actionurl);
}

if ($mform->is_cancelled()) {
    //Back to view.php
    redirect(new moodle_url('/local/discount/view.php'), 'Cancelled');
} else if ($fromform = $mform->get_data()) {
    
    if ($id) {
       
        // Update the record.
        $record = new stdClass();
        $record = $DB->get_record('local_discount', array('id' => $id));
        
        $record->id = $id;
        $record->course_id = $fromform->course_id;
        $record->created_by = $USER->id;
        $record->max_use = $fromform->max_use;
        $record->discount_percentage = $fromform->discount_percentage;
        $record->type = $fromform->type;
        $record->active = 1;
        $record->timemodified = time();
        $record->timeexpired = $fromform->timeexpired;

        $DB->update_record('local_discount', $record);
        // Go back to view page.
        redirect(new moodle_url('/local/discount/view.php'), get_string('update_message', 'local_discount'));  

    } else {
        // Insert the record.
        
        $record = new stdClass();
        
        $record->course_id = $fromform->course_id;
        $record->created_by = $USER->id;
        $record->coupon_code = local_discount_generate_coupon_code();
        $record->type = $fromform->type;
        $record->max_use = $fromform->max_use;
        $record->discount_percentage = $fromform->discount_percentage;
        $record->active = 1;
        $record->timecreated = time();
        $record->timemodified = time();
        $record->timeexpired = $fromform->timeexpired;
        // var_dump($record);
        // die;

        $DB->insert_record('local_discount', $record);
        // Go back to view page.
        redirect(new moodle_url('/local/discount/view.php'), get_string('insert_message', 'local_discount'));
    }
}

// Delete the entry and redirect to view page.
if($delete == 1) {
    $record = $DB->get_record('local_discount', array('id' => $id));
            
    $record->active = 0;
    $record->deleted = 1;

    $DB->update_record('local_discount', $record);
    //Back to view.php
    redirect(new moodle_url('/local/discount/view.php'), get_string('deleted_message', 'local_discount'));

}

if($active != -1) {
    $record = $DB->get_record('local_discount', array('id' => $id));
            
    $record->active = $active;

    $DB->update_record('local_discount', $record);
    // Go back to view page.
    redirect(new moodle_url('/local/discount/view.php'), get_string('update_active_status', 'local_discount'));
}
if($id) {
    $coupon = $DB->get_record('local_discount', array('id' => $id));
    $mform->set_data($coupon);
}

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();