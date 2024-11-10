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
 * @author      2023 Brain Station 23 Ltd.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_dcms\form\footer;
use local_dcms\manager;

global $DB, $COURSE, $CFG, $PAGE, $OUTPUT;
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/local/dcms/lib.php');


require_login();
$context = context_system::instance();

$id = optional_param('id', 0, PARAM_INT);
$delete = optional_param('del', 0, PARAM_INT);

$PAGE->set_url(new moodle_url('/local/dcms/footer.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Footer Links');

// Delete the record
if($delete == 1) {
    $DB->delete_records('dcms_footer', ['id' => $id]);
    redirect($CFG->wwwroot . '/local/dcms/footermanage.php', get_string('valuedeleted', 'local_dcms'));
}

if($id != NULL) {
    $mform = new footer($CFG->wwwroot . '/local/dcms/footer.php?id='.$id);
}
else {
    $mform = new footer($CFG->wwwroot . '/local/dcms/footer.php');
}
if ($mform->is_cancelled()) {
    // Go back to footermanage.php page
    redirect($CFG->wwwroot . '/local/dcms/footermanage.php', get_string('cancelled_form', 'local_dcms'));

} else if ($fromform = $mform->get_data()) {

    if($id != NULL) {
        // We are updating an existing record.
        update_record_footer ($fromform, $id);
        redirect($CFG->wwwroot . '/local/dcms/footermanage.php', get_string('updated_form', 'local_dcms'));
    }
    // Insert.
    try {
        insert_record_footer ($fromform);
    } catch (dml_exception $e) {
        return false;
    }

    // Go back to footermanage.php page
    redirect($CFG->wwwroot . '/local/dcms/footermanage.php', get_string('created_form', 'local_dcms'));
}

if ($id) {
    // Add extra data to the form.
    global $DB;
    $manager = new manager();
    $record = $manager->get_footerlink($id);

    if (!$record) {
        throw new invalid_parameter_exception('ID not found');
    }
    $mform->set_data($id);
}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
