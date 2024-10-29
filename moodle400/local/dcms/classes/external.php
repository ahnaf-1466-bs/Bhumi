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
 * dcms external API
 *
 * @package    local_dcms
 * @category   external
 * @copyright  2023 Brain Station 23 Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * dcms external functions
 *
 * @package    local_dcms
 * @category   external
 * @copyright  2023 Brain Station 23 Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class local_dcms_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function get_homepage_contents_parameters() {
        return new external_function_parameters(
            array ()
        );
    }

    /**
     * Simulate the dcms/view.php web interface dcms: trigger events, completion, etc...
     *
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function get_homepage_contents() {
        global $CFG;
        require_once($CFG->dirroot . "/local/dcms/locallib.php");

        $homepage = homepage_contents();
        $result = [];
        $result['siteintro'] = $homepage->siteintro;
        $result['feedbacks'] = $homepage->feedback;
        $result['partners'] = $homepage->partner;

        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function get_homepage_contents_returns() {
        return new external_single_structure(
            array(
                'siteintro' => new external_multiple_structure(self::get_all_siteintro(),'site intro',VALUE_OPTIONAL),
                'feedbacks' => new external_multiple_structure(self::get_all_feedback(),'feedback',VALUE_OPTIONAL),
                'partners' => new external_multiple_structure(self::get_all_partner(),'partner',VALUE_OPTIONAL),
            )
        );
    }
    static function get_all_siteintro (){
        return new external_single_structure(
            array(
                'siteintro' => new external_value(PARAM_RAW, 'siteintro', VALUE_OPTIONAL),
                'siteintro_bn' => new external_value(PARAM_RAW, 'siteintro bangla', VALUE_OPTIONAL),
            )
        );
    }
    static function get_all_partner (){
        return new external_single_structure(
            array(
                'partnername' => new external_value(PARAM_RAW, 'partner name', VALUE_OPTIONAL),
                'partnername_bn' => new external_value(PARAM_RAW, 'partner name', VALUE_OPTIONAL),
                'picurl' => new external_value(PARAM_RAW, 'picurl', VALUE_OPTIONAL)
            )
        );
    }

    static function get_all_feedback (){
        return new external_single_structure(
            array(
                'feedbackname' => new external_value(PARAM_RAW, 'feedback name', VALUE_OPTIONAL),
                'position' => new external_value(PARAM_RAW, 'position', VALUE_OPTIONAL),
                'company' => new external_value(PARAM_RAW, 'company', VALUE_OPTIONAL),
                'subject' => new external_value(PARAM_RAW, 'subject', VALUE_OPTIONAL),
                'feedbacktext' => new external_value(PARAM_RAW, 'feedbacktext', VALUE_OPTIONAL),
                'feedbackname_bn' => new external_value(PARAM_RAW, 'feedback name', VALUE_OPTIONAL),
                'position_bn' => new external_value(PARAM_RAW, 'position', VALUE_OPTIONAL),
                'company_bn' => new external_value(PARAM_RAW, 'company', VALUE_OPTIONAL),
                'subject_bn' => new external_value(PARAM_RAW, 'subject', VALUE_OPTIONAL),
                'feedbacktext_bn' => new external_value(PARAM_RAW, 'feedbacktext', VALUE_OPTIONAL),
                'picurl' => new external_value(PARAM_RAW, 'picurl', VALUE_OPTIONAL)
            )
        );
    }

    // About Page API.
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function get_aboutpage_contents_parameters() {
        return new external_function_parameters(
            array ()
        );
    }

    /**
     * Simulate the dcms/view.php web interface dcms: trigger events, completion, etc...
     *
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function get_aboutpage_contents() {
        global $DB, $CFG;
        require_once($CFG->dirroot . "/local/dcms/locallib.php");

        $aboutpage = aboutpage_contents();

        $result = [];
        $result['ourstory'] = $aboutpage->ourstory->ourstory;
        $result['ourstory_bn'] = $aboutpage->ourstory_bn->ourstory_bn;
        $result['whyvumi'] = $aboutpage->whyvumi->whyvumitext;
        $result['whyvumi_bn'] = $aboutpage->whyvumi_bn->whyvumitext_bn;
        $result['vision'] = $aboutpage->vision->vision;
        $result['vision_bn'] = $aboutpage->vision_bn->vision_bn;
        $result['whoisvumifor'] = $aboutpage->vumifor;
        $result['ourstrength'] = $aboutpage->strength;

        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function get_aboutpage_contents_returns() {
        return new external_single_structure(
            array(
                'ourstory' => new external_value(PARAM_TEXT, 'our story', VALUE_OPTIONAL),
                'ourstory_bn' => new external_value(PARAM_TEXT, 'our story bangla', VALUE_OPTIONAL),
                'vision' => new external_value(PARAM_TEXT, 'why vumi', VALUE_OPTIONAL),
                'vision_bn' => new external_value(PARAM_TEXT, 'vision bangla', VALUE_OPTIONAL),
                'whyvumi' => new external_value(PARAM_RAW, 'why vumi', VALUE_OPTIONAL),
                'whyvumi_bn' => new external_value(PARAM_RAW, 'why vumi bangla', VALUE_OPTIONAL),
                'whoisvumifor' => new external_multiple_structure(self::get_all_vumifor(),'dcms',VALUE_OPTIONAL),
                'ourstrength' => new external_multiple_structure(self::get_all_strengths(),'strengths',VALUE_OPTIONAL),
            )
        );
    }

    static function get_all_vumifor () {
        return new external_single_structure(
            array(
                'vumiforname' => new external_value(PARAM_RAW, 'vumifor name', VALUE_OPTIONAL),
                'vumiforname_bn' => new external_value(PARAM_RAW, 'vumifor name bangla', VALUE_OPTIONAL),
                'picurl' => new external_value(PARAM_RAW, 'news pic url', VALUE_OPTIONAL)
            )
        );
    }

    static function get_all_strengths () {
        return new external_single_structure(
            array(
                'strengthname' => new external_value(PARAM_RAW, 'strength name', VALUE_OPTIONAL),
                'strengthname_bn' => new external_value(PARAM_RAW, 'strength name', VALUE_OPTIONAL),
                'strengthbody' => new external_value(PARAM_RAW, 'strength body', VALUE_OPTIONAL),
                'strengthbody_bn' => new external_value(PARAM_RAW, 'strength body', VALUE_OPTIONAL)
            )
        );
    }

    // Our Team Page API.
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function get_ourteampage_contents_parameters() {
        return new external_function_parameters(
            array ()
        );
    }

    /**
     * Simulate the dcms/view.php web interface dcms: trigger events, completion, etc...
     *
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function get_ourteampage_contents () {
        global $DB, $CFG;
        require_once($CFG->dirroot . "/local/dcms/locallib.php");

        $ourteam = outteampage_contents();

        $result = [];
        $result['directorlist'] = $ourteam->director;
        $result['founderlist'] = $ourteam->founder;
        $result['instructorlist'] = $ourteam->instructor;
        $result['operationteamlist'] = $ourteam->operation;

        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function get_ourteampage_contents_returns() {
        return new external_single_structure(
            array(
                'directorlist' => new external_multiple_structure(self::get_all_directors(),'director',VALUE_OPTIONAL),
                'founderlist' => new external_multiple_structure(self::get_all_founders(),'founder',VALUE_OPTIONAL),
                'instructorlist' => new external_multiple_structure(self::get_all_instructors(),'instructor',VALUE_OPTIONAL),
                'operationteamlist' => new external_multiple_structure(self::get_all_operations(),'operation',VALUE_OPTIONAL)
            )
        );
    }

    static function get_all_directors () {
        return new external_single_structure(
            array(
                'directorname' => new external_value(PARAM_RAW, 'director name', VALUE_OPTIONAL),
                'directorname_bn' => new external_value(PARAM_RAW, 'director name bangla', VALUE_OPTIONAL),
                'directordeg' => new external_value(PARAM_RAW, 'director designation', VALUE_OPTIONAL),
                'directordeg_bn' => new external_value(PARAM_RAW, 'director designation bangla', VALUE_OPTIONAL),
                'picurl' => new external_value(PARAM_RAW, 'pic url', VALUE_OPTIONAL),
                'email' => new external_value(PARAM_RAW, 'email', VALUE_OPTIONAL),
                'tier' => new external_value(PARAM_RAW, 'tier', VALUE_OPTIONAL),
            )
        );
    }

    static function get_all_founders () {
        return new external_single_structure(
            array(
                'foundername' => new external_value(PARAM_RAW, 'founder name', VALUE_OPTIONAL),
                'foundername_bn' => new external_value(PARAM_RAW, 'founder name bangla', VALUE_OPTIONAL),
                'founderdeg' => new external_value(PARAM_RAW, 'founder designation bangla', VALUE_OPTIONAL),
                'founderdeg_bn' => new external_value(PARAM_RAW, 'founder designation bangla', VALUE_OPTIONAL),
                'picurl' => new external_value(PARAM_RAW, 'pic url', VALUE_OPTIONAL),
                'email' => new external_value(PARAM_RAW, 'email', VALUE_OPTIONAL),
                'tier' => new external_value(PARAM_RAW, 'tier', VALUE_OPTIONAL),
            )
        );
    }
    static function get_all_instructors () {
        return new external_single_structure(
            array(
                'instructorname' => new external_value(PARAM_RAW, 'instructor name', VALUE_OPTIONAL),
                'instructorname_bn' => new external_value(PARAM_RAW, 'instructor name bangla', VALUE_OPTIONAL),
                'instructordeg' => new external_value(PARAM_RAW, 'instructor designation', VALUE_OPTIONAL),
                'instructordeg_bn' => new external_value(PARAM_RAW, 'instructor designation bangla', VALUE_OPTIONAL),
                'picurl' => new external_value(PARAM_RAW, 'pic url', VALUE_OPTIONAL),
                'email' => new external_value(PARAM_RAW, 'email', VALUE_OPTIONAL),
                'tier' => new external_value(PARAM_RAW, 'tier', VALUE_OPTIONAL),
            )
        );
    }

    static function get_all_operations () {
        return new external_single_structure(
            array(
                'operationname' => new external_value(PARAM_RAW, 'operation name', VALUE_OPTIONAL),
                'operationname_bn' => new external_value(PARAM_RAW, 'operation name bangla', VALUE_OPTIONAL),
                'operationdeg' => new external_value(PARAM_RAW, 'operation designation', VALUE_OPTIONAL),
                'operationdeg_bn' => new external_value(PARAM_RAW, 'operation designation bangla', VALUE_OPTIONAL),
                'operationmail' => new external_value(PARAM_RAW, 'operation email', VALUE_OPTIONAL),
                'picurl' => new external_value(PARAM_RAW, 'pic url', VALUE_OPTIONAL),
                'tier' => new external_value(PARAM_RAW, 'tier', VALUE_OPTIONAL),
            )
        );
    }

    /**
     * Returns description of method get_footer_links parameters.
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function get_footer_links_parameters() {
        return new external_function_parameters(
            array (
                'name' => new external_value(PARAM_TEXT, 'name', VALUE_DEFAULT, ''),
            )
        );
    }

    /**
     * Returns all the footer links.
     *
     * @return array of warnings and result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function get_footer_links($name) {
        global $CFG;
        require_once($CFG->dirroot . "/local/dcms/lib.php");

        //validate parameter
        $params = self::validate_parameters(self::get_footer_links_parameters(),
            array('name' => $name));

        $footer_links = local_dcms_get_footer_links();

        $links = [];
        foreach($footer_links as $link) {
            if(!$name) {
                $record = new stdClass();
                $record->name = $link->name;
                $record->name_bn = $link->name_bn;
                $record->title = $link->title;
                $record->title_bn = $link->title_bn;
                $record->description = $link->description;
                $record->description_bn = $link->description_bn;
                array_push($links, $record);
            } else {
                if($name == $link->name) {
                    $record = new stdClass();
                    $record->name = $link->name;
                    $record->name_bn = $link->name_bn;
                    $record->title = $link->title;
                    $record->title_bn = $link->title_bn;
                    $record->description = $link->description;
                    $record->description_bn = $link->description_bn;
                    array_push($links, $record);
                }
            }


        }

        $result = [];
        $result['links'] = $links;

        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function get_footer_links_returns() {
        return new external_single_structure(
            array(
                'links' => new external_multiple_structure(self::get_all_links_structure(),'links',VALUE_OPTIONAL),
            )
        );
    }

    static function get_all_links_structure () {
        return new external_single_structure(
            array(
                'name' => new external_value(PARAM_RAW, 'Name', VALUE_OPTIONAL),
                'name_bn' => new external_value(PARAM_RAW, 'Name bangla', VALUE_OPTIONAL),
                'title' => new external_value(PARAM_RAW, 'Title', VALUE_OPTIONAL),
                'title_bn' => new external_value(PARAM_RAW, 'Title bangla', VALUE_OPTIONAL),
                'description' => new external_value(PARAM_RAW, 'Description', VALUE_OPTIONAL),
                'description_bn' => new external_value(PARAM_RAW, 'Description bangla', VALUE_OPTIONAL),
            )
        );
    }

}
