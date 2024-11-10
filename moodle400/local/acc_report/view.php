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
 * @package    local_acc_report
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


use local_acc_report\form\generate_report_form;


require('../../config.php');

require_once($CFG->dirroot.'/local/acc_report/lib.php');
require_once($CFG->dirroot.'/local/acc_report/classes/form/generate_report_form.php');

global $CFG, $PAGE, $DB, $OUTPUT, $USER;

if(!is_siteadmin($USER)) {
    return redirect(new moodle_url('/'), 'Unauthorized', null, \core\output\notification::NOTIFY_ERROR);
}

$PAGE->set_url('/local/acc_report/view.php');
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('pagetitle', 'local_acc_report'));
$PAGE->set_heading(get_string('pagetitle', 'local_acc_report'));

// Setting course id to "0" for all courses
$courseid = 0;
$from = time() - (60 * 60 * 24) * 30; // 30 days.
$to = time();
$showall = 0;

$actionurl = new moodle_url('/local/acc_report/view.php');
$mform = new generate_report_form($actionurl);
if ($mform->is_cancelled()) {
    //Back to view.php
    redirect(new moodle_url('/local/acc_report/view.php'), get_string('cancelled_message', 'local_acc_report'));
} else if ($fromform = $mform->get_data()) {
    $courseid = $fromform->courseid;
    $from = $fromform->from;
    $to = $fromform->to;
    $showall = $fromform->showall;

    if($showall) {
        $from = 0;
    }
}


$entries = array();

// $courses = ($courseid && $courseid =! 1) ? array($courseid) : local_acc_report_get_all_coursesids();
if($courseid == -1 || $courseid == 0) {
    $courses = local_acc_report_get_all_coursesids();
} else {
    $courses = array($courseid);
}
$coursefullname = get_string('all_courses_text', 'local_acc_report');

if($courseid == -1) {
    $coursefullname = get_string('all_courses_text', 'local_acc_report');
}
else if($courseid) {
    $course = $DB->get_record('course', array('id' => $courseid));

    $coursefullname = $course->fullname;
}


$entriesdata = local_acc_report_get_entries_by_courseid($courses, $from, $to);

// Get shurjo pay itemids based on courss table.
$shurjopayitemids = local_acc_report_get_shurjopay_itemids($courses);

// Get all the shurjopay data based on itemids.
$shurjopaydata = local_acc_report_get_shurjopay_data($shurjopayitemids);
$entries = local_acc_report_prepare_data_for_template($entriesdata, $shurjopaydata, $from, $to);

list($totalincome, $totalexpense, $difference) = local_acc_report_get_total_data($entries);

echo $OUTPUT->header();

$templatecontext = [
    'edit_acc_entry' => new moodle_url('/local/acc_report/edit_acc_entry.php'),
    'entries' => array_values($entries),
    'totalincome' => $totalincome, 
    'totalexpense' => $totalexpense,
    'difference' => $difference,
    'coursefullname' => $coursefullname,
    'from' => local_acc_report_convert_to_datetime($from),
    'to' => local_acc_report_convert_to_datetime($to),
];

echo html_writer::tag(
    'h5', 
    get_string('generatereport', 'local_acc_report'), 
    array('class' => 'mt-5')    
);
$mform->display();


echo $OUTPUT->render_from_template('local_acc_report/view', $templatecontext);

if($courseid) {
    echo html_writer::tag('a', 
        get_string('show_report_of_all_coureses', 'local_acc_report'), 
        array(
            'href' => new moodle_url('/local/acc_report/view.php'),
            'class' => 'btn btn-outline-info mb-5'
        )
    );
}

echo $OUTPUT->download_dataformat_selector(
    get_string('export', 'local_acc_report'), 
    'download.php', 
    'dataformat', 
    array(
        'courses' => implode('-', $courses), 
        'from' => $from,
        'to' => $to,
    )
);

echo $OUTPUT->footer();
