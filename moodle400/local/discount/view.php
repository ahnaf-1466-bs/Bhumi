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
 * Version details.
 *
 * @package    local_discount
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG, $PAGE, $DB, $USER;

require('../../config.php');
require_once($CFG->dirroot.'/local/discount/lib.php');

if(!is_siteadmin($USER)) {
    return redirect(new moodle_url('/'), 'Unauthorized', null, \core\output\notification::NOTIFY_ERROR);
}

$PAGE->set_url('/local/discount/view.php');
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_discount'));
$PAGE->set_heading(get_string('pluginname', 'local_discount'));

// Get all the coupons.
$couponsdata = local_discount_get_all_coupons();

$coupons = [];
foreach($couponsdata as $key => $value) {
    $temp = [];

    $temp['id'] = $value->id;
    $temp['course_id'] = $value->course_id;
    $temp['coupon_code'] = $value->coupon_code;
    $temp['type'] = $value->type;
    $temp['max_use'] = $value->max_use;
    $temp['active'] = $value->active;
    $temp['discount_percentage'] = $value->discount_percentage;
    $temp['timecreated'] = $value->timecreated;
    $temp['timemodified'] = $value->timemodified;
    $temp['timeexpired'] = $value->timeexpired;
    $temp['fullname'] = $value->fullname;
    $temp['firstname'] = $value->firstname;

    // Converting timestamp to date time format.
    $date =  new DateTime('@'.$temp['timecreated']);   
    $temp['timecreated'] = $date->format('m-d-Y H:i:s');

    $date =  new DateTime('@'.$temp['timemodified']);   
    $temp['timemodified'] = $date->format('m-d-Y H:i:s');

    $date =  new DateTime('@'.$temp['timeexpired']);   
    $temp['timeexpired'] = $date->format('m-d-Y H:i:s');
    
    array_push($coupons, $temp);
}

echo $OUTPUT->header();

$templatecontext = [
    'edit_coupon' => new moodle_url('/local/discount/edit_coupon.php'),
    'coupons' => array_values($coupons),
    
];

echo $OUTPUT->render_from_template('local_discount/view', $templatecontext);

echo $OUTPUT->footer();
