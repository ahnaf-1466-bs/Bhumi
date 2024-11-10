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
 * @package local_dcms
 * @copyright 2021 Brain Station 23 LTD.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

function homepage_contents () {
    global $DB;

    // Site Intro
    $siteintro = $DB->get_records('dcms_siteintro', [], 'siteintro, siteintro_bn');

    // Feedback .
    $SQL = 'SELECT MF.id, MF.feedbackname, MF.position, MF.company, MF.subject, MF.feedbacktext, MF.feedbackname_bn, MF.position_bn, MF.company_bn, MF.subject_bn, MF.feedbacktext_bn, MFU.picurl 
            FROM {dcms_feedback} MF
            JOIN {dcms_feedbackurl} MFU
            WHERE MF.draftid = MFU.draftid';

    $feedbacks = $DB->get_records_sql($SQL);

    // Partner .
    $SQL = 'SELECT MDP.id, MDP.partnername,  MDP.partnername_bn, MDU.picurl 
            FROM {dcms_partner} MDP
            JOIN {dcms_partnerurl} MDU
            WHERE MDP.draftid = MDU.draftid';

    $partner = $DB->get_records_sql($SQL);

    $homepage = new stdClass();
    $homepage->siteintro = $siteintro;
    $homepage->feedback = $feedbacks;
    $homepage->partner = $partner;

    return $homepage;
}

function aboutpage_contents () {
    global $DB;
    $ourstory = $DB->get_record('dcms_ourstory', [], 'ourstory');
    $ourstory_bn = $DB->get_record('dcms_ourstory', [], 'ourstory_bn');
    $vision = $DB->get_record('dcms_vision', [], 'vision');
    $vision_bn = $DB->get_record('dcms_vision', [], 'vision_bn');
    $whyvumi = $DB->get_record('dcms_whyvumi', [], 'whyvumitext');
    $whyvumi_bn = $DB->get_record('dcms_whyvumi', [], 'whyvumitext_bn');
    $strength = $DB->get_records('dcms_strength');


    $SQL = 'SELECT MDP.id, MDP.vumiforname, MDP.vumiforname_bn, MDU.picurl 
            FROM {dcms_vumifor} MDP
            JOIN {dcms_vumiforurl} MDU
            WHERE MDP.draftid = MDU.draftid';
    $vumifor = $DB->get_records_sql($SQL);

    if($ourstory == NULL || $vision == NULL || $vumifor == NULL || $strength == NULL || $whyvumi == NULL) {
        $ourstory = get_string('nodatafound', 'local_dcms');
        $vision = get_string('nodatafound', 'local_dcms');
        $vumifor = get_string('nodatafound', 'local_dcms');
        $strength = get_string('nodatafound', 'local_dcms');
        $whyvumi = get_string('nodatafound', 'local_dcms');
    }

    $aboutpage = new stdClass();
    $aboutpage->ourstory = $ourstory;
    $aboutpage->ourstory_bn = $ourstory_bn;
    $aboutpage->vision = $vision;
    $aboutpage->vision_bn = $vision_bn;
    $aboutpage->vumifor = $vumifor;
    $aboutpage->strength = $strength;
//    $aboutpage->strength_bn = $strength_bn;
    $aboutpage->whyvumi = $whyvumi;
    $aboutpage->whyvumi_bn = $whyvumi_bn;

    return $aboutpage;
}

function outteampage_contents () {
    global $DB;

    $SQL = 'SELECT MD.id, MD.directorname,MD.directorname_bn, MD.directordeg, MD.directordeg_bn, MD.email, MD.tier, MDU.picurl
            FROM {dcms_director} MD
            JOIN {dcms_directorurl} MDU
            WHERE MD.draftid = MDU.draftid';
    $director = $DB->get_records_sql($SQL);

    $SQL = 'SELECT MF.id, MF.foundername, MF.foundername_bn, MF.founderdeg, MF.founderdeg_bn, MF.email, MF.tier, MFU.picurl
            FROM {dcms_founder} MF
            JOIN {dcms_founderurl} MFU
            WHERE MF.draftid = MFU.draftid';
    $founder = $DB->get_records_sql($SQL);

    $SQL = 'SELECT MF.id, MF.instructorname, MF.instructorname_bn, MF.instructordeg, MF.instructordeg_bn, MF.email, MF.tier, MFU.picurl
            FROM {dcms_instructor} MF
            JOIN {dcms_instructorurl} MFU
            WHERE MF.draftid = MFU.draftid';
    $instructor = $DB->get_records_sql($SQL);

    $SQL = 'SELECT MF.id, MF.operationname, MF.operationname_bn, MF.operationdeg, MF.operationdeg_bn, MF.operationmail, MF.tier, MFU.picurl
            FROM {dcms_operation} MF
            JOIN {dcms_operationurl} MFU
            WHERE MF.draftid = MFU.draftid';
    $operation = $DB->get_records_sql($SQL);

    $ourteam = new stdClass();
    $ourteam->director = $director;
    $ourteam->founder = $founder;
    $ourteam->instructor = $instructor;
    $ourteam->operation = $operation;

    return $ourteam;
}