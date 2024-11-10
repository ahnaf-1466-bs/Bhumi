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
 * @package     mod_videoplus
 * @author      Brain Station 23 Ltd.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/mod/videoplus/lib.php');
global $DB, $PAGE, $OUTPUT;

require_login();
$context = context_system::instance();
$courseid = optional_param('id', 0, PARAM_INT);
$cmid = optional_param('cmid', 0, PARAM_INT);

$PAGE->set_url(new moodle_url('/mod/videoplus/pdfmanage.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('manage_pdffile', 'mod_videoplus'));
$PAGE->set_heading(get_string('manage_pdffile', 'mod_videoplus'));
$PAGE->requires->css('/mod/videoplus/styles.css');

echo $OUTPUT->header();
$html = '';
$messages = get_url_for_pdffile($courseid, $cmid);
if (!$messages) {
    $html .= '<div class="alert alert-warning fade show" role="alert">';
    $html .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
    $html .= '<span aria-hidden="true">&times;</span>';
    $html .= '</button>';
    $html .= '<strong>No PDF Found!</strong>';
    $html .= '</div>';
}

$addpdfbtn = get_string('addpdfbtn', 'mod_videoplus');
$editprogrampdf = get_string('editprogrampdf', 'mod_videoplus');
$back = get_string('goback', 'mod_videoplus');

$templatecontext = (object)[
    'messages' => array_values($messages),
    'featureurl' => new moodle_url('/mod/videoplus/pdffile.php?id='.$courseid),
    'addpdfbtn' => $addpdfbtn,
    'editprogrampdf' => $editprogrampdf,
    'back' => $back,
    'backurl' => new moodle_url('/mod/videoplus/view.php?id='. $cmid),
    'cm_id' => $cmid,
    'html' => $html,
];

echo $OUTPUT->render_from_template('mod_videoplus/pdfmanage', $templatecontext);

echo $OUTPUT->footer();
