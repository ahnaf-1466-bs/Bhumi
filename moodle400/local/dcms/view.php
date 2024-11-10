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
 * dcms module version information
 *
 * @package local_dcms
 * @copyright 2021 Brain Station 23 LTD.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG, $PAGE, $DB;

require('../../config.php');
require_once($CFG->dirroot.'/local/dcms/lib.php');
require_once($CFG->libdir.'/completionlib.php');

$PAGE->set_url('/local/dcms/view.php');
$PAGE->set_context(\context_system::instance());

echo $OUTPUT->header();

$values = [
    'urls' => new moodle_url('../../admin/search.php#linkmodules'),
    'director' => new moodle_url('/local/dcms/directormanage.php'),
    'founder' => new moodle_url('/local/dcms/foundermanage.php'),
    'instructor' => new moodle_url('/local/dcms/instructormanage.php'),
    'operation' => new moodle_url('/local/dcms/operationmanage.php'),
    'siteintro' => new moodle_url('/local/dcms/siteintromanage.php'),
    'ourstory' => new moodle_url('/local/dcms/ourstorymanage.php'),
    'vision' => new moodle_url('/local/dcms/visionmanage.php'),
    'partner' => new moodle_url('/local/dcms/partnermanage.php'),
    'footer' => new moodle_url('/local/dcms/footermanage.php'),
    'feedback' => new moodle_url('/local/dcms/feedbackmanage.php'),
    'vumifor' => new moodle_url('/local/dcms/vumiformanage.php'),
    'strength' => new moodle_url('/local/dcms/strengthmanage.php'),
    'whyvumi' => new moodle_url('/local/dcms/whyvumimanage.php'),
];

echo $OUTPUT->render_from_template('local_dcms/view', $values);

echo $OUTPUT->footer();
