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
 * COURSEFEEDBACK external API
 *
 * @package    mod_coursefeedback
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * COURSEFEEDBACK external functions
 *
 * @package    mod_coursefeedback
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_coursefeedback_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function view_coursefeedback_parameters() {
        return new external_function_parameters(
            array(
                'coursefeedbackid' => new external_value(PARAM_INT, 'coursefeedback instance id')
            )
        );
    }

    /**
     * Simulate the coursefeedback/view.php web interface coursefeedback: trigger events, completion, etc...
     *
     * @param int $coursefeedbackid the coursefeedback instance id
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function view_coursefeedback($coursefeedbackid) {
        global $DB, $CFG;
        require_once($CFG->dirroot . "/mod/coursefeedback/lib.php");

        $params = self::validate_parameters(self::view_coursefeedback_parameters(),
                                            array(
                                                'coursefeedbackid' => $coursefeedbackid
                                            ));
        $warnings = array();

        // Request and permission validation.
        $coursefeedback = $DB->get_record('coursefeedback', array('id' => $params['coursefeedbackid']), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($coursefeedback, 'coursefeedback');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        require_capability('mod/coursefeedback:view', $context);

        // Call the coursefeedback/lib API.
        coursefeedback_view($coursefeedback, $course, $cm, $context);

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function view_coursefeedback_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for get_coursefeedbacks_by_courses.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_coursefeedbacks_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'Course id'), 'Array of course ids', VALUE_DEFAULT, array()
                ),
            )
        );
    }

    /**
     * Returns a list of coursefeedbacks in a provided list of courses.
     * If no list is provided all coursefeedbacks that the user can view will be returned.
     *
     * @param array $courseids course ids
     * @return array of warnings and coursefeedbacks
     * @since Moodle 3.3
     */
    public static function get_coursefeedbacks_by_courses($courseids = array()) {

        $warnings = array();
        $returnedcoursefeedbacks = array();

        $params = array(
            'courseids' => $courseids,
        );
        $params = self::validate_parameters(self::get_coursefeedbacks_by_courses_parameters(), $params);

        $mycourses = array();
        if (empty($params['courseids'])) {
            $mycourses = enrol_get_my_courses();
            $params['courseids'] = array_keys($mycourses);
        }

        // Ensure there are courseids to loop through.
        if (!empty($params['courseids'])) {

            list($courses, $warnings) = external_util::validate_courses($params['courseids'], $mycourses);

            // Get the coursefeedbacks in this course, this function checks users visibility permissions.
            // We can avoid then additional validate_context calls.
            $coursefeedbacks = get_all_instances_in_courses("coursefeedback", $courses);
            foreach ($coursefeedbacks as $coursefeedback) {
                $context = context_module::instance($coursefeedback->coursemodule);
                // Entry to return.
                $coursefeedback->name = external_format_string($coursefeedback->name, $context->id);

                $options = array('noclean' => true);
                list($coursefeedback->intro, $coursefeedback->introformat) =
                    external_format_text($coursefeedback->intro, $coursefeedback->introformat, $context->id, 'mod_coursefeedback', 'intro', null, $options);
                $coursefeedback->introfiles = external_util::get_area_files($context->id, 'mod_coursefeedback', 'intro', false, false);

                $options = array('noclean' => true);
                list($coursefeedback->content, $coursefeedback->contentformat) = external_format_text($coursefeedback->content, $coursefeedback->contentformat,
                                                                $context->id, 'mod_coursefeedback', 'content', $coursefeedback->revision, $options);
                $coursefeedback->contentfiles = external_util::get_area_files($context->id, 'mod_coursefeedback', 'content');

                $returnedcoursefeedbacks[] = $coursefeedback;
            }
        }

        $result = array(
            'coursefeedbacks' => $returnedcoursefeedbacks,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Describes the get_coursefeedbacks_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_coursefeedbacks_by_courses_returns() {
        return new external_single_structure(
            array(
                'coursefeedbacks' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Module id'),
                            'coursemodule' => new external_value(PARAM_INT, 'Course module id'),
                            'course' => new external_value(PARAM_INT, 'Course id'),
                            'name' => new external_value(PARAM_RAW, 'COURSEFEEDBACK name'),
                            'intro' => new external_value(PARAM_RAW, 'Summary'),
                            'introformat' => new external_format_value('intro', 'Summary format'),
                            'introfiles' => new external_files('Files in the introduction text'),
                            'content' => new external_value(PARAM_RAW, 'COURSEFEEDBACK content'),
                            'contentformat' => new external_format_value('content', 'Content format'),
                            'contentfiles' => new external_files('Files in the content'),
                            'legacyfiles' => new external_value(PARAM_INT, 'Legacy files flag'),
                            'legacyfileslast' => new external_value(PARAM_INT, 'Legacy files last control flag'),
                            'display' => new external_value(PARAM_INT, 'How to display the coursefeedback'),
                            'displayoptions' => new external_value(PARAM_RAW, 'Display options (width, height)'),
                            'revision' => new external_value(PARAM_INT, 'Incremented when after each file changes, to avoid cache'),
                            'timemodified' => new external_value(PARAM_INT, 'Last time the coursefeedback was modified'),
                            'section' => new external_value(PARAM_INT, 'Course section id'),
                            'visible' => new external_value(PARAM_INT, 'Module visibility'),
                            'groupmode' => new external_value(PARAM_INT, 'Group mode'),
                            'groupingid' => new external_value(PARAM_INT, 'Grouping id'),
                        )
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function get_coursefeedback_questions_parameters() {
        return new external_function_parameters(
            array(
                'feedbackid' => new external_value(PARAM_INT, 'coursefeedback instance id'),
                'cmid' => new external_value(PARAM_INT, 'Course module id'),
                'userid' => new external_value(PARAM_INT, 'Course module id')
            )
        );
    }

    /**
     * Get all coursefeedback question by cmid and feeedbackid.
     *
     * @param int $feedbackid the coursefeedback instance id
     * @param int $cmid the coursefeedback instance id
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function get_coursefeedback_questions($feedbackid, $cmid, $userid) {
        global $DB, $CFG;
        require_once($CFG->dirroot . "/mod/coursefeedback/lib.php");

        $params = self::validate_parameters(self::get_coursefeedback_questions_parameters(),
                                            array(
                                                'feedbackid' => $feedbackid,
                                                'cmid' => $cmid,
                                                'userid' => $userid
                                            ));
        $warnings = array();

        // Request and permission validation.
        $coursefeedback = $DB->get_record('coursefeedback', array('id' => $params['feedbackid']), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($coursefeedback, 'coursefeedback');
        $courseid = $course->id;                                
        $context = context_module::instance($cm->id);
        self::validate_context($context);

        require_capability('mod/coursefeedback:view', $context);

        $sql = "SELECT cq.id as questionid, cq.feedbackid, cq.courseid, cq.cmid, cq.question, cq.type, cr.response
                FROM {coursefeedback_questions} cq 
                LEFT JOIN {coursefeedback_response} cr ON cq.id = cr.questionid
                WHERE cr.userid= :userid AND feedbackid=:feedbackid AND cmid= :cmid";
        
        $params = [
            'userid' => $userid,
            'feedbackid' => $feedbackid,
            'cmid' => $cmid
        ];

        $questionrecords = $DB->get_records_sql($sql, $params);
        if(count($questionrecords) == 0) {
            $questionrecords = $DB->get_records('coursefeedback_questions', array('feedbackid' => $feedbackid, 'cmid' => $cmid));
        }
        $questions = array();

        $courserating = $DB->get_record('coursefeedback_all_ratings', array('userid' => $userid, 'courseid' => $courseid));

        // Adding the question for rating and course comments.
        // Course rating.
        $questionobj = new stdClass();
        $questionobj->questionid = 0;
        $questionobj->question = get_string('give_couurse_rating', 'mod_coursefeedback');
        if($courserating) {
            $questionobj->response = $courserating->rating;
        } else {
            $questionobj->response = 0;
        }
        $questionobj->type = 'course';
        $questionobj->inputtype = 'int';
        array_push($questions, $questionobj);

        // Course comment.
        $questionobj = new stdClass();
        $questionobj->questionid = 0;
        $questionobj->question = get_string('give_couurse_comment', 'mod_coursefeedback');
        if($courserating) {
            $questionobj->response = $courserating->comment;
        } else {
            $questionobj->response = "";
        }
        $questionobj->type = 'course';
        $questionobj->inputtype = 'text';                                    
        
        array_push($questions, $questionobj);

        foreach($questionrecords as $question) {
            $questionobj = new stdClass();
            $questionobj->questionid = $question->questionid ? $question->questionid : $question->id;
            $questionobj->question = $question->question;
            $questionobj->response = $question->response;
            $questionobj->type = $question->type;
            $questionobj->inputtype = 'int';

            array_push($questions, $questionobj);
        }

        $result = array();
        $result['questions'] = $questions;
        $result['iscommentrequired'] = $coursefeedback->iscommentrequired;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function get_coursefeedback_questions_returns() {
        return new external_single_structure(
            array(
                'questions' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'questionid' => new external_value(PARAM_INT, 'Question id, 0 for rating and comment'),
                            'question' => new external_value(PARAM_TEXT, 'Question text'),
                            'response' => new external_value(PARAM_TEXT, 'Question response'),
                            'type' => new external_value(PARAM_TEXT, 'Type of question'),
                            'inputtype' => new external_value(PARAM_TEXT, 'Type of input field'),
                        )
                    )
                ),
                'iscommentrequired' => new external_value(PARAM_INT, 'Comment required for course or not, 0 for optional, 1 for required'),
                'warnings' => new external_warnings()
            )
        );
    }
    

    /**
     * Describes the parameters for get_coursefeedbacks_by_courses.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function save_feedback_responses_parameters() {
        return new external_function_parameters (
            array(
                'courseid' => new external_value(PARAM_INT, 'Course id'),
                'userid' => new external_value(PARAM_INT, 'User id'),
                'feedbackid' => new external_value(PARAM_INT, 'coursefeedback instance id'),
                'cmid' => new external_value(PARAM_INT, 'coursefeedback instance id'),
                'responses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'questionid' => new external_value(PARAM_INT, 'Question id, 0 for rating and comment'),
                            'response' => new external_value(PARAM_TEXT, 'Response'),
                            'inputtype' => new external_value(PARAM_TEXT, 'Type of input field'),
                        )
                    )
                ),
            )
        );
    }

    /**
     * Returns a list of coursefeedbacks in a provided list of courses.
     * If no list is provided all coursefeedbacks that the user can view will be returned.
     *
     * @param array $courseids course ids
     * @return array of warnings and coursefeedbacks
     * @since Moodle 3.3
     */
    public static function save_feedback_responses($courseid, $userid, $feedbackid, $cmid, $responses) {
        global $DB;
        $warnings = array();

        $params = array(
            'courseid' => $courseid,
            'userid' => $userid,
            'feedbackid' => $feedbackid,
            'cmid' => $cmid,
            'responses' => $responses
        );
        $params = self::validate_parameters(self::save_feedback_responses_parameters(), $params);
        foreach($responses as $response) {
            if($response['questionid'] == 0 ) {
                
                $record = new stdClass();
                $record = $DB->get_record('coursefeedback_all_ratings', array('userid' => $userid, 'courseid' => $courseid));
                if($record) {
                    if($response['inputtype'] == 'int') {
                        // The response is for course rating.
                        $record->rating = $response['response'];
                        $record->timemodified = time();
                        $DB->update_record('coursefeedback_all_ratings', $record);
                    } else {
                        // The response is for course comment.
                        $record->comment = $response['response'];
                        $record->timemodified = time();
                        $DB->update_record('coursefeedback_all_ratings', $record);
                    }
                } else {
                    $record->courseid = $courseid;
                    $record->userid = $userid;
                    $record->timecreated = time();
                    $record->timemodified = time();
                    if($response['inputtype'] == 'int') {
                        // The response is for course rating.
                        $record->rating = $response['response'];
                        
                        $record->comment = "";  
                    } else {
                        // The response is for course comment.
                        $record->comment = $response['response'];
                        $record->rating = 0;
                    }
                    $responseid = $DB->insert_record('coursefeedback_all_ratings', $record);
                   
                }

            }  else {
                // The response is for specific question.
                $record = new stdClass();

                // Check if there is already a response for the questionid
                $record = $DB->get_record('coursefeedback_response', array('questionid' => $response['questionid'], 'userid' => $userid));

                if($record) {
                    $record->response = $response['response'];
                    $record->timemodified = time();

                    $DB->update_record('coursefeedback_response', $record);
                } else {
                    $record->questionid = $response['questionid'];
                    $record->courseid = $courseid;
                    $record->response = $response['response'];
                    $record->userid = $userid;
                    $record->timecreated = time();
                    $record->timemodified = time();

                    $responseid = $DB->insert_record('coursefeedback_response', $record);
                    
                }
            }
        }

        $result = array(
            'status' => true,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Describes the get_coursefeedbacks_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function save_feedback_responses_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }
}
