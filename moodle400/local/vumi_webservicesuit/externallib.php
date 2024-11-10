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
 * External Library
 *
 * @package    local
 * @subpackage rating_helper
 * @author     Brain Station 23
 * @copyright  2021 Brain Station 23 Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_completion\progress;

global $CFG;

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot.'/local/vumi_webservicesuit/lib.php');
/*
 *
 * @subpackage rating_helper
 */

class local_vumi_webservicesuit_external extends external_api
{
    /**
     * @return external_function_parameters
     */
    public static function get_certificate_list_parameters () {
        return new external_function_parameters(
            array(
                'userid' =>
                    new external_value(
                        PARAM_INT,
                        'User id.'
                    )
            )
        );
    }

    /**
     * @param $cmid
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function get_certificate_list ($userid) {
        global $DB, $CFG;
        require_once $CFG->dirroot . '/lib/completionlib.php';
        require_once $CFG->dirroot . '/completion/completion_aggregation.php';
        require_once $CFG->dirroot . '/completion/criteria/completion_criteria.php';
        require_once $CFG->dirroot . '/completion/completion_completion.php';
        require_once $CFG->dirroot . '/completion/completion_criteria_completion.php';
        require_once($CFG->dirroot . '/local/vumi_webservicesuit/lib.php');

        $response = [];
        $message = '';
        // Parameter validation.
        $params = self::validate_parameters(
            self::get_certificate_list_parameters(),
            array(
                'userid' => $userid
            )
        );

        $SQL = "SELECT * 
                FROM {modules} m
                JOIN {course_modules} cm ON m.id = cm.module
                WHERE m.name = 'customcert'";

        $res = $DB->get_records_sql($SQL);

        $certarr = [];

        foreach ($res as $cert) {
            $courseid = $cert->course;
            $course = $DB->get_record('course', ['id' => $courseid]);

            try {
                $percentage = progress::get_course_progress_percentage($course, $userid);
            } catch (Exception $e) {
                $percentage = $e;
            }

            if ($percentage == "100") {
                $certcmid = vumi_webservicesuit_get_cert_cmid($courseid);

                $customcert = $DB->get_record('customcert', ['course' => $courseid]);

                if (!$DB->record_exists('customcert_issues', array('userid' => $userid, 'customcertid' => $certcmid->id))) {
                    \mod_customcert\certificate::issue_certificate($customcert->id, $userid);
                }

                $cm = get_coursemodule_from_id('customcert', $cert->id);

                // Set the custom certificate as viewed.
                $completion = new completion_info($course);
                $completion->set_module_viewed($cm, $userid);

                $isCertificate = $DB->record_exists('customcert', ['course'=>$courseid]);

                if ($isCertificate){
                    $url = generate_certificate($userid, $cert->id);
                    $issue = issued_date($cert->id, $userid);
                    array_push($certarr, ['url' => $url, 'name' => $cert->name, 'coursefullname' => $course->fullname, 'courseshortname' => $course->shortname, 'issueddate' => $issue->issuedtime]);
                }
            }
        }

        if ($certarr == NULL) {
            $message = get_string('nocertificate', 'local_vumi_webservicesuit');
        } else {
            $message = get_string('found', 'local_vumi_webservicesuit');
        }

        $response['certificateList'] = $certarr;
        $response['message'] = $message;

        return $response;
    }

    /**
     * @return external_single_structure
     */
    public static function get_certificate_list_returns () {
        return new external_single_structure(
            array(
                'certificateList' => new external_multiple_structure(self::add_custom_fields_in_response_structure(), 'certificate link', VALUE_OPTIONAL),
                'message' => new external_value(PARAM_TEXT, 'Max idle time config in seconds', VALUE_OPTIONAL),
            )
        );
    }

    private static function add_custom_fields_in_response_structure()
    {
        return new external_single_structure(
            array(
                'name' => new external_value(PARAM_TEXT, 'Certificate name'),
                'url' => new external_value(PARAM_TEXT, 'Download Url'),
                'coursefullname' => new external_value(PARAM_TEXT, 'Course full name'),
                'courseshortname' => new external_value(PARAM_TEXT, 'Course short name'),
                'issueddate' => new external_value(PARAM_TEXT, 'Certificate issued date'),
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
    public static function get_sample_customcert_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course ID'),
                'userid' => new external_value(PARAM_INT, 'User ID')
            )
        );
    }
    /**
     * Get Course completion status
     *
     * @param int $courseid ID of the Course
     * @param int $userid ID of the User
     * @return array of course completion status and warnings
     * @since Moodle 2.9
     * @throws moodle_exception
     */
    public static function get_sample_customcert($courseid, $userid) {
        global $CFG,$DB;

        require_once $CFG->dirroot.'/lib/completionlib.php';
        require_once $CFG->dirroot.'/completion/completion_aggregation.php';
        require_once $CFG->dirroot.'/completion/criteria/completion_criteria.php';
        require_once $CFG->dirroot.'/completion/completion_completion.php';
        require_once $CFG->dirroot.'/completion/completion_criteria_completion.php';

        $params = [
            'userid' => $userid,
            'courseid' => $courseid
        ];

        self::validate_parameters(self::get_sample_customcert_parameters(), $params);

        $certcmid = vumi_webservicesuit_get_cert_cmid($courseid);
        if ($certcmid) {
            $template = vumi_webservicesuit_get_certificate_sample($certcmid, $userid);
            return $template;
        } else {
            return "First add a certificate";
        }

    }
    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.9
     */
    public static function get_sample_customcert_returns() {
        return new external_value(PARAM_RAW, 'File if successful, false otherwise');
    }


    /**
     * @return external_function_parameters
     */
    public static function get_certificate_url_parameters() {
        return new external_function_parameters(
            array(
                'userid' =>
                    new external_value(
                        PARAM_INT,
                        'User id.'
                    ),
                'courseid' =>
                    new external_value(
                        PARAM_INT,
                        'Course id.'
                    )
            )
        );
    }

    /**
     * @param $userid
     * @param $courseid
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function get_certificate_url($userid, $courseid) {
        global $DB, $CFG;
        require_once $CFG->dirroot . '/lib/completionlib.php';
        require_once $CFG->dirroot . '/completion/completion_aggregation.php';
        require_once $CFG->dirroot . '/completion/criteria/completion_criteria.php';
        require_once $CFG->dirroot . '/completion/completion_completion.php';
        require_once $CFG->dirroot . '/completion/completion_criteria_completion.php';
        require_once($CFG->dirroot . '/local/vumi_webservicesuit/lib.php');

        // Parameter validation.
        self::validate_parameters(
            self::get_certificate_url_parameters(),
            array(
                'userid' => $userid,
                'courseid' => $courseid
            )
        );

        $certcmid = vumi_webservicesuit_get_cert_cmid($courseid);

        $course = $DB->get_record('course', ['id' => $courseid]);
        try {
            $percentage = progress::get_course_progress_percentage($course, $userid);
        } catch (Exception $e) {
            $percentage = $e;
        }

        if ($percentage == "100") {

            $customcert = $DB->get_record('customcert', ['course' => $courseid]);

            if (!$DB->record_exists('customcert_issues', array('userid' => $userid, 'customcertid' => $certcmid->id))) {
                \mod_customcert\certificate::issue_certificate($customcert->id, $userid);
            }
            $cm = get_coursemodule_from_id('customcert', $certcmid->id);

            //    Set the custom certificate as viewed.
            $completion = new completion_info($course);
            $completion->set_module_viewed($cm, $userid);

            $isCertificate = $DB->record_exists('customcert', ['course'=>$courseid]);

            if ($isCertificate) {
                $url = generate_certificate($userid, $certcmid->id);
            } else {
                $url = '';
            }

            $result['url'] = $url;
        }

        if ($certcmid == NULL) {
            $result['message'] = get_string('nocertificate', 'local_vumi_webservicesuit');
        } else {
            $result['message'] = get_string('found', 'local_vumi_webservicesuit');
        }

        return $result;
    }

    /**
     * @return external_single_structure
     */
    public static function get_certificate_url_returns () {
        return new external_single_structure(
            array(
                'message' => new external_value(PARAM_TEXT, 'message', VALUE_OPTIONAL),
                'url' => new external_value(PARAM_TEXT, 'certificate url', VALUE_OPTIONAL),
            )
        );
    }

    /**
     * @return external_function_parameters
     */
    public static function get_certificate_details_parameters() {
        return new external_function_parameters(
            array(
                'code' =>
                    new external_value(
                        PARAM_RAW,
                        'Varification Code'
                    )
            )
        );
    }

    /**
     * @param $code
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function get_certificate_details(string $code) {
        global $DB, $CFG;
        require_once $CFG->dirroot . '/lib/completionlib.php';
        require_once $CFG->dirroot . '/completion/completion_aggregation.php';
        require_once $CFG->dirroot . '/completion/criteria/completion_criteria.php';
        require_once $CFG->dirroot . '/completion/completion_completion.php';
        require_once $CFG->dirroot . '/completion/completion_criteria_completion.php';
        require_once($CFG->dirroot . '/local/vumi_webservicesuit/lib.php');

        // Parameter validation.
        $params = self::validate_parameters(self::get_certificate_details_parameters(),
            array(
                'code' => $code
            ));

        $SQL = "SELECT MCI.userid, MCI.customcertid 
                FROM {customcert_issues} MCI
                WHERE code = '".$code."'";

        $record_found = $DB->get_record_sql($SQL, ['code' => $code]);

        if($record_found!=null) {
            $data = check_cert_exist($record_found->customcertid, $code);
            if($data) {
                $result['coursefullname'] = $data->fullname;
                $result['courseshortname'] = $data->shortname;
                $result['courseid'] = $data->id;

                $certcmid = vumi_webservicesuit_get_cert_cmid($data->id);

                $url = generate_certificate($record_found->userid, $certcmid->id);
                $result['url'] = $url;
                $result['message'] = get_string('found', 'local_vumi_webservicesuit');
            } else {
                $result['message'] = get_string('foundnocourse', 'local_vumi_webservicesuit');
            }
        } else {
            $result['message'] = get_string('nocertificate', 'local_vumi_webservicesuit');
        }
        return $result;
    }

    /**
     * @return external_single_structure
     */
    public static function get_certificate_details_returns () {
        return new external_single_structure(
            array(
                'url' => new external_value(PARAM_TEXT, 'certificate url', VALUE_OPTIONAL),
                'coursefullname' => new external_value(PARAM_TEXT, 'course full name', VALUE_OPTIONAL),
                'courseshortname' => new external_value(PARAM_TEXT, 'course short name', VALUE_OPTIONAL),
                'courseid' => new external_value(PARAM_TEXT, 'courseid', VALUE_OPTIONAL),
                'message' => new external_value(PARAM_TEXT, 'Message', VALUE_REQUIRED),
            )
        );
    }

    /**
     * @return external_function_parameters
     */
    public static function get_course_ratings_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course Id', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * @param $courseid
     * @return void
     */
    public static function get_course_ratings($courseid) {
        global $DB;
        self::validate_parameters (
            self::get_course_ratings_parameters(),
            array (
                'courseid' => $courseid
            )
        );

        $courserating = $DB->get_record('tool_courserating_summary', ['courseid' => $courseid]);
        $ratingcomments = $DB->get_records('tool_courserating_rating', ['courseid' => $courseid], '', 'id, review');

        if ($courserating == NULL) {
            $result['rating'] = get_string('noratings', 'local_vumi_webservicesuit');
            $result['message'] = get_string('nocomments', 'local_vumi_webservicesuit');
        } else {
            $result['rating'] = $courserating->avgrating;

            foreach ($ratingcomments as $comments) {
                if ($comments->review != NULL) {
                    $result['comments'] = $ratingcomments;
                }
            }

            if ($result['comments'] == NULL) {
                $result['message'] = get_string('nocomments', 'local_vumi_webservicesuit');
            }
        }
        return $result;
    }

    public static function get_course_ratings_returns () {
        return new external_single_structure(
            array(
                'rating' => new external_value(PARAM_RAW, 'Ratings', VALUE_OPTIONAL),
                'message' => new external_value(PARAM_RAW, 'If no Comments are available', VALUE_OPTIONAL),
                'comments' =>  new external_multiple_structure(self::get_all_comments(),'comments of rating', VALUE_OPTIONAL)
            )
        );
    }

    static function get_all_comments () {
        return new external_single_structure(
            array(
                'review' => new external_value(PARAM_RAW, 'Rating Comments', VALUE_OPTIONAL)
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function zoom_get_state_parameters() {
        return new external_function_parameters(
            array(
                'zoomid' => new external_value(PARAM_INT, 'zoom course module id')
            )
        );
    }

    /**
     * Determine if a zoom meeting is available, meeting status, and the start time, duration, and other meeting options.
     * This function grabs most of the options to display for users in /mod/zoom/view.php
     * Host functions are not currently supported
     *
     * @param int $zoomid the zoom course module id
     * @return array of warnings and status result
     * @since Moodle 3.1
     * @throws moodle_exception
     */
    public static function zoom_get_state($zoomid) {
        global $DB, $CFG;
        require_once($CFG->dirroot . "/mod/zoom/locallib.php");

        $params = self::validate_parameters(self::zoom_get_state_parameters(),
            array(
                'zoomid' => $zoomid
            ));
        $warnings = array();

        // Request and permission validation.
        $cm = $DB->get_record('course_modules', array('id' => $params['zoomid']), '*', MUST_EXIST);
        $zoom  = $DB->get_record('zoom', array('id' => $cm->instance), '*', MUST_EXIST);

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        require_capability('mod/zoom:view', $context);

        // Call the zoom/locallib API.
        list($inprogress, $available, $finished) = zoom_get_state($zoom);

        $result = array();
        $result['available'] = $available;

        if ($zoom->recurring) {
            $result['start_time'] = 0;
            $result['duration'] = 0;
        } else {
            $result['start_time'] = $zoom->start_time;
            $result['duration'] = $zoom->duration;
        }
        $result['name'] = $zoom->name;
        $result['intro'] = $zoom->intro;
        $result['haspassword'] = (isset($zoom->password) && $zoom->password !== '');
        $result['joinbeforehost'] = $zoom->option_jbh;
        $result['startvideohost'] = $zoom->option_host_video;
        $result['startvideopart'] = $zoom->option_participants_video;
        $result['audioopt'] = $zoom->option_audio;

        if (!$zoom->recurring) {
            if ($zoom->exists_on_zoom == ZOOM_MEETING_EXPIRED) {
                $status = get_string('meeting_nonexistent_on_zoom', 'mod_zoom');
            } else if ($finished) {
                $status = get_string('meeting_finished', 'mod_zoom');
            } else if ($inprogress) {
                $status = get_string('meeting_started', 'mod_zoom');
            } else {
                $status = get_string('meeting_not_started', 'mod_zoom');
            }
        } else {
            $status = get_string('recurringmeetinglong', 'mod_zoom');
        }
        $result['status'] = $status;

        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.1
     */
    public static function zoom_get_state_returns() {
        return new external_single_structure(
            array(
                'name' => new external_value(PARAM_RAW, 'Name of zoom activity'),
                'intro' => new external_value(PARAM_RAW, 'Description or intro of the zoom activity'),
                'available' => new external_value(PARAM_BOOL, 'if true, run grade_item_update and redirect to meeting url'),

                'start_time' => new external_value(PARAM_INT, 'meeting start time as unix timestamp (0 if recurring)'),
                'duration' => new external_value(PARAM_INT, 'meeting duration in seconds (0 if recurring)'),

                'haspassword' => new external_value(PARAM_BOOL, ''),
                'joinbeforehost' => new external_value(PARAM_BOOL, ''),
                'startvideohost' => new external_value(PARAM_BOOL, ''),
                'startvideopart' => new external_value(PARAM_BOOL, ''),
                'audioopt' => new external_value(PARAM_TEXT, ''),

                'status' => new external_value(PARAM_TEXT, 'meeting status: not_started, started, finished, expired, recurring'),

                'warnings' => new external_warnings()
            )
        );
    }
}