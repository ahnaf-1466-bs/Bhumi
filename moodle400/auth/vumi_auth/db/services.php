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
 * Auth vumi_auth webservice definitions.
 *
 * @package    auth_vumi_auth
 * @copyright  2023 Brain Station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(

    'auth_vumi_auth_get_signup_settings' => array(
        'classname'   => 'auth_vumi_auth_external',
        'methodname'  => 'get_signup_settings',
        'description' => 'Get the signup required settings and profile fields.',
        'type'        => 'read',
        'ajax'          => true,
        'loginrequired' => false,
    ),
    'auth_vumi_auth_signup_user' => array(
        'classname'   => 'auth_vumi_auth_external',
        'methodname'  => 'signup_user',
        'description' => 'Adds a new user (pendingto be confirmed) in the site.',
        'type'        => 'write',
        'ajax'          => true,
        'loginrequired' => false,
    ),
    'auth_vumi_auth_request_password_reset' => array(
        'classname'   => 'auth_vumi_auth_external',
        'methodname'  => 'request_password_reset',
        'description' => 'Requests a password reset.',
        'type'        => 'write',
        'ajax'          => true,
        'loginrequired' => false,
    ),

    'auth_vumi_login_with_issuers' => array(
        'classname'     => 'auth_vumi_auth_external',
        'methodname'    => 'oauth2_redirect',
        'description'   => 'Returns the Issuer ID(ex. google, linkedin etc.) as per the request',
        'type'          => 'write',
        'ajax'          => true,
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'auth_vumi_user_info_by_token' => array(
        'classname'     => 'auth_vumi_auth_external',
        'methodname'    => 'auth_vumi_userinfo',
        'description'   => 'Get user info by token',
        'type'          => 'read',
        'ajax'          => true,
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'auth_vumi_auth_update_password_after_validation' => array(
        'classname'   => 'auth_vumi_auth_external',
        'methodname'  => 'update_password_after_validation',
        'description' => 'Updates a password after validation.',
        'type'        => 'write',
        'ajax'          => true,
        'loginrequired' => false,
    ),
);

