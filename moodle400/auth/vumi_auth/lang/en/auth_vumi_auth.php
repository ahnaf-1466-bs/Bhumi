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
 * Strings for component 'auth_vumi_auth', language 'en'.
 *
 * @package    auth_vumi_auth
 * @copyright  2023 Brain Station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['auth_vumi_authdescription'] = '<p>Vumi_auth-based self-registration enables a user to create their own account via a \'Create new account\' button on the login page. The user then receives an vumi_auth containing a secure link to a page where they can confirm their account. Future logins just check the username and password against the stored values in the Moodle database.</p><p>Note: In addition to enabling the plugin, vumi_auth-based self-registration must also be selected from the self registration drop-down menu on the \'Manage authentication\' page.</p>';
$string['auth_vumi_authnovumi_auth'] = 'Tried to send you an email but failed!';
$string['auth_vumi_authrecaptcha'] = 'Adds a visual/audio confirmation form element to the sign-up page for vumi_auth self-registering users. This protects your site against spammers and contributes to a worthwhile cause. See https://www.google.com/recaptcha for more details.';
$string['auth_vumi_authrecaptcha_key'] = 'Enable reCAPTCHA element';
$string['auth_vumi_authsettings'] = 'Settings';
$string['pluginname'] = 'Vumi_auth-based self-registration';
$string['privacy:metadata'] = 'The Vumi_auth-based self-registration authentication plugin does not store any personal data.';
$string['auth_vumi_frontend_endpoint'] = 'Frontend URL for confirmation';
$string['auth_vumi_frontend_endpoint_help'] = 'Frontend URL for confirmation';

$string['auth_vumi_frontend_forgot_password'] = 'Frontend URL for forgot password';
$string['auth_vumi_frontend_forgot_password_help'] = 'Frontend URL for forgot password';
$string['emailconfirmation'] = 'Hi {$a->fullname},

A new account has been requested at VUMI Bangladesh Ltd
using your email address.

To confirm your new account, please go to this web address:

{$a->link}

In most mail programs, this should appear as a blue link
which you can just click on.  If that doesn\'t work,
then cut and paste the address into the address
line at the top of your web browser window.';

$string['issuernotfound'] = 'The Issuer not Found! Please contact the Admin.';
$string['redirect_url'] = 'Redirect URL';
$string['redirect_url_desc'] = 'Angular Redirect URL';
$string['token_invalid'] = 'Token invalid or expired!';

$string['emailresetconfirmation'] = 'Hi {$a->firstname},

A password reset was requested for your account \'{$a->username}\' at {$a->sitename}.

To confirm this request, and set a new password for your account, please go to the following web address:
{$a->link}

(This link is valid for {$a->resetminutes} minutes from the time this reset was first requested.)

If this password reset was not requested by you, no action is needed.

If you need help, please contact the site administrator, 
Vumi Bangladesh Ltd.';
