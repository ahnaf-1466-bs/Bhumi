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
 * Settings page for local marketing.
 *
 * @package    local_marketing
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once('./locallib.php');
require_once('./classes/form/create_form.php');

try {
    require_login();
} catch (Exception $exception) {
    print_r($exception);
}

$PAGE->set_url(new moodle_url('/local/marketing/manage.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_marketing'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'local_marketing'));

local_marketing_display_mails();

echo $OUTPUT->footer();