<?php
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
 * External Web Service Template
 * @package local
 * @subpackage local_book_seat
 * @author     Brain station 23 ltd <brainstation-23.com>
 * @copyright  2023 Brain station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");

class local_book_seat_external extends external_api
{

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
    public static function store_userinfo_parameters()
    {
        return new external_function_parameters(
            array(
                'name' => new external_value(PARAM_RAW, 'name', VALUE_DEFAULT, ''),
                'email' => new external_value(PARAM_RAW, 'email', VALUE_DEFAULT, ''),
                'phone' => new external_value(PARAM_RAW, 'phone', VALUE_DEFAULT, ''),
            )
        );
    }

    /**
     * Get Custom Field Details
     *
     * @param int $courseid ID of the Course
     * @param int $cmid ID of the User
     * @throws moodle_exception
     * @since Moodle 2.9
     */
    public static function store_userinfo($name, $email, $phone)
    {
        global $DB, $CFG;
        $arrayparams = array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        );
        $params = self::validate_parameters(self::store_userinfo_parameters(), $arrayparams);

        // Initialize result object
        $result = new stdClass();
        $result->status = false;
        $result->message = '';

        try {
            $record = new stdClass();
            $record->username = $name;
            $record->email = $email;
            $record->phone = $phone;
            $record->timecreated = time();

            $DB->insert_record('local_book_seat_userinfo', $record);

            // Set success message
            $result->status = true;
            $result->message = 'Userinfo stored successfully.';
        } catch (Exception $e) {
            // Set error message
            $result->message = 'Error: ' . $e->getMessage();
        }

        return $result;

    }

    /**
     * Returns description of method result value
     *
     * @return external_single_structure
     * @since Moodle 2.9
     */
    public static function store_userinfo_returns()
    {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'message' => new external_value(PARAM_TEXT, 'Error message if any.'),
                'warnings' => new external_warnings()
            )
        );
    }

    public static function get_book_seat_info_parameters()
    {
        return new external_function_parameters(
            array(
            )
        );
    }

    public static function get_book_seat_info () {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/local/book_seat/lib.php');

        $records = $DB->get_records('local_book_seat_userinfo');

        $result = array();

        if ($records) {
            $result['records'] = $records;
        }
        else {
            $message = "No Data Found";
            $result['message'] = $message;
        }
        return $result;
    }

    public static function get_book_seat_info_returns() {
        return new external_single_structure(
            array(
                'records' => new external_multiple_structure(self::get_all_records(), 'feature', VALUE_OPTIONAL),
                'message' => new external_value(PARAM_BOOL, 'message', VALUE_OPTIONAL),
            )
        );
    }

    static function get_all_records() {
        return new external_single_structure(
            array(
                'username' => new external_value(PARAM_RAW, 'User Name', VALUE_OPTIONAL),
                'email' => new external_value(PARAM_RAW, 'User Email', VALUE_OPTIONAL),
                'phone' => new external_value(PARAM_RAW, 'User Phone', VALUE_OPTIONAL),
            )
        );
    }
}