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
include('../../../RedirectIncludes.php');

include_once('modules/students/includes/FunctionsInc.php');


#########################################################ENROLLMENT##############################################


################################################################################


$qr=  DBQuery("SELECT ethnicity_name FROM ethnicity where ethnicity_id>15");
$res=  DBGet($qr);
$ethnic_option = array('White, Non-Hispanic'=>'White, Non-Hispanic','Black, Non-Hispanic'=>'Black, Non-Hispanic','Hispanic'=>'Hispanic','American Indian or Native Alaskan'=>'American Indian or Native Alaskan','Pacific Islander'=>'Pacific Islander','Asian'=>'Asian','Indian'=>'Indian','Middle Eastern'=>'Middle Eastern','African'=>'African','Mixed Race'=>'Mixed Race','White British'=>'White British','Asian'=>'Asian','Black'=>'Black','Chinese'=>'Chinese','Other'=>'Other');
foreach($res as $v)
{
    ;
   $ethnic_option[$v['ETHNICITY_NAME']] =$v['ETHNICITY_NAME'];
}
$language_option = array('English'=>'English','Arabic'=>'Arabic','Bengali'=>'Bengali','Chinese'=>'Chinese','French'=>'French','German'=>'German','Haitian Creole'=>'Haitian Creole','Hindi'=>'Hindi','Italian'=>'Italian','Japanese'=>'Japanese','Korean'=>'Korean','Malay'=>'Malay','Polish'=>'Polish','Portuguese'=>'Portuguese','Russian'=>'Russian','Somali'=>'Somali','Spanish'=>'Spanish','Thai'=>'Thai','Turkish'=>'Turkish','Urdu'=>'Urdu','Vietnamese'=>'Vietnamese');



echo '<TABLE width=100% border=0 cellpadding=3>';
echo '<TR><td height="30px" colspan=2 class=hseparator><b>Demographic Information</b></td></tr>';
echo '<TR><td valign="top">';
echo '<TABLE border=0>';
echo '<tr><td style=width:120px>Name<font color=red>*</font></td><td>:</td><td>';
if($_REQUEST['student_id']=='new')
{
   unset($_SESSION['students_order']);
	echo '<TABLE ><TR><TD >'.TextInput($student['FIRST_NAME'],'students[FIRST_NAME]','<FONT color=red>First</FONT>','size=12 class=cell_floating maxlength=50 style="font-size:14px; font-weight:bold;"').'</TD><TD>'.TextInput($student['MIDDLE_NAME'],'students[MIDDLE_NAME]','Middle','class=cell_floating maxlength=50 style="font-size:14px; font-weight:bold;"').'</TD><TD>'.TextInput($student['LAST_NAME'],'students[LAST_NAME]','<FONT color=red>Last</FONT>','size=12 class=cell_floating maxlength=50 style="font-size:14px; font-weight:bold;"').'</TD><TD>'.SelectInput($student['NAME_SUFFIX'],'students[NAME_SUFFIX]','Suffix',array('Jr.'=>'Jr.','Sr.'=>'Sr.','II'=>'II','III'=>'III','IV'=>'IV','V'=>'V'),'','style="font-size:14px; font-weight:bold;"').'</TD></TR></TABLE>';
}
else
	echo '<DIV id=student_name><div style="font-size:14px; font-weight:bold;" onclick=\'addHTML("<TABLE><TR><TD>'.str_replace('"','\"',TextInput($student['FIRST_NAME'],'students[FIRST_NAME]','<FONT color=red>First</FONT>','maxlength=50 style="font-size:14px; font-weight:bold;"',false)).'</TD><TD>'.str_replace('"','\"',TextInput($student['MIDDLE_NAME'],'students[MIDDLE_NAME]','Middle','size=3 maxlength=50 style="font-size:14px; font-weight:bold;"',false)).'</TD><TD>'.str_replace('"','\"',TextInput($student['LAST_NAME'],'students[LAST_NAME]','<FONT color=red>Last</FONT>','maxlength=50 style="font-size:14px; font-weight:bold;"',false)).'</TD><TD>'.str_replace('"','\"',SelectInput($student['NAME_SUFFIX'],'students[NAME_SUFFIX]','Suffix',array('Jr.'=>'Jr.','Sr.'=>'Sr.','II'=>'II','III'=>'III','IV'=>'IV','V'=>'V'),'','style="font-size:14px; font-weight:bold;"',false)).'</TD></TR></TABLE>","student_name",true);\'>'.$student['FIRST_NAME'].' '.$student['MIDDLE_NAME'].' '.$student['LAST_NAME'].' '.$student['NAME_SUFFIX'].'</div></DIV>';
echo'</td></tr>';

echo '<tr><td>Estimated Grad. Date </td><td>:</td><td>'.DateInputAY($student['ESTIMATED_GRAD_DATE'],'students[ESTIMATED_GRAD_DATE]','1').'</td></tr>';
echo '<tr><td>Gender</td><td>:</td><td>'.SelectInput($student['GENDER'],'students[GENDER]','',array('Male'=>'Male','Female'=>'Female'),'N/A','').'</td></tr>';
echo '<tr><td>Ethnicity</td><td>:</td><td>'.SelectInput($student['ETHNICITY'],'students[ETHNICITY]','',$ethnic_option,'N/A','').'</td></tr>';
echo '<tr><td>Common Name</td><td>:</td><td>'.TextInput($student['COMMON_NAME'],'students[COMMON_NAME]','','size=10 class=cell_medium maxlength=10').'</td></tr>';
echo '<input type=hidden id=current_date value='.date('Y-m-d').'>';
echo '<tr><td>Date of Birth<font color="red">*</font></td><td>:</td><td>'.DateInputAY($student['BIRTHDATE'],'students[BIRTHDATE]','2').'</td></tr>';
echo '<tr><td>Primary Language</td><td>:</td><td>'.SelectInput($student['LANGUAGE'],'students[LANGUAGE]','',$language_option,'N/A','').'</td></tr>';

if($_REQUEST['student_id']=='new')
    $id_sent=0;
else
    $id_sent=  UserStudentID();
echo '<tr><td>Email</td><td>:</td><td>'.TextInput($student['EMAIL'],'students[EMAIL]','','size=100 class=cell_medium maxlength=100 onkeyup=check_email(this,'.$id_sent.',3); onblur=check_email(this,'.$id_sent.',3)').'<div id=email_error></div></td></tr>';
echo '<tr><td>Phone</td><td>:</td><td>'.TextInput($student['PHONE'],'students[PHONE]','','size=100 class=cell_medium maxlength=100').'</td></tr>';

#############################################CUSTOM FIELDS###############################
$fields_RET = DBGet(DBQuery('SELECT ID,TITLE,TYPE,SELECT_OPTIONS,DEFAULT_SELECTION,REQUIRED,HIDE,SORT_ORDER FROM custom_fields WHERE SYSTEM_FIELD=\'N\' AND CATEGORY_ID=\''.$_REQUEST[category_id].'\' ORDER BY SORT_ORDER,TITLE'));

if(UserStudentID())
{
	$custom_RET = DBGet(DBQuery('SELECT * FROM students WHERE STUDENT_ID=\''.UserStudentID().'\''));
	$value = $custom_RET[1];
}


if(count($fields_RET))
	echo $separator;
$i=1;
$q=0;
foreach($fields_RET as $field)
{
	$q++;
	if( $fields_RET[$q]['HIDE']=='Y')
continue;
                if($field['REQUIRED']=='Y'){
                $req='<font color=red>*</font> ';
                }else{
                $req='';
                }
	switch($field['TYPE'])
	{
		case 'text':
			if(($i-1)%1==0)
				echo '<TR>';
			echo '<TD>'.$field['TITLE'].$req.'</td><td>:</td><td>';
			echo _makeTextInput('CUSTOM_'.$field['ID'],'','class=cell_medium');
			echo '</TD>';
			if($i%1==0)
				echo '</TR>';
			$i++;
			break;

		case 'autos':
			if(($i-1)%1==0)
				echo '<TR>';
			echo '<TD>'.$field['TITLE'].$req.'</td><td>:</td><td>';
			echo _makeAutoSelectInput('CUSTOM_'.$field['ID'],'');
			echo '</TD>';
			if($i%1==0)
				echo '</TR>';
			$i++;
			break;

		case 'edits':
			if(($i-1)%1==0)
				echo '<TR>';
			echo '<TD>'.$field['TITLE'].$req.'</td><td>:</td><td>';
			echo _makeAutoSelectInput('CUSTOM_'.$field['ID'],'');
			echo '</TD>';
			if($i%1==0)
				echo '</TR>';
			$i++;
			break;

		case 'numeric':
			if(($i-1)%1==0)
				echo '<TR>';
			echo '<TD>'.$field['TITLE'].$req.'</td><td>:</td><td>';
			echo _makeTextInput('CUSTOM_'.$field['ID'],'','size=5 maxlength=10 class=cell_medium');
			echo '</TD>';
			if($i%1==0)
				echo '</TR>';
			$i++;
			break;

		case 'date':
			if(($i-1)%1==0)
				echo '<TR>';
			echo '<TD>'.$field['TITLE'].$req.'</td><td>:</td><td>';
			echo DateInputAY($value['CUSTOM_'.$field['ID']],'CUSTOM_'.$field['ID'],$field['ID']+2);
			echo  '<input type=hidden name=custom_date_id[] value="'.$field['ID'].'" />';
                        echo '</TD>';
			if($i%1==0)
				echo '</TR>';
			$i++;
			break;
			

		case 'codeds':
		case 'select':
			if(($i-1)%1==0)
				echo '<TR>';
			echo '<TD>'.$field['TITLE'].$req.'</td><td>:</td><td>';
			echo _makeSelectInput('CUSTOM_'.$field['ID'],'');
			echo '</TD>';
			if($i%1==0)
				echo '</TR>';
			$i++;
			break;

		case 'multiple':
			if(($i-1)%1==0)
				echo '<TR>';
			echo '<TD>'.$field['TITLE'].$req.'</td><td>:</td><td>';
			echo _makeMultipleInput('CUSTOM_'.$field['ID'],'');
			echo '</TD>';
			if($i%1==0)
				echo '</TR>';
			$i++;
			break;

		case 'radio':
			if(($i-1)%1==0)
				echo '<TR>';
			echo '<TD>'.$field['TITLE'].$req.'</td><td>:</td><td>';
			echo _makeCheckboxInput('CUSTOM_'.$field['ID'],'');
			echo '</TD>';
			if($i%1==0)
				echo '</TR>';
			$i++;
			break;
	}
}

if(($i-1)%1!=0)
	echo '</TR>';
echo '<TABLE cellpadding=5>';
$i = 1;
foreach($fields_RET as $field)
{
	if($field['TYPE']=='textarea')
	{
                        if($field['REQUIRED']=='Y'){
                        $req='<font color=red>*</font> ';
                        }else{
                        $req='';
                        }
		if(($i-1)%1==0)
			echo '<TR>';
		echo '<TD>'.$field['TITLE'].$req.'</td><td>:</td><td>';
		echo _makeTextareaInput('CUSTOM_'.$field['ID'],'class=cell_medium');
		echo '</TD>';
		if($i%2==0)
			echo '</TR>';
		$i++;
	}
}
if(($i-1)%1!=0)
	echo '</TR>';


#############################################CUSTOM FIELDS###############################
echo '</table>';
echo '</td><td valign="top" align="right"><div class=clear></div>';
// IMAGE
if($_REQUEST['student_id']!='new' && $StudentPicturesPath && (($file = @fopen($picture_path=$StudentPicturesPath.'/'.UserStudentID().'.JPG','r')) || ($file = @fopen($picture_path=$StudentPicturesPath.'/'.UserStudentID().'.JPG','r'))))
{
	fclose($file);
	echo '<div width=150 align="center"><IMG SRC="'.$picture_path.'?id='.rand(6,100000).'" width=150 class=pic>';
	if(User('PROFILE')=='admin' && User('PROFILE')!='student' && User('PROFILE')!='parent')
	echo '<br><a href=Modules.php?modname=students/Upload.php?modfunc=edit style="text-decoration:none"><b>Update Student\'s Photo</b></a></div>';
	else
	echo '';
}
else
{
	if($_REQUEST['student_id']!='new')
	{
	
	echo '<div align="center"><IMG SRC="assets/noimage.jpg?id='.rand(6,100000).'" width=144 class=pic>';
	if(User('PROFILE')=='admin' && User('PROFILE')!='student' && User('PROFILE')!='parent')
        {
            echo '<div align=center>Upload Student\'s Photo: <div class="fileUpload btn_wide"><span>Upload</span><input id="uploadBtn" type="file" name="file" class="upload" onchange="selectFile(this.value)" /></div></div>';
        echo '<div id="uploadFile"></div>';
        }
        }
	else
        {
         echo '<div align="center"><IMG SRC="assets/noimage.jpg?id='.rand(6,100000).'" width=144 class=pic>';
	if(User('PROFILE')=='admin' && User('PROFILE')!='student' && User('PROFILE')!='parent')

	echo '<div align=center>Upload Student\'s Photo: <div class="fileUpload btn_wide"><span>Upload</span><input id="uploadBtn" type="file" name="file" class="upload" onchange="selectFile(this.value)" /></div></div>';
        echo '<div id="uploadFile"></div>';

        }
	
}
	
echo '</td></TR>';
echo '</td></tr></table>';
echo '</TD></TR>';


echo '<TR><td height="30px" colspan=2 class=hseparator><b>School Information</b></td></tr><tr><td colspan="2">';
echo '<TABLE border=0>';
echo '<tr><td>Student ID</td><td>:</td><td>';
if($_REQUEST['student_id']=='new')
{
echo NoInput('Will automatically be assigned','');
	
	echo '<span id="ajax_output_stid"></span>';
}
else
	echo NoInput(UserStudentID(),'');

// ----------------------------- Alternate id ---------------------------- //

echo '<tr><td>Alternate ID</td><td>:</td><td>';
echo TextInput($student['ALT_ID'],'students[ALT_ID]','','size=10 class=cell_medium maxlength=45');
echo '</td></tr>';

// ----------------------------- Alternate id ---------------------------- //

echo'</td></tr>';
echo '<tr><td>Grade<font color=red>*</font></td><td>:</td><td>';
if($_REQUEST['student_id']!='new' && $student['SCHOOL_ID'])
	$school_id = $student['SCHOOL_ID'];
else
	$school_id = UserSchool();
$sql = 'SELECT ID,TITLE FROM school_gradelevels WHERE SCHOOL_ID=\''.$school_id.'\' ORDER BY SORT_ORDER';
$QI = DBQuery($sql);
$grades_RET = DBGet($QI);
unset($options);
if(count($grades_RET))
{
	foreach($grades_RET as $value)
		$options[$value['ID']] = $value['TITLE'];
}
if($_REQUEST['student_id']!='new' && $student['SCHOOL_ID']!=UserSchool())
{
	$allow_edit = $_openSIS['allow_edit'];
	$AllowEdit = $_openSIS['AllowEdit'][$_REQUEST['modname']];
	$_openSIS['AllowEdit'][$_REQUEST['modname']] = $_openSIS['allow_edit'] = false;
}

if($_REQUEST['student_id']=='new')
	$student_id = 'new';
else
	$student_id = UserStudentID();

if($student_id=='new' && !VerifyDate($_REQUEST['day_values']['student_enrollment']['new']['START_DATE'].'-'.$_REQUEST['month_values']['student_enrollment']['new']['START_DATE'].'-'.$_REQUEST['year_values']['student_enrollment']['new']['START_DATE']))
	unset($student['GRADE_ID']);

echo SelectInput($student['GRADE_ID'],'values[student_enrollment]['.$student_id.'][GRADE_ID]',(!$student['GRADE_ID']?'<FONT color=red>':'').''.(!$student['GRADE_ID']?'</FONT>':''),$options,'','');
echo'</td></tr>';
echo'</table>';
echo '</td></TR>';
echo '<TR><td height="30px" colspan=2 class=hseparator><b>Access Information</b></td></tr><tr><td colspan="2">';
echo '<TABLE border=0>';
echo '<tr><td style=width:120px>Username</td><td>:</td><td>';
echo TextInput($student['USERNAME'],'students[USERNAME]','','class=cell_medium onkeyup="usercheck_init_student(this)"');
echo '<span id="ajax_output_st"></span>';
echo'</td></tr>';
echo '<tr><td>Password</td><td>:</td><td>';
echo TextInput(array($student['PASSWORD'],str_repeat('*',strlen($student['PASSWORD']))),'students[PASSWORD]','','class=cell_medium onkeyup=passwordStrength(this.value)','AUTOCOMPLETE = off');
echo '<div id="passwordStrength" style=display:none></div>';
echo '</td></tr>';

if($_REQUEST['student_id']!='new')
{
echo '<tr><td>Last Login</td><td>:</td><td>';
echo NoInput(ProperDate(substr($student['LAST_LOGIN'],0,10)).substr($student['LAST_LOGIN'],10),'');
echo '</td></tr>';
if(User('PROFILE')=='admin'){
echo '<tr><td>Disable Student</td><td>:</td><td>';
echo CheckboxInput($student['IS_DISABLE'],'students[IS_DISABLE]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>');
echo '</td></tr>';
}
}
echo'</table>';
echo '</td></TR>';
?>
