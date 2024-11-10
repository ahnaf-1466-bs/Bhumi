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

$id      = optional_param('id', 0, PARAM_INT); // Course Module ID
$p      = optional_param('c', 0, PARAM_INT);  // COURSEFEEDBACK instance ID
// $inpopup = optional_param('inpopup', 0, PARAM_BOOL);

if ($p) {
    if (!$coursefeedback = $DB->get_record('coursefeedback', array('id'=>$p))) {
        print_error('invalidaccessparameter');
    }
    $cm = get_coursemodule_from_instance('coursefeedback', $coursefeedback->id, $coursefeedback->course, false, MUST_EXIST);

} else {
    if (!$cm = get_coursemodule_from_id('coursefeedback', $id)) {
        print_error('invalidcoursemodule');
    }
    $coursefeedback = $DB->get_record('coursefeedback', array('id'=>$cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);

$haseditcapability = has_capability('mod/coursefeedback:editquestion', $context);
require_capability('mod/coursefeedback:view', $context);

// Completion and trigger events.
coursefeedback_view($coursefeedback, $course, $cm, $context);

$PAGE->set_url('/mod/coursefeedback/view.php', array('id' => $cm->id));
$PAGE->set_title($course->shortname.': '.$coursefeedback->name);
$PAGE->set_heading($course->fullname);
$PAGE->add_body_class('limitedwidth');

echo $OUTPUT->header();

if(!$haseditcapability) {
    // Student view - No add question button.
    // Answer questions button.
    echo html_writer::tag('a', get_string('btn_answer_question','mod_coursefeedback'), array(
        'class' => "btn btn-info",
        'href' => new moodle_url("/mod/coursefeedback/answer.php", array("cmid" => $cm->id, "feedbackid" => $coursefeedback->id))
    ));
} else {
    // Admin and manager view.
    // Edit question button.
    echo html_writer::tag('a', get_string('btn_edit_question','mod_coursefeedback'), array(
        'class' => "btn btn-primary mr-3",
        'href' => new moodle_url("/mod/coursefeedback/question.php", array("cmid" => $cm->id, "feedbackid" => $coursefeedback->id))
    ));
    $totalquestions = coursefeedback_get_questions($cm->id, $coursefeedback->id);
    $questioncount = count($totalquestions);

    echo html_writer::tag('h3', get_string('overview', 'mod_coursefeedback'), array('class' => 'mt-5'));
    echo html_writer::tag('a', get_string('total_questions', 'mod_coursefeedback') . ": $questioncount", array(
        'href' => new moodle_url("/mod/coursefeedback/question.php", array("cmid" => $cm->id, "feedbackid" => $coursefeedback->id)) 
    ));
    echo '<br>';

    $totalresponses = coursefeedback_get_responses($cm->id, $coursefeedback->id);
    $responsecount = count($totalresponses);
    echo html_writer::tag('a', get_string('total_responses', 'mod_coursefeedback'), array(
        'href' => new moodle_url("/mod/coursefeedback/answer.php", array("cmid" => $cm->id, "feedbackid" => $coursefeedback->id))
    ));
}

echo $OUTPUT->footer();
