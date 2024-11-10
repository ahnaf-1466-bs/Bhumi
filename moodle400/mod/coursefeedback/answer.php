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

$haseditcapability = has_capability('mod/coursefeedback:editquestion', $context);
require_capability('mod/coursefeedback:view', $context);

$PAGE->set_url('/mod/coursefeedback/answer.php', array('cmid' => $cm->id, 'feedbackid' => $feedbackid));
$PAGE->set_title($course->shortname.': '.$coursefeedback->name);
$PAGE->set_heading($course->fullname);
$PAGE->add_body_class('limitedwidth');


$responses = coursefeedback_get_responses($cmid, $feedbackid);

$sql = "SELECT * 
        FROM {coursefeedback_questions}
        WHERE courseid=$course->id";

$questionsdata = array_values($DB->get_records_sql($sql));

$querypart = array();
$i = 1;
foreach($questionsdata as $question) {
    $temp = "MAX(CASE WHEN cr.questionid =". $question->id . " THEN response END) AS question". $i ."_response ";
    array_push($querypart, $temp);
    $i++;
}
$querystring = join(",", $querypart);

$sql = "SELECT cr.userid, u.firstname, u.lastname,
        " . $querystring . 
        "FROM {coursefeedback_response} cr
        LEFT JOIN {user} u ON cr.userid = u.id
        WHERE cr.courseid=". $course->id .
        " GROUP BY cr.userid";
$responsesdata = array_values($DB->get_records_sql($sql));

$allresponses = [];
// Template data.
$templatecontext = (object)[
    'back_url' => new moodle_url("/mod/coursefeedback/view.php"),
    'cmid' => $cmid,
    'feedbackid' => $feedbackid,
    'responses' => array_values($responses),
    'questionsdata' => $questionsdata,
    'responsesdata' => $responsesdata,
];

echo $OUTPUT->header();
echo html_writer::tag('h3', 'Responses');

$table = new html_table();
$tableheading = array('Userid');
array_push($tableheading, 'Rating');
array_push($tableheading, 'Comments');
foreach($questionsdata as $question) {
    array_push($tableheading, $question->question);
}

$table->head = $tableheading;

$sql = "SELECT * 
FROM {coursefeedback_all_ratings}
WHERE courseid = $course->id";

$allratings = $DB->get_records_sql($sql);
$row = array();
foreach($responsesdata as $response) {
    $temp = array();
    // If the user is a valid uesr, then it should have a firstname.
    if($response->firstname) {
        array_push($temp, $response->firstname . " " . $response->lastname);
        foreach($allratings as $ratingdata) {
            if($ratingdata->userid === $response->userid) {
                array_push($temp, $ratingdata->rating);
                array_push($temp, $ratingdata->comment);
            }
        }
        for($i = 1; $i <= count((array)$response) - 3; $i++) {
            $key = "question" . $i . "_response";
            array_push($temp, $response->$key);
        }
        array_push($row, $temp);
        }

}
$table->data = $row;
$html = html_writer::table($table);
echo $html;

// echo $OUTPUT->render_from_template('mod_coursefeedback/answer', $templatecontext);


echo $OUTPUT->footer();
