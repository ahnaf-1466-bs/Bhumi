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
 * @package     mod_videoplus
 * @author      Brain Station 23 Ltd.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_videoplus;

use dml_exception;
use stdClass;

class manager {
    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_videofile(int $messageid)
    {
        global $DB;
        return $DB->get_record('videoplus_videofile', ['id' => $messageid]);
    }

    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_pdffile(int $messageid)
    {
        global $DB;
        return $DB->get_record('videoplus_pdffile', ['id' => $messageid]);
    }
}
