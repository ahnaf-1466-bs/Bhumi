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
 * Page module upgrade code
 *
 * This file keeps track of upgrades to
 * the resource module
 *
 * Sometimes, changes between versions involve
 * alterations to database structures and other
 * major things that may break installations.
 *
 * The upgrade function in this file will attempt
 * to perform all the necessary actions to upgrade
 * your older installation to the current version.
 *
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 *
 * The commands in here will all be database-neutral,
 * using the methods of database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @package mod_syllabusoverview
 * @copyright 2021 Brain Station 23 LTD.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

function xmldb_syllabusoverview_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2024021500) {

        // Define field fileurl to be added to syllabusoverview_prog_title.
        $table = new xmldb_table('syllabusoverview_courseimg');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0, 'id');
        $table->add_field('course_image', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'course');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'course_image');

        // Adding keys to table syllabusoverview_prog_title.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table syllabusoverview_prog_title.
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, ['course']);

        // Conditionally launch create table for syllabusoverview_prog_title.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Syllabusoverview savepoint reached.
        upgrade_mod_savepoint(true, 2024021500, 'syllabusoverview');
    }

    if ($oldversion < 2024021500) {

        // Define field fileurl to be added to syllabusoverview_prog_title.
        $table = new xmldb_table('syllabusoverview_img_url');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0, 'id');
        $table->add_field('courseimgurl', XMLDB_TYPE_TEXT, null, null, null, null, null, 'course');
        $table->add_field('draftid', XMLDB_TYPE_TEXT, null, null, null, null, null, 'courseimgurl');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'draftid');

        // Adding keys to table syllabusoverview_prog_title.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table syllabusoverview_prog_title.
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, ['course']);

        // Conditionally launch create table for syllabusoverview_prog_title.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Syllabusoverview savepoint reached.
        upgrade_mod_savepoint(true, 2024021500, 'syllabusoverview');
    }

    if ($oldversion < 2024021503) {

        // Define field fileurl to be added to syllabusoverview_prog_title.
        $table = new xmldb_table('syllabusoverview_prog_title');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0, 'id');
        $table->add_field('name', XMLDB_TYPE_TEXT, null, null, null, null, null, 'course');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'name');

        // Adding keys to table syllabusoverview_prog_title.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table syllabusoverview_prog_title.
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, ['course']);

        // Conditionally launch create table for syllabusoverview_prog_title.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define field fileurl to be added to syllabusoverview_prog_data.
        $table1 = new xmldb_table('syllabusoverview_prog_data');
        $table1->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table1->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0, 'id');
        $table1->add_field('title_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0, 'course');
        $table1->add_field('value', XMLDB_TYPE_TEXT, null, null, null, null, null, 'title_id');
        $table1->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'value');

        // Adding keys to table syllabusoverview_prog_data.
        $table1->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table1->add_key('foreign', XMLDB_KEY_FOREIGN, ['title_id']);

        // Adding indexes to table syllabusoverview_prog_data.
        $table1->add_index('course', XMLDB_INDEX_NOTUNIQUE, ['course']);

        // Conditionally launch create table for syllabusoverview_prog_data.
        if (!$dbman->table_exists($table1)) {
            $dbman->create_table($table1);
        }

        // Define field fileurl to be added to syllabusoverview_prog_name.
        $table2 = new xmldb_table('syllabusoverview_prog_name');
        $table2->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table2->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0, 'id');
        $table2->add_field('name', XMLDB_TYPE_TEXT, null, null, null, null, null, 'course');
        $table2->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'name');

        // Adding keys to table syllabusoverview_prog_name.
        $table2->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table syllabusoverview_prog_name.
        $table2->add_index('course', XMLDB_INDEX_NOTUNIQUE, ['course']);

        // Conditionally launch create table for syllabusoverview_prog_name.
        if (!$dbman->table_exists($table2)) {
            $dbman->create_table($table2);
        }

        $table3 = new xmldb_table('syllabusoverview_program');
        $field = new xmldb_field('prog_id', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, 0, 'course');

        // Conditionally launch add field fileurl.
        if (!$dbman->field_exists($table3, $field)) {
            $dbman->add_field($table3, $field);
        }

        $table4  = new xmldb_table('syllabusoverview_prog_pdfurl');
        $field1 = new xmldb_field('programstructure');
        $field2 = new xmldb_field('deadline');
        $field3 = new xmldb_field('length');
        $field4 = new xmldb_field('fee');

        // Conditionally launch drop field programstructure.
        if ($dbman->field_exists($table4, $field1)) {
            $dbman->drop_field($table4, $field1);
        }
        if ($dbman->field_exists($table4, $field2)) {
            $dbman->drop_field($table4, $field2);
        }
        if ($dbman->field_exists($table4, $field3)) {
            $dbman->drop_field($table4, $field3);
        }
        if ($dbman->field_exists($table4, $field4)) {
            $dbman->drop_field($table4, $field4);
        }

        // Syllabusoverview savepoint reached.
        upgrade_mod_savepoint(true, 2024021503, 'syllabusoverview');
    }

    if ($oldversion < 2024021504) {

        // Define field programdate to be added to syllabusoverview_program.
        $table = new xmldb_table('syllabusoverview_program');
        $field = new xmldb_field('programdate_bangla', XMLDB_TYPE_TEXT, null, null, null, null, null, 'programdate');

        // Conditionally launch add field programdate.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table1 = new xmldb_table('syllabusoverview_prog_title');
        $field1 = new xmldb_field('name_bangla', XMLDB_TYPE_TEXT, null, null, null, null, null, 'name');

        if (!$dbman->field_exists($table1, $field1)) {
            $dbman->add_field($table1, $field1);
        }

        $table2 = new xmldb_table('syllabusoverview_prog_data');
        $field2 = new xmldb_field('value_bangla', XMLDB_TYPE_TEXT, null, null, null, null, null, 'value');

        if (!$dbman->field_exists($table2, $field2)) {
            $dbman->add_field($table2, $field2);
        }

        $table3 = new xmldb_table('syllabusoverview_prog_name');
        $field3 = new xmldb_field('name_bangla', XMLDB_TYPE_TEXT, null, null, null, null, null, 'name');

        if (!$dbman->field_exists($table3, $field3)) {
            $dbman->add_field($table3, $field3);
        }

        $table4 = new xmldb_table('syllabusoverview_syllabus');
        $field4 = new xmldb_field('heading_bangla', XMLDB_TYPE_TEXT, null, null, null, null, null, 'heading');

        if (!$dbman->field_exists($table4, $field4)) {
            $dbman->add_field($table4, $field4);
        }

        $table5 = new xmldb_table('syllabusoverview_description');
        $field5 = new xmldb_field('description_bangla', XMLDB_TYPE_TEXT, null, null, null, null, null, 'description');

        if (!$dbman->field_exists($table5, $field5)) {
            $dbman->add_field($table5, $field5);
        }

        $table6 = new xmldb_table('syllabusoverview_benefitted');
        $field6 = new xmldb_field('content_bangla', XMLDB_TYPE_TEXT, null, null, null, null, null, 'content');

        if (!$dbman->field_exists($table6, $field6)) {
            $dbman->add_field($table6, $field6);
        }

        $table7 = new xmldb_table('mod_syllabusoverview_feature');
        $field7 = new xmldb_field('content_bangla', XMLDB_TYPE_TEXT, null, null, null, null, null, 'content');

        if (!$dbman->field_exists($table7, $field7)) {
            $dbman->add_field($table7, $field7);
        }

        // Syllabusoverview savepoint reached.
        upgrade_mod_savepoint(true, 2024021504, 'syllabusoverview');
    }

    if ($oldversion < 2024022801) {

        $table = new xmldb_table('syllabusoverview_description');
        $field = new xmldb_field('fileurl', XMLDB_TYPE_TEXT, null, null, null, null, null, 'description');

        // Conditionally launch add field fileurl.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Syllabusoverview savepoint reached.
        upgrade_mod_savepoint(true, 2024022801, 'syllabusoverview');
    }

    if ($oldversion < 2024022816) {

        $table = new xmldb_table('syllabusoverview_learn');
        $field = new xmldb_field('learn_bangla', XMLDB_TYPE_TEXT, null, null, null, null, null, 'learn');

        // Conditionally launch add field fileurl.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table1 = new xmldb_table('syllabusoverview_syllabus');
        $field1 = new xmldb_field('body_bangla', XMLDB_TYPE_TEXT, null, null, null, null, null, 'body');

        // Conditionally launch add field fileurl.
        if (!$dbman->field_exists($table1, $field1)) {
            $dbman->add_field($table1, $field1);
        }
        // Syllabusoverview savepoint reached.
        upgrade_mod_savepoint(true, 2024022816, 'syllabusoverview');
    }

    return true;
}
