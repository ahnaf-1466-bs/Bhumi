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
 * Lib functions for 'vumi_auth'.
 *
 * @package    auth_vumi_auth
 * @copyright  2023 Brain Station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Send email to specified user with confirmation text and activation link.
 *
 * @param stdClass $user A {@link $USER} object
 * @param string $confirmationurl user confirmation URL
 * @return bool Returns true if mail was sent OK and false if there was an error.
 */
function auth_vumi_auth_send_confirmation_email($user, $confirmationurl = null) {
    global $CFG;

    $site = get_site();
    $supportuser = core_user::get_support_user();

    $data = new stdClass();
    $data->sitename  = format_string($site->fullname);
    $data->admin     = generate_email_signoff();
    $data->fullname = $user->firstname . ' ' . $user->lastname;

    $subject = get_string('emailconfirmationsubject', '', format_string($site->fullname));

    if (empty($confirmationurl)) {
        $confirmationurl = '/login/conirm.php';
    }

    $confirmationurl = new moodle_url($confirmationurl);
    // Remove data parameter just in case it was included in the confirmation so we can add it manually later.
    $confirmationurl->remove_params('data');
    $confirmationpath = $confirmationurl->out(false);

    // We need to custom encode the username to include trailing dots in the link.
    // Because of this custom encoding we can't use moodle_url directly.
    // Determine if a query string is present in the confirmation url.
    $hasquerystring = strpos($confirmationpath, '?') !== false;
    // Perform normal url encoding of the username first.
    $username = urlencode($user->username);
    // Prevent problems with trailing dots not being included as part of link in some mail clients.
    $username = str_replace('.', '%2E', $username);

    //$data->link = $confirmationpath . ( $hasquerystring ? '&' : '?') . 'data='. $user->secret .'/'. $username;

    // Get the frontend url from settings.
    $frontendurl = get_config('auth_vumi_auth', 'frontend_endpoint');
    $data->link = $frontendurl . 'data=' . $user->secret . '&email=' . $user->email;
    $message     = get_string('emailconfirmation', 'auth_vumi_auth', $data);
    $messagehtml = text_to_html(get_string('emailconfirmation', 'auth_vumi_auth', $data), false, false, true);

    // Directly email rather than using the messaging system to ensure its not routed to a popup or jabber.
    return email_to_user($user, $supportuser, $subject, $message, $messagehtml);
}

/**
 * Process the password reset for the given user (via username or email).
 *
 * @param  string $username the user name
 * @param  string $email    the user email
 * @return array an array containing fields indicating the reset status, a info notice and redirect URL.
 * @since  Moodle 3.4
 */
function auth_vumi_auth_process_password_reset($username, $email) {
    global $CFG, $DB;

    if (empty($username) && empty($email)) {
        print_error('cannotmailconfirm');
    }

    // Next find the user account in the database which the requesting user claims to own.
    if (!empty($username)) {
        // Username has been specified - load the user record based on that.
        $username = core_text::strtolower($username); // Mimic the login page process.
        $userparams = array('username' => $username, 'mnethostid' => $CFG->mnet_localhost_id, 'deleted' => 0, 'suspended' => 0);
        $user = $DB->get_record('user', $userparams);
    } else {
        // Try to load the user record based on email address.
        // This is tricky because:
        // 1/ the email is not guaranteed to be unique - TODO: send email with all usernames to select the account for pw reset
        // 2/ mailbox may be case sensitive, the email domain is case insensitive - let's pretend it is all case-insensitive.
        //
        // The case-insensitive + accent-sensitive search may be expensive as some DBs such as MySQL cannot use the
        // index in that case. For that reason, we first perform accent-insensitive search in a subselect for potential
        // candidates (which can use the index) and only then perform the additional accent-sensitive search on this
        // limited set of records in the outer select.
        $sql = "SELECT *
                  FROM {user}
                 WHERE " . $DB->sql_equal('email', ':email1', false, true) . "
                   AND id IN (SELECT id
                                FROM {user}
                               WHERE mnethostid = :mnethostid
                                 AND deleted = 0
                                 AND suspended = 0
                                 AND " . $DB->sql_equal('email', ':email2', false, false) . ")";

        $params = array(
            'email1' => $email,
            'email2' => $email,
            'mnethostid' => $CFG->mnet_localhost_id,
        );

        $user = $DB->get_record_sql($sql, $params, IGNORE_MULTIPLE);
    }

    // Target user details have now been identified, or we know that there is no such account.
    // Send email address to account's email address if appropriate.
    $pwresetstatus = PWRESET_STATUS_NOEMAILSENT;
    if ($user and !empty($user->confirmed)) {
        $systemcontext = context_system::instance();

        $userauth = get_auth_plugin($user->auth);
        if (!$userauth->can_reset_password() or !is_enabled_auth($user->auth)
          or !has_capability('moodle/user:changeownpassword', $systemcontext, $user->id)) {
            if (auth_vumi_auth_send_password_change_info($user)) {
                $pwresetstatus = PWRESET_STATUS_OTHEREMAILSENT;
            } else {
                print_error('cannotmailconfirm');
            }
        } else {
            // The account the requesting user claims to be is entitled to change their password.
            // Next, check if they have an existing password reset in progress.
            $resetinprogress = $DB->get_record('user_password_resets', array('userid' => $user->id));
            if (empty($resetinprogress)) {
                // Completely new reset request - common case.
                //$resetrecord = core_login_generate_password_reset($user);
                $resetrecord = auth_vumi_auth_generate_password_reset($user);
                $sendemail = true;
            } else if ($resetinprogress->timerequested < (time() - $CFG->pwresettime)) {
                // Preexisting, but expired request - delete old record & create new one.
                // Uncommon case - expired requests are cleaned up by cron.
                $DB->delete_records('user_password_resets', array('id' => $resetinprogress->id));
                //$resetrecord = core_login_generate_password_reset($user);
                $resetrecord = auth_vumi_auth_generate_password_reset($user);
                
                $sendemail = true;
            } else if (empty($resetinprogress->timererequested)) {
                // Preexisting, valid request. This is the first time user has re-requested the reset.
                // Re-sending the same email once can actually help in certain circumstances
                // eg by reducing the delay caused by greylisting.
                $resetinprogress->timererequested = time();
                $DB->update_record('user_password_resets', $resetinprogress);
                $resetrecord = $resetinprogress;
                $sendemail = true;
            } else {
                // Preexisting, valid request. User has already re-requested email.
                $pwresetstatus = PWRESET_STATUS_ALREADYSENT;
                $sendemail = false;
            }

            if ($sendemail) {
                $sendresult = auth_vumi_auth_send_password_change_confirmation_email($user, $resetrecord);
                if ($sendresult) {
                    $pwresetstatus = PWRESET_STATUS_TOKENSENT;
                } else {
                    print_error('cannotmailconfirm');
                }
            }
        }
    }

    $url = $CFG->wwwroot.'/index.php';
    if (!empty($CFG->protectusernames)) {
        // Neither confirm, nor deny existance of any username or email address in database.
        // Print general (non-commital) message.
        $status = 'emailpasswordconfirmmaybesent';
        $notice = get_string($status);
    } else if (empty($user)) {
        // Protect usernames is off, and we couldn't find the user with details specified.
        // Print failure advice.
        $status = 'emailpasswordconfirmnotsent';
        $notice = get_string($status);
        $url = $CFG->wwwroot.'/forgot_password.php';
    } else if (empty($user->email)) {
        // User doesn't have an email set - can't send a password change confimation email.
        $status = 'emailpasswordconfirmnoemail';
        $notice = get_string($status);
    } else if ($pwresetstatus == PWRESET_STATUS_ALREADYSENT) {
        // User found, protectusernames is off, but user has already (re) requested a reset.
        // Don't send a 3rd reset email.
        $status = 'emailalreadysent';
        $notice = get_string($status);
    } else if ($pwresetstatus == PWRESET_STATUS_NOEMAILSENT) {
        // User found, protectusernames is off, but user is not confirmed.
        // Pretend we sent them an email.
        // This is a big usability problem - need to tell users why we didn't send them an email.
        // Obfuscate email address to protect privacy.
        $protectedemail = preg_replace('/([^@]*)@(.*)/', '******@$2', $user->email);
        $status = 'emailpasswordconfirmsent';
        $notice = get_string($status, '', $protectedemail);
    } else {
        // Confirm email sent. (Obfuscate email address to protect privacy).
        $protectedemail = preg_replace('/([^@]*)@(.*)/', '******@$2', $user->email);
        // This is a small usability problem - may be obfuscating the email address which the user has just supplied.
        $status = 'emailresetconfirmsent';
        $notice = get_string($status, '', $protectedemail);
    }
    return array($status, $notice, $url);
}

/** Create a new record in the database to track a new password set request for user.
 * @param object $user the user record, the requester would like a new password set for.
 * @return record created.
 */
function auth_vumi_auth_generate_password_reset ($user) {
    global $DB;
    $resetrecord = new stdClass();
    $resetrecord->timerequested = time();
    $resetrecord->userid = $user->id;
    $resetrecord->token = random_string(32);
    $resetrecord->id = $DB->insert_record('user_password_resets', $resetrecord);
    return $resetrecord;
}

/**
 * Sends a password change confirmation email.
 *
 * @param stdClass $user A {@link $USER} object
 * @param stdClass $resetrecord An object tracking metadata regarding password reset request
 * @return bool Returns true if mail was sent OK and false if there was an error.
 */
function auth_vumi_auth_send_password_change_confirmation_email($user, $resetrecord) {

    global $CFG, $DB, $USER;

    $site = get_site();
//    $supportuser = core_user::get_support_user();
    $sender = $DB->get_record('user', ['id' => $USER->id]);
    $pwresetmins = isset($CFG->pwresettime) ? floor($CFG->pwresettime / MINSECS) : 30;
    $forgotpwdurl = get_config('auth_vumi_auth', 'frontend_forgot_password');

    // Admin Name Add in E-mail
    $admininfo = $DB->get_record('user', ['id' => 2]);

    $data = new stdClass();
    $data->firstname = $user->firstname;
    $data->lastname  = $user->lastname;
    $data->username  = $user->username;
    $data->sitename  = format_string($site->fullname);
    $data->link      = $forgotpwdurl . 'token='. $resetrecord->token . '&email=' . $user->email;
    $data->admin = $admininfo->firstname . ' ' . $admininfo->lastname;
    $data->resetminutes = $pwresetmins;

    $message = get_string('emailresetconfirmation', 'auth_vumi_auth', $data);
    $subject = get_string('emailresetconfirmationsubject', '', format_string($site->fullname));

    // Directly email rather than using the messaging system to ensure its not routed to a popup or jabber.
    return email_to_user($user, $sender, $subject, $message);

}

/**
 * Sends an email containing information on how to change your password.
 *
 * @param stdClass $user A {@link $USER} object
 * @return bool Returns true if mail was sent OK and false if there was an error.
 */
function auth_vumi_auth_send_password_change_info($user) {
    $site = get_site();
    $supportuser = core_user::get_support_user();

    $data = new stdClass();
    $data->firstname = $user->firstname;
    $data->lastname  = $user->lastname;
    $data->username  = $user->username;
    $data->sitename  = format_string($site->fullname);
    $data->admin     = generate_email_signoff();

    if (!is_enabled_auth($user->auth)) {
        $message = get_string('emailpasswordchangeinfodisabled', '', $data);
        $subject = get_string('emailpasswordchangeinfosubject', '', format_string($site->fullname));
        // Directly email rather than using the messaging system to ensure its not routed to a popup or jabber.
        return email_to_user($user, $supportuser, $subject, $message);
    }

    $userauth = get_auth_plugin($user->auth);
    ['subject' => $subject, 'message' => $message] = $userauth->get_password_change_info($user);

    // Directly email rather than using the messaging system to ensure its not routed to a popup or jabber.
    return email_to_user($user, $supportuser, $subject, $message);
}

/**
 * Validates the forgot password form data.
 *
 * This is used by the forgot_password_form and by the core_auth_request_password_rest WS.
 * @param  array $data array containing the data to be validated (email and username)
 * @return array array of errors compatible with mform
 * @since  Moodle 3.4
 */
function auth_vumi_auth_validate_forgot_password_data($data) {
    global $CFG, $DB;

    $errors = array();

    if ((!empty($data['username']) and !empty($data['email'])) or (empty($data['username']) and empty($data['email']))) {
        $errors['username'] = get_string('usernameoremail');
        $errors['email']    = get_string('usernameoremail');

    } else if (!empty($data['email'])) {
        if (!validate_email($data['email'])) {
            $errors['email'] = get_string('invalidemail');

        } else {
            try {
                $user = get_complete_user_data('email', $data['email'], null, true);
                if (empty($user->confirmed)) {
                    send_confirmation_email($user);
                    if (empty($CFG->protectusernames)) {
                        $errors['email'] = get_string('confirmednot');
                    }
                }
            } catch (dml_missing_record_exception $missingexception) {
                // User not found. Show error when $CFG->protectusernames is turned off.
                if (empty($CFG->protectusernames)) {
                    $errors['email'] = get_string('emailnotfound');
                }
            } catch (dml_multiple_records_exception $multipleexception) {
                // Multiple records found. Ask the user to enter a username instead.
                if (empty($CFG->protectusernames)) {
                    $errors['email'] = get_string('forgottenduplicate');
                }
            }
        }

    } else {
        if ($user = get_complete_user_data('username', $data['username'])) {
            if (empty($user->confirmed)) {
                send_confirmation_email($user);
                if (empty($CFG->protectusernames)) {
                    $errors['username'] = get_string('confirmednot');
                }
            }
        }
        if (!$user and empty($CFG->protectusernames)) {
            $errors['username'] = get_string('usernamenotfound');
        }
    }

    return $errors;
}

/**
 * Checks if the token is valid for the user's email.
 *
 * @param stdClass $user A {@link $USER} object
 * @return object user Returns user oject.
 */
function auth_vumi_auth_get_user_from_token($token) {
    global $DB;
    $sql = "SELECT u.*, upr.token, upr.timerequested, upr.id as tokenid
            FROM {user} u
            JOIN {user_password_resets} upr ON upr.userid = u.id
            WHERE upr.token = ?";
    $user = $DB->get_record_sql($sql, array($token));
    return $user;
}
