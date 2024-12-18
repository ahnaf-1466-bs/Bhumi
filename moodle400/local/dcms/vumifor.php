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
 * @package     local_dcms
 * @author      Brain Station 23 Ltd.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_dcms\form\vumifor;
use local_dcms\manager;

global $DB, $COURSE, $CFG, $PAGE, $OUTPUT;
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/local/dcms/lib.php');


require_login();
$context = context_system::instance();

$messageid = optional_param('messageid', 0, PARAM_INT);
$delete = optional_param('del', 0, PARAM_INT);

$PAGE->set_url(new moodle_url('/local/dcms/vumifor.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('vumifor');

// Delete the record.
if ($delete == 1) {
    $DB->delete_records('dcms_vumifor', ['id' => $messageid]);
    $DB->delete_records('dcms_vumiforurl', ['id' => $messageid]);
    redirect($CFG->wwwroot . '/local/dcms/vumiformanage.php', get_string('valuedeleted', 'local_dcms'));
}

if($messageid != NULL) {
    $mform = new vumifor($CFG->wwwroot . '/local/dcms/vumifor.php?messageid='.$messageid);
} else {
    $mform = new vumifor($CFG->wwwroot . '/local/dcms/vumifor.php');
}

if ($mform->is_cancelled()) {
    // Go back to vumiformanage.php page
    redirect($CFG->wwwroot . '/local/dcms/vumiformanage.php', get_string('cancelled_form', 'local_dcms'));

} else if ($fromform = $mform->get_data()) {
    $manager = new manager();

    if ( $messageid != NULL) {
        // We are updating an existing record.
        global $DB;

        update_record_vumifor($fromform, $messageid);
        update_record_vumiforurl ($fromform, $messageid);
        redirect($CFG->wwwroot . '/local/dcms/vumiformanage.php', get_string('updated_form', 'local_dcms'));
        }

        try {
            // Insert data into tables.
            insert_record_vumifor($fromform);
            insert_record_vumiforurl($fromform);

        } catch  (dml_exception $e) {
            return false;
        }

    // Go back to vumiformanage.php page.
    redirect($CFG->wwwroot . '/local/dcms/vumiformanage.php', get_string('created_form', 'local_dcms'));
}

if ($messageid) {
    // Add extra data to the form.
    global $DB;
    $manager = new manager();
    $message = $manager->get_vumifor($messageid);

    if (!$message) {
        throw new invalid_parameter_exception('Message not found');
    }
    $mform->set_data($message);
}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
