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
 * Upgrade file.
 *
 * @package    local_discount
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


function xmldb_local_discount_upgrade($oldversion) {

    global $CFG, $DB;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.
    
    if ($oldversion < 2023031405) {
        // Define table dcms_footer to be created.
        $table = new xmldb_table('local_discount');

        $field1 = new xmldb_field('deleted', XMLDB_TYPE_INTEGER, 10, null, null, null, 0);

        // Conditonally launch add field tier.
        if(!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        }

        // Plugin savepoint reached.
        upgrade_plugin_savepoint(true, 2023031405, 'local', 'discount');
    }

    return true;
}