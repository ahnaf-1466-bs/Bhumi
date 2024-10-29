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
 * videoplus external API
 *
 * @package    mod_videoplus
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * videoplus external functions
 *
 * @package    mod_videoplus
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_videoplus_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function view_videoplus_parameters() {
        return new external_function_parameters(
            array(
                'videoplusid' => new external_value(PARAM_INT, 'videoplus instance id')
            )
        );
    }

    /**
     * Simulate the videoplus/view.php web interface videoplus: trigger events, completion, etc...
     *
     * @param int $videoplusid the videoplus instance id
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function view_videoplus($videoplusid) {
        global $DB, $CFG;
        require_once($CFG->dirroot . "/mod/videoplus/lib.php");

        $params = self::validate_parameters(self::view_videoplus_parameters(),
                                            array(
                                                'videoplusid' => $videoplusid
                                            ));
        $warnings = array();

        // Request and permission validation.
        $videoplus = $DB->get_record('videoplus', array('id' => $params['videoplusid']), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($videoplus, 'videoplus');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        require_capability('mod/videoplus:view', $context);

        // Call the videoplus/lib API.
        videoplus_view($videoplus, $course, $cm, $context);

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
    public static function view_videoplus_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for get_syllabusoverviews_by_courses.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_details_parameters() {
        return new external_function_parameters (
            array(
                'courseid' => new external_value(PARAM_INT, 'course id'),
                'cmid'     => new external_value(PARAM_INT, 'cm id'),
            )
        );
    }

    /**
     * Returns a list of videoplus data by providing course id.
     *
     * @param $courseid
     * @param $cmid
     * @return array of warnings and videoplus
     * @since Moodle 3.3
     */
    public static function get_details($courseid, $cmid) {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/mod/videoplus/lib.php');

        self::validate_parameters(
            self::get_details_parameters(),
            array(
                'courseid' => $courseid,
                'cmid' => $cmid,
            )
        );

        $details = videoplus_get_video_and_pdf($courseid, $cmid);

        if ($details != NULL) {
            $result['details'] = $details;
        }
        else {
            $message = "No Data Found";
            $result['message'] = $message;
        }

        return $result;
    }

    /**
     * Describes the get_syllabusoverviews_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_details_returns() {
        return new external_single_structure(
            array(
                'details' => new external_multiple_structure(self::get_all_details(), 'feature', VALUE_OPTIONAL),
                'message' => new external_value(PARAM_TEXT, 'message', VALUE_OPTIONAL),
            )
        );
    }

    static function get_all_details() {
        return new external_single_structure(
            array(
                'name' => new external_value(PARAM_RAW, 'name', VALUE_OPTIONAL),
                'intro' => new external_value(PARAM_RAW, 'description', VALUE_OPTIONAL),
                'videourl' => new external_value(PARAM_RAW, 'Video Url', VALUE_OPTIONAL),
                'pdfurl' => new external_value(PARAM_RAW, 'PDF Url', VALUE_OPTIONAL),
            )
        );
    }
}
