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
require_once($CFG->dirroot . '/mod/coursefeedback/classes/form/edit_question.php');

$cmid = required_param('cmid', PARAM_INT);
$feedbackid = required_param('feedbackid', PARAM_INT);
$questionid = optional_param('questionid', 0, PARAM_INT);

$urlparams = array(
    'cmid' => $cmid,
    'feedbackid' => $feedbackid,
);

$cm = get_coursemodule_from_id('coursefeedback', $cmid);
$coursefeedback = $DB->get_record('coursefeedback', array('id'=>$cm->instance), '*', MUST_EXIST);

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);

// $haseditcapability = has_capability('mod/coursefeedback:editquestion', $context);
require_capability('mod/coursefeedback:view', $context);
require_capability('mod/coursefeedback:editquestion', $context);


$PAGE->set_url(new moodle_url('/mod/coursefeedback/edit_question.php', $urlparams), $urlparams);
$PAGE->set_title($course->shortname.': '.$coursefeedback->name);
$PAGE->set_heading($course->fullname);
$PAGE->add_body_class('limitedwidth');

$mform = new edit_question(new moodle_url("/mod/coursefeedback/edit_question.php", $urlparams));

if($questionid) {
    $question = $DB->get_record('coursefeedback_questions', array('id' => $questionid));
    $mform->set_data($question);
}

if ($mform->is_cancelled()) {
    redirect(new moodle_url("/mod/coursefeedback/question.php", $urlparams), get_string("form_cancelled", "mod_coursefeedback"));
} else if ($fromform = $mform->get_data()) {

    $msg = "";
    if ($questionid) {
        coursefeedback_update_question($fromform, $feedbackid, $cmid, $course->id, $questionid);
        $msg = get_string("question_updated", "mod_coursefeedback");
    } else {
        $mayoquestionid = coursefeedback_insert_question($fromform, $feedbackid, $cmid, $course->id);
        $msg = get_string('question_added', 'mod_coursefeedback');
    }
    $redirectto = new moodle_url("/mod/coursefeedback/question.php", $urlparams);
    redirect($redirectto, $msg);
} 

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
