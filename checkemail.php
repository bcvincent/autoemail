<?php

//Check records for users expiring in 24 hour range 12-12 from day that this is run.  
//24 hours is 

//mark users as emailed already - this is probably a new table - ketautoemails_sent
//ability to unsubscribe?

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
 * This is a one-line short description of the file
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_ketautoemail
 * @copyright  2015 Brian Vincent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

### required libraries ###
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

require_login();
if ( (!has_capability('local/autoemail:manage', context_system::instance())) && (!has_capability('local/autoemail:manageall', context_system::instance())) ) {
 	require_capability('local/autoemail:manage', context_system::instance());
}

$pagetitle="Query of this email";

//add_to_log(0, 'ketautoemail', 'autoemail index page', "index.php", '');


$id = required_param('id', PARAM_INT);
//$report = $DB->get_record('ketautoemail', array('deleted' => 0,'id' => $id));
//if (!$report) {
  //  print_error('Email does not exist or has been deleted');
//}

if(has_capability('local/autoemail:manageall', context_system::instance())){
	$emails = $DB->get_record('ketautoemail',array('deleted' => 0, 'id' => $id));
} 

if(!$emails || !$emails->emailtime || !$emails->subject || !$emails->body){
    print_error('Missing or incomplete email.  Go back and edit email to include all parameters');
}

### start processing the page/requests ###

$page_output = "";

$PAGE->set_cacheable(false);
$PAGE->set_url("/local/autoemail/checkemail.php");
$PAGE->set_context(context_system::instance());
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);
$PAGE->requires->css("/local/autoemail/styles.css");
$PAGE->add_body_class('ketlicense-index');
$PAGE->set_pagelayout('standard');

// breadcrumb setup
$PAGE->navbar->ignore_active();
$PAGE->navbar->add("Auto Email Management", new moodle_url("/local/autoemail/index.php"));

echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle, 2);

// begin page body
echo "<div class='ketlicense'>";

echo "<table class='license-table'>";
echo "<tr>\n";
echo "<th style='text-align:left; padding-right:.5em;'>Time</th>\n";
echo "<th style='text-align:left; padding-right:.5em;'>Email Type</th>\n";
echo "<th style='text-align:left'>Subject</th>\n";
echo "<th style='text-align:left'>Active</th>\n";
echo "<th style='text-align:left'>Admin</th>\n";

echo "<th></th>\n";
echo "<th></th>\n";
echo "</tr>\n";

    $user = $DB->get_record('user',array("id"=>$emails->admin));
    
    echo "<tr>\n";
    echo "<td style='text-align:left'>".$emails->emailtime." days</td>\n";
    $tempactive = ($emails->emailtype=='enrolling') ? "Since Enrolment" : "Til Expiration";
    echo "<td style='text-align:left'>$tempactive</td>\n";
    echo "<td style='text-align:left'>$emails->subject</td>\n";
 echo "<td style='text-align:left'>$emails->active</td>\n";
    echo "<td style='text-align:left'><a href='".$CFG->wwwroot."/user/view.php?id=$user->id'>".$user->firstname." ".$user->lastname."</a></td>\n";
   

    echo "</tr>\n";


echo "</table>\n";
echo "</div>";

    
$current = strtotime('today midnight'); //timestamp for today's day at midnight
if($emails->emailtype == "expiring"){
    $timediff = '+' . $emails->emailtime . ' days';
}

else if($emails->emailtype == "enrolling"){
    $timediff = '-' . $emails->emailtime . ' days';
}

$later = intval(strtotime($timediff, $current)); //timestamp for X days til expiration from today

$timetogo_int = intval(strtotime('+1 day', $later));//timestamp for 24 hours after expiration date 

if($emails->emailtype == "expiring"){
    $test_SQL = "SELECT ue.id,ue.userid AS 'user id',CONCAT(u.firstname,' ',u.lastname) AS 'name', CONCAT(u.email) AS 'email', c.theme AS 'courses', DATE_FORMAT( FROM_UNIXTIME( ue.timeend ) , '%M %d, %Y' ) AS 'diff date' " 

        . "FROM {course} c "
        . "JOIN {enrol} e ON e.courseid = c.id "
        . "JOIN {user_enrolments} ue ON ue.enrolid = e.id "
        . "JOIN {user} u ON u.id = ue.userid "
        . "JOIN {role_assignments} ra ON ra.userid = u.id "
        . "WHERE u.deleted=0 AND ra.roleid = '16' AND u.auth = 'manual' "
        . "AND ue.timeend > ? "
        . "AND ue.timeend <= ? "
        . "ORDER BY u.lastname, u.firstname ";
}

else if($emails->emailtype == "enrolling"){
 $test_SQL = "SELECT ue.id,ue.userid AS 'user id',CONCAT(u.firstname,' ',u.lastname) AS 'name', CONCAT(u.email) AS 'email', c.theme AS 'courses', DATE_FORMAT( FROM_UNIXTIME( ue.timestart ) , '%M %d, %Y' ) AS 'diff date' " 

        . "FROM {course} c "
        . "JOIN {enrol} e ON e.courseid = c.id "
        . "JOIN {user_enrolments} ue ON ue.enrolid = e.id "
        . "JOIN {user} u ON u.id = ue.userid "
        . "JOIN {role_assignments} ra ON ra.userid = u.id "
        . "WHERE u.deleted=0 AND ra.roleid = '16' AND u.auth = 'manual' "
        . "AND ue.timestart > ? "
        . "AND ue.timestart <= ? "
        . "ORDER BY u.lastname, u.firstname ";
    
}
//
//        . "AND ue.timestart <= ? "


$groups = $DB->get_records_sql($test_SQL,array($later,$timetogo_int));
//var_dump($groups);
$uservar = 'user id';
$coursevar = 'courses';
$namevar = 'name';
$diffvar = 'diff date';
$emailvar = 'email';

//var_dump($groups);
$persons = [];

//groups returns single records for each user + course they are in.  This section parses those records and creates a single user object that includes an array of courses they are enrolled in that are relevant to this query.

foreach ($groups as $indie){ 
    if(sizeof($persons)==0){
        $person = new stdClass();
        $person->id=$indie->$uservar;
        $person->name=$indie->$namevar;
        $person->email=$indie->$emailvar;
        $person->diffdate=$indie->$diffvar;
        $person->course[]=$indie->$coursevar;
        $persons[]=$person;
    }
    else if (sizeof($persons)>0){
        if($persons[sizeof($persons)-1]->id==$indie->$uservar){ //if this record is the same user as the previous record, add the current course to the previous record's course array
            if(!in_array($indie->$coursevar,$persons[sizeof($persons)-1]->course)){
                $persons[sizeof($persons)-1]->course[]=$indie->$coursevar;
            }
        }
        else {
            //if this is a new unique user, create a new record
           $person = new stdClass();
            $person->id=$indie->$uservar;
            $person->name=$indie->$namevar;
            $person->email=$indie->$emailvar;
            $person->diffdate=$indie->$diffvar;
            $person->course[]=$indie->$coursevar;
            $persons[]=$person;
            }
    }
}


echo "<table class='license-table'>";
echo "<tr>\n";
echo "<th style='text-align:left; padding-right:.5em;'>User ID</th>\n";
echo "<th style='text-align:left'>Name</th>\n";
echo "<th style='text-align:left'>Email</th>\n";
echo "<th style='text-align:left'>Day</th>\n";
echo "<th style='text-align:left'>Courses</th>\n";

echo "<th></th>\n";
echo "<th></th>\n";
echo "</tr>\n";

foreach($persons as $person){
    
    $temparray = $person->course;
    
    foreach($temparray as $key => $crse){
        switch($crse){
            case "aeketmath":
                $temparray[$key]="Math";
                break;
            case "aeketlanguagearts":
                $temparray[$key]="Language Arts";
                break;
            case "aeketscience":
                $temparray[$key]="Science";
                break;
            case "aeketsocialstudies":
                $temparray[$key]="Social Studies";
                break;
            default:
                unset($temparray[$key]);                
        }
    }
    
    $person->course = $temparray;
    
    echo "<tr>\n";
    echo "<td style='text-align:left'>$person->id</td>\n";
    echo "<td style='text-align:left'><a href='".$CFG->wwwroot."/user/view.php?id=$person->id'>".$person->name."</a></td>\n";
    echo "<td style='text-align:left'>$person->email</td>\n";
        echo "<td style='text-align:left'>$person->diffdate</td>\n";

    echo "<td style='text-align:left'>".join(', ', $person->course)."</td>\n";
    echo "</tr>\n";
}

echo "</table>\n";

  echo '<input type="button" value="Back" onclick="document.location.href=\''.$CFG->wwwroot.'/local/autoemail/index.php\'">';

$toUser = new stdClass();
$toUser->email = 'bcvincent@gmail.com';
$toUser->firstname = 'Brian Vincent';
$toUser->lastname = '';
$toUser->maildisplay = true;
$toUser->mailformat = 0;
$toUser->id = 975;

$fromUser = new stdClass();
$fromUser->email = 'bvincent@ket.org';
$fromUser->firstname = 'Bryan Vyncent';
$fromUser->lastname = '';
$fromUser->maildisplay = true;
$fromUser->mailformat = 1;
$fromUser->id = 975;

$subject = 'Test email';
$messageText = 'Message Text Test Test Test';
$messageHtml = ' ';

//email_to_user($toUser, $fromUser, $subject, $messageText, $messageHtml, ", ", false);

echo $OUTPUT->footer();
?>


