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
 * @author      2023 Brain Station 23 Ltd.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/local/dcms/lib.php');
global $DB, $PAGE, $OUTPUT;

require_login();
$context = context_system::instance();

$PAGE->set_url(new moodle_url('/local/dcms/footermanage.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('manage_footer', 'local_dcms'));
$PAGE->set_heading(get_string('manage_footer', 'local_dcms'));
$PAGE->requires->css('/local/dcms/styles.css');

echo $OUTPUT->header();
$footerlinks = local_dcms_get_footer_links();
$newfooterlink = get_string('new_footer_link', 'local_dcms');
$back = get_string('goback', 'local_dcms');

$templatecontext = (object)[
    'footerlinks' => array_values($footerlinks),
    'footerurl' => new moodle_url('/local/dcms/footer.php'),
    'newfooterlink' => $newfooterlink,
    'back' => $back,
    'backurl' => new moodle_url('/local/dcms/view.php')
];

echo $OUTPUT->render_from_template('local_dcms/footermanage', $templatecontext);

echo $OUTPUT->footer();
