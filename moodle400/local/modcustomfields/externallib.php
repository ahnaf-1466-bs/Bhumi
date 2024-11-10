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
 * @subpackage local_modcustomfields
 * @author     Brain station 23 ltd <brainstation-23.com>
 * @copyright  2023 Brain station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");

class local_modcustomfields_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
    public static function get_customfields_details_parameters () {
        return new external_function_parameters(
            array(
                'cmid' => new external_value(PARAM_INT, 'Cmid'),
            )
        );
    }

    /**
     * Get Custom Field Details
     *
     * @param int $courseid ID of the Course
     * @param int $cmid ID of the User
     * @since Moodle 2.9
     * @throws moodle_exception
     */
    public static function get_customfields_details ($cmid) {
        global $DB, $CFG;
        $arrayparams = array(
            'cmid' => $cmid
        );
        $params = self::validate_parameters(self::get_customfields_details_parameters(), $arrayparams);

        $sql = "SELECT cd.id, cc.name as category_name,
                    JSON_ARRAYAGG(JSON_OBJECT('field_name', cf.shortname, 'field_value', cd.value)) as category_details
                FROM {customfield_data} cd
                JOIN {customfield_field} cf ON cd.fieldid = cf.id
                JOIN {customfield_category} cc ON cf.categoryid = cc.id
                WHERE cd.instanceid= " .$cmid." AND cc.component = 'local_modcustomfields'
                GROUP BY cc.name";
        $results = $DB->get_records_sql($sql);

        foreach ($results as $resultItem) {
            if ($resultItem) {
                $a = $resultItem->category_details;
                $temp = json_decode($a);
                $resultItem->category_details = $temp;
            }
        }
        if ($results != NULL) {
//            $result['results'] = $results;
            // Check if any result contains non-NULL values
            $allNull = true;
            foreach ($results as $resultItem) {
                if ($resultItem->id !== null || $resultItem->category_name !== null || $resultItem->category_details !== null) {
                    $allNull = false;
                    break;
                }
            }
            // If at least one result contains non-NULL values, process and return the results
            if (!$allNull) {
                $result['results'] = $results;
            }
        }
        else {
            $message = "No Data Found";
            $result['message'] = $message;
        }

        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_single_structure
     * @since Moodle 2.9
     */
    public static function get_customfields_details_returns () {
        return new external_single_structure(
            array(
                'results' => new external_multiple_structure(self::get_all_customfields_details(), 'customfields details', VALUE_OPTIONAL),
                'message' => new external_value(PARAM_TEXT, 'message', VALUE_OPTIONAL),
            )
        );
    }

    static function get_all_customfields_details(){
        return new external_single_structure(
            array(
                'category_name' => new external_value(PARAM_RAW, 'category name', VALUE_OPTIONAL),
                'category_details' => new external_multiple_structure(self::get_category_details(), 'category details', VALUE_OPTIONAL),
            )
        );
    }

    static function get_category_details() {
        return new external_single_structure(
            array(
                'field_name' => new external_value(PARAM_RAW, 'field name', VALUE_OPTIONAL),
                'field_value' => new external_value(PARAM_RAW, 'field value', VALUE_OPTIONAL),
            )
        );
    }

}