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
 * dcms external functions and service definitions.
 *
 * @package    local_dcms
 * @category   external
 * @copyright  2023 Brain Station 23 Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die;

$functions = array(
    'local_dcms_get_homepage_contents' => array(
        'classname'     => 'local_dcms_external',
        'methodname'    => 'get_homepage_contents',
        'classpath'     => 'local/dcms/classes/external.php',
        'description'   => 'Simulate the view.php web interface page: trigger events, completion, etc...',
        'type'          => 'read',
        'ajax'          => true,
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'local_dcms_get_ourteampage_contents' => array(
        'classname'     => 'local_dcms_external',
        'methodname'    => 'get_ourteampage_contents',
        'classpath'     => 'local/dcms/classes/external.php',
        'description'   => 'Simulate the view.php web interface page: trigger events, completion, etc...',
        'type'          => 'read',
        'ajax'          => true,
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
     'local_dcms_get_aboutpage_contents' => array(
        'classname'     => 'local_dcms_external',
        'methodname'    => 'get_aboutpage_contents',
        'classpath'     => 'local/dcms/classes/external.php',
        'description'   => 'Simulate the view.php web interface page: trigger events, completion, etc...',
        'type'          => 'read',
        'ajax'          => true,
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'local_dcms_get_footer_links' => array(
        'classname'     => 'local_dcms_external',
        'methodname'    => 'get_footer_links',
        'classpath'     => 'local/dcms/classes/external.php',
        'description'   => 'Get all the footer links',
        'type'          => 'read',
        'ajax'          => true,
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
);
