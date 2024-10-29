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
 * @package local_newsfeed
 * @copyright 2023 Brain Station 23 LTD.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Given an array with a file path, it returns the itemid and the filepath for the defined filearea.
 *
 * @param  string $filearea The filearea.
 * @param  array  $args The path (the part after the filearea and before the filename).
 * @return array The itemid and the filepath inside the $args path, for the defined filearea.
 */
function local_newsfeed_get_path_from_pluginfile(string $filearea, array $args) : array {
    // newsfeed never has an itemid (the number represents the revision but it's not stored in database).
    array_shift($args);

    // Get the filepath.
    if (empty($args)) {
        $filepath = '/';
    } else {
        $filepath = '/' . implode('/', $args) . '/';
    }

    return [
        'itemid' => 0,
        'filepath' => $filepath,
    ];
}


/**
 * Serve the files.
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param context $context the context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 *
 * @return bool false if the file not found, just send the file otherwise and do not return anything
 */
function local_newsfeed_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    $itemid = array_shift($args);
    $filename = array_pop($args);

    if (!$args) {
        $filepath = '/';
    } else {
        $filepath = '/' . implode('/', $args) . '/';
    }

    $fs = get_file_storage();

    $file = $fs->get_file($context->id, 'local_newsfeed', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false;
    }
    send_stored_file($file, 0, 0, $forcedownload, $options);
}


function local_newsfeed_get_image_url($userid) {
    $context = context_system::instance();

    $fs = get_file_storage();
    if ($files = $fs->get_area_files($context->id, 'local_newsfeed', 'user_photo')) {

        foreach ($files as $file) {
            if ($userid == $file->get_itemid() && $file->get_filename() != '.') {
                // Build the File URL. Long process! But extremely accurate.
                $fileurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename(), true);
                // Display the image
                $download_url = $fileurl->get_port() ? $fileurl->get_scheme() . '://' . $fileurl->get_host() . ':' . $fileurl->get_port() . $fileurl->get_path() : $fileurl->get_scheme() . '://' . $fileurl->get_host() . $fileurl->get_path();
                return $download_url;
            }
        }
    }
    return false;
}

/**
 * Function for converting picture from draftid to URL
 */

function convert_image_to_url (stdClass $fromform) {
    $image = '';
    $context = context_system::instance();

    $filemanageropts = array('subdirs' => 0, 'maxbytes' => '0', 'maxfiles' => 50, 'context' => $context);
    // Adding a new News.
    file_save_draft_area_files( $fromform->newsimage, $context->id, 'local_newsfeed', 'attachment',  $fromform->newsimage, $filemanageropts);

    if ($fromform->newsimage) {
        $fs = get_file_storage();

        if ($files = $fs->get_area_files($context->id, 'local_newsfeed', 'attachment',  $fromform->newsimage, 'sortorder', false)) {
            // Look through each file being managed.
            foreach ($files as $file) {

                // Build the File URL. Long process! But extremely accurate.
                $fileurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
                // Display the image
                $download_url = $fileurl->get_port() ? $fileurl->get_scheme() . '://' . $fileurl->get_host() . ':' . $fileurl->get_port() . $fileurl->get_path() : $fileurl->get_scheme() . '://' . $fileurl->get_host() . $fileurl->get_path();

                $image = $download_url;

            }
        }
    }
    return $image;
}

/**
 * @return array
 * @throws dml_exception
 * For viewing the image link with content this function is used
 */

function local_get_picurl () {
    global $DB;
    $SQL = 'SELECT sp.id, sp.newstitle, sp.newssubtitle, sp.newsbody, sp.picurl, FROM_UNIXTIME(sp.dateofpublish, "%Y-%m-%d") as date, sp.status
            FROM {newsfeed_newsdetailurl} sp 
            JOIN {newsfeed_newsdetails} sf
            WHERE sp.draftid = sf.newsimage';

    $messages = $DB->get_records_sql($SQL);

    return $messages;
}



/**
 * @return array
 * @throws dml_exception
 * For viewing the image link with content this function is used
 */

function local_fullnews (int $newsid) {
    global $DB;
    $SQL = 'SELECT sp.id, sp.newstitle, sp.newssubtitle, sp.newsbody, sp.picurl, FROM_UNIXTIME(sp.dateofpublish, "%Y-%m-%d") as date, sp.status
            FROM {newsfeed_newsdetailurl} sp 
            JOIN {newsfeed_newsdetails} sf
            WHERE sp.draftid = sf.newsimage AND sp.id ='. $newsid;

    $messages = $DB->get_records_sql($SQL);

    return $messages;
}

function update_record_newsfeed_newsdetailurl(stdClass $fromform, int $newsid) {
    global $DB;
    $image = convert_image_to_url($fromform);
    $object = new stdClass();
    $object->id = $newsid;
    $object->newstitle = $fromform->newstitle;
    $object->newstitle_bn = $fromform->newstitle_bn;
    $object->newssubtitle = $fromform->newssubtitle;
    $object->newssubtitle_bn = $fromform->newssubtitle_bn;
    $object->newsbody = $fromform->newsbody['text'];
    $object->newsbody_bn = $fromform->newsbody_bn['text'];
    $object->picurl = $image;
    $object->draftid = $fromform->newsimage;
    $object->dateofpublish = $fromform->dateofpublish;
    $object->status = $fromform->status;

    $DB->update_record('newsfeed_newsdetailurl', $object);

}

function insert_record_newsfeed_newsdetailurl(stdClass $fromform) {
    global $DB;
    $image = convert_image_to_url($fromform);
    $object = new stdClass();
    $object->newstitle = $fromform->newstitle;
    $object->newstitle_bn = $fromform->newstitle_bn;
    $object->newssubtitle = $fromform->newssubtitle;
    $object->newssubtitle_bn = $fromform->newssubtitle_bn;
    $object->newsbody = $fromform->newsbody['text'];
    $object->newsbody_bn = $fromform->newsbody_bn['text'];
    $object->picurl = $image;
    $object->draftid = $fromform->newsimage;
    $object->dateofpublish = $fromform->dateofpublish;
    $object->status = $fromform->status;
    $object->timecreated = time();

    $DB->insert_record('newsfeed_newsdetailurl', $object);

}