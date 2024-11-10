<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Upgrade file of local_dcms plugin.
 *
 * @package    local_dcms
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 *
 * @param int $oldversion The old version of the local dcms plugin.
 * @return bool
 */
function xmldb_local_dcms_upgrade($oldversion)
{
    global $CFG, $DB;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.


    if ($oldversion < 2023020304) {

        // Adding field email and tier in dcms_director
        $table = new xmldb_table('dcms_director');
        $field1 = new xmldb_field('email', XMLDB_TYPE_TEXT, null, null, null, null, null, 'directordeg');
        $field2 = new xmldb_field('tier', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'email');

        // Conditonally launch add field email.
        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        }

        // Conditonally launch add field tier.
        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        }

        // Adding field email and tier in dcms_founder.
        $table = new xmldb_table('dcms_founder');
        $field1 = new xmldb_field('email', XMLDB_TYPE_TEXT, null, null, null, null, null, 'founderdeg');
        $field2 = new xmldb_field('tier', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'email');

        // Conditonally launch add field email.
        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        }

        // Conditonally launch add field tier.
        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        }

        // Adding field email and tier in dcms_instructor.
        $table = new xmldb_table('dcms_instructor');
        $field1 = new xmldb_field('email', XMLDB_TYPE_TEXT, null, null, null, null, null, 'instructordeg');
        $field2 = new xmldb_field('tier', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'email');

        // Conditonally launch add field email.
        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        }

        // Conditonally launch add field tier.
        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        }

        // Adding field tier in dcms_instructor.
        $table = new xmldb_table('dcms_instructor');
        $field1 = new xmldb_field('tier', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'operationmail');

        // Conditonally launch add field tier.
        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        }

        // Plugin savepoint reached.
        upgrade_plugin_savepoint(true, 2023020304, 'local', 'dcms');
    }

    if ($oldversion < 2023020307) {
        // Define table dcms_footer to be created.
        $table = new xmldb_table('dcms_footer');

        // Adding fields to table dcms_footer. 
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('title', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table enrol_lti_app_registration.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for dcms_footer.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Plugin savepoint reached.
        upgrade_plugin_savepoint(true, 2023020307, 'local', 'dcms');
    }

    if ($oldversion < 2023020313) {

        // Adding field email and tier in dcms_director
        $table = new xmldb_table('dcms_siteintro');
        $field = new xmldb_field('siteintro_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'siteintro');

        // Conditonally launch add field email.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Update the old version in the database.
        upgrade_plugin_savepoint(true, 2023020313, 'local', 'dcms');

    }

    if ($oldversion < 2023020314) {
        // Adding field email and tier in dcms_director
        $table = new xmldb_table('dcms_feedback');
        $field = new xmldb_field('feedbackname_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'feedbacktext');
        $field1 = new xmldb_field('position_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'feedbackname_bn');
        $field2 = new xmldb_field('company_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'position_bn');
        $field3 = new xmldb_field('subject_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'company_bn');
        $field4 = new xmldb_field('feedbacktext_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'subject_bn');

        // Conditonally launch add field email.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        }
        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        }
        if (!$dbman->field_exists($table, $field3)) {
            $dbman->add_field($table, $field3);
        }
        if (!$dbman->field_exists($table, $field4)) {
            $dbman->add_field($table, $field4);
        }

        // Update the old version in the database.
        upgrade_plugin_savepoint(true, 2023020314, 'local', 'dcms');

    }

    if ($oldversion < 2023020315) {
        // Adding field email and tier in dcms_director
        $table = new xmldb_table('dcms_partner');
        $field = new xmldb_field('partnername_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'partnername');

        // Conditonally launch add field email.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Update the old version in the database.
        upgrade_plugin_savepoint(true, 2023020315, 'local', 'dcms');
    }
    if ($oldversion < 2023020316) {
        // Adding field email and tier in dcms_director
        $table = new xmldb_table('dcms_footer');
        $field = new xmldb_field('name_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'description');
        $field1 = new xmldb_field('title_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'name_bn');
        $field2 = new xmldb_field('description_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'title_bn');

        // Conditonally launch add field email.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        }
        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        }

        //Our Teams Page

        $table = new xmldb_table('dcms_director');
        $field1 = new xmldb_field('directorname_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'directorname');
        $field2 = new xmldb_field('directordeg_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'directordeg');

        // Conditonally launch add field email.
        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        }
        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        }

        // Adding field email and tier in dcms_founder.
        $table = new xmldb_table('dcms_founder');
        $field1 = new xmldb_field('foundername_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'foundername');
        $field2 = new xmldb_field('founderdeg_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'founderdeg');

        // Conditonally launch add field email.
        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        }
        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        }

        // Adding field email and tier in dcms_instructor.
        $table = new xmldb_table('dcms_instructor');

        $field1 = new xmldb_field('instructorname_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'instructorname');
        $field2 = new xmldb_field('instructordeg_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'instructordeg');

        // Conditonally launch add field email.
        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        }
        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        }

        //Adding Field in dcms_operation
        $table = new xmldb_table('dcms_operation');
        $field1 = new xmldb_field('operationname_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'operationname');
        $field2 = new xmldb_field('operationdeg_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'operationdeg');

        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        }
        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        }

        // Update the old version in the database.
        upgrade_plugin_savepoint(true, 2023020316, 'local', 'dcms');
    }

    if ($oldversion < 2023020317) {

        $table = new xmldb_table('dcms_ourstory');
        $field1 = new xmldb_field('ourstory_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'ourstory');

        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        }

        $table=new xmldb_table('dcms_vision');
        $field1 = new xmldb_field('vision_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'vision');

        if(!$dbman->field_exists($table,$field1)){
            $dbman->add_field($table, $field1);
        }

        $table=new xmldb_table('dcms_vumifor');
        $field1 = new xmldb_field('vumiforname_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'vumiforname');

        if(!$dbman->field_exists($table,$field1)){
            $dbman->add_field($table, $field1);
        }

        $table=new xmldb_table('dcms_whyvumi');
        $field1 = new xmldb_field('whyvumitext_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'whyvumitext');

        if(!$dbman->field_exists($table,$field1)){
            $dbman->add_field($table, $field1);
        }

        $table=new xmldb_table('dcms_strength');
        $field1 = new xmldb_field('strengthname_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'strengthname');
        $field2 = new xmldb_field('strengthbody_bn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'strengthbody');

        if(!$dbman->field_exists($table,$field1)){
            $dbman->add_field($table, $field1);
        }
        if(!$dbman->field_exists($table,$field2)){
            $dbman->add_field($table, $field2);
        }


        upgrade_plugin_savepoint(true, 2023020317, 'local', 'dcms');

    }
    return true;
}