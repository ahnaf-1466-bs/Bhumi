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
 * Process payment of bkash.
 *
 * @package    paygw_bkash
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


use core_payment\helper;
use paygw_bkash\bkash_helper;

global $CFG, $USER, $DB;
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');

defined('MOODLE_INTERNAL') || die();


$courseid = required_param("courseid", PARAM_INT);
$component = required_param('component', PARAM_ALPHANUMEXT);
$paymentarea = required_param('paymentarea', PARAM_ALPHANUMEXT);
$itemid = required_param('itemid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);

// From Create payment API Response.
$status = required_param('status', PARAM_TEXT);
$paymentID = required_param('paymentID', PARAM_TEXT);

$config = (object)helper::get_gateway_configuration($component, $paymentarea, $itemid, 'bkash');
$payable = helper::get_payable($component, $paymentarea, $itemid);
$surcharge = helper::get_gateway_surcharge('bkash');

if($status == 'failure') {
    // Redirect to frontend url with failure status.
    $redirecturl = $config->frontendurl . 'status=2&message=payment_failed&paygw=bkash&userid=' . $userid . '&courseid=' . $courseid;
    header("Location: $redirecturl", true, 301);
    exit;
} else if($status == 'cancel') {
    // Redirect to frontend url with cancel status.
    $redirecturl = $config->frontendurl . 'status=3&message=payment_cancelled&paygw=bkash&userid=' . $userid . '&courseid=' . $courseid;
    header("Location: $redirecturl", true, 301);
    exit;
} else {
    // Success status.
    $bkashhelper = new bkash_helper(
        $config->username,
        $config->password,
        $config->appkey,
        $config->appsecret,
        $config->paymentmodes,
        $config->gatewayurl
    );
    // Get response after execute payment API.
    $response = $bkashhelper->execute_payment($paymentID);

    $transactiondata = json_decode($response, true);

    if (array_key_exists("statusCode",$transactiondata) && $transactiondata['statusCode'] != '0000'){
        // Redirect to frontend url with failure status.
        // Case for insufficient balance.
        $redirecturl = $config->frontendurl . 'status=2&message=payment_failed&statusCode='.$transactiondata['statusCode'].'&statusMessage='.$transactiondata['statusMessage'].'&paygw=bkash&userid='.$userid.'&courseid='.$courseid;
        header("Location: $redirecturl", true, 301);
        exit;
    } else if(array_key_exists("errorCode", $transactiondata)) {
        // If execute api failed to response.

        $redirecturl = $config->frontendurl . 'status=2&message=payment_failed&errorCode='.$transactiondata['errorCode'].'&errorMessage=' . $transactiondata['errorMessage'].'&paygw=bkash&userid='.$userid.'&courseid='.$courseid;
        header("Location: $redirecturl", true, 301);
        exit;
    } else if(array_key_exists("message", $transactiondata)) {
        $redirecturl = $config->frontendurl . 'status=2&message=payment_failed&errorMessage='.$transactiondata['message'].'&paygw=bkash&userid='.$userid.'&courseid='.$courseid;
        header("Location: $redirecturl", true, 301);
        exit;
    }

    // Execution payment successful.
    // TODO: Redirect to frontend url with success status after saving data.
    $data = new stdClass();

    $data->userid = $userid;
    $data->txn_id = $transactiondata['trxID'];
    $data->payment_id = $transactiondata['paymentID'];
    $data->payer_reference = $transactiondata['payerReference'];
    $data->amount = $transactiondata['amount'];
    $data->currency = $transactiondata['currency'];
    $data->customer_msisdn = $transactiondata['customerMsisdn'];
    $data->payment_execute_time = $transactiondata['paymentExecuteTime'];
    $data->transaction_status = $transactiondata['transactionStatus'];
    $data->intent = $transactiondata['intent'];
    $data->merchant_invoice_number = $transactiondata['merchantInvoiceNumber'];
    $data->component = $component;
    $data->itemid = $itemid;
    $data->paymentarea = $paymentarea;
    $data->timeupdated = time();

    $DB->insert_record('paygw_bkash_log', $data);

    // Deliver course.
    $payable = helper::get_payable($component, $paymentarea, $itemid);
    $cost = helper::get_rounded_cost($payable->get_amount(),
        $payable->get_currency(),
        helper::get_gateway_surcharge('bkash'));
    $paymentid = helper::save_payment(
        $payable->get_account_id(),
        $component,
        $paymentarea,
        $itemid,
        $userid,
        $cost,
        $payable->get_currency(),
        'bkash'
    );
    helper::deliver_order($component, $paymentarea, $itemid, $paymentid, $userid);

    // Redirect
    $redirecturl = $config->frontendurl . 'status=1&message=payment_successful&paygw=bkash&userid=' . $userid . '&courseid=' . $courseid;
    header("Location: $redirecturl", true, 301);
}
