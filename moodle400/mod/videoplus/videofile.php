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
 * @package     mod_videoplus
 * @author      Brain Station 23 Ltd.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_videoplus\manager;
use mod_videoplus\form\videofile;

global $DB, $COURSE, $CFG, $PAGE, $OUTPUT;
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/videoplus/classes/form/videofile.php');
require_once($CFG->dirroot.'/mod/videoplus/lib.php');


require_login();
$context = context_system::instance();

$messageid = optional_param('messageid', 0, PARAM_INT);
$courseid = required_param('id', PARAM_INT);
$delete = optional_param('del', 0, PARAM_INT);
$cmid = optional_param('cmid', 0, PARAM_INT);

$PAGE->set_url(new moodle_url('/mod/videoplus/videofile.php?id='.$courseid.'&cmid='.$cmid));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('addcourseimage', 'mod_videoplus'));

// Delete the record
if($delete == 1) {
    $DB->delete_records('videoplus_videofile', ['id' => $messageid]);
    $DB->delete_records('videoplus_videourl', ['id' => $messageid]);
    redirect($CFG->wwwroot . '/mod/videoplus/videomanage.php?id='.$courseid.'&cmid='.$cmid, get_string('valuedeleted', 'mod_videoplus'));
}

if($messageid != NULL) {
    $mform = new videofile($CFG->wwwroot . '/mod/videoplus/videofile.php?id='.$courseid.'&cmid='.$cmid.'&messageid='.$messageid);
}
else {
    $mform = new videofile($CFG->wwwroot . '/mod/videoplus/videofile.php?id='.$courseid.'&cmid='.$cmid);
}
if ($mform->is_cancelled()) {
    if ($messageid) {
        redirect($CFG->wwwroot . '/mod/videoplus/videomanage.php?id='.$courseid.'&cmid='.$cmid, get_string('cancelled_form', 'mod_videoplus'));
    }
    else {
        redirect($CFG->wwwroot . '/mod/videoplus/videomanage.php?id=' . $courseid.'&cmid='.$cmid, get_string('cancelled_form', 'mod_videoplus'));
    }


} else if ($fromform = $mform->get_data()) {
    $manager = new manager();

    if($messageid != NULL) {
        // We are updating an existing record.
        update_record_videofile ($courseid, $cmid, $fromform, $messageid);
        update_record_videofileurl ($courseid, $cmid, $fromform, $messageid);
        redirect($CFG->wwwroot . '/mod/videoplus/videomanage.php?id='.$courseid.'&cmid='.$cmid, get_string('updated_form', 'mod_videoplus'));
    }

    // Insert.
    try {
        insert_record_videofile ($courseid, $cmid, $fromform);
        insert_record_videofileurl ($courseid, $cmid, $fromform);
    } catch (dml_exception $e) {
        return false;
    }
    // Go back to videomanage.php page
    redirect($CFG->wwwroot . '/mod/videoplus/videomanage.php?id='.$courseid.'&cmid='.$cmid, get_string('created_form', 'mod_videoplus'));
}

if ($messageid) {
    // Add extra data to the form.
    global $DB;
    $manager = new manager();
    $message = $manager->get_videofile($messageid);
    if (!$message) {
        throw new invalid_parameter_exception('Message not found');
    }
    $mform->set_data($message);
}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
