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
 * @package     mod_syllabusoverview
 * @author      Brain Station 23 Ltd.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_syllabusoverview;

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
            FROM {mod_syllabusoverview} lm 
            LEFT OUTER JOIN {mod_syllabusoverview_read} lmr ON lm.id = lmr.messageid AND lmr.userid = :userid 
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
        return $DB->get_records('mod_syllabusoverview');
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
            return $DB->insert_record('mod_syllabusoverview_read', $read_record, false);
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
        return $DB->get_record('mod_syllabusoverview_feature', ['id' => $messageid]);
    }


    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_benefit(int $messageid)
    {
        global $DB;
        return $DB->get_record('syllabusoverview_benefitted', ['id' => $messageid]);
    }

    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_programname(int $messageid)
    {
        global $DB;
        return $DB->get_record('syllabusoverview_prog_name', ['id' => $messageid]);
    }

    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_programdate(int $dateid, int $programid)
    {
        global $DB;
        return $DB->get_record('syllabusoverview_program', ['id' => $dateid, 'prog_id'=> $programid]);
    }


    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_programpdf(int $messageid)
    {
        global $DB;
        return $DB->get_record('syllabusoverview_programpdf', ['id' => $messageid]);
    }

    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_programdetails(int $messageid)
    {
        global $DB;
        $SQL = 'SELECT spt.id, spt.name, spt.name_bangla, spd.value, spd.value_bangla
            FROM {syllabusoverview_prog_title} spt 
            JOIN {syllabusoverview_prog_data} spd
            WHERE spt.course = spd.course AND 
                spt.id = spd.title_id AND 
                spt.id = '. $messageid;

        $message = $DB->get_record_sql($SQL);
        return $message;
    }

    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_description(int $messageid)
    {
        global $DB;
        return $DB->get_record('syllabusoverview_description', ['id' => $messageid]);
    }

    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_courseimage(int $messageid)
    {
        global $DB;
        return $DB->get_record('syllabusoverview_courseimg', ['id' => $messageid]);
    }



    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_syllabus(int $messageid)
    {
        global $DB;
        return $DB->get_record('syllabusoverview_syllabus', ['id' => $messageid]);
    }


    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_syllabuspdf(int $messageid)
    {
        global $DB;
        return $DB->get_record('syllabusoverview_syllabuspdf', ['id' => $messageid]);
    }

    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_learn(int $messageid)
    {
        global $DB;
        return $DB->get_record('syllabusoverview_learn', ['id' => $messageid]);
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
        $deletedMessage = $DB->delete_records('mod_syllabusoverview', ['id' => $messageid]);
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
        $deletedMessages = $DB->delete_records_select('mod_syllabusoverview', "id $ids", $params);
        $deletedReads = $DB->delete_records_select('mod_syllabusoverview_read', "messageid $ids", $params);
        if ($deletedMessages && $deletedReads) {
            $DB->commit_delegated_transaction($transaction);
        }
        return true;
    }
}
