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
 * @category   external
 * @copyright  2022 Brain Statin 23 LTD
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die;

$functions = array(

    'vumi_webservicesuit_get_certificate_list_by_userid' => array(
        'classname' => 'local_vumi_webservicesuit_external',
        'methodname' => 'get_certificate_list',
        'classpath' => 'local/vumi_webservicesuit/externallib.php',
        'description' => 'Certificate List',
        'type' => 'read',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'vumi_webservicesuit_sample_customcert' => array(
        'classname' => 'local_vumi_webservicesuit_external',
        'methodname' => 'get_sample_customcert',
        'classpath' => 'local/vumi_webservicesuit/externallib.php',
        'description' => 'Return sample certificate',
        'type' => 'read',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'vumi_webservicesuit_certificate_url_by_courseid_userid' => array(
        'classname' => 'local_vumi_webservicesuit_external',
        'methodname' => 'get_certificate_url',
        'classpath' => 'local/vumi_webservicesuit/externallib.php',
        'description' => 'Return certificate',
        'type' => 'read',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'vumi_webservicesuit_certificate_varify_by_code' => array(
        'classname' => 'local_vumi_webservicesuit_external',
        'methodname' => 'get_certificate_details',
        'classpath' => 'local/vumi_webservicesuit/externallib.php',
        'description' => 'Return certificate details',
        'type' => 'read',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'tool_courserating_get_rating' => array(
        'classname'     => 'local_vumi_webservicesuit_external',
        'methodname'    => 'get_course_ratings',
        'classpath'     => 'local/vumi_webservicesuit/externallib.php',
        'description'   => 'Get course rating done by users',
        'type'          => 'write',
        'ajax'          => true,
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'vumi_webservicesuit_zoom_get_state' => array(
        'classname'     => 'local_vumi_webservicesuit_external',
        'methodname'    => 'zoom_get_state',
        'classpath'     => 'local/vumi_webservicesuit/externallib.php',
        'description'   => 'Get zoom activity description and state',
        'type'          => 'read',
        'ajax'          => true,
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
);
