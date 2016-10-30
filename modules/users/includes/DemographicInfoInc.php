<?php
#**************************************************************************
#  openSIS is a free student information system for public and non-public 
#  schools from Open Solutions for Education, Inc. web: www.os4ed.com
#
#  openSIS is  web-based, open source, and comes packed with features that 
#  include staff demographic info, scheduling, grade book, attendance,
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





#########################################################ENROLLMENT##############################################

echo '<TABLE width=100% border=0 cellpadding=3>';

echo '<TR><td valign="top">';
echo '<TABLE border=0>';
echo '<tr><td style=width:120px><span class=red>*</span>Name</td><td>:</td><td>';

$_SESSION['staff_selected']=$staff['STAFF_ID'];

if($_REQUEST['staff_id']=='new')
    echo '<TABLE><TR><TD>'.SelectInput($staff['TITLE'],'staff[TITLE]','<span class=red>Salutation</span>',array('Mr.'=>'Mr.','Mrs.'=>'Mrs.','Ms.'=>'Ms.','Miss'=>'Miss', 'Dr'=>'Dr', 'Rev'=>'Rev'),'').'</TD><TD>'.TextInput($staff['FIRST_NAME'],'staff[FIRST_NAME]','<FONT class=red>First</FONT>','maxlength=50 class=cell_floating').'</TD><TD>'.TextInput($staff['MIDDLE_NAME'],'staff[MIDDLE_NAME]','Middle','maxlength=50 class=cell_floating').'</TD><TD>'.TextInput($staff['LAST_NAME'],'staff[LAST_NAME]','<FONT color=red>Last</FONT>','maxlength=50 class=cell_floating').'</TD><TD valign=top>'.SelectInput($staff['NAME_SUFFIX'],'staff[NAME_SUFFIX]','Suffix',array('Jr.'=>'Jr.','Sr.'=>'Sr.','II'=>'II','III'=>'III','IV'=>'IV','V'=>'V'),'','style="font-size:14px; font-weight:bold;"').'</TD></TR></TABLE>';
else
    echo '<DIV id=user_name><div onclick=\'addHTML("<TABLE><TR><TD>'.str_replace('"','\"',SelectInput($staff['TITLE'],'staff[TITLE]','Salutation',array('Mr.'=>'Mr.','Mrs.'=>'Mrs.','Ms.'=>'Ms.','Miss'=>'Miss', 'Dr'=>'Dr', 'Rev'=>'Rev'),'','',false)).'</TD><TD>'.str_replace('"','\"',TextInput(trim($staff['FIRST_NAME']),'staff[FIRST_NAME]',(!$staff['FIRST_NAME']?'<FONT color=red>':'').'First'.(!$staff['FIRST_NAME']?'</FONT>':''),'maxlength=50',false)).'</TD><TD>'.str_replace('"','\"',TextInput($staff['MIDDLE_NAME'],'staff[MIDDLE_NAME]','Middle','size=3 maxlength=50',false)).'</TD><TD>'.str_replace('"','\"',TextInput(trim($staff['LAST_NAME']),'staff[LAST_NAME]',(!$staff['LAST_NAME']?'<FONT color=red>':'').'Last'.(!$staff['LAST_NAME']?'</FONT>':''),'maxlength=50',false)).'</TD><TD valign=top>'.str_replace('"','\"',SelectInput($staff['NAME_SUFFIX'],'staff[NAME_SUFFIX]','',array('Jr.'=>'Jr.','Sr.'=>'Sr.','II'=>'II','III'=>'III','IV'=>'IV','V'=>'V'),'','style="font-size:14px; font-weight:bold;"',false)).'</TD></TR></TABLE>","user_name",true);\'>'.(!$staff['TITLE']&&!$staff['FIRST_NAME']&&!$staff['MIDDLE_NAME']&&!$staff['LAST_NAME']&&!$staff['NAME_SUFFIX']?'-':$staff['TITLE'].' '.$staff['FIRST_NAME'].' '.$staff['MIDDLE_NAME'].' '.$staff['LAST_NAME']).' '.$staff['NAME_SUFFIX'].'</div></DIV><small>'.(!$staff['FIRST_NAME']||!$staff['LAST_NAME']?'<FONT color=red>':'<FONT color='.Preferences('TITLES').'>').'</FONT></small>';
echo'</td></tr>';


echo '<tr><td>Staff ID</td><td>:</td><td>'.NoInput($staff['STAFF_ID'],'').'</td></tr>';


echo '<tr><td>Alternate ID</td><td>:</td><td>';
echo TextInput($staff['ALTERNATE_ID'],'staff[ALTERNATE_ID]','','size=12 maxlength=100 class=cell_floating ').'</td></tr>';
$options = array('Dr.'=>'Dr.','Mr.'=>'Mr.','Ms.'=>'Ms.','Rev.'=>'Rev.','Miss.'=>'Miss.');

echo '<tr><td><span class=red></span>Gender</td><td>:</td><td>'.SelectInput($staff['GENDER'],'staff[GENDER]','',array('Male'=>'Male','Female'=>'Female'),'N/A','').'</td></tr>';

echo '<tr><td><span class=red></span>Date of Birth</td><td>:</td><td>';

echo DateInputAY($staff['BIRTHDATE'],'staff[BIRTHDATE]',1).'</td></tr>';

$ETHNICITY_RET=DBGet(DBQuery("SELECT ETHNICITY_ID,ETHNICITY_NAME FROM ethnicity ORDER BY SORT_ORDER"));
foreach($ETHNICITY_RET as $ethnicity_array){
$ethnicity[$ethnicity_array['ETHNICITY_ID']]=$ethnicity_array['ETHNICITY_NAME'];
}
echo '<tr><td><span class=red></span>Ethnicity</td><td>:</td><td>'.SelectInput($staff['ETHNICITY_ID'],'staff[ETHNICITY_ID]','',$ethnicity,'N/A','').'</td></tr>';
$LANGUAGE_RET=DBGet(DBQuery("SELECT LANGUAGE_ID,LANGUAGE_NAME FROM language ORDER BY SORT_ORDER"));
foreach($LANGUAGE_RET as $language_array){
$language[$language_array['LANGUAGE_ID']]=$language_array['LANGUAGE_NAME'];
}
echo '<tr><td><span class=red></span>Primary Language</td><td>:</td><td>'.SelectInput($staff['PRIMARY_LANGUAGE_ID'],'staff[PRIMARY_LANGUAGE_ID]','',$language,'N/A','').'</td></tr>';
echo '<tr><td>Second Language</td><td>:</td><td>'.SelectInput($staff['SECOND_LANGUAGE_ID'],'staff[SECOND_LANGUAGE_ID]','',$language,'N/A','').'</td></tr>';
echo '<tr><td>Third Language</td><td>:</td><td>'.SelectInput($staff['THIRD_LANGUAGE_ID'],'staff[THIRD_LANGUAGE_ID]','',$language,'N/A','').'</td></tr>';
if($_REQUEST['staff_id']=='new')
    $id_sent=0;
else
{
    if($_REQUEST['staff_id']!='')
    $id_sent=$_REQUEST['staff_id'];
    else
    $id_sent= UserStaffID();
  
}

echo '<TR><td><span class=red>*</span>Email</td><td>:</td><td>'.TextInput($staff['EMAIL'],'staff[EMAIL]','','autocomplete=off id=email_id class=cell_medium onkeyup=check_email(this,'.$id_sent.',2); onblur=check_email(this,'.$id_sent.',2) ').'</td><td> <span id="email_error"></span></td></tr></tr>';

echo '<tr><td>Physical Disability</td><td>:</td><td>'.SelectInput($staff['PHYSICAL_DISABILITY'],'staff[PHYSICAL_DISABILITY]','',array('N'=>'No','Y'=>'Yes'),false,'onchange=show_span("span_disability_desc",this.value)').'</td></tr>';


// IMAGE
// 
$_REQUEST['category_id'] = 1;
$_REQUEST['custom']='staff';
echo '<tr><td>';
include('modules/users/includes/OtherInfoInc.php');
echo '</td></tr>';

echo '</table>';
echo '</td><td valign="top" align="right"><div class=clear></div>';

if($_REQUEST['staff_id']!='new' && $UserPicturesPath && (($file = @fopen($picture_path=$UserPicturesPath.'/'.UserStaffID().'.JPG','r')) || ($file = @fopen($picture_path=$UserPicturesPath.'/'.UserStudentID().'.JPG','r'))))
{
        fclose($file);
	echo '<div width=150 align="center"><IMG SRC="'.$picture_path.'?id='.rand(6,100000).'" width=150 class=pic>';
	if(User('PROFILE')=='admin' && User('PROFILE')!='student' && User('PROFILE')!='parent')
	echo '<br><a href=Modules.php?modname=users/UploadUserPhoto.php?modfunc=edit style="text-decoration:none"><b>Update Staff\'s Photo</b></a></div>';
	else
	echo '';
}
else
{
    
	if($_REQUEST['staff_id']!='new')
	{
	echo '<div align="center"><IMG SRC="assets/noimage.jpg?id='.rand(6,100000).'" width=144 class=pic>';
	if(User('PROFILE')=='admin' && User('PROFILE')!='student' && User('PROFILE')!='parent')
	{
            echo '<div align=center>Upload Staff\'s Photo: <div class="fileUpload btn_wide"><span>Upload</span><input id="uploadBtn" type="file" name="file" class="upload" onchange="selectFile(this.value)" /></div></div>';
        echo '<div id="uploadFile"></div>';
        }
        }
	else
        {
            
         echo '<div align="center"><IMG SRC="assets/noimage.jpg?id='.rand(6,100000).'" width=144 class=pic>';
	if(User('PROFILE')=='admin' && User('PROFILE')!='student' && User('PROFILE')!='parent')

	echo '<div align=center>Upload Staff\'s Photo: <div class="fileUpload btn_wide"><span>Upload</span><input type="file" id="uploadBtn"  name="file" class="upload" onchange="selectFile(this.value)" /></div></div>';
        echo '<div id="uploadFile"></div>';

        }
	
}

echo '</td></TR>';
echo '</td></tr>';




echo '</table>';
if($staff['PHYSICAL_DISABILITY']=='Y'){
echo '<table id="span_disability_desc"><tr><td style="width:120px">Disability Description</td><td>:</td><td>'.TextAreaInput($staff['DISABILITY_DESC'],'staff[DISABILITY_DESC]','', '', 'true').'</td></tr></table>';
}else{
    echo '<table id="span_disability_desc" style="display:none"><tr><td style="width:120px">Disability Description</td><td>:</td><td>'.TextAreaInput('','staff[DISABILITY_DESC]','', '', 'true').'</td></tr></table>';
}

?>
