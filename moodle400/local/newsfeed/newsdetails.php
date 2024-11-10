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
 * @package     local_newsfeed
 * @author      Brain Station 23 Ltd.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_newsfeed\form\newsdetails;
use local_newsfeed\manager;

global $DB, $COURSE, $CFG, $PAGE, $OUTPUT;
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/newsfeed/lib.php');

require_login();
$context = context_system::instance();

$newsid = optional_param('newsid', 0, PARAM_INT);
$delete = optional_param('del', 0, PARAM_INT);

$PAGE->set_url(new moodle_url('/local/newsfeed/newsdetails.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Newsfeed Edit');

// Delete the record
if ($delete == 1) {
    $DB->delete_records('newsfeed_newsdetails', ['id' => $newsid]);
    $DB->delete_records('newsfeed_newsdetailurl', ['id' => $newsid]);
    redirect($CFG->wwwroot . '/local/newsfeed/newsdetailsmanage.php?', get_string('valuedeleted', 'local_newsfeed'));
}

if ($newsid != NULL) {
    $mform = new newsdetails($CFG->wwwroot . '/local/newsfeed/newsdetails.php?newsid=' . $newsid);
} else {
    $mform = new newsdetails($CFG->wwwroot . '/local/newsfeed/newsdetails.php');
}
if ($mform->is_cancelled()) {
    // Go back to newsdetailsmanage.php page
    redirect($CFG->wwwroot . '/local/newsfeed/newsdetailsmanage.php', get_string('cancelled_form', 'local_newsfeed'));

} else if ($fromform = $mform->get_data()) {
    $manager = new manager();

    if ($newsid != NULL) {
        // We are updating an existing message.
        global $DB;
        $object = new stdClass();
        $object->id = $newsid;
        $object->newsimage = $fromform->newsimage;


        $DB->update_record('newsfeed_newsdetails', $object);
        update_record_newsfeed_newsdetailurl($fromform, $newsid);
        redirect($CFG->wwwroot . '/local/newsfeed/newsdetailsmanage.php', get_string('updated_form', 'local_newsfeed'));
    }

    // Insert.
    $record_to_insert = new stdClass();
    $record_to_insert->newsimage = $fromform->newsimage;
    $record_to_insert->timecreated = time();

    try {
        $data = $DB->insert_record('newsfeed_newsdetails', $record_to_insert, false);

        insert_record_newsfeed_newsdetailurl($fromform);

    } catch (dml_exception $e) {
        return false;
    }

    // Go back to newsdetailsmanage.php page
    redirect($CFG->wwwroot . '/local/newsfeed/newsdetailsmanage.php', get_string('created_form', 'local_newsfeed'));
}

if ($newsid) {
    // Add extra data to the form.
    global $DB;
    $manager = new manager();
    $message = $manager->get_message($newsid);
    if (!$message) {
        throw new invalid_parameter_exception('Message not found');
    }
    $mform->set_data($message);
}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
