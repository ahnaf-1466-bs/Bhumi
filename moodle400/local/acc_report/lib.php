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
 * Library functions for local acc_report plugin.
 *
 * @package    local_acc_report
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;



/**
 * Return the list of all courses with id and fullname for entry creation form options.
 * 
 * @return array $courses
 */
function local_acc_report_get_all_courses() {
    global $DB;

    $courses = $DB->get_records('course', array(), '', 'id, fullname');
    return $courses;

}

/**
 * Returns the list of currencies that the payment subsystem supports and therefore we can work with.
 *
 * @return array[currencycode => currencyname]
 */
function local_acc_report_get_possible_currencies() {
    return array (
        'ALL' => 'Albania Lek',
        'AFN' => 'Afghanistan Afghani',
        'ARS' => 'Argentina Peso',
        'AWG' => 'Aruba Guilder',
        'AUD' => 'Australia Dollar',
        'AZN' => 'Azerbaijan New Manat',
        'BSD' => 'Bahamas Dollar',
        'BBD' => 'Barbados Dollar',
        'BDT' => 'Bangladeshi taka',
        'BYR' => 'Belarus Ruble',
        'BZD' => 'Belize Dollar',
        'BMD' => 'Bermuda Dollar',
        'BOB' => 'Bolivia Boliviano',
        'BAM' => 'Bosnia and Herzegovina Convertible Marka',
        'BWP' => 'Botswana Pula',
        'BGN' => 'Bulgaria Lev',
        'BRL' => 'Brazil Real',
        'BND' => 'Brunei Darussalam Dollar',
        'KHR' => 'Cambodia Riel',
        'CAD' => 'Canada Dollar',
        'KYD' => 'Cayman Islands Dollar',
        'CLP' => 'Chile Peso',
        'CNY' => 'China Yuan Renminbi',
        'COP' => 'Colombia Peso',
        'CRC' => 'Costa Rica Colon',
        'HRK' => 'Croatia Kuna',
        'CUP' => 'Cuba Peso',
        'CZK' => 'Czech Republic Koruna',
        'DKK' => 'Denmark Krone',
        'DOP' => 'Dominican Republic Peso',
        'XCD' => 'East Caribbean Dollar',
        'EGP' => 'Egypt Pound',
        'SVC' => 'El Salvador Colon',
        'EEK' => 'Estonia Kroon',
        'EUR' => 'Euro Member Countries',
        'FKP' => 'Falkland Islands (Malvinas) Pound',
        'FJD' => 'Fiji Dollar',
        'GHC' => 'Ghana Cedis',
        'GIP' => 'Gibraltar Pound',
        'GTQ' => 'Guatemala Quetzal',
        'GGP' => 'Guernsey Pound',
        'GYD' => 'Guyana Dollar',
        'HNL' => 'Honduras Lempira',
        'HKD' => 'Hong Kong Dollar',
        'HUF' => 'Hungary Forint',
        'ISK' => 'Iceland Krona',
        'INR' => 'India Rupee',
        'IDR' => 'Indonesia Rupiah',
        'IRR' => 'Iran Rial',
        'IMP' => 'Isle of Man Pound',
        'ILS' => 'Israel Shekel',
        'JMD' => 'Jamaica Dollar',
        'JPY' => 'Japan Yen',
        'JEP' => 'Jersey Pound',
        'KZT' => 'Kazakhstan Tenge',
        'KPW' => 'Korea (North) Won',
        'KRW' => 'Korea (South) Won',
        'KGS' => 'Kyrgyzstan Som',
        'LAK' => 'Laos Kip',
        'LVL' => 'Latvia Lat',
        'LBP' => 'Lebanon Pound',
        'LRD' => 'Liberia Dollar',
        'LTL' => 'Lithuania Litas',
        'MKD' => 'Macedonia Denar',
        'MYR' => 'Malaysia Ringgit',
        'MUR' => 'Mauritius Rupee',
        'MXN' => 'Mexico Peso',
        'MNT' => 'Mongolia Tughrik',
        'MZN' => 'Mozambique Metical',
        'NAD' => 'Namibia Dollar',
        'NPR' => 'Nepal Rupee',
        'ANG' => 'Netherlands Antilles Guilder',
        'NZD' => 'New Zealand Dollar',
        'NIO' => 'Nicaragua Cordoba',
        'NGN' => 'Nigeria Naira',
        'NOK' => 'Norway Krone',
        'OMR' => 'Oman Rial',
        'PKR' => 'Pakistan Rupee',
        'PAB' => 'Panama Balboa',
        'PYG' => 'Paraguay Guarani',
        'PEN' => 'Peru Nuevo Sol',
        'PHP' => 'Philippines Peso',
        'PLN' => 'Poland Zloty',
        'QAR' => 'Qatar Riyal',
        'RON' => 'Romania New Leu',
        'RUB' => 'Russia Ruble',
        'SHP' => 'Saint Helena Pound',
        'SAR' => 'Saudi Arabia Riyal',
        'RSD' => 'Serbia Dinar',
        'SCR' => 'Seychelles Rupee',
        'SGD' => 'Singapore Dollar',
        'SBD' => 'Solomon Islands Dollar',
        'SOS' => 'Somalia Shilling',
        'ZAR' => 'South Africa Rand',
        'LKR' => 'Sri Lanka Rupee',
        'SEK' => 'Sweden Krona',
        'CHF' => 'Switzerland Franc',
        'SRD' => 'Suriname Dollar',
        'SYP' => 'Syria Pound',
        'TWD' => 'Taiwan New Dollar',
        'THB' => 'Thailand Baht',
        'TTD' => 'Trinidad and Tobago Dollar',
        'TRY' => 'Turkey Lira',
        'TRL' => 'Turkey Lira',
        'TVD' => 'Tuvalu Dollar',
        'UAH' => 'Ukraine Hryvna',
        'GBP' => 'United Kingdom Pound',
        'USD' => 'United States Dollar',
        'UYU' => 'Uruguay Peso',
        'UZS' => 'Uzbekistan Som',
        'VEF' => 'Venezuela Bolivar',
        'VND' => 'Viet Nam Dong',
        'YER' => 'Yemen Rial',
        'ZWD' => 'Zimbabwe Dollar'
    );
}

/**
 * Returns all the entries of 'local_acc_report_data' table
 * by courseid.
 * 
 * @param int $courseid
 * @return array
 */
function local_acc_report_get_entries_by_courseid($courses, $from, $to) {
    global $DB;
    $coursesids = join("','",$courses); 
    $sql = "SELECT ard.id, u.id as userid, u.firstname, u.lastname, c.id as courseid, c.fullname, ard.type, ard.amount, ard.currency, ard.comment, ard.timemodified
            FROM {local_acc_report_data} ard
            LEFT JOIN {user} u ON u.id = ard.createdby
            LEFT JOIN {course} c ON c.id = ard.courseid
            WHERE ard.courseid IN ('$coursesids')
            AND ard.timemodified > $from
            AND ard.timemodified <= $to";
    $entries = $DB->get_records_sql($sql);
    return $entries;
}

/**
 * Convert timestamp to date format.
 * 
 * @param int $timestamps
 * @return string
 */
function local_acc_report_convert_to_date($timestamp) {
    $timezone = 'Asia/Dhaka'; // No need to convert to lang string.
    $date =  new DateTime('@'. $timestamp);
    $date->setTimezone(new DateTimeZone($timezone));
    // return $date->format('d F, Y');
    return $date->format('Y-m-d');
}

/**
 * Convert timestamp to date-time format.
 * 
 * @param int $timestamps
 * @return string
 */
function local_acc_report_convert_to_datetime($timestamp) {
    $timezone = 'Asia/Dhaka'; // No need to convert to lang string.
    $date =  new DateTime('@'. $timestamp);
    $date->setTimezone(new DateTimeZone($timezone));
    // return $date->format('d F, Y');
    return $date->format('Y-m-d h:i:sa');
}


function local_acc_report_prepare_data_for_template($entriesdata, $shurjopaydata, $from, $to) {

    $entries = [];
    foreach($entriesdata as $value) {
        $temp = [];
        $temp['id'] = $value->id;
        $temp['courseid'] = $value->courseid;
        $temp['fullname'] = $value->fullname;
        $temp['type'] = $value->type;
        $temp['amount'] = $value->amount;
        $temp['currency'] = $value->currency;
        $temp['comment'] = $value->comment;
        $temp['source'] = 'Manual';
        $temp['createdby'] = $value->firstname;
        $temp['timemodified'] = $value->timemodified;

        $temp['timemodified'] = local_acc_report_convert_to_date($temp['timemodified']);
        array_push($entries, $temp);
    }
    foreach($shurjopaydata as $shurjopay) {
        foreach($shurjopay as $data) {
            $temp = [];
            $temp['courseid'] = $data->courseid;
            $temp['fullname'] = $data->fullname;
            $temp['type'] = 'income';
            $temp['amount'] = $data->received_amount;
            $temp['currency'] = $data->currency;
            $temp['comment'] = '';
            $temp['source'] = 'Shurjopay';
            $temp['createdby'] = $data->name;
            $date = explode(" ", $data->method);
            $temp['timemodified'] = $date[0];

            // set default timezone
            date_default_timezone_set('Asia/Dhaka');

            //define date and time
            $date = strtotime($temp['timemodified']);

            if($date > $from && $date <= $to) {
                array_push($entries, $temp);
            }
        }

    }
    
    usort($entries, function ($item1, $item2) {
        return $item2['timemodified'] <=> $item1['timemodified'];
    });
    return $entries;
}

function local_acc_report_get_all_coursesids() {
    global $DB;

    $courses = $DB->get_records('course', [], '', 'id');
    $courseids = array();
    foreach($courses as $course) {
        array_push($courseids, $course->id);
    }
    return $courseids;
}

function local_acc_report_get_shurjopay_itemids($courses) {
    global $DB;

    $itemids = array();

    foreach($courses as $course) {
        // Get itemid from 'enrol' table.
        $sql = "SELECT * FROM {enrol} 
        WHERE enrol='fee' AND courseid=$course";

        $item = $DB->get_record_sql($sql);
        if($item) {
            $itemrecord = new stdClass();
            $itemrecord->courseid = $course;
            $itemrecord->itemid = $item->id;
            array_push($itemids, $itemrecord);
        }
    }

    return $itemids;
}

function local_acc_report_get_shurjopay_data($shurjopayitemids) {
    global $DB;
    $data = array();
    foreach($shurjopayitemids as $item) {
        $sql = "SELECT psl.id, psl.currency, psl.received_amount, psl.name, psl.method, e.enrol, e.courseid, c.fullname 
                FROM {paygw_shurjopay_log} psl
                LEFT JOIN {enrol} e ON e.id=psl.itemid
                LEFT JOIN {course} c ON e.courseid = c.id
                WHERE itemid=$item->itemid
                AND sp_message='Success'
                AND bank_status='Success'";
        $shurjopaydata = $DB->get_records_sql($sql);
        if($shurjopaydata) {
            array_push($data, $shurjopaydata);
        }
    }
    return $data;
}

function local_acc_report_get_total_data($entries) {
    $totalincome = 0;
    $totalexpense = 0;
    foreach($entries as $entry) {

        if($entry['type'] == 'income') {
            $totalincome += (float)$entry['amount'];
        } else {
            $totalexpense += (float)$entry['amount'];
        }
    }
    $difference = $totalincome - $totalexpense;
    return [$totalincome, $totalexpense, $difference];
}

function local_acc_report_prepare_data_for_export($entriesdata, $shurjopaydata, $from, $to, $totalincome, $totalexpense, $difference) {

    $entries = [];

    $temp = [];
    $temp['title'] = "Total revenue";
    $temp['value'] = $totalincome;
    array_push($entries, $temp);

    $temp = [];
    $temp['title'] = "Total expense";
    $temp['value'] = $totalexpense;
    array_push($entries, $temp);

    $temp = [];
    $temp['title'] = "Profit / Loss:";
    $temp['value'] = $difference;
    array_push($entries, $temp);

    $temp = [];
    $temp['title'] = "";
    $temp['value'] = "";
    array_push($entries, $temp);

    $temp = [];
    $temp['timemodified'] = "Date";
    $temp['fullname'] = "Course Name";
    $temp['type'] = "Type";
    $temp['source'] = "Source";
    $temp['amount'] = "Amount";
    $temp['currency'] = "Currency";
    $temp['comment'] = "Comment";
    $temp['createdby'] = "Edited By";
    array_push($entries, $temp);

    foreach($entriesdata as $value) {
        $temp = [];
        $temp['timemodified'] = $value->timemodified;
        $temp['timemodified'] = local_acc_report_convert_to_date($temp['timemodified']);
        $temp['fullname'] = $value->fullname;
        $temp['type'] = $value->type;
        $temp['source'] = 'Manual';
        $temp['amount'] = $value->amount;
        $temp['currency'] = $value->currency;
        $temp['comment'] = $value->comment;
        $temp['createdby'] = $value->firstname;
        array_push($entries, $temp);
    }
    foreach($shurjopaydata as $shurjopay) {
        foreach($shurjopay as $data) {
            $temp = [];
            $date = explode(" ", $data->method);
            $temp['timemodified'] = $date[0];
            $temp['fullname'] = $data->fullname;
            $temp['type'] = 'income';
            $temp['source'] = 'Shurjopay';
            $temp['amount'] = $data->received_amount;
            $temp['currency'] = $data->currency;
            $temp['comment'] = '';
            $temp['createdby'] = $data->name;
            
            // set default timezone
            date_default_timezone_set('Asia/Dhaka');

            //define date and time
            $date = strtotime($temp['timemodified']);

            if($date > $from && $date <= $to) {
                array_push($entries, $temp);
            }
        }

    }
    
    
    // usort($entries, function ($item1, $item2) {
    //     return $item2['timemodified'] <=> $item1['timemodified'];
    // });
    return $entries;
}