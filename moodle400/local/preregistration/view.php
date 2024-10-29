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

global $CFG, $PAGE, $DB;

require('../../config.php');
require_once($CFG->dirroot.'/local/preregistration/lib.php');

$PAGE->set_url('/local/preregistration/view.php');
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_preregistration'));
$PAGE->set_heading(get_string('pluginname', 'local_preregistration'));

$batchdata = local_preregistration_get_batches();

$batches = [];
foreach($batchdata as $key => $value) {
    $temp = [];

    $temp['id'] = $value->id;
    $temp['course_id'] = $value->courseid;
    $temp['name'] = $value->name;
    $temp['description'] = $value->description;
    $temp['startdate'] = $value->startdate;
    $temp['enddate'] = $value->enddate;
    $temp['active'] = $value->active;
    $temp['timecreated'] = $value->timecreated;
    $temp['timemodified'] = $value->timemodified;
    $temp['fullname'] = $value->fullname;

    // Converting timestamp to date time format.
    $temp['startdate'] = local_preregistration_convert_to_date($temp['startdate']);
    $temp['enddate'] = local_preregistration_convert_to_date($temp['enddate']);
    $temp['timecreated'] = local_preregistration_convert_to_date($temp['timecreated']);
    $temp['timemodified'] = local_preregistration_convert_to_date($temp['timemodified']);
    
    array_push($batches, $temp);
}

echo $OUTPUT->header();

$templatecontext = [
    'edit_batch' => new moodle_url('/local/preregistration/edit_batch.php'),
    'edit_email_template_url' => new moodle_url('/local/preregistration/email_templates.php'),
    'batch_details_url' => new moodle_url('/local/preregistration/batch_details.php'),
    'batches' => array_values($batches),
    'users_list_url' => new moodle_url('/local/preregistration/users_list.php'),
    
];

echo $OUTPUT->render_from_template('local_preregistration/view', $templatecontext);

echo $OUTPUT->footer();
