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
 * External web service functions.
 *
 * @package    local_preregistration
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * local_preregistration_external class for API.
 *
 * @package    local_preregistration
 * @copyright  2023 Brain Station 23 Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class local_preregistration_external extends external_api {
    /**
     * Returns description of method get parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function get_batch_data_by_courseid_parameters() {
        return new external_function_parameters(
            array (
                'courseid' => new external_value(PARAM_INT, 'Course ID')
            )
        );
    }

    /**
     * Get latest discount coupon expiry time.
     *
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function get_batch_data_by_courseid($courseid) {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/local/preregistration/lib.php");
        require_once($CFG->dirroot . '/mod/syllabusoverview/lib.php');
        
        $statuscode = 200;
        $message = 'Data Found';
        $data = [];

        // To Do: Need to restrict the admin to create multiple active batch at a time.
        $batchinfo = $DB->get_record('local_preregistration_batch', array('courseid' => $courseid, 'active' => 1));
        if($batchinfo) {

            $batchid = $batchinfo->id;
            $data['batchid'] = $batchid;
            $programdates = local_preregistration_get_programdates($batchid);
            $allprogramdates = [];
            foreach($programdates as $programdate) {
                $dateobj = new stdClass();
                $dateobj->programdate = $programdate['value'];

                array_push($allprogramdates, $dateobj);
            }
            $data['all_program_dates'] = $allprogramdates;

            $deadline = local_preregistration_get_data_by_type($batchid, 'deadline');
            $cost = local_preregistration_get_data_by_type($batchid, 'cost');
            $courselength = local_preregistration_get_data_by_type($batchid, 'courselength');
            $programpdf = local_preregistration_get_data_by_type($batchid, 'programpdf');
            if($programpdf) {
                $data['programpdf'] = $programpdf->value;
            } else {
                $program_details =  get_program_details($courseid) ;
                $data['programpdf'] = $program_details->programpdf;
            }
            

            $data['fee'] = $cost ? $cost->value : null;
            $data['length'] = $courselength ? $courselength->value : null;
            $data['deadline'] = $deadline ? local_preregistration_convert_to_date($deadline->value) : null;
        } else {
            $message = "There is no active next batch data";
            $statuscode = 4001; // Data not found!
        }
        
        $data['message'] = $message;
        $data['statuscode'] = $statuscode;
        return $data;
        
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function get_batch_data_by_courseid_returns() {
        return new external_single_structure(
            array(
                'statuscode' => new external_value(PARAM_INT, 'Status code', VALUE_OPTIONAL),
                'batchid' => new external_value(PARAM_INT, 'batch id', VALUE_OPTIONAL),
                'programpdf' => new external_value(PARAM_RAW, 'program pdf', VALUE_OPTIONAL),
                'deadline' => new external_value(PARAM_RAW, 'deadline', VALUE_OPTIONAL),
                'length' => new external_value(PARAM_RAW, 'length', VALUE_OPTIONAL),
                'fee' => new external_value(PARAM_RAW, 'fee', VALUE_OPTIONAL),
                'all_program_dates' => new external_multiple_structure(self::get_all_program_dt(), 'program date and time', VALUE_OPTIONAL),
                'message' => new external_value(PARAM_TEXT, 'message', VALUE_OPTIONAL),
            )
        );
    }

    static function get_all_program_dt(){
        return new external_single_structure(
            array(
                'programdate' => new external_value(PARAM_RAW, 'program date', VALUE_OPTIONAL)
            )
        );
    }

        /**
     * Returns description of method get parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function add_user_to_batch_parameters() {
        return new external_function_parameters(
            array (
                'batchid' => new external_value(PARAM_INT, 'Batch ID'),
                'userid' => new external_value(PARAM_INT, 'User ID'),
                'name' => new external_value(PARAM_RAW, 'Name'),
                'email' => new external_value(PARAM_RAW, 'Email')
            )
        );
    }

    /**
     * Register user to a batch and sends an email.
     *
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function add_user_to_batch($batchid, $userid, $name, $email) {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/local/preregistration/lib.php");
        
        $statuscode = 200;
        $message = 'Data saved';
        $data = [];

        // To Do: Need to restrict the admin to create multiple active batch at a time.
        $batchinfo = $DB->get_record('local_preregistration_batch', array('id' => $batchid, 'active' => 1));
        
        if($batchinfo) {

            $batchid = $batchinfo->id;
            $courseid = $batchinfo->courseid;

            $record = new stdClass();
            $record = $DB->get_record('local_preregistration_users', array('batchid' => $batchid, 'userid' => $userid));
            if($record) {
                $message = "The user has already registered for this batch";
                $statuscode = 4000; // User already registered.
            } else {
                $record->batchid = $batchid;
                $record->courseid = $courseid;
                $record->userid = $userid;
                $record->name = $name;
                $record->email = $email;
                $record->timecreated = time();
                $record->timemodified = time();

                $DB->insert_record('local_preregistration_users', $record);

                local_preregistration_send_email($userid, '', $courseid);

            }
        } else {
            $message = "There is no active next batch data";
            $statuscode = 4001; // Data was not saved.
        }
        
        $data['message'] = $message;
        $data['statuscode'] = $statuscode;
        return $data;
        
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function add_user_to_batch_returns() {
        return new external_single_structure(
            array(
                'statuscode' => new external_value(PARAM_INT, 'Status code', VALUE_OPTIONAL),
                'message' => new external_value(PARAM_TEXT, 'message', VALUE_OPTIONAL),
            )
        );
    }
    
}
