<?php

// required libraries
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

### process normal show and edit (post) ###
require_login();
if ( (!has_capability('local/autoemail:manage', context_system::instance())) && (!has_capability('local/autoemail:manageall', context_system::instance())) ) {
 	require_capability('local/autoemail:manage', context_system::instance());
}

if (has_capability('local/autoemail:manageall', context_system::instance())) {
	$pagetitle=get_string('allketautoemail', 'local_autoemail');
    $emails = $DB->get_records('ketautoemail',array('deleted' => 0), 'id ASC');
}

$PAGE->set_cacheable(false);
$PAGE->set_url("/local/autoemail/index.php");
$PAGE->set_context(context_system::instance());
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);
$PAGE->requires->css("/local/autoemail/styles.css");
$PAGE->add_body_class('ketlicense-index');
$PAGE->set_pagelayout('standard');

// breadcrumb setup
$PAGE->navbar->ignore_active();
$PAGE->navbar->add("Auto Email Management");

echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle, 2);

// begin page body
echo "<div class='ketlicense'>";
echo "<p>&nbsp;</p>\n";
echo "Welcome to the managing page for the automatic emails. Below is a list of the emails that are currently set up.";
echo "<p>&nbsp;</p>\n";
echo "Time: This is how many days since enrolment or til expiration.";
echo "<p>&nbsp;</p>\n";
echo "Email Type: This is the option to pick if it's 'since enrolment' or 'til expiration.'";
echo "<p>&nbsp;</p>\n";
echo "Active: This denotes whether this auto-email is on or off, that is, if it is being actively sent or dormant.";
echo "<p>&nbsp;</p>\n";
echo "The rest is more self-explanatory.  The admin that created the email and the Subject line of the email.";
echo "<p>&nbsp;</p>\n";
echo "Click new to create a new email, edit to edit, delete to delete, query to see a list of users that expire in this many days.";
echo "<p>&nbsp;</p>\n";

if(has_capability('local/ketautoemail:manageall', context_system::instance())){
    echo '<input type="button" value="New Email" onclick="document.location.href=\''.$CFG->wwwroot.'/local/autoemail/edit.php\'">';
    echo "<p>&nbsp;</p>\n";
}
echo "<table class='license-table'>";
echo "<tr>\n";
echo "<th style='text-align:left; padding-right:.5em;'>Time</th>\n";
echo "<th style='text-align:left'>Email Type</th>\n";
echo "<th style='text-align:left'>Subject</th>\n";
echo "<th style='text-align:left'>Active</th>\n";
echo "<th style='text-align:left'>Admin</th>\n";

echo "<th></th>\n";
echo "<th></th>\n";
echo "</tr>\n";

foreach($emails as $email){
    $user = $DB->get_record('user',array("id"=>$email->admin));
    
    echo "<tr>\n";
    echo "<td style='text-align:left'>".$email->emailtime." days</td>\n";
    $tempactive = ($email->emailtype=='enrolling') ? "Since Enrolment" : "Til Expiration";
    echo "<td style='text-align:left'>$tempactive</td>\n";
    echo "<td style='text-align:left'>$email->subject</td>\n";
    $tempactive = ($email->active==1) ? "Yes" : "No";
    echo "<td style='text-align:left'>$tempactive</td>\n";
    echo "<td style='text-align:left'><a href='".$CFG->wwwroot."/user/view.php?id=$user->id'>".$user->firstname." ".$user->lastname."</a></td>\n";
    echo '<td><input type="button" value="Edit" onclick="document.location.href=\''.$CFG->wwwroot.'/local/autoemail/edit.php?id='.$email->id.'\'"></td>';
        echo '<td><input type="button" value="Delete" onclick="document.location.href=\''.$CFG->wwwroot.'/local/autoemail/edit.php?delete='.$email->id.'\'"></td>';
     echo '<td><input type="button" value="Query" onclick="document.location.href=\''.$CFG->wwwroot.'/local/autoemail/checkemail.php?id='.$email->id.'\'"></td>';


    echo "</tr>\n";
}

echo "</table>\n";
echo "</div>";
echo $OUTPUT->footer();
?>