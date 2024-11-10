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
 * @package local_dcms
 * @copyright 2021 Brain Station 23 LTD.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * List of features supported in dcms module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function dcms_supports($feature) {
    switch($feature) {
        case FEATURE_local_ARCHETYPE:           return local_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_local_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_siteintro:        return true;

        default: return null;
    }
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function dcms_reset_userdata($data) {

    // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
    // See MDL-9367.

    return array();
}

/**
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function dcms_get_view_actions() {
    return array('view','view all');
}

/**
 * List the actions that correspond to a post of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = ('c' || 'u' || 'd') and edulevel = LEVEL_PARTICIPATING
 *       will be considered as post action.
 *
 * @return array
 */
function dcms_get_post_actions() {
    return array('update', 'add');
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 *
 * See {@link get_array_of_activities()} in course/lib.php
 *
 * @param stdClass $coursemodule
 * @return cached_cm_info Info to customise main dcms display
 */
function dcms_get_coursemodule_info($coursemodule) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");

    if (!$dcms = $DB->get_record('dcms', array('id'=>$coursemodule->instance),
            'id, name, display, displayoptions, intro, introformat')) {
        return NULL;
    }

    $info = new cached_cm_info();
    $info->name = $dcms->name;

    if ($coursemodule->showsiteintro) {
        // Convert intro to html. Do not filter cached version, filters run at display time.
        $info->content = format_module_intro('dcms', $dcms, $coursemodule->id, false);
    }

    if ($dcms->display != RESOURCELIB_DISPLAY_POPUP) {
        return $info;
    }

    $fullurl = "$CFG->wwwroot/local/dcms/view.php?id=$coursemodule->id&amp;inpopup=1";
    $options = empty($dcms->displayoptions) ? array() : unserialize($dcms->displayoptions);
    $width  = empty($options['popupwidth'])  ? 620 : $options['popupwidth'];
    $height = empty($options['popupheight']) ? 450 : $options['popupheight'];
    $wh = "width=$width,height=$height,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
    $info->onclick = "window.open('$fullurl', '', '$wh'); return false;";

    return $info;
}


/**
 * Lists all browsable file areas
 *
 * @package  local_dcms
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @return array
 */
function dcms_get_file_areas($course, $cm, $context) {
    $areas = array();
    $areas['content'] = get_string('content', 'local_dcms');
    return $areas;
}

/**
 * File browsing support for dcms module content area.
 *
 * @package  local_dcms
 * @category files
 * @param stdClass $browser file browser instance
 * @param stdClass $areas file areas
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param int $itemid item ID
 * @param string $filepath file path
 * @param string $filename file name
 * @return file_info instance or null if not found
 */
function dcms_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    global $CFG;

    if (!has_capability('moodle/course:managefiles', $context)) {
        // students can not peak here!
        return null;
    }

    $fs = get_file_storage();

    if ($filearea === 'content') {
        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        if (!$storedfile = $fs->get_file($context->id, 'local_dcms', 'content', 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, 'local_dcms', 'content', 0);
            } else {
                // not found
                return null;
            }
        }
        return new dcms_content_file_info($browser, $context, $storedfile, $urlbase, $areas[$filearea], true, true, true, false);
    }

    // note: dcms_intro handled in file_browser automatically

    return null;
}

/**
 * Serves the dcms files.
 *
 * @package  local_dcms
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - just send the file
 */
function dcms_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

//    require_course_login($course, true, $cm);
    if (!has_capability('local/dcms:view', $context)) {
        return false;
    }

    if ($filearea !== 'content') {
        // intro is handled automatically in pluginfile.php
        return false;
    }

    // $arg could be revision number or index.html
    $arg = array_shift($args);
    if ($arg == 'index.html' || $arg == 'index.htm') {
        // serve dcms content
        $filename = $arg;

        if (!$dcms = $DB->get_record('dcms', array('id'=>$cm->instance), '*', MUST_EXIST)) {
            return false;
        }

        // We need to rewrite the pluginfile URLs so the media filters can work.
        $content = file_rewrite_pluginfile_urls($dcms->content, 'webservice/pluginfile.php', $context->id, 'local_dcms', 'content',
                                                $dcms->revision);
        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->overflowdiv = true;
        $formatoptions->context = $context;

        // Remove @@PLUGINFILE@@/.
        $options = array('reverse' => true);
        $content = file_rewrite_pluginfile_urls($content, 'webservice/pluginfile.php', $context->id, 'local_dcms', 'content',
                                                $dcms->revision, $options);
        $content = str_replace('@@PLUGINFILE@@/', '', $content);

        send_file($content, $filename, 0, 0, true, true);
    } else {
        $fs = get_file_storage();
        $relativepath = implode('/', $args);
        $fullpath = "/$context->id/local_dcms/$filearea/0/$relativepath";
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            $dcms = $DB->get_record('dcms', array('id'=>$cm->instance), 'id, legacyfiles', MUST_EXIST);
            if ($dcms->legacyfiles != RESOURCELIB_LEGACYFILES_ACTIVE) {
                return false;
            }
            if (!$file = resourcelib_try_file_migration('/'.$relativepath, $cm->id, $cm->course, 'local_dcms', 'content', 0)) {
                return false;
            }
            //file migrate - update flag
            $dcms->legacyfileslast = time();
            $DB->update_record('dcms', $dcms);
        }

        // finally send the file
        send_stored_file($file, null, 0, $forcedownload, $options);
    }
}

/**
 * Return a list of dcms types
 * @param string $dcmstype current dcms type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function dcms_page_type_list($dcmstype, $parentcontext, $currentcontext) {
    $module_dcmstype = array('mod-dcms-*'=>get_string('dcms-mod-dcms-x', 'local_dcms'));
    return $module_dcmstype;
}

/**
 * Export dcms resource contents
 *
 * @return array of file content
 */
function dcms_export_contents($cm, $baseurl) {
    global $CFG, $DB;
    $contents = array();
    $context = context_module::instance($cm->id);

    $dcms = $DB->get_record('dcms', array('id'=>$cm->instance), '*', MUST_EXIST);

    // dcms contents
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'local_dcms', 'content', 0, 'sortorder DESC, id ASC', false);
    foreach ($files as $fileinfo) {
        $file = array();
        $file['type']         = 'file';
        $file['filename']     = $fileinfo->get_filename();
        $file['filepath']     = $fileinfo->get_filepath();
        $file['filesize']     = $fileinfo->get_filesize();
        $file['fileurl']      = file_encode_url("$CFG->wwwroot/" . $baseurl, '/'.$context->id.'/local_dcms/content/'.$dcms->revision.$fileinfo->get_filepath().$fileinfo->get_filename(), true);
        $file['timecreated']  = $fileinfo->get_timecreated();
        $file['timemodified'] = $fileinfo->get_timemodified();
        $file['sortorder']    = $fileinfo->get_sortorder();
        $file['userid']       = $fileinfo->get_userid();
        $file['author']       = $fileinfo->get_author();
        $file['license']      = $fileinfo->get_license();
        $file['mimetype']     = $fileinfo->get_mimetype();
        $file['isexternalfile'] = $fileinfo->is_external_file();
        if ($file['isexternalfile']) {
            $file['repositorytype'] = $fileinfo->get_repository_type();
        }
        $contents[] = $file;
    }

    // dcms html conent
    $filename = 'index.html';
    $dcmsfile = array();
    $dcmsfile['type']         = 'file';
    $dcmsfile['filename']     = $filename;
    $dcmsfile['filepath']     = '/';
    $dcmsfile['filesize']     = 0;
    $dcmsfile['fileurl']      = file_encode_url("$CFG->wwwroot/" . $baseurl, '/'.$context->id.'/local_dcms/content/' . $filename, true);
    $dcmsfile['timecreated']  = null;
    $dcmsfile['timemodified'] = $dcms->timemodified;
    // make this file as main file
    $dcmsfile['sortorder']    = 1;
    $dcmsfile['userid']       = null;
    $dcmsfile['author']       = null;
    $dcmsfile['license']      = null;
    $contents[] = $dcmsfile;

    return $contents;
}

/**
 * Register the ability to handle drag and drop file uploads
 * @return array containing details of the files / types the mod can handle
 */
function dcms_dndupload_register() {
    return array('types' => array(
                     array('identifier' => 'text/html', 'message' => get_string('createpage', 'local_dcms')),
                     array('identifier' => 'text', 'message' => get_string('createpage', 'local_dcms'))
                 ));
}

/**
 * Handle a file that has been uploaded
 * @param object $uploadinfo details of the file / content that has been uploaded
 * @return int instance id of the newly created mod
 */
function dcms_dndupload_handle($uploadinfo) {
    // Gather the required info.
    $data = new stdClass();
    $data->course = $uploadinfo->course->id;
    $data->name = $uploadinfo->displayname;
    $data->intro = '<p>'.$uploadinfo->displayname.'</p>';
    $data->introformat = FORMAT_HTML;
    if ($uploadinfo->type == 'text/html') {
        $data->content = clean_param($uploadinfo->content, PARAM_CLEANHTML);
    } else {
        $data->content = clean_param($uploadinfo->content, PARAM_TEXT);
    }
    $data->coursemodule = $uploadinfo->coursemodule;

    // Set the display options to the site defaults.
    $config = get_config('dcms');
    $data->display = $config->display;
    $data->popupheight = $config->popupheight;
    $data->popupwidth = $config->popupwidth;
    $data->printheading = $config->printheading;
    $data->printintro = $config->printintro;
    $data->printlastmodified = $config->printlastmodified;

    return dcms_add_instance($data, null);
}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @param  stdClass $dcms       dcms object
 * @param  stdClass $course     course object
 * @param  stdClass $cm         course module object
 * @param  stdClass $context    context object
 * @since Moodle 3.0
 */
function dcms_view($dcms, $course, $cm, $context) {

    // Trigger course_module_viewed event.
    $params = array(
        'context' => $context,
        'objectid' => $dcms->id
    );

    $event = \local_dcms\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('dcms', $dcms);
    $event->trigger();

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}

/**
 * Check if the module has any update that affects the current user since a given time.
 *
 * @param  cm_info $cm course module data
 * @param  int $from the time to check updates from
 * @param  array $filter  if we need to check only specific updates
 * @return stdClass an object with the different type of areas indicating if they were updated or not
 * @since Moodle 3.2
 */
function dcms_check_updates_since(cm_info $cm, $from, $filter = array()) {
    $updates = course_check_module_updates_since($cm, $from, array('content'), $filter);
    return $updates;
}

/**
 * This function receives a calendar event and returns the action associated with it, or null if there is none.
 *
 * This is used by block_myoverview in order to display the event appropriately. If null is returned then the event
 * is not displayed on the block.
 *
 * @param calendar_event $event
 * @param \core_calendar\action_factory $factory
 * @return \core_calendar\local\event\entities\action_interface|null
 */
function local_dcms_core_calendar_provide_event_action(calendar_event $event,
                                                      \core_calendar\action_factory $factory, $userid = 0) {
    global $USER;
    if (empty($userid)) {
        $userid = $USER->id;
    }

    $cm = get_fast_modinfo($event->courseid, $userid)->instances['dcms'][$event->instance];
    $completion = new \completion_info($cm->get_course());
    $completiondata = $completion->get_data($cm, false, $userid);

    if ($completiondata->completionstate != COMPLETION_INCOMPLETE) {
        return null;
    }

    return $factory->create_instance(
        get_string('view'),
        new \moodle_url('/local/dcms/view.php', ['id' => $cm->id]),
        1,
        true
    );
}

/**
 * Given an array with a file path, it returns the itemid and the filepath for the defined filearea.
 *
 * @param  string $filearea The filearea.
 * @param  array  $args The path (the part after the filearea and before the filename).
 * @return array The itemid and the filepath inside the $args path, for the defined filearea.
 */
function local_dcms_get_path_from_pluginfile(string $filearea, array $args) : array {
    // dcms never has an itemid (the number represents the revision but it's not stored in database).
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
function local_dcms_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    $itemid = array_shift($args);
    $filename = array_pop($args);

    if (!$args) {
        $filepath = '/';
    } else {
        $filepath = '/' . implode('/', $args) . '/';
    }

    $fs = get_file_storage();

    $file = $fs->get_file($context->id, 'local_dcms', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false;
    }
    send_stored_file($file, 0, 0, $forcedownload, $options);
}

function local_dcms_get_image_url($userid) {
    $context = context_system::instance();

    $fs = get_file_storage();
    if ($files = $fs->get_area_files($context->id, 'local_dcms', 'user_photo')) {

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

function  local_convert_image_url (stdClass $fromform) {
    $image = '';
    $context = context_system::instance();

    $filemanageropts = array('subdirs' => 0, 'maxbytes' => '0', 'maxfiles' => 50, 'context' => $context);
    //adding a new feature
    file_save_draft_area_files( $fromform->draftid, $context->id, 'local_dcms', 'attachment',  $fromform->draftid, $filemanageropts);

    if ($fromform->draftid) {
        $fs = get_file_storage();

        if ($files = $fs->get_area_files($context->id, 'local_dcms', 'attachment',  $fromform->draftid, 'sortorder', false)) {
            // Look through each file being managed
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
 * @param int $courseid
 * @return array
 * @throws dml_exception
 * For viewing the image link with content this function is used
 */

function get_picurl_with_content_director () {
    global $DB;
    $SQL = 'SELECT *
            FROM {dcms_directorurl} sp 
            JOIN {dcms_director} sf
            WHERE sp.draftid = sf.draftid';

    $messages = $DB->get_records_sql($SQL);

    return $messages;
}

/**
 * @param $courseid
 * @return array
 * @throws dml_exception
 */
function get_director() {
    global $DB;
    $SQL_directors = 'SELECT sb.id, sb.directorname as director_name, sbu.picurl as director_image
                        FROM {dcms_director} sb
                        JOIN {dcms_directorurl} sbu';

    $director = $DB->get_records_sql($SQL_directors);
    return $director;
}



/**
 * @param int $courseid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_directorurl (stdClass $fromform, int $messageid) {
    global $DB;
    $image = local_convert_image_url($fromform);
    $object = new stdClass();
    $object->id = $messageid;
    $object->picurl = $image;
    $object->draftid = $fromform->draftid;
    $object->timemodified = time();

    $DB->update_record('dcms_directorurl', $object, ['id' => $messageid]);

}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_directorurl(stdClass $fromform)
{
    global $DB;
    $image = local_convert_image_url($fromform);
    $object = new stdClass();
    $object->picurl = $image;
    $object->draftid = $fromform->draftid;
    $object->timemodified = time();

    $DB->insert_record('dcms_directorurl', $object);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */

function insert_record_director (stdClass $fromform) {
    global $DB;
    $record_to_insert = new stdClass();
    $record_to_insert->draftid = $fromform->draftid;
    $record_to_insert->directorname = $fromform->directorname;
    $record_to_insert->directorname_bn = $fromform->directorname_bn;
    $record_to_insert->directordeg = $fromform->directordeg;
    $record_to_insert->directordeg_bn = $fromform->directordeg_bn;
    if(!$fromform->email) {
        $record_to_insert->email = null;
    } else {
        $record_to_insert->email = $fromform->email;
    }
    $record_to_insert->tier = $fromform->tier;
    $record_to_insert->timemodified = time();

    $DB->insert_record('dcms_director', $record_to_insert, false);
}

/**
 * @param $courseid
 * @param $fromform
 * @param $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_director(stdClass $fromform, int $messageid) {
    global $DB;
    $object = new stdClass();
    $object->id = $messageid;
    $object->draftid = $fromform->draftid;
    $object->directorname = $fromform->directorname;
    $object->directorname_bn = $fromform->directorname_bn;
    $object->directordeg = $fromform->directordeg;
    $object->directordeg_bn = $fromform->directordeg_bn;
    if(!$fromform->email) {
        $object->email = null;
    } else {
        $object->email = $fromform->email;
    }
    $object->tier = $fromform->tier;
    $object->timemodified = time();

    $DB->update_record('dcms_director', $object, ['id' => $messageid]);
}

// Founder part functions.


/**
 * @param int $courseid
 * @return array
 * @throws dml_exception
 * For viewing the image link with content this function is used
 */

function get_picurl_with_content_founder () {
    global $DB;
    $SQL = 'SELECT *
            FROM {dcms_founderurl} sp 
            JOIN {dcms_founder} sf
            WHERE sp.draftid = sf.draftid';

    $messages = $DB->get_records_sql($SQL);

    return $messages;
}

/**
 * @param $courseid
 * @return array
 * @throws dml_exception
 */
function get_founder() {
    global $DB;
    $SQL_founders = 'SELECT sb.id, sb.foundername as founder_name, sbu.picurl as founder_image
                        FROM {dcms_founder} sb
                        JOIN {dcms_founderurl} sbu';

    $founder = $DB->get_records_sql($SQL_founders);
    return $founder;
}



/**
 * @param int $courseid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_founderurl (stdClass $fromform, int $messageid) {
    global $DB;
    $image = local_convert_image_url($fromform);
    $object = new stdClass();
    $object->id = $messageid;
    $object->picurl = $image;
    $object->draftid = $fromform->draftid;
    $object->timemodified = time();

    $DB->update_record('dcms_founderurl', $object, ['id' => $messageid]);

}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_founderurl(stdClass $fromform)
{
    global $DB;
    $image = local_convert_image_url($fromform);
    $object = new stdClass();
    $object->picurl = $image;
    $object->draftid = $fromform->draftid;
    $object->timemodified = time();

    $DB->insert_record('dcms_founderurl', $object);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */

function insert_record_founder (stdClass $fromform) {
    global $DB;
    $record_to_insert = new stdClass();
    $record_to_insert->draftid = $fromform->draftid;
    $record_to_insert->foundername = $fromform->foundername;
    $record_to_insert->foundername_bn = $fromform->foundername_bn;
    $record_to_insert->founderdeg = $fromform->founderdeg;
    $record_to_insert->founderdeg_bn = $fromform->founderdeg_bn;
    if(!$fromform->email) {
        $record_to_insert->email = null;
    } else {
        $record_to_insert->email = $fromform->email;
    }
    $record_to_insert->tier = $fromform->tier;
    $record_to_insert->timemodified = time();

    $DB->insert_record('dcms_founder', $record_to_insert, false);
}

/**
 * @param $courseid
 * @param $fromform
 * @param $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_founder(stdClass $fromform, int $messageid) {
    global $DB;
    $object = new stdClass();
    $object->id = $messageid;
    $object->draftid = $fromform->draftid;
    $object->foundername = $fromform->foundername;
    $object->foundername_bn = $fromform->foundername_bn;
    $object->founderdeg = $fromform->founderdeg;
    $object->founderdeg_bn = $fromform->founderdeg_bn;
    if(!$fromform->email) {
        $object->email = null;
    } else {
        $object->email = $fromform->email;
    }
    $object->tier = $fromform->tier;
    $object->timemodified = time();

    $DB->update_record('dcms_founder', $object, ['id' => $messageid]);
}

// Instructor Part Functions.

/**
 * @param int $courseid
 * @return array
 * @throws dml_exception
 * For viewing the image link with content this function is used
 */

function get_picurl_with_content_instructor () {
    global $DB;
    $SQL = 'SELECT *
            FROM {dcms_instructorurl} sp 
            JOIN {dcms_instructor} sf
            WHERE sp.draftid = sf.draftid';

    $messages = $DB->get_records_sql($SQL);

    return $messages;
}

/**
 * @param $courseid
 * @return array
 * @throws dml_exception
 */
function get_instructor() {
    global $DB;
    $SQL_instructors = 'SELECT sb.id, sb.instructorname as instructor_name, sbu.picurl as instructor_image
                        FROM {dcms_instructor} sb
                        JOIN {dcms_instructorurl} sbu';

    $instructor = $DB->get_records_sql($SQL_instructors);
    return $instructor;
}



/**
 * @param int $courseid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_instructorurl (stdClass $fromform, int $messageid) {
    global $DB;
    $image = local_convert_image_url($fromform);
    $object = new stdClass();
    $object->id = $messageid;
    $object->picurl = $image;
    $object->draftid = $fromform->draftid;
    $object->timemodified = time();

    $DB->update_record('dcms_instructorurl', $object, ['id' => $messageid]);

}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_instructorurl(stdClass $fromform)
{
    global $DB;
    $image = local_convert_image_url($fromform);
    $object = new stdClass();
    $object->picurl = $image;
    $object->draftid = $fromform->draftid;
    $object->timemodified = time();

    $DB->insert_record('dcms_instructorurl', $object);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */

function insert_record_instructor (stdClass $fromform) {
    global $DB;
    $record_to_insert = new stdClass();
    $record_to_insert->draftid = $fromform->draftid;
    $record_to_insert->instructorname = $fromform->instructorname;
    $record_to_insert->instructorname_bn = $fromform->instructorname_bn;
    $record_to_insert->instructordeg = $fromform->instructordeg;
    $record_to_insert->instructordeg_bn = $fromform->instructordeg_bn;
    if(!$fromform->email) {
        $record_to_insert->email = null;
    } else {
        $record_to_insert->email = $fromform->email;
    }
    $record_to_insert->tier = $fromform->tier;
    $record_to_insert->timemodified = time();

    $DB->insert_record('dcms_instructor', $record_to_insert, false);
}

/**
 * @param $courseid
 * @param $fromform
 * @param $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_instructor(stdClass $fromform, int $messageid) {
    global $DB;
    $object = new stdClass();
    $object->id = $messageid;
    $object->draftid = $fromform->draftid;
    $object->instructorname = $fromform->instructorname;
    $object->instructorname_bn = $fromform->instructorname_bn;
    $object->instructordeg = $fromform->instructordeg;
    $object->instructordeg_bn = $fromform->instructordeg_bn;
    if(!$fromform->email) {
        $object->email = null;
    } else {
        $object->email = $fromform->email;
    }
    $object->tier = $fromform->tier;
    $object->timemodified = time();

    $DB->update_record('dcms_instructor', $object, ['id' => $messageid]);
}

// Operation Function.


/**
 * @param int $courseid
 * @return array
 * @throws dml_exception
 * For viewing the image link with content this function is used
 */

function get_picurl_with_content_operation () {
    global $DB;
    $SQL = 'SELECT *
            FROM {dcms_operationurl} sp 
            JOIN {dcms_operation} sf
            WHERE sp.draftid = sf.draftid';

    $messages = $DB->get_records_sql($SQL);

    return $messages;
}

/**
 * @param $courseid
 * @return array
 * @throws dml_exception
 */
function get_operation() {
    global $DB;
    $SQL_operations = 'SELECT sb.id, sb.operationname as operation_name, sbu.picurl as operation_image
                        FROM {dcms_operation} sb
                        JOIN {dcms_operationurl} sbu';

    $operation = $DB->get_records_sql($SQL_operations);
    return $operation;
}



/**
 * @param int $courseid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_operationurl (stdClass $fromform, int $messageid) {
    global $DB;
    $image = local_convert_image_url($fromform);
    $object = new stdClass();
    $object->id = $messageid;
    $object->picurl = $image;
    $object->draftid = $fromform->draftid;
    $object->timemodified = time();

    $DB->update_record('dcms_operationurl', $object, ['id' => $messageid]);

}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_operationurl(stdClass $fromform)
{
    global $DB;
    $image = local_convert_image_url($fromform);
    $object = new stdClass();
    $object->picurl = $image;
    $object->draftid = $fromform->draftid;
    $object->timemodified = time();

    $DB->insert_record('dcms_operationurl', $object);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */

function insert_record_operation (stdClass $fromform) {
    global $DB;
    $record_to_insert = new stdClass();
    $record_to_insert->draftid = $fromform->draftid;
    $record_to_insert->operationname = $fromform->operationname;
    $record_to_insert->operationname_bn = $fromform->operationname_bn;
    $record_to_insert->operationdeg = $fromform->operationdeg;
    $record_to_insert->operationdeg_bn = $fromform->operationdeg_bn;
    $record_to_insert->operationmail = $fromform->operationmail;
    $record_to_insert->tier = $fromform->tier;
    $record_to_insert->timemodified = time();

    $DB->insert_record('dcms_operation', $record_to_insert, false);
}

/**
 * @param $courseid
 * @param $fromform
 * @param $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_operation(stdClass $fromform, int $messageid) {
    global $DB;
    $object = new stdClass();
    $object->id = $messageid;
    $object->draftid = $fromform->draftid;
    $object->operationname = $fromform->operationname;
    $object->operationname_bn = $fromform->operationname_bn;
    $object->operationmail = $fromform->operationmail;
    $object->operationdeg = $fromform->operationdeg;
    $object->operationdeg_bn = $fromform->operationdeg_bn;
    $object->tier = $fromform->tier;
    $object->timemodified = time();

    $DB->update_record('dcms_operation', $object, ['id' => $messageid]);
}


// Site Intro Functions.

/**
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_siteintro (stdClass $fromform, int $messageid){
    global $DB;
    $object = new stdClass();
    $object->id = $messageid;
    $object->siteintro = $fromform->siteintro;
    $object->siteintro_bn = $fromform->siteintro_bn;
    $object->timemodified = time();

    $DB->update_record('dcms_siteintro', $object, ['id' => $messageid]);
}

/**
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_siteintro (stdClass $fromform){
    global $DB;

    $record_to_insert = new stdClass();
    $record_to_insert->siteintro = $fromform->siteintro;
    $record_to_insert->siteintro_bn = $fromform->siteintro_bn;
    $record_to_insert->timemodified = time();

    $DB->insert_record('dcms_siteintro', $record_to_insert, false);
}


// Footer functions.

/**
 * Inserts record into dcms_footer table.
 * 
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_footer (stdClass $fromform){
    global $DB;

    $record_to_insert = new stdClass();
    $record_to_insert->name = $fromform->name;
    $record_to_insert->title = $fromform->title;
    $record_to_insert->description = $fromform->description['text'];
    $record_to_insert->name_bn = $fromform->name_bn;
    $record_to_insert->title_bn = $fromform->title_bn;
    $record_to_insert->description_bn = $fromform->description_bn['text'];
    $record_to_insert->timemodified = time();

    $DB->insert_record('dcms_footer', $record_to_insert, false);
}

/**
 * Update record of dcms_footer table from id.
 * 
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_footer (stdClass $fromform, int $id){
    global $DB;
    $object = new stdClass();
    $object->id = $id;
    $object->name = $fromform->name;
    $object->title = $fromform->title;
    $object->description = $fromform->description['text'];

    $object->name_bn = $fromform->name_bn;
    $object->title_bn = $fromform->title_bn;
    $object->description_bn = $fromform->description_bn['text'];
    $object->timemodified = time();

    $DB->update_record('dcms_footer', $object, ['id' => $id]);
}

/**
 * Returns the list of all footer links.
 * 
 * @return array
 * @throws dml_exception
 */
function local_dcms_get_footer_links() {
    global $DB;
    // $sql = 'SELECT name, title, description
    //         FROM {dcms_footer}';

    // $footerlinks = $DB->get_records_sql($sql);
    // return $footerlinks;
    return $DB->get_records('dcms_footer');
}

// Our Story Functions.

/**
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_ourstory (stdClass $fromform, int $messageid){
    global $DB;
    $object = new stdClass();
    $object->id = $messageid;
    $object->ourstory = $fromform->ourstory;
    $object->ourstory_bn = $fromform->ourstory_bn;
    $object->timemodified = time();

    $DB->update_record('dcms_ourstory', $object, ['id' => $messageid]);
}

/**
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_ourstory (stdClass $fromform){
    global $DB;

    $record_to_insert = new stdClass();
    $record_to_insert->ourstory = $fromform->ourstory;
    $record_to_insert->ourstory_bn = $fromform->ourstory_bn;
    $record_to_insert->timemodified = time();

    $DB->insert_record('dcms_ourstory', $record_to_insert, false);
}

// Vision Functions.

/**
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_vision (stdClass $fromform, int $messageid){
    global $DB;
    $object = new stdClass();
    $object->id = $messageid;
    $object->vision = $fromform->vision;
    $object->vision_bn = $fromform->vision_bn;
    $object->timemodified = time();

    $DB->update_record('dcms_vision', $object, ['id' => $messageid]);
}

/**
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_vision (stdClass $fromform) {
    global $DB;

    $record_to_insert = new stdClass();
    $record_to_insert->vision = $fromform->vision;
    $record_to_insert->vision_bn = $fromform->vision_bn;
    $record_to_insert->timemodified = time();

    $DB->insert_record('dcms_vision', $record_to_insert, false);
}

// Key Partner Functions.


/**
 * @param int $courseid
 * @return array
 * @throws dml_exception
 * For viewing the image link with content this function is used
 */

function get_picurl_with_content_partner () {
    global $DB;
    $SQL = 'SELECT *
            FROM {dcms_partnerurl} sp 
            JOIN {dcms_partner} sf
            WHERE sp.draftid = sf.draftid';

    $messages = $DB->get_records_sql($SQL);

    return $messages;
}

/**
 * @param $courseid
 * @return array
 * @throws dml_exception
 */
function get_partner() {
    global $DB;
    $SQL_partners = 'SELECT sb.id, sb.partnername as partner_name, sbu.picurl as partner_image
                        FROM {dcms_partner} sb
                        JOIN {dcms_partnerurl} sbu';

    $partner = $DB->get_records_sql($SQL_partners);
    return $partner;
}



/**
 * @param int $courseid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_partnerurl (stdClass $fromform, int $messageid) {
    global $DB;
    $image = local_convert_image_url($fromform);
    $object = new stdClass();
    $object->id = $messageid;
    $object->picurl = $image;
    $object->draftid = $fromform->draftid;
    $object->timemodified = time();

    $DB->update_record('dcms_partnerurl', $object, ['id' => $messageid]);

}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_partnerurl(stdClass $fromform)
{
    global $DB;
    $image = local_convert_image_url($fromform);
    $object = new stdClass();
    $object->picurl = $image;
    $object->draftid = $fromform->draftid;
    $object->timemodified = time();

    $DB->insert_record('dcms_partnerurl', $object);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */

function insert_record_partner (stdClass $fromform) {
    global $DB;
    $record_to_insert = new stdClass();
    $record_to_insert->draftid = $fromform->draftid;
    $record_to_insert->partnername = $fromform->partnername;
    $record_to_insert->partnername_bn = $fromform->partnername_bn;
    $record_to_insert->timemodified = time();

    $DB->insert_record('dcms_partner', $record_to_insert, false);
}

/**
 * @param $courseid
 * @param $fromform
 * @param $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_partner(stdClass $fromform, int $messageid) {
    global $DB;
    $object = new stdClass();
    $object->id = $messageid;
    $object->draftid = $fromform->draftid;
    $object->partnername = $fromform->partnername;
    $object->partnername_bn = $fromform->partnername_bn;
    $object->timemodified = time();

    $DB->update_record('dcms_partner', $object, ['id' => $messageid]);
}

// Feedback part functions.

/**
 * @param int $courseid
 * @return array
 * @throws dml_exception
 * For viewing the image link with content this function is used
 */

function get_picurl_with_content_feedback () {
    global $DB;
    $SQL = 'SELECT *
            FROM {dcms_feedbackurl} sp 
            JOIN {dcms_feedback} sf
            WHERE sp.draftid = sf.draftid';

    $messages = $DB->get_records_sql($SQL);

    return $messages;
}

/**
 * @param $courseid
 * @return array
 * @throws dml_exception
 */
function get_feedback() {
    global $DB;
    $SQL_feedbacks = 'SELECT sb.id, sb.feedbackname as feedback_name, sb.position, sb.company, sb.subject, sbu.picurl as feedback_image, sb.feedbacktext
                        FROM {dcms_feedback} sb
                        JOIN {dcms_feedbackurl} sbu';

    $feedback = $DB->get_records_sql($SQL_feedbacks);
    return $feedback;
}



/**
 * @param int $courseid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_feedbackurl (stdClass $fromform, int $messageid) {
    global $DB;
    $image = local_convert_image_url($fromform);
    $object = new stdClass();
    $object->id = $messageid;
    $object->picurl = $image;
    $object->draftid = $fromform->draftid;
    $object->timemodified = time();

    $DB->update_record('dcms_feedbackurl', $object, ['id' => $messageid]);

}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_feedbackurl(stdClass $fromform)
{
    global $DB;
    $image = local_convert_image_url($fromform);
    $object = new stdClass();
    $object->picurl = $image;
    $object->draftid = $fromform->draftid;
    $object->timemodified = time();

    $DB->insert_record('dcms_feedbackurl', $object);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */

function insert_record_feedback (stdClass $fromform) {
    global $DB;
    $record_to_insert = new stdClass();
    $record_to_insert->draftid = $fromform->draftid;
    $record_to_insert->feedbackname = $fromform->feedbackname;
    $record_to_insert->company = $fromform->company;
    $record_to_insert->position = $fromform->position;
    $record_to_insert->subject = $fromform->subject;
    $record_to_insert->feedbacktext = $fromform->feedbacktext;
    $record_to_insert->feedbackname_bn = $fromform->feedbackname_bn;
    $record_to_insert->company_bn = $fromform->company_bn;
    $record_to_insert->position_bn = $fromform->position_bn;
    $record_to_insert->subject_bn = $fromform->subject_bn;
    $record_to_insert->feedbacktext_bn = $fromform->feedbacktext_bn;
    $record_to_insert->timemodified = time();

    $DB->insert_record('dcms_feedback', $record_to_insert, false);
}

/**
 * @param $courseid
 * @param $fromform
 * @param $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_feedback(stdClass $fromform, int $messageid) {
    global $DB;
    $object = new stdClass();
    $object->id = $messageid;
    $object->draftid = $fromform->draftid;
    $object->feedbackname = $fromform->feedbackname;
    $object->position = $fromform->position;
    $object->company = $fromform->company;
    $object->subject = $fromform->subject;
    $object->feedbacktext = $fromform->feedbacktext;
    $object->feedbackname_bn = $fromform->feedbackname_bn;
    $object->position_bn = $fromform->position_bn;
    $object->company_bn = $fromform->company_bn;
    $object->subject_bn = $fromform->subject_bn;
    $object->feedbacktext_bn = $fromform->feedbacktext_bn;
    $object->timemodified = time();

    $DB->update_record('dcms_feedback', $object, ['id' => $messageid]);
}

// Who is Vumi for functions.


/**
 * @param int $courseid
 * @return array
 * @throws dml_exception
 * For viewing the image link with content this function is used
 */

function get_picurl_with_content_vumifor () {
    global $DB;
    $SQL = 'SELECT *
            FROM {dcms_vumiforurl} sp 
            JOIN {dcms_vumifor} sf
            WHERE sp.draftid = sf.draftid';

    $messages = $DB->get_records_sql($SQL);

    return $messages;
}

/**
 * @param $courseid
 * @return array
 * @throws dml_exception
 */
function get_vumifor() {
    global $DB;
    $SQL_vumifors = 'SELECT sb.id, sb.vumiforname as vumifor_name, sbu.picurl as vumifor_image
                        FROM {dcms_vumifor} sb
                        JOIN {dcms_vumiforurl} sbu';

    $vumifor = $DB->get_records_sql($SQL_vumifors);
    return $vumifor;
}



/**
 * @param int $courseid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_vumiforurl (stdClass $fromform, int $messageid) {
    global $DB;
    $image = local_convert_image_url($fromform);
    $object = new stdClass();
    $object->id = $messageid;
    $object->picurl = $image;
    $object->draftid = $fromform->draftid;
    $object->timemodified = time();

    $DB->update_record('dcms_vumiforurl', $object, ['id' => $messageid]);

}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_vumiforurl(stdClass $fromform)
{
    global $DB;
    $image = local_convert_image_url($fromform);
    $object = new stdClass();
    $object->picurl = $image;
    $object->draftid = $fromform->draftid;
    $object->timemodified = time();

    $DB->insert_record('dcms_vumiforurl', $object);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */

function insert_record_vumifor (stdClass $fromform) {
    global $DB;
    $record_to_insert = new stdClass();
    $record_to_insert->draftid = $fromform->draftid;
    $record_to_insert->vumiforname = $fromform->vumiforname;
    $record_to_insert->vumiforname_bn = $fromform->vumiforname_bn;
    $record_to_insert->timemodified = time();

    $DB->insert_record('dcms_vumifor', $record_to_insert, false);
}

/**
 * @param $courseid
 * @param $fromform
 * @param $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_vumifor(stdClass $fromform, int $messageid) {
    global $DB;
    $object = new stdClass();
    $object->id = $messageid;
    $object->draftid = $fromform->draftid;
    $object->vumiforname = $fromform->vumiforname;
    $object->vumiforname_bn = $fromform->vumiforname_bn;
    $object->timemodified = time();

    $DB->update_record('dcms_vumifor', $object, ['id' => $messageid]);
}

// Our Strength functions.

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */

function insert_record_strength (stdClass $fromform) {
    global $DB;
    $record_to_insert = new stdClass();
    $record_to_insert->strengthname = $fromform->strengthname;
    $record_to_insert->strengthname_bn = $fromform->strengthname_bn;
    $record_to_insert->strengthbody = $fromform->strengthbody;
    $record_to_insert->strengthbody_bn = $fromform->strengthbody_bn;
    $record_to_insert->timemodified = time();

    $DB->insert_record('dcms_strength', $record_to_insert, false);
}

/**
 * @param $courseid
 * @param $fromform
 * @param $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_strength (stdClass $fromform, int $messageid) {
    global $DB;
    $object = new stdClass();
    $object->id = $messageid;
    $object->strengthname = $fromform->strengthname;
    $object->strengthname_bn = $fromform->strengthname_bn;
    $object->strengthbody = $fromform->strengthbody;
    $object->strengthbody_bn = $fromform->strengthbody_bn;
    $object->timemodified = time();

    $DB->update_record('dcms_strength', $object, ['id' => $messageid]);
}

/**
 * @param int $courseid
 * @return array
 * @throws dml_exception
 * For viewing the image link with content this function is used
 */

function get_picurl_with_content_strength () {
    global $DB;
    $SQL = 'SELECT *
            FROM {dcms_strength}';

    $messages = $DB->get_records_sql($SQL);

    return $messages;
}

// Why Vumi Functions.

/**
 * @param int $courseid
 * @return array
 * @throws dml_exception
 * For viewing the image link with content this function is used
 */

function get_picurl_with_content_whyvumi () {
    global $DB;
    $SQL = 'SELECT *
            FROM {dcms_whyvumi}';
    $messages = $DB->get_records_sql($SQL);

    return $messages;
}

/**
 * @param $courseid
 * @return array
 * @throws dml_exception
 */
function get_whyvumi() {
    global $DB;
    $SQL_whyvumis = 'SELECT sb.id, sb.whyvumitext as whyvumi_name, sbu.picurl as whyvumi_image
                        FROM {dcms_whyvumi} sb
                        JOIN {dcms_whyvumiurl} sbu';

    $whyvumi = $DB->get_records_sql($SQL_whyvumis);
    return $whyvumi;
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */

function insert_record_whyvumi (stdClass $fromform) {
    global $DB;
    $record_to_insert = new stdClass();
    $record_to_insert->whyvumitext = $fromform->whyvumitext['text'];
    $record_to_insert->whyvumitext_bn = $fromform->whyvumitext_bn['text'];
    $record_to_insert->timemodified = time();

    $DB->insert_record('dcms_whyvumi', $record_to_insert, false);
}

/**
 * @param $courseid
 * @param $fromform
 * @param $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_whyvumi(stdClass $fromform, int $messageid) {
    global $DB;
    $object = new stdClass();
    $object->id = $messageid;
    $object->whyvumitext = $fromform->whyvumitext['text'];
    $object->whyvumitext_bn = $fromform->whyvumitext_bn['text'];
    $object->timemodified = time();

    $DB->update_record('dcms_whyvumi', $object, ['id' => $messageid]);
}