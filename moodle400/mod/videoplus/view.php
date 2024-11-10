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
 * videoplus module version information
 *
 * @package mod_videoplus
 * @copyright 2021 Brain Station 23 LTD.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG, $PAGE, $DB;

require('../../config.php');
require_once($CFG->dirroot.'/mod/videoplus/lib.php');
require_once($CFG->dirroot.'/mod/videoplus/locallib.php');
require_once($CFG->libdir.'/completionlib.php');

$id      = optional_param('id', 0, PARAM_INT); // Course Module ID
$p       = optional_param('p', 0, PARAM_INT);  // videoplus instance ID
$inpopup = optional_param('inpopup', 0, PARAM_BOOL);


if ($p) {
    if (!$videoplus = $DB->get_record('videoplus', array('id'=>$p))) {
        print_error('invalidaccessparameter');
    }
    $cm = get_coursemodule_from_instance('videoplus', $videoplus->id, $videoplus->course, false, MUST_EXIST);

} else {
    if (!$cm = get_coursemodule_from_id('videoplus', $id)) {
        print_error('invalidcoursemodule');
    }
    $videoplus = $DB->get_record('videoplus', array('id'=>$cm->instance), '*', MUST_EXIST);
}

$cmodule = $id;
$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/videoplus:view', $context);

// Completion and trigger events.
videoplus_view($videoplus, $course, $cm, $context);

$PAGE->set_url('/mod/videoplus/view.php', array('id' => $cm->id));

$options = empty($videoplus->displayoptions) ? array() : unserialize($videoplus->displayoptions);

if ($inpopup and $videoplus->display == RESOURCELIB_DISPLAY_POPUP) {
    $PAGE->set_pagelayout('popup');
    $PAGE->set_title($course->shortname.': '.$videoplus->name);
    $PAGE->set_heading($course->fullname);
} else {
    $PAGE->set_title($course->shortname.': '.$videoplus->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_activity_record($videoplus);
}
echo $OUTPUT->header();
if (!isset($options['printheading']) || !empty($options['printheading'])) {
    echo $OUTPUT->heading(format_string($videoplus->name), 2);
}

// Display any activity information (eg completion requirements / dates).
$cminfo = cm_info::create($cm);
$completiondetails = \core_completion\cm_completion_details::get_instance($cminfo, $USER->id);
$activitydates = \core\activity_dates::get_dates_for_module($cminfo, $USER->id);
echo $OUTPUT->activity_information($cminfo, $completiondetails, $activitydates);

if (!empty($options['printintro'])) {
    if (trim(strip_tags($videoplus->intro))) {
        echo $OUTPUT->box_start('mod_introbox', 'videoplusintro');
        echo format_module_intro('videoplus', $videoplus, $cm->id);
        echo $OUTPUT->box_end();
    }
}

$content = file_rewrite_pluginfile_urls($videoplus->content, 'pluginfile.php', $context->id, 'mod_videoplus', 'content', $videoplus->revision);
$formatoptions = new stdClass;
$formatoptions->noclean = true;
$formatoptions->overflowdiv = true;
$formatoptions->context = $context;
$heading = get_string('syllabus_heading', 'mod_videoplus');

$values = [
    'urls' => new moodle_url('/course/view.php?id=' . $course->id . '&section=0'),
    'heading' => $heading,
    'videomanageurl' => new moodle_url('/mod/videoplus/videomanage.php?id=' . $course->id),
    'pdfmanageurl' => new moodle_url('/mod/videoplus/pdfmanage.php?id=' . $course->id),
    'cmid' => $id
];

echo $OUTPUT->render_from_template('mod_videoplus/view', $values);

echo $OUTPUT->footer();
