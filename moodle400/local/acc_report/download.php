<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Download accounting report.
 *
 * @package    local_acc_report
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/local/acc_report/lib.php');

require_login();
if (!is_siteadmin()) {
    redirect($CFG->wwwroot, get_string('no_permission', 'local_acc_report'), null, \core\output\notification::NOTIFY_ERROR);
}

$courselist = optional_param('courses', "", PARAM_RAW);
$from = optional_param('from', mktime(-5,1,0), PARAM_RAW);  // Get the starting of date (12:01 AM)
$to = optional_param('to', mktime(18,59,59), PARAM_RAW);  // Get the end of date (11:59 PM)
$dataformat = optional_param('dataformat', '', PARAM_ALPHA);

$courses = explode('-', $courselist); 

$entriesdata = local_acc_report_get_entries_by_courseid($courses, $from, $to);

// Get shurjo pay itemids based on courss table.
$shurjopayitemids = local_acc_report_get_shurjopay_itemids($courses);

// Get all the shurjopay data based on itemids.
$shurjopaydata = local_acc_report_get_shurjopay_data($shurjopayitemids);
$entriestemp = local_acc_report_prepare_data_for_template($entriesdata, $shurjopaydata, $from, $to);

list($totalincome, $totalexpense, $difference) = local_acc_report_get_total_data($entriestemp);


$entries = local_acc_report_prepare_data_for_export($entriesdata, $shurjopaydata, $from, $to, $totalincome, $totalexpense, $difference);

// $columns = array(
//     'timemodified' => 'Date',
//     'fullname' => 'Course Name',
//     'type' => 'Type',
//     'source' => 'Source',
//     'amount' => 'Amount',
//     'currency' => 'Currency',
//     'comment' => 'Comment',
//     'createdby' => 'Edited By',  
// );

$columns = array(
    'title' => 'Generated at',
    'value' => local_acc_report_convert_to_date(time()),
);

$filename = 'Accounting_report_' . date('Y-m-d');

\core\dataformat::download_data($filename, $dataformat, $columns, $entries, function ($record) {
    // Process the data in some way.
    // You can add and remove columns as needed
    // as long as the resulting data matches the $column metadata.
    return $record;
});
