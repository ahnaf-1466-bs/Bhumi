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
 * View page of local_preregistration plugin.
 *
 * @package    local_preregistration
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/local/preregistration/lib.php');

global $CFG, $PAGE, $DB;

$batchid = required_param('batchid', PARAM_INT);

$PAGE->set_url('/local/preregistration/batch_details.php');
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_preregistration'));
$PAGE->set_heading(get_string('pluginname', 'local_preregistration'));

// Get batch info.
$batchinfo = $DB->get_record('local_preregistration_batch', array('id' => $batchid));
$course = $DB->get_record('course', array('id' => $batchinfo->courseid));


// Program dates data.
$programdates  = local_preregistration_get_programdates($batchid);
$hasprogramdates = 0;
if($programdates) {
    $hasprogramdates = 1;
}

// Application deadline data.
$deadline = local_preregistration_get_data_by_type($batchid, 'deadline');
$hasdeadline = 0;
$deadlineid = null;
$deadlinedate = null;
if($deadline) {
    $hasdeadline = 1;
    $deadlineid = $deadline->id;
    $deadlinedate = local_preregistration_convert_to_date($deadline->value);
}

// Cost data.
$cost = local_preregistration_get_data_by_type($batchid, 'cost');
$hascost = 0;
$costid = null;
$costvalue = null;
if($cost) {
    $hascost = 1;
    $costid = $cost->id;
    $costvalue = $cost->value;
}

// Course length data.
$courselength = local_preregistration_get_data_by_type($batchid, 'courselength');
$hascourselength = 0;
$courselengthid = null;
$courselengthvalue = null;
if($courselength) {
    $hascourselength = 1;
    $courselengthid = $courselength->id;
    $courselengthvalue = $courselength->value;
}

$hasdata = $hasprogramdates || $hasdeadline;

echo $OUTPUT->header();

$templatecontext = [
    'goback' => new moodle_url('/local/preregistration/view.php'),
    'edit_programdate' => new moodle_url('/local/preregistration/edit_programdate.php'),
    'edit_deadline' => new moodle_url('/local/preregistration/edit_deadline.php'),
    'edit_cost' => new moodle_url('/local/preregistration/edit_cost.php'),
    'edit_courselength' => new moodle_url('/local/preregistration/edit_courselength.php'),
    'edit_programpdf' => new moodle_url('/local/preregistration/edit_programpdf.php'),
    'batchid' => $batchid,
    'batchname' => $batchinfo->name,
    'coursename' => $course->fullname,
    'hasdata' => $hasdata,
    'hasprogramdates' => $hasprogramdates,
    'programdates' => $programdates,
    'hasdeadline' => $hasdeadline,
    'deadlineid' => $deadlineid,
    'deadlinedate' => $deadlinedate,
    'hascost' => $hascost,
    'costid' => $costid,
    'costvalue' => $costvalue,
    'hascourselength' => $hascourselength,
    'courselengthid' => $courselengthid,
    'courselengthvalue' => $courselengthvalue,
];

echo $OUTPUT->render_from_template('local_preregistration/batch_details', $templatecontext);


echo $OUTPUT->footer();