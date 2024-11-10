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
 * newsfeed external API
 *
 * @package    local_newsfeed
 * @category   external
 * @copyright  2023 Brain Station 23 Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * newsfeed external functions
 *
 * @package    local_newsfeed
 * @category   external
 * @copyright  2023 Brain Station 23 Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class local_newsfeed_external extends external_api
{

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function view_full_newsfeed_parameters()
    {
        return new external_function_parameters(
            array(
                'startdate' => new external_value(PARAM_RAW, 'Start date of searching', VALUE_REQUIRED),
                'enddate' => new external_value(PARAM_RAW, 'End date of searching', VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Simulate the newsfeed/view.php web interface newsfeed: trigger events, completion, etc...
     *
     * @param $startdate
     * @param $enddate
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function view_full_newsfeed($startdate, $enddate)
    {
        global $DB, $CFG;
        require_once($CFG->dirroot . "/local/newsfeed/lib.php");

        self::validate_parameters(
            self::view_full_newsfeed_parameters(),
            array(
                'startdate' => $startdate,
                'enddate' => $enddate
            )
        );

        if ($enddate == 0) {
            $enddate = date("Y-m-d");
        }
        $startdate = $startdate . " 00:00:00";
        $enddate = $enddate . " 23:59:59";


        $sql = "SELECT id, newstitle,newstitle_bn, newssubtitle, newssubtitle_bn, picurl, newsbody,newsbody_bn, FROM_UNIXTIME(dateofpublish) as dateofpublish
                FROM {newsfeed_newsdetailurl} 
                Where status = 'Published' AND dateofpublish between ? AND ?";

        if ($DB->record_exists_sql($sql, ['startdate' => strtotime($startdate), 'enddate' => strtotime($enddate)])) {
            // Request and permission validation.
            $newsfeed = $DB->get_records_sql($sql, ['startdate' => strtotime($startdate), 'enddate' => strtotime($enddate)]);
            $result['allnews'] = $newsfeed;
        } else {
            $result['message'] = get_string('norecord', 'local_newsfeed');
        }

        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function view_full_newsfeed_returns()
    {
        return new external_single_structure(
            array(
                'allnews' => new external_multiple_structure(self::get_all_news(), 'newsfeed', VALUE_OPTIONAL),
                'message' => new external_value(PARAM_TEXT, 'message', VALUE_OPTIONAL)
            )
        );
    }

    static function get_all_news()
    {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'ID', VALUE_OPTIONAL),
                'newstitle' => new external_value(PARAM_RAW, 'news title', VALUE_OPTIONAL),
                'newstitle_bn' => new external_value(PARAM_RAW, 'news title bangla', VALUE_OPTIONAL),
                'newssubtitle' => new external_value(PARAM_RAW, 'news subtitle', VALUE_OPTIONAL),
                'newssubtitle_bn' => new external_value(PARAM_RAW, 'news subtitle bangla', VALUE_OPTIONAL),
                'newsbody' => new external_value(PARAM_RAW, 'news body', VALUE_OPTIONAL),
                'newsbody_bn' => new external_value(PARAM_RAW, 'news body bangla', VALUE_OPTIONAL),
                'picurl' => new external_value(PARAM_RAW, 'news pic url', VALUE_OPTIONAL),
                'dateofpublish' => new external_value(PARAM_RAW, 'news time', VALUE_OPTIONAL)
            )
        );
    }


    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function newsfeed_by_newsid_parameters()
    {
        return new external_function_parameters(
            array(
                'newsid' => new external_value(PARAM_INT, 'newsfeed instance id')
            )
        );
    }

    /**
     * Simulate the newsfeed/view.php web interface newsfeed: trigger events, completion, etc...
     *
     * @param int $newsid the newsfeed instance id
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function newsfeed_by_newsid($newsid)
    {
        global $DB, $CFG;
        require_once($CFG->dirroot . "/local/newsfeed/lib.php");

        self::validate_parameters(
            self::newsfeed_by_newsid_parameters(),
            array(
                'newsid' => $newsid
            )
        );

        if ($DB->record_exists('newsfeed_newsdetailurl', ['id' => $newsid])) {
            // Request and permission validation.
            $newsfeed_rec = $DB->get_record('newsfeed_newsdetailurl', array('id' => $newsid), '*', MUST_EXIST);
            if ($newsfeed_rec != NULl) {
                $result['id'] = $newsfeed_rec->id;
                $result['newstitle'] = $newsfeed_rec->newstitle;
                $result['newstitle_bn'] = $newsfeed_rec->newstitle_bn;
                $result['newssubtitle'] = $newsfeed_rec->newssubtitle;
                $result['newssubtitle_bn'] = $newsfeed_rec->newssubtitle_bn;
                $result['newsbody'] = $newsfeed_rec->newsbody;
                $result['newsbody_bn'] = $newsfeed_rec->newsbody_bn;
                $result['picurl'] = $newsfeed_rec->picurl;
                $result['dateofpublish'] = date("Y-m-d H:i:s", $newsfeed_rec->dateofpublish);
            }
        } else {
            $result['message'] = get_string('norecord', 'local_newsfeed');
        }
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function newsfeed_by_newsid_returns()
    {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'ID', VALUE_OPTIONAL),
                'newstitle' => new external_value(PARAM_RAW, 'news title', VALUE_OPTIONAL),
                'newstitle_bn' => new external_value(PARAM_RAW, 'news title bangla', VALUE_OPTIONAL),
                'newssubtitle' => new external_value(PARAM_RAW, 'news subtitle', VALUE_OPTIONAL),
                'newssubtitle_bn' => new external_value(PARAM_RAW, 'news subtitle bangla', VALUE_OPTIONAL),
                'newsbody' => new external_value(PARAM_RAW, 'news body', VALUE_OPTIONAL),
                'newsbody_bn' => new external_value(PARAM_RAW, 'news body bangla', VALUE_OPTIONAL),
                'picurl' => new external_value(PARAM_RAW, 'news pic url', VALUE_OPTIONAL),
                'message' => new external_value(PARAM_TEXT, 'message', VALUE_OPTIONAL),
                'dateofpublish' => new external_value(PARAM_TEXT, 'dateofpublish', VALUE_OPTIONAL)
            )
        );
    }
}
