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
 * Settings page for local_preregistration.
 *
 * @package    local_preregistration
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
global $CFG, $ADMIN;

if ($hassiteconfig) {
    $ADMIN->add('localplugins', 
        new admin_externalpage('local_preregistration',get_string('pluginname','local_preregistration'), 
        $CFG->wwwroot.'/local/preregistration/view.php'));

    $ADMIN->add('localplugins', 
        new admin_externalpage('local_preregistration_email_templates',get_string('email_template_settings','local_preregistration'), 
        $CFG->wwwroot.'/local/preregistration/email_templates.php'));
}
