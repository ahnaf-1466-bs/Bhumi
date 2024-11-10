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
 * Handles fail or cancel requests of shurjopay paygw.
 *
 * @package    paygw_shurjopay
 * @copyright  2022 Brain station 23 ltd.
 * @author     Brain station 23 ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_payment\helper;

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');

$component = optional_param('component', PARAM_ALPHANUMEXT, PARAM_TEXT);
$paymentarea = optional_param('paymentarea', PARAM_ALPHANUMEXT, PARAM_TEXT);
$itemid = optional_param('itemid', 0,PARAM_INT);
$userid = required_param('userid', PARAM_INT);

$config_data = $DB->get_record('payment_gateways', ['gateway' => 'shurjopay']);
$config = json_decode($config_data->config);

if ($component == null || $paymentarea == null || $itemid == null) {
    $url = new moodle_url('../../../../'.$config->frontendurl.'/course');
} else {
    // Find redirection.
    if ($component == 'enrol_fee' && $paymentarea == 'fee') {
        $courseid = $DB->get_field('enrol', 'courseid', ['enrol' => 'fee', 'id' => $itemid]);
        if (!empty($courseid)) {
            $url = '../../../../'.$config->frontendurl.'?status=2&courseid='.$courseid.'&userid='.$userid.'&message=payment_failed&paygw=shurjopay';
        }
    }
}

redirect($url, get_string('paymentfailed', 'paygw_shurjopay'), null);



