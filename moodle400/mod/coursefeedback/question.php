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
 * COURSEFEEDBACK module version information
 *
 * @package mod_coursefeedback
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/mod/coursefeedback/lib.php');
require_once($CFG->dirroot.'/mod/coursefeedback/locallib.php');
require_once($CFG->libdir.'/completionlib.php');

$cmid = required_param('cmid', PARAM_INT);
$feedbackid = required_param('feedbackid', PARAM_INT);


$cm = get_coursemodule_from_id('coursefeedback', $cmid);
$coursefeedback = $DB->get_record('coursefeedback', array('id'=>$cm->instance), '*', MUST_EXIST);

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);

// $haseditcapability = has_capability('mod/coursefeedback:editquestion', $context);
require_capability('mod/coursefeedback:view', $context);
require_capability('mod/coursefeedback:editquestion', $context);


$PAGE->set_url('/mod/coursefeedback/question.php', array('cmid' => $cm->id, 'feedbackid' => $feedbackid));
$PAGE->set_title($course->shortname.': '.$coursefeedback->name);
$PAGE->set_heading($course->fullname);
$PAGE->add_body_class('limitedwidth');

$questions = coursefeedback_get_questions($cmid, $feedbackid);

$responses = coursefeedback_get_responses($cmid, $feedbackid);


// Template data.
$templatecontext = (object)[
    'edit_question_url' => new moodle_url("/mod/coursefeedback/edit_question.php"),
    'back_url' => new moodle_url("/mod/coursefeedback/view.php"),
    'cmid' => $cmid,
    'feedbackid' => $feedbackid,
    'questions' => array_values($questions),
    'responses' => array_values($responses),
];

echo $OUTPUT->header();

echo $OUTPUT->render_from_template('mod_coursefeedback/question', $templatecontext);


echo $OUTPUT->footer();
