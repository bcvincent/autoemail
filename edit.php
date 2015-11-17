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

### start processing the page/requests ###
$id = optional_param('id', 0, PARAM_INT); // id of the email
$delete = optional_param('delete', 0, PARAM_INT); // id of the email to delete
$lic = optional_param('l', 0, PARAM_INT); // id of the purchase

$debug = optional_param('debug', 0, PARAM_INT); 

$nowtime = time();
$new_record = false;
$page_output = "";

if(isset($_POST['id'])){
    var_dump($_POST);

    $eid = $_POST['id'];
      switch($_POST["action"]){
        case "create":
            $newemail = new stdClass();
            //$newemail->id = $_POST["id"];
            $newemail->emailtime = $_POST["time"];
            $newemail->emailtype = $_POST["etype"];    
            $newemail->admin = $USER->id;
            $newemail->active = $_POST["active"];
            $newemail->subject = $_POST["subject"];
            $newemail->body = $_POST["body"];
            $newemail->deleted = 0;
            $newemail->timecreated = strtotime(date('Y-m-d H:i:s'));
            $newemail->effectivedate = null;
            $newemail->timedeleted = null;
            $purchaseid = $DB->insert_record('ketautoemail',$newemail,$returnid=true);
              
        break;
        case "edit":
            $post_email = $DB->get_record('ketautoemail',array('id'=>$_POST['id']),'*',MUST_EXIST);

            $update_values = new stdClass();
            $update_values->id = $post_email->id;
            $update_values->emailtime = $_POST["time"];
            $update_values->emailtype = $_POST["etype"];
            $update_values->active = $_POST["active"];
            $update_values->subject = $_POST["subject"];
            $update_values->body = $_POST["body"];
            $update_values->timemodified = strtotime(date('Y-m-d H:i:s'));
                          var_dump($_POST);
var_dump($update_values);
            if(!$DB->update_record('ketautoemail',$update_values)) {
                die("Couldn't update purchase record.\nFile: ".__FILE__."\nLine: ".__LINE__."\n");
            }
        break;
        case "delete":
            $deleted_email = $DB->get_record('ketautoemail',array('id'=>$_POST['id']),'*',MUST_EXIST);
            $update_values = new stdClass();
            $update_values->id = $deleted_email->id;
            $update_values->deleted = 1;
            $update_values->timedeleted = strtotime(date('Y-m-d H:i:s')); 
            $update_values->timemodified = strtotime(date('Y-m-d H:i:s'));   
            if(!$DB->update_record('ketautoemail',$update_values)) {
                die("Couldn't update purchase record.\nFile: ".__FILE__."\nLine: ".__LINE__."\n");
            }
            break;
      }

   $redirect_url = new moodle_url("/local/autoemail/index.php");
  redirect($redirect_url);
}

else{
}



//add_to_log(0, 'autoemail', 'license manage page', "manage.php?id=$id", '');

$current_user_admin_allowed=array();
if ($id) {
	$emails=$DB->get_record('ketautoemail', array('id' => $id), '*', MUST_EXIST);
    //var_dump($emails);
    $eid = $emails->id;
	$pagetitle="Edit Email";
    $action = "edit";


} else if ($delete){
    $pagetitle="Delete Email";
    $emails=$DB->get_record('ketautoemail', array('id' => $delete), '*', MUST_EXIST);
    $eid = $emails->id;
    $action = "delete";

}

else {
    $emails = new stdClass();
    $eid = "new";
    $emails->active = 0;
    $emails->emailtime = null;
    $emails->emailtype = null;
    $emails->subject = null;
    $emails->body = null;
    $pagetitle="Create Email";
    $action = "create";

}



### process normal show and edit (post) ###
require_login();
if ( (!has_capability('local/autoemail:manage', context_system::instance())) && (!has_capability('local/autoemail:manageall', context_system::instance())) ) {
 	require_capability('local/autoemail:manage', context_system::instance());
}
  
// determine permission schema / admin 

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
$PAGE->navbar->add("Auto Email Management", new moodle_url("/local/autoemail/index.php"));
$PAGE->navbar->add($pagetitle);

echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle, 2);

echo '
<script language="JavaScript">
</script>
';

if(!($id) && ($delete)){ //delete email.  only works if only delete param is present
    $delete_output = "Are you absolutely sure you want to completely delete the following email?";
    $delete_output .= "<br><br>";
    $user = $DB->get_record('user',array("id"=>$emails->admin));

    $delete_output .= "<table class='license-table'>";
    $delete_output .= "<tr>\n";
    $delete_output .= "<th style='text-align:left; padding-right:.5em;'>Time</th>\n";
    $delete_output .= "<th style='text-align:left'>Subject</th>\n";
    $delete_output .= "<th style='text-align:left'>Admin</th>\n";

    $delete_output .= "<th></th>\n";
    $delete_output .= "<th></th>\n";
    $delete_output .= "</tr>\n";

    $delete_output .= "<tr>\n";
    $delete_output .= "<td style='text-align:left'>$emails->emailtime</td>\n";
    $delete_output .= "<td style='text-align:left'>$emails->subject</td>\n";
    $delete_output .= "<td style='text-align:left'><a href='".$CFG->wwwroot."/user/view.php?id=$user->id'>".$user->firstname." ".$user->lastname."</a></td>\n";
    

    $delete_output .= "</tr>\n";


    $delete_output .= "</table>\n";
    
    
    $form_output = html_writer::start_tag('form',array('method' => 'POST', 'action' => 'edit.php', 'id' => 'delete_form'));
    $form_output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'id', 'value' => $eid));
    $form_output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'action', 'value' => $action));


    $form_fields = html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'submit', 'id' => 'submit', 'class' => 'submit', 'value' => 'Delete'));
    $form_fields .= html_writer::empty_tag('input', array('type' => 'button', 'value' => 'Cancel', 'onclick' => 'document.location.href="index.php"'));
    
    $form_output .= $form_fields;
    $form_output .= html_writer::end_tag('form');

    if(!empty($output_msg)){
        $page_output .= $output_msg;
    }
    $page_output .= "<div class='ketlicense'>";
    $page_output .= $delete_output;
    $page_output .= $form_output;
    $page_output .= "</div>";



    echo $page_output;

    echo $OUTPUT->footer();
}
else {  //create or edit an email
    $form_output = html_writer::start_tag('form',array('method' => 'POST', 'action' => 'edit.php', 'id' => 'edit_form'));
    $form_output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'id', 'value' => $eid));
    $form_output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'action', 'value' => $action));

    ///////////////////////////////////////////////////////////////////////////////////////
    // Info
    ///////////////////////////////////////////////////////////////////////////////////////
    $form_fields = html_writer::start_tag('fieldset');
    $form_fields .= html_writer::nonempty_tag('legend','Info');
    $purchase = "";
    // draft select options
    $draft_options = array();
    $draft_options['1'] = 'Yes';
    $draft_options['0'] = 'No';


    // build active select
    $form_fields .= html_writer::start_tag('p');
    $form_fields .= html_writer::tag('label', 'Active', array( 'for' => 'draft', 'class' => 'ae_reports_label' ));
    $dropdown_output = html_writer::select($draft_options, 'active', $emails->active, 'Select Below');//, array('class' => 'ae_reports_select'));


    $form_fields .= $dropdown_output;
    $form_fields .= html_writer::end_tag('p');

    // time
    $form_fields .= html_writer::start_tag('p');
    $form_fields .= html_writer::tag('label', 'Days', array( 'for' => 'time', 'class' => 'ae_reports_label' ));
    $form_fields .= html_writer::empty_tag('input', array('type' => 'number', 'name' => 'time', 'id' => 'fiscalyear', 'class' => 'ae_reports_input', 'value' => $emails->emailtime));
    $form_fields .= html_writer::end_tag('p');
    
    $type_options = array();
    $type_options['expiring'] = 'Until expiration';
    $type_options['enrolling'] = 'Since enrolling';
    
    $form_fields .= html_writer::start_tag('p');
    $form_fields .= html_writer::tag('label', 'Type', array( 'for' => 'etype', 'class' => 'ae_reports_label' ));
    $dropdown_output = html_writer::select($type_options, 'etype', $emails->emailtype, 'Select Below');
    $form_fields .= $dropdown_output;

    //$form_fields .= html_writer::empty_tag('input', array('type' => 'number', 'name' => 'etype', 'id' => 'fiscalyear', 'class' => 'ae_reports_input', 'value' => $emails->emailtype));
    $form_fields .= html_writer::end_tag('p');
    

    $form_fields .= html_writer::end_tag('p');

    $form_fields .= html_writer::end_tag('fieldset');
    ///////////////////////////////////////////////////////////////////////////////////////


    ///////////////////////////////////////////////////////////////////////////////////////
    // email
    ///////////////////////////////////////////////////////////////////////////////////////
    $form_fields .= html_writer::start_tag('fieldset');
    $form_fields .= html_writer::nonempty_tag('legend','Email');

    // subject
    $form_fields .= html_writer::start_tag('p');
    $form_fields .= html_writer::tag('label', 'Subject', array( 'for' => 'subject', 'class' => 'ae_reports_label' ));
    $form_fields .= html_writer::empty_tag('input', array('type' => 'text', 'name' => 'subject', 'id' => 'fiscalyear', 'class' => 'ae_reports_input', 'value' => $emails->subject));
    $form_fields .= html_writer::end_tag('p');

    // body
    $form_fields .= html_writer::start_tag('p');
    $form_fields .= html_writer::tag('label', 'Body', array( 'for' => 'body', 'class' => 'ae_reports_label' ));
    $form_fields .= html_writer::start_tag('textarea', array('name' => 'body', 'id' => 'privatenote', 'class' => 'ae_reports_input'));
    $form_fields .= $emails->body;
    $form_fields .= html_writer::end_tag('textarea');
    $form_fields .= html_writer::end_tag('p');

    $form_fields .= html_writer::end_tag('fieldset');
    ///////////////////////////////////////////////////////////////////////////////////////

    $form_fields .= html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'submit', 'id' => 'submit', 'class' => 'submit', 'value' => 'Save'));
    $form_fields .= html_writer::empty_tag('input', array('type' => 'button', 'value' => 'Cancel', 'onclick' => 'document.location.href="index.php"'));

    $form_output .= $form_fields;
    $form_output .= html_writer::end_tag('form');

    if(!empty($output_msg)){
        $page_output .= $output_msg;
    }
    $page_output .= "<div class='ketlicense'>";
    $page_output .= $form_output;
    $page_output .= "</div>";



    echo $page_output;

    echo $OUTPUT->footer();

}




