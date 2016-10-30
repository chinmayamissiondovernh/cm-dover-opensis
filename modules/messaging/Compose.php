<?php
#**************************************************************************
#  openSIS is a free student information system for public and non-public 
#  schools from Open Solutions for Education, Inc. web: www.os4ed.com
#
#  openSIS is  web-based, open source, and comes packed with features that 
#  include student demographic info, scheduling, grade book, attendance, 
#  report cards, eligibility, transcripts, parent portal, 
#  student portal and more.   
#
#  Visit the openSIS web site at http://www.opensis.com to learn more.
#  If you have question regarding this system or the license, please send 
#  an email to info@os4ed.com.
#
#  This program is released under the terms of the GNU General Public License as  
#  published by the Free Software Foundation, version 2 of the License. 
#  See license.txt.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#***************************************************************************************
include_once("fckeditor/fckeditor.php") ;
PopTable('header','Compose Message');
$userName=  User('USERNAME');
$_SESSION['course_period_id']='';
echo "<FORM name=ComposeMail id=Compose action=Modules.php?modname=messaging/Inbox.php&count=$c  METHOD=POST enctype=multipart/form-data >";
if($_REQUEST['modfunc']!='choose_course')
{
    if(User('PROFILE')=='admin' || User('PROFILE')=='teacher')
    {
        echo "<DIV id=course_div>";
    } 
if(isset($_REQUEST['mod']) && $_REQUEST['mod']=='draft')
{
    $mail_id=$_REQUEST['mail_id'];
   $query="select * from msg_inbox where mail_id='$mail_id'";
   $result=DBGet(DBQuery($query));

foreach($result as $v)
{
    $to_user=$v['TO_USER'];
    $to_cc=$v['TO_CC'];
       $to_bcc=$v['TO_BCC'];
       $mail_subject=$v['MAIL_SUBJECT'];
       $mail_id=$v['MAIL_ID'];
       $mail_body=$v['MAIL_BODY'];
}
}
if(!isset($_REQUEST['modto']) && !isset($_REQUEST['mod']))
{
       $to_user='';
    $to_cc='';
    $to_bcc='';
    $mail_subject='';
    $mail_id='';
        $mail_body='';
}
if(isset($_REQUEST['modto']) && $_REQUEST['m']=='reply')
{
   $to_user=$_REQUEST['modto'];
   $mail_subject=  base64_decode($_REQUEST['sub']);
  
}

echo '<table width="100%">
    
   <tr>
      <td align="left" width="50px">
            To :
       </td>
       <td align="left">';

 echo  TextInput_mail($to_user,'txtToUser','','onkeyup="nameslist(this.value,1)" class=mail_input');
 echo ' &nbsp; <a href="#" onclick="show_cc()">CC</a>';
 echo ' &nbsp; <a href="#" onclick="show_bcc()">BCC</a>';
 $groupList = DBGet(DBQuery("SELECT GROUP_ID,GROUP_NAME FROM mail_group where user_name='".$userName."'"));
        
     echo "&nbsp; <SELECT name='groups' style='max-width:250;' onChange=\"list_of_groups(this.options[this.selectedIndex].value);\"><OPTION value=''>Select Group</OPTION>";
     foreach($groupList as $groupArr)
     {	
         $option=$groupArr['GROUP_NAME'];
         $value=$groupArr['GROUP_ID'];
     
         if($_REQUEST['sel_group']==$value)  
         echo "<OPTION selected='selected' value=\"$value\">$option</OPTION>";
         else
         echo "<OPTION value=\"$option\">$option</OPTION>";   

      }
      echo '</SELECT>';

      
 echo'&nbsp; ';
if(User('PROFILE')=='teacher')
{
    if(!isset($_REQUEST['modto']) && $_REQUEST['m']!='reply')
{
  echo "<a href='#' style='background:#efefef; border:1px solid #7f9db9; text-decoration:none; padding:2px 7px; display:inline-block;' onclick='window.open(\"ForWindow.php?modname=".strip_tags(trim($_REQUEST[modname]))."&modfunc=choose_course\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'>Message My Class</a>";
}
}
echo " <div id=ajax_response></div>";

    echo   '</td>
        </tr><tr><td colspan=2><div id="message_my_class_div"></div></td></tr>';
   
     
        echo '<tr>
      <td colspan="2">
	  <div id="cc" style="display:none"><table width="100%" cellpadding="0"><tr><td align="left" width="50px">
            CC :
       </td>
       <td align="left" style="padding-bottom:5px">&nbsp;&nbsp;
'.  TextInput_mail($to_cc,'txtToCCUser','','onkeyup="nameslist(this.value,2)" class=mail_input').'
        &nbsp
        <div id=ajax_response_cc></div>
        </td>
        </tr></table></div>
        </tr>
          <tr>
      <td colspan="2">
      <div id="bcc" style="display:none"><table width="100%" cellpadding="0"><tr><td width="50px">
            BCC :
       </td>
       <td align="left">&nbsp;&nbsp;
'.  TextInput_mail($to_bcc,'txtToBCCUser','','onkeyup="nameslist(this.value,3)" class=mail_input').'
        &nbsp
        <div id=ajax_response_bcc></div>
        </td>
        </tr></table></div></td>
        </tr>';
     
    echo '<tr>
       <td align="left" width="50px">
           Subject:
        </td>
        <td align="left">
            '.  TextInput_mail($mail_subject,'txtSubj','','class=mail_input_full').'
       </td>
   </tr>
   
<tr><td colspan="2">';

                        $oFCKeditor = new FCKeditor("txtBody") ;
			$oFCKeditor->BasePath = "modules/messaging/fckeditor/" ;
			$oFCKeditor->Value = '';
			$oFCKeditor->Height = "350px";
			$oFCKeditor->Width = "600px";
                        $oFCKeditor->ToolbarSet	= 'Mytoolbar ';
			$oFCKeditor->Create() ;


echo '</td></tr>';
echo '<tr><td colspan=2><table id="append_tab">';
echo '<tr><td align="left" width="60px" colspan="2">Attach file: </td></tr>';
echo '<tr id="tr1"><td align="left" colspan="2"><input style="float: left;" type="file" name="f[]" id="up1" onchange="attachfile(1);" multiple/><div id="del1" style="display:none;float: left;"><input type="button" value="Clear"  onclick="clearfile(1)" /></div></td></tr>';
echo '</table></td></tr>';
echo '<tr><td colspan="2"><input type="button" style="display:none;"  id="attach1" onclick="appendFile();" value="Attach Another File" /></td></tr>';
echo'</table>';
echo '<input type=hidden id=counter value=1 />';
DrawHeader('','',"<br /><INPUT TYPE=SUBMIT name=button id=button class=btn_medium VALUE='Send' onClick='validate_email();' />");
}
if($_REQUEST['modfunc']=='choose_course')
{
   
                     
        if(!$_REQUEST['course_period_id'])
        {   
                $message_my_class='yes';
		include 'modules/scheduling/CoursesforWindow.php';
        }
	else
	{
		$_SESSION['MassSchedule.php']['subject_id'] = $_REQUEST['subject_id'];
		$_SESSION['MassSchedule.php']['course_id'] = $_REQUEST['course_id'];
		$_SESSION['MassSchedule.php']['course_period_id'] = $_REQUEST['course_period_id'];

		$course_title = DBGet(DBQuery('SELECT TITLE FROM courses WHERE COURSE_ID=\''.$_SESSION['MassSchedule.php']['course_id'].'\''));
		$course_title = $course_title[1]['TITLE'];
		$period_title_RET = DBGet(DBQuery('SELECT COURSE_PERIOD_ID,TITLE,MARKING_PERIOD_ID,GENDER_RESTRICTION FROM course_periods WHERE COURSE_PERIOD_ID=\''.$_SESSION['MassSchedule.php']['course_period_id'].'\''));
		$period_title = $period_title_RET[1]['TITLE'];
		$mperiod = $period_title_RET[1]['MARKING_PERIOD_ID'];
                $course_period_id=$period_title_RET[1]['COURSE_PERIOD_ID'];
                $_SESSION['course_period_id']=$_REQUEST['course_period_id'];
                $grp=DBGet(DBQuery("select * from mail_group")); 
                $title=trim($course_title).' '.trim($period_title);
               echo "<script language=javascript>opener.document.getElementById(\"txtToUser\").value=\"$title\";opener.document.getElementById(\"ajax_response\").innerHTML='';opener.document.getElementById(\"txtToUser\").readOnly='true';opener.document.getElementById(\"message_my_class_div\").innerHTML = \"<input type=hidden name=cp_id id=cp_id value=$course_period_id><INPUT type=checkbox id=list_gpa_student name=list_gpa_student value=Y CHECKED>Only Students<INPUT type=checkbox name=list_gpa_parent id=list_gpa_parent value=Y CHECKED>Only Parents".(User('PROFILE')!='teacher'?'<INPUT type=checkbox name=list_gpa_teacher id=list_gpa_teacher value=Y CHECKED>Only Teachers':'')."&nbsp;&nbsp;<a href='Modules.php?modname=messaging/Compose.php'><font color='red'>Remove Course</font>\";window.close();</script>";                                   
	}
}
 echo "</form>";?>
 
<?php PopTable('footer');
?>



