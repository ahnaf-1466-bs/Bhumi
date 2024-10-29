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
 * newsfeed external functions and service definitions.
 *
 * @package    local_newsfeed
 * @category   uninstall
 * @copyright  2023 Brain Station 23 Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */


global $DB;

$dbman = $DB->get_manager();

if ($dbman->table_exists('dcms_director')) {
    $table = new xmldb_table('dcms_director');
    $dbman->drop_table($table);
}
if ($dbman->table_exists('dcms_directorurl')) {
    $table = new xmldb_table('dcms_directorurl');
    $dbman->drop_table($table);
}
if ($dbman->table_exists('dcms_founder')) {
    $table = new xmldb_table('dcms_founder');
    $dbman->drop_table($table);
}
if ($dbman->table_exists('dcms_founderurl')) {
    $table = new xmldb_table('dcms_founderurl');
    $dbman->drop_table($table);
}
if ($dbman->table_exists('dcms_instructor')) {
    $table = new xmldb_table('dcms_instructor');
    $dbman->drop_table($table);
}
if ($dbman->table_exists('dcms_instructorurl')) {
    $table = new xmldb_table('dcms_instructorurl');
    $dbman->drop_table($table);
}
if ($dbman->table_exists('dcms_operation')) {
    $table = new xmldb_table('dcms_operation');
    $dbman->drop_table($table);
}
if ($dbman->table_exists('dcms_operationurl')) {
    $table = new xmldb_table('dcms_operationurl');
    $dbman->drop_table($table);
}
if ($dbman->table_exists('dcms_siteintro')) {
    $table = new xmldb_table('dcms_siteintro');
    $dbman->drop_table($table);
}
if ($dbman->table_exists('dcms_feedback')) {
    $table = new xmldb_table('dcms_feedback');
    $dbman->drop_table($table);
}
if ($dbman->table_exists('dcms_feedbackurl')) {
    $table = new xmldb_table('dcms_feedbackurl');
    $dbman->drop_table($table);
}
if ($dbman->table_exists('dcms_partner')) {
    $table = new xmldb_table('dcms_partner');
    $dbman->drop_table($table);
}
if ($dbman->table_exists('dcms_partnerurl')) {
    $table = new xmldb_table('dcms_partnerurl');
    $dbman->drop_table($table);
}
if ($dbman->table_exists('dcms_ourstory')) {
    $table = new xmldb_table('dcms_ourstory');
    $dbman->drop_table($table);
}
if ($dbman->table_exists('dcms_whyvumi')) {
    $table = new xmldb_table('dcms_whyvumi');
    $dbman->drop_table($table);
}
if ($dbman->table_exists('dcms_vision')) {
    $table = new xmldb_table('dcms_vision');
    $dbman->drop_table($table);
}
if ($dbman->table_exists('dcms_vumifor')) {
    $table = new xmldb_table('dcms_vumifor');
    $dbman->drop_table($table);
}
if ($dbman->table_exists('dcms_vumiforurl')) {
    $table = new xmldb_table('dcms_vumiforurl');
    $dbman->drop_table($table);
}
if ($dbman->table_exists('dcms_strength')) {
    $table = new xmldb_table('dcms_strength');
    $dbman->drop_table($table);
}
