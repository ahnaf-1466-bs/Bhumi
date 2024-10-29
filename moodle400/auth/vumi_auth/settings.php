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
 * Admin settings and defaults.
 *
 * @package    auth_vumi_auth
 * @copyright  2023 Brain Station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Introductory explanation.
    $settings->add(new admin_setting_heading('auth_vumi_auth/pluginname', '',
        new lang_string('auth_vumi_authdescription', 'auth_vumi_auth')));

    $options = array(
        new lang_string('no'),
        new lang_string('yes'),
    );

    $settings->add(new admin_setting_configselect('auth_vumi_auth/recaptcha',
        new lang_string('auth_vumi_authrecaptcha_key', 'auth_vumi_auth'),
        new lang_string('auth_vumi_authrecaptcha', 'auth_vumi_auth'), 0, $options));

    $settings->add(new admin_setting_configtext('auth_vumi_auth/frontend_endpoint',
        new lang_string('auth_vumi_frontend_endpoint', 'auth_vumi_auth'),
        new lang_string('auth_vumi_frontend_endpoint_help', 'auth_vumi_auth'), ''));

    $settings->add(new admin_setting_configtext('auth_vumi_auth/frontend_forgot_password',
        new lang_string('auth_vumi_frontend_forgot_password', 'auth_vumi_auth'),
        new lang_string('auth_vumi_frontend_forgot_password_help', 'auth_vumi_auth'), ''));

    // Redirecting URL for OAuth
    $settings->add(new admin_setting_configtext('auth_vumi_auth/redirect_url',
        new lang_string('redirect_url', 'auth_vumi_auth'),
        new lang_string('redirect_url_desc', 'auth_vumi_auth'), ''));


    // Display locking / mapping of profile fields.
    $authplugin = get_auth_plugin('vumi_auth');
    display_auth_lock_options($settings, $authplugin->authtype, $authplugin->userfields,
            get_string('auth_fieldlocks_help', 'auth'), false, false);
}
