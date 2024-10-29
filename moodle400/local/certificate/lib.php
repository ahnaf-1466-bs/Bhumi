<?php
/**
 * Library of interface functions and constants for lict
 *
 * @package    local
 * @subpackage bs_webservicesuite
 * @author     Brain station 23 ltd <brainstation-23.com>
 * @copyright  2020 Brain station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

// verify the Certificate
// receive the token and userid and certid from url
// check into the database
//
function verify_certificate($token){
    global $DB;
    $sql = 'select * from {certificate_token_table} where token =:token';
    $query = $DB->get_record_sql($sql,array('token' => $token));
    return $query;
}