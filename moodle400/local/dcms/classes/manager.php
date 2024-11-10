<?php
// This file is part of Moodle Course Rollover Plugin
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
 * @package     local_dcms
 * @author      Brain Station 23 Ltd.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_dcms;

use dml_exception;
use stdClass;

class manager {

    /** Gets all messages that have not been read by this user
     * @param int $userid the user that we are getting messages for
     * @return array of messages
     */
    public function get_messages(int $userid): array
    {
        global $DB;
        $sql = "SELECT lm.id, lm.messagetext, lm.messagetype 
            FROM {local_dcms} lm 
            LEFT OUTER JOIN {local_dcms_read} lmr ON lm.id = lmr.messageid AND lmr.userid = :userid 
            WHERE lmr.userid IS NULL";
        $params = [
            'userid' => $userid,
        ];
        try {
            return $DB->get_records_sql($sql, $params);
        } catch (dml_exception $e) {
            // Log error here.
            return [];
        }
    }

    /** Gets all messages
     * @return array of messages
     */
    public function get_all_messages(): array {
        global $DB;
        return $DB->get_records('local_dcms');
    }

    /** Mark that a message was read by this user.
     * @param int $message_id the message to mark as read
     * @param int $userid the user that we are marking message read
     * @return bool true if successful
     */
    public function mark_message_read(int $message_id, int $userid): bool
    {
        global $DB;
        $read_record = new stdClass();
        $read_record->messageid = $message_id;
        $read_record->userid = $userid;
        $read_record->timeread = time();
        try {
            return $DB->insert_record('local_dcms_read', $read_record, false);
        } catch (dml_exception $e) {
            return false;
        }
    }

    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_message(int $messageid)
    {
        global $DB;
        return $DB->get_record('local_dcms_feature', ['id' => $messageid]);
    }

    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_director (int $messageid)
    {
        global $DB;
        return $DB->get_record('dcms_director', ['id' => $messageid]);
    }

    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_strength (int $messageid)
    {
        global $DB;
        return $DB->get_record('dcms_strength', ['id' => $messageid]);
    }

    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_whyvumi (int $messageid)
    {
        global $DB;
        return $DB->get_record('dcms_whyvumi', ['id' => $messageid]);
    }

    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_founder (int $messageid)
    {
        global $DB;
        return $DB->get_record('dcms_founder', ['id' => $messageid]);
    }


    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_instructor (int $messageid)
    {
        global $DB;
        return $DB->get_record('dcms_instructor', ['id' => $messageid]);
    }


    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_operation (int $messageid)
    {
        global $DB;
        return $DB->get_record('dcms_operation', ['id' => $messageid]);
    }

    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_siteintro(int $messageid)
    {
        global $DB;
        return $DB->get_record('dcms_siteintro', ['id' => $messageid]);
    }
    /** Get a single footer link from its id.
     * @param int $id the link we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_footerlink(int $id) {
        global $DB;
        return $DB->get_record('dcms_footer', ['id' => $id]);
    }
    

    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_ourstory(int $messageid)
    {
        global $DB;
        return $DB->get_record('dcms_ourstory', ['id' => $messageid]);
    }

    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_vision(int $messageid)
    {
        global $DB;
        return $DB->get_record('dcms_vision', ['id' => $messageid]);
    }

    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_dcms(int $messageid)
    {
        global $DB;
        return $DB->get_record('dcms_dcms', ['id' => $messageid]);
    }


    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_dcmspdf(int $messageid)
    {
        global $DB;
        return $DB->get_record('dcms_dcmspdf', ['id' => $messageid]);
    }

    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_partner (int $messageid)
    {
        global $DB;
        return $DB->get_record('dcms_partner', ['id' => $messageid]);
    }

    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_feedback (int $messageid)
    {
        global $DB;
        return $DB->get_record('dcms_feedback', ['id' => $messageid]);
    }


    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_vumifor (int $messageid)
    {
        global $DB;
        return $DB->get_record('dcms_vumifor', ['id' => $messageid]);
    }


    /** Delete a message and all the read history.
     * @param $messageid
     * @return bool
     * @throws \dml_transaction_exception
     * @throws dml_exception
     */
    public function delete_message($messageid)
    {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        $deletedMessage = $DB->delete_records('local_dcms', ['id' => $messageid]);
            if ($deletedMessage) {
            $DB->commit_delegated_transaction($transaction);
        }
        return true;
    }

    /** Delete all messages by id.
     * @param $messageids
     * @return bool
     */
    public function delete_messages($messageids)
    {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        list($ids, $params) = $DB->get_in_or_equal($messageids);
        $deletedMessages = $DB->delete_records_select('local_dcms', "id $ids", $params);
        $deletedReads = $DB->delete_records_select('local_dcms_read', "messageid $ids", $params);
        if ($deletedMessages && $deletedReads) {
            $DB->commit_delegated_transaction($transaction);
        }
        return true;
    }
}
