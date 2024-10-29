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
 * syllabusoverview external API
 *
 * @package    mod_syllabusoverview
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * syllabusoverview external functions
 *
 * @package    mod_syllabusoverview
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_syllabusoverview_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function view_syllabusoverview_parameters() {
        return new external_function_parameters(
            array(
                'syllabusoverviewid' => new external_value(PARAM_INT, 'syllabusoverview instance id')
            )
        );
    }

    /**
     * Simulate the syllabusoverview/view.php web interface syllabusoverview: trigger events, completion, etc...
     *
     * @param int $syllabusoverviewid the syllabusoverview instance id
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function view_syllabusoverview($syllabusoverviewid) {
        global $DB, $CFG;
        require_once($CFG->dirroot . "/mod/syllabusoverview/lib.php");

        $params = self::validate_parameters(self::view_syllabusoverview_parameters(),
                                            array(
                                                'syllabusoverviewid' => $syllabusoverviewid
                                            ));
        $warnings = array();

        // Request and permission validation.
        $syllabusoverview = $DB->get_record('syllabusoverview', array('id' => $params['syllabusoverviewid']), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($syllabusoverview, 'syllabusoverview');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        require_capability('mod/syllabusoverview:view', $context);

        // Call the syllabusoverview/lib API.
        syllabusoverview_view($syllabusoverview, $course, $cm, $context);

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
    public static function view_syllabusoverview_returns() {
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
    public static function get_syllabusoverview_by_courseid_parameters() {
        return new external_function_parameters (
            array(
                'courseid' => new external_value(PARAM_INT, 'course id')

            )
        );
    }

    /**
     * Returns a list of syllabusoverviews data by providing course id.
     *
     * @param $courseid
     * @return array of warnings and syllabusoverviews
     * @since Moodle 3.3
     */
    public static function get_syllabusoverview_by_courseid($courseid) {

        global $DB, $CFG;
        require_once($CFG->dirroot . '/mod/syllabusoverview/lib.php');

        self::validate_parameters(
            self::get_syllabusoverview_by_courseid_parameters(),
            array(
                'courseid' => $courseid
            )
        );

        $features = get_feature($courseid);
        $benefits =get_benefits($courseid);
        $course_image = get_course_image($courseid);

        $description = get_description($courseid);
        $learn = get_learn($courseid);

        $program_details =  get_program_title_and_data($courseid);
        $program_name = get_program_name($courseid);
        $program_dt = get_program_dt($courseid);
        $program_pdf = get_program_pdf($courseid);


        $syll_url = get_syll_url($courseid);
        $syll_details = get_syll_details($courseid);

        if($features != NULL || $benefits != NULL
            || $description != NULL || $learn != NULL
            || $program_details != NULL || $program_dt != NULL
            || $syll_url != NULL || $syll_details != NULL || $course_image != NULL || $program_name != NULL
            || $program_pdf != NULL ) {

            $result['features'] = $features;
            $result['benefits'] = $benefits;
            $result['description'] = $description;
            $result['learn'] = $learn;
            $result['syllabuspdf'] = $syll_url->syllabuspdf;
            $result['programname'] = $program_name;
            $result['all_program_dates'] = $program_dt;
            $result['programpdf'] = $program_pdf;
            $result['programdetails'] = $program_details;
            $result['syllabus_details'] = $syll_details;
            $result['course_image'] = $course_image;

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
    public static function get_syllabusoverview_by_courseid_returns() {
        return new external_single_structure(
            array(
                'features' => new external_multiple_structure(self::get_all_features(), 'feature', VALUE_OPTIONAL),
                'benefits' => new external_multiple_structure(self::get_all_benefits(), 'benefit', VALUE_OPTIONAL),
                'description' => new external_multiple_structure(self::get_all_description(), 'description', VALUE_OPTIONAL),
                'learn' => new external_multiple_structure(self::get_all_learn(), 'learn', VALUE_OPTIONAL),
                'programname' => new external_multiple_structure(self::get_all_programname(), 'program name', VALUE_OPTIONAL),
                'all_program_dates' => new external_multiple_structure(self::get_all_program_dt(), 'program date and time', VALUE_OPTIONAL),
                'programpdf' => new external_value(PARAM_RAW, 'program pdf', VALUE_OPTIONAL),
                'programdetails' => new external_multiple_structure(self::get_all_program_details(), 'program details', VALUE_OPTIONAL),
                'syllabuspdf' => new external_value(PARAM_RAW, 'syllabus pdf', VALUE_OPTIONAL),
                'syllabus_details' => new external_multiple_structure(self::get_all_syll_details(), 'syllabus details', VALUE_OPTIONAL),
                'course_image' => new external_multiple_structure(self::get_courseimage(), 'course image', VALUE_OPTIONAL),
                'message' => new external_value(PARAM_TEXT, 'message', VALUE_OPTIONAL),
            )
        );
    }



    static function get_all_features(){
        return new external_single_structure(
            array(
                'feature_heading' => new external_value(PARAM_RAW, 'feature heading', VALUE_OPTIONAL),
                'feature_heading_bangla' => new external_value(PARAM_RAW, 'feature heading bangla', VALUE_OPTIONAL),
                'feature_pic' => new external_value(PARAM_RAW, 'feature pic', VALUE_OPTIONAL),
            )
        );
    }

    static function get_all_benefits(){
        return new external_single_structure(
            array(
                'beneficiary_name' => new external_value(PARAM_RAW, 'beneficiary name', VALUE_OPTIONAL),
                'beneficiary_name_bangla' => new external_value(PARAM_RAW, 'beneficiary name bangla', VALUE_OPTIONAL),
                'beneficiary_image' => new external_value(PARAM_RAW, 'beneficiary image', VALUE_OPTIONAL),
            )
        );
    }
    static function get_all_description(){
        return new external_single_structure(
            array(
                'short_description' => new external_value(PARAM_RAW, 'short description', VALUE_OPTIONAL),
                'short_description_bangla' => new external_value(PARAM_RAW, 'short description bangla', VALUE_OPTIONAL),
                'description_url' => new external_value(PARAM_RAW, 'description url', VALUE_OPTIONAL),
                'file_url' => new external_value(PARAM_RAW, 'file url', VALUE_OPTIONAL),
               )
        );
    }

    static function get_courseimage(){
        return new external_single_structure(
            array(
                'courseimg' => new external_value(PARAM_RAW, 'course image', VALUE_OPTIONAL),
            )
        );
    }

    static function get_all_learn(){
        return new external_single_structure(
            array(
                'learning' => new external_value(PARAM_RAW, 'learning name', VALUE_OPTIONAL),
                'learning_bangla' => new external_value(PARAM_RAW, 'learning name in bangla', VALUE_OPTIONAL),
               )
        );
    }

    static function get_all_programname(){
        return new external_single_structure(
            array(
                'name' => new external_value(PARAM_RAW, 'program name', VALUE_OPTIONAL),
                'name_bangla' => new external_value(PARAM_RAW, 'program name bangla', VALUE_OPTIONAL)
            )
        );
    }

    static function get_all_program_dt(){
        return new external_single_structure(
            array(
                'programdate' => new external_value(PARAM_RAW, 'program date', VALUE_OPTIONAL),
                'programdate_bangla' => new external_value(PARAM_RAW, 'program date bangla', VALUE_OPTIONAL)
            )
        );
    }
    static function get_all_syll_details(){
        return new external_single_structure(
            array(
                'syllabusheading' => new external_value(PARAM_RAW, 'syllabus heading', VALUE_OPTIONAL),
                'syllabusheading_bangla' => new external_value(PARAM_RAW, 'syllabus heading bangla', VALUE_OPTIONAL),
                'syllabusbody' => new external_value(PARAM_RAW, 'syllabus body', VALUE_OPTIONAL),
                'syllabusbody_bangla' => new external_value(PARAM_RAW, 'syllabus body bangla', VALUE_OPTIONAL),
            )
        );
    }

    static function get_all_program_details(){
        return new external_single_structure(
            array(
                'name' => new external_value(PARAM_RAW, 'program details name', VALUE_OPTIONAL),
                'name_bangla' => new external_value(PARAM_RAW, 'program details name bangla', VALUE_OPTIONAL),
                'value' => new external_value(PARAM_RAW, 'program details value', VALUE_OPTIONAL),
                'value_bangla' => new external_value(PARAM_RAW, 'program details value bangla', VALUE_OPTIONAL),
            )
        );
    }
}
