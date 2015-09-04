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
 * English strings for autoemails
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_autoemails
 * @copyright  2015 Brian Vincent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['ketlicense'] = 'License';
$string['ketlicenses'] = 'Licenses';
$string['newketautoemail'] = 'New Email';
$string['autoemailsforthing'] = 'Edit Email';
$string['editketlicenseforthing'] = 'Edit License for {$a}';
$string['ketlicenseforthing'] = 'License for {$a}';
$string['ketlicensesforthing'] = 'Licenses for {$a}';
$string['allketautoemail'] = 'All Emails';
$string['notyourlicense'] = 'You are not an admin of this license';
$string['allyourketautoemail'] = 'All Your Emails';
$string['ketlicensesforsite'] = 'Licenses for Site';
$string['roster'] = 'Roster';
$string['centerroster'] = 'Center Roster';
$string['allcenterrosters'] = 'All Center Rosters';
$string['allyourcenterrosters'] = 'All Your Center Rosters';
$string['classroster'] = 'Class Roster';
$string['allclassrosters'] = 'All Class Rosters';
$string['allyourclassrosters'] = 'All Your Class Rosters';
$string['rosterforthing'] = 'Roster for {$a}';
$string['centerrosterforthing'] = 'Center Roster for {$a}';
$string['classrosterforthing'] = 'Class Roster for {$a}';
$string['activepurchases'] = 'Active Purchases';
$string['expiredpurchases'] = 'Expired Purchases';
$string['allautoemails'] = 'All Emails';
$string['allyourautoemails'] = 'Emails for {$a}';
$string['ketlicensesforsite_introduction'] = 'These are the licenses for the site that are associated with your account.';
$string['ketlicensespurchased'] = 'Licenses Purchased';
$string['ketlicensespurchased_introduction'] = 'These are the licenses you have purchased that still need to be processed.';
$string['ketlicensecourses'] = 'License Courses';
$string['ketlicensecourses_introduction'] = 'These are the courses that will be used with the KET Licensing local plugin.';
$string['ketlicensetypes'] = 'License Types';
$string['ketlicensetypes_introduction'] = 'These are the types of licenses and courses associated with those licenses that will be available.';
$string['ketlicense_nav_groupname'] = 'Licenses &amp; Rosters';
$string['ketlicense_nav_licenses'] = 'Licenses';
$string['ketlicense_nav_purchases'] = 'Purchases';
$string['ketlicense_nav_centerrosters'] = 'Center Rosters';
$string['ketlicense_nav_classrosters'] = 'Class Rosters';
$string['ketlicense_nav_courses'] = 'Courses';
$string['ketlicense_nav_types'] = 'Types';
$string['ketlicense_nav_settings'] = 'Settings';
$string['ketlicense_error_notketstaff'] = 'You do not have privileges to view this page';
$string['ketlicense_error_notadminforlicense'] = 'You are not an admin for that license';
$string['ketlicense_error_notadminorteacherforlicense'] = 'You are not an admin or teacher for that license';
$string['ketlicense_error_courseexpired'] = 'Your access to this course has expired';
$string['ketlicense_error_licenseexpired'] = 'Your license has expired';
$string['ketlicense_error_licenseinactive'] = 'Your license has been marked as inactive';
$string['ketlicense_error_notacenter'] = 'The license you selected is not a Center license';
$string['ketlicense_error_notagroup'] = 'The license you selected is not a Class or Group license';
$string['ketlicense_error_overallocated'] = 'Currently your class has more students enrolled then allowed, please contact your teacher.';
$string['ketlicense_error_missingpurchase'] = 'Unable to locate a purchase for your license, please contact your teacher.';
$string['ketlicense_preflight_requirescohortsync'] = 'The KET License local plugin requires that the cohort enrollment plugin be activated';
$string['ketlicense_preflight_requiredsettings'] = 'The KET License local plugin has required settings that are missing '.
														'(go to <a href="/admin/settings.php?section=local_ketlicense">settings</a> to fix)';
$string['ketlicense_preflight_requirescohortsync'] = 'The KET License local plugin requires that the cohort enrollment plugin be activated';
$string['ketlicense_preflight_requiresketlicenseauth'] = 'The KET License local plugin requires that the KET License Auth plugin (ketlicenseauth) be activated';
$string['pluginadministration'] = 'KET License Administration';
$string['pluginname'] = 'KET License';
#permissions names
$string['ketlicense:manage'] = 'Manage an individual KET License';
$string['ketlicense:manageall'] = 'Manage all the KET Licenses (for KET employee use only)';
$string['ketlicense:config'] = 'Configure the KET License System (for KET employee use only)';
$string['ketlicense:active'] = 'Active users with a licensed course';
$string['ketlicense:expired'] = 'Expired users with a licensed course';
