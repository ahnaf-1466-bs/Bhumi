<?php
// This file is part of the customcert module for Moodle - http://moodle.org/
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
 * Handles viewing a customcert.
 *
 * @package    mod_customcert
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
global $CFG, $SESSION, $DB;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

require_once($CFG->dirroot . '/local/certificate/lib.php');

$id = required_param('id', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$cm = get_coursemodule_from_id('customcert', $id, 0, false, MUST_EXIST);

$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$customcert = $DB->get_record('customcert', array('id' => $cm->instance), '*', MUST_EXIST);

$template = $DB->get_record('customcert_templates', array('id' => $customcert->templateid), '*', MUST_EXIST);

$isIssue = $DB->record_exists('customcert_issues', array('userid' => $userid, 'customcertid' => $customcert->id));

$context = context_module::instance($cm->id);

// Hack alert - don't initiate the download when running Behat.
if (defined('BEHAT_SITE_RUNNING')) {
    redirect(new moodle_url('/mod/customcert/view.php', array('id' => $cm->id)));
}

\core\session\manager::write_close();

// Now we want to generate the PDF.
$template = new \mod_customcert\template($template);
$template->generate_pdf(false, $userid);

exit();
