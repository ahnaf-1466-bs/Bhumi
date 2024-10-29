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
 * View page for local_book_seat.
 *
 * @package    local_book_seat
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_book_seat\form\book_seat_form;

require('../../config.php');

require_once($CFG->dirroot.'/local/book_seat/lib.php');
require_once($CFG->dirroot.'/local/book_seat/classes/form/book_seat_form.php');

global $CFG, $PAGE, $DB, $OUTPUT, $USER;

if(!is_siteadmin($USER)) {
    return redirect(new moodle_url('/'), 'Unauthorized', null, \core\output\notification::NOTIFY_ERROR);
}

$PAGE->set_url('/local/book_seat/view.php');
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('pagetitle', 'local_book_seat'));
$PAGE->set_heading(get_string('pagetitle', 'local_book_seat'));

$seatid = optional_param('seatid', 0, PARAM_INT);
$delete = optional_param('del', 0, PARAM_INT);

if ($delete == 1) {
    $DB->delete_records('local_book_seat_userinfo', ['id' => $seatid]);
    redirect($CFG->wwwroot . '/local/book_seat/view.php', get_string('valuedeleted', 'local_book_seat'));
}

if ($seatid) {
    $mform = new book_seat_form($CFG->wwwroot . '/local/book_seat/view.php?seatid=' . $seatid);

    if ($mform->is_cancelled()) {
        // Go back to programmanage.php page
        redirect($CFG->wwwroot . '/local/book_seat/view.php', get_string('cancelled_message', 'local_book_seat'));

    } else if ($fromform = $mform->get_data()) {

        update_record_seat($seatid, $fromform);
        // Go back to programmanage.php page
        redirect($CFG->wwwroot . '/local/book_seat/view.php', get_string('updated_form', 'local_book_seat'));
    }
    // Add extra data to the form.
    global $DB;
    $message = $DB->get_record('local_book_seat_userinfo', ['id' => $seatid]);
    if (!$message) {
        throw new invalid_parameter_exception('Message not found');
    }

    echo $OUTPUT->header();
    $mform->set_data($message);
    $mform->display();
    echo $OUTPUT->footer();
} else {
    $userdetails = $DB->get_records('local_book_seat_userinfo');

    $count = 1;

    $templatecontext = [
        'userdetails' => array_values($userdetails),
        'featureurl' => new moodle_url('/local/book_seat/view.php'),
        'count' => function () use (&$count) {
            return $count++;
        },
    ];

    echo $OUTPUT->header();
    echo $OUTPUT->render_from_template('local_book_seat/view', $templatecontext);
    echo $OUTPUT->footer();
}


