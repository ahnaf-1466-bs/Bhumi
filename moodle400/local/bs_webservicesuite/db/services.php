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
 * Web service local plugin template external functions and service definitions.
 *
 * @package    local
 * @subpackage bs_webservicesuite
 * @author     Brain station 23 ltd <brainstation-23.com>
 * @copyright  2023 Brain station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We defined the web service functions to install.
$functions = array(
    'bs_webservicesuite_get_instructor_details_by_courseid' => array(
        'classname'     => 'local_bs_webservicesuite_external',
        'methodname'    => 'get_instructor_details_by_courseid',
        'classpath'     => 'local/bs_webservicesuite/externallib.php',
        'description'   => 'Returns instructor details by course id',
        'ajax'          => true,
        'type'          => 'read',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
    'bs_webservicesuite_get_popular_courses' => array(
        'classname'     => 'local_bs_webservicesuite_external',
        'methodname'    => 'get_popular_courses',
        'classpath'     => 'local/bs_webservicesuite/externallib.php',
        'description'   => 'Returns list of popular courses by order of popularity (Number of enrolled users).',
        'ajax'          => true,
        'type'          => 'read',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),

    'bs_webservicesuite_get_future_courses' => array(
        'classname'     => 'local_bs_webservicesuite_external',
        'methodname'    => 'get_future_courses',
        'classpath'     => 'local/bs_webservicesuite/externallib.php',
        'description'   => 'Returns list of future courses based on the starting date.',
        'ajax'          => true,
        'type'          => 'read',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),

    'bs_webservicesuite_get_past_courses' => array(
        'classname'     => 'local_bs_webservicesuite_external',
        'methodname'    => 'get_past_courses',
        'classpath'     => 'local/bs_webservicesuite/externallib.php',
        'description'   => 'Returns list of past courses based on the end date.',
        'ajax'          => true,
        'type'          => 'read',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),

    'bs_webservicesuite_get_user_past_courses' => array(
        'classname'     => 'local_bs_webservicesuite_external',
        'methodname'    => 'get_user_past_courses',
        'classpath'     => 'local/bs_webservicesuite/externallib.php',
        'description'   => 'Returns list of past courses based on the end date and user.',
        'ajax'          => true,
        'type'          => 'read',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
    
     'bs_webservicesuite_get_course_details_with_instructor' => array(
        'classname'     => 'local_bs_webservicesuite_external',
        'methodname'    => 'get_course_details_with_instructor',
        'classpath'     => 'local/bs_webservicesuite/externallib.php',
        'description'   => 'Returns instructor details by course id',
        'ajax'          => true,
        'type'          => 'read',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),

    'bs_webservicesuite_get_enrolment_info_by_userid' => array(
        'classname'     => 'local_bs_webservicesuite_external',
        'methodname'    => 'get_enrolment_info_by_userid',
        'classpath'     => 'local/bs_webservicesuite/externallib.php',
        'description'   => 'Returns enrolment info of a course by userid and courseids',
        'ajax'          => true,
        'type'          => 'read',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),

    'bs_webservicesuite_get_recommended_courses' => array(
        'classname'     => 'local_bs_webservicesuite_external',
        'methodname'    => 'get_recommended_courses',
        'classpath'     => 'local/bs_webservicesuite/externallib.php',
        'description'   => 'Returns recommended courses of a user based on tags',
        'ajax'          => true,
        'type'          => 'read',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
    'bs_webservicesuite_search_courses_by_lang' => array(
        'classname' => 'local_bs_webservicesuite_external',
        'methodname' => 'search_courses_by_lang',
        'classpath' => 'local/bs_webservicesuite/externallib.php',
        'description' => 'Search courses by (name, module, block, tag) and lang',
        'type' => 'read',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
);

