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
 * Upgrade file of paygw_shurjopay plugin.
 *
 * @package    paygw_shurjopay
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 *
 * @param int $oldversion The old version of the paygw shurjopay plugin.
 * @return bool
 */
function xmldb_paygw_shurjopay_upgrade($oldversion)
{
    global $CFG, $DB;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    if ($oldversion < 2024012416) {

        // Changing nullability of field received_amount on table paygw_shurjopay_log to null.
        $table = new xmldb_table('paygw_shurjopay_log');
        $field1 = new xmldb_field('received_amount', XMLDB_TYPE_FLOAT, '20', null, null, null, null, 'payable_amount');
        $field2 = new xmldb_field('payable_amount', XMLDB_TYPE_FLOAT, '20', null, null, null, null, 'amount');
        $field3 = new xmldb_field('bank_trx_id', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'card_holder_name');
        $field4 = new xmldb_field('invoice_no', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'bank_trx_id');
        $field5 = new xmldb_field('customer_order_id', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'bank_status');
        $field6 = new xmldb_field('sp_code', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'sp_message');
        $field7 = new xmldb_field('sp_message', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'customer_order_id');
        $field8 = new xmldb_field('txn_id', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'id');
        $field9 = new xmldb_field('order_id', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'txn_id');
        $field10 = new xmldb_field('currency', XMLDB_TYPE_CHAR, '10', null, null, null, null, 'order_id');
        $field11 = new xmldb_field('amount', XMLDB_TYPE_FLOAT, '20', null, null, null, null, 'currency');
        $field12 = new xmldb_field('bank_status', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'invoice_no');
        $field13 = new xmldb_field('method', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'city');
        $field14 = new xmldb_field('component', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'method');
        $field15 = new xmldb_field('itemid', XMLDB_TYPE_CHAR, '20', null, null, null, null, 'component');
        $field16 = new xmldb_field('paymentarea', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'itemid');

        // Launch change of nullability for field received_amount.
        $dbman->change_field_notnull($table, $field1);
        $dbman->change_field_notnull($table, $field2);
        $dbman->change_field_notnull($table, $field3);
        $dbman->change_field_notnull($table, $field4);
        $dbman->change_field_notnull($table, $field5);
        $dbman->change_field_notnull($table, $field6);
        $dbman->change_field_notnull($table, $field7);
        $dbman->change_field_notnull($table, $field8);
        $dbman->change_field_notnull($table, $field9);
        $dbman->change_field_notnull($table, $field10);
        $dbman->change_field_notnull($table, $field11);
        $dbman->change_field_notnull($table, $field12);
        $dbman->change_field_notnull($table, $field13);
        $dbman->change_field_notnull($table, $field14);
        $dbman->change_field_notnull($table, $field15);
        $dbman->change_field_notnull($table, $field16);

        // Shurjopay savepoint reached.
        upgrade_plugin_savepoint(true, 2024012416, 'paygw', 'shurjopay');
    }
    return true;
}