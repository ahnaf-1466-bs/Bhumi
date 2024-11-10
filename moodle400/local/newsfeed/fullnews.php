<?php
//This file is part of Moodle Course Rollover Plugin
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
 * @package     local_newsfeed
 * @author      Brain Station 23 Ltd.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/local/newsfeed/lib.php');
global $DB, $PAGE, $OUTPUT;

require_login();
$context = context_system::instance();
$newsid = optional_param('newsid', 0, PARAM_INT);
$PAGE->set_url(new moodle_url('/local/newsfeed/fullnews.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('manage_messages', 'local_newsfeed'));
$PAGE->set_heading(get_string('fullnews', 'local_newsfeed'));
$PAGE->requires->js_call_amd('local_newsfeed/confirm');
$PAGE->requires->css('/local/newsfeed/styles.css');

echo $OUTPUT->header();
$messages = local_fullnews ($newsid);
$val = get_string('newfeature', 'local_newsfeed');
$back = get_string('goback', 'local_newsfeed');
$editnews = get_string('editnews', 'local_newsfeed');
$deletenews = get_string('deletenews', 'local_newsfeed');

if($messages != NULL) {
    foreach ($messages as $message) {
        $newsbodys[] = html_entity_decode($message->newsbody);
    }
} else {
    $newsbodys[] = get_string('nodata', 'local_newsfeed');
}

$templatecontext = (object)[
    'messages' => array_values($messages),
    'featureurl' => new moodle_url('/local/newsfeed/newsdetails.php'),
    'val' => $val,
    'back' => $back,
    'backurl' => new moodle_url('/local/newsfeed/newsdetailsmanage.php'),
    'fullnewsurl' => new moodle_url('/local/newsfeed/fullnews.php'),
    'newsbodys' => array_values($newsbodys),
    'editnews' => $editnews,
    'deletenews' => $deletenews,
];

echo $OUTPUT->render_from_template('local_newsfeed/fullnews', $templatecontext);

echo $OUTPUT->footer();
