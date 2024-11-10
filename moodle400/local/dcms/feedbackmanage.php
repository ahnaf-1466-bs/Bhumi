<?php
 //This file is part of Moodle Course Rollover Plugin
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

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/local/dcms/lib.php');
global $DB, $PAGE, $OUTPUT;

require_login();
$context = context_system::instance();

$PAGE->set_url(new moodle_url('/local/dcms/feedbackmanage.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('manage_feedback', 'local_dcms'));
$PAGE->set_heading(get_string('manage_feedback', 'local_dcms'));
$PAGE->requires->css('/local/dcms/styles.css');

echo $OUTPUT->header();
$messages = get_picurl_with_content_feedback();
$record = $DB->get_records('dcms_feedback');
$val = get_string('newfeedback', 'local_dcms');
$back = get_string('goback', 'local_dcms');

$templatecontext = (object)[
    'messages' => array_values($messages),
    'feedbackurl' => new moodle_url('/local/dcms/feedback.php'),
    'val' => $val,
    'back' => $back,
    'backurl' => new moodle_url('/local/dcms/view.php'),
    'fullfeed' => new moodle_url('/local/dcms/fullfeedback.php'),
];

echo $OUTPUT->render_from_template('local_dcms/feedbackmanage', $templatecontext);

echo $OUTPUT->footer();
