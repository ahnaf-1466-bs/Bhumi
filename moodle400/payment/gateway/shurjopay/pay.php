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
 * Redirects to the shurjopay checkout for payment
 *
 * @package    paygw_shurjopay
 * @copyright  2022 Brain station 23 ltd.
 * @author     Brain station 23 ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_payment\helper;
use paygw_shurjopay\shurjopay_helper;

require_once(__DIR__ . '/../../../config.php');

global $DB;

$component      = required_param('component', PARAM_ALPHANUMEXT);
$paymentarea    = required_param('paymentarea', PARAM_ALPHANUMEXT);
$itemid         = required_param('itemid', PARAM_INT);
$description    = required_param('description', PARAM_TEXT);
$userid         = required_param('userid', PARAM_INT);
$amount         = optional_param('amount', 0, PARAM_FLOAT);

$config     = (object) helper::get_gateway_configuration($component, $paymentarea, $itemid, 'shurjopay');
$payable    = helper::get_payable($component, $paymentarea, $itemid);

$surcharge  = helper::get_gateway_surcharge('shurjopay');

if ($amount == 0) {
    $cost = helper::get_rounded_cost($payable->get_amount(), $payable->get_currency(), $surcharge);
} else {
    if ($DB->record_exists('local_discount_used_coupon', ['used_by' => $userid, 'amount' => $amount])) {
        $cost = $amount;
    } else {
        // Redirect URL
        $url = '../../../../'.$config->frontendurl.'status=3&message=payment_cancelled&paygw=shurjopay';
        redirect($url, get_string('paymentcancelled', 'paygw_shurjopay'), 0);
        die;
    }
}

$shurjopayhelper = new shurjopay_helper(
    $config->username,
    $config->password,
    $config->paymentmodes,
    $config->gatewayurl
);

$result = $shurjopayhelper->generate_token();

if($result != null) {
    $shurjopayhelper->generate_payment(
        $result,
        $payable->get_currency(),
        $cost,
        $userid,
        $component,
        $paymentarea,
        $itemid
    );
}
