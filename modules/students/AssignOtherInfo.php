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
include('../../RedirectModulesInc.php');
if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='save')
{

	$date = $_REQUEST['day'].'-'.$_REQUEST['month'].'-'.$_REQUEST['year'];
	if(count($_REQUEST['month_values']))
	{
		foreach($_REQUEST['month_values'] as $field_name=>$month)
		{
			$_REQUEST['values'][$field_name] = $_REQUEST['day_values'][$field_name].'-'.$month.'-'.$_REQUEST['year_values'][$field_name];
			if(!VerifyDate($_REQUEST['values'][$field_name]))
			{
				if($_REQUEST['values'][$field_name]!='--')
					$note = '<IMG SRC=assets/warning_button.gif>The date you specified is not valid, so was not used.  The other data was saved.';
				unset($_REQUEST['values'][$field_name]);
			}
		}
	}

	if(count($_REQUEST['values']) && count($_REQUEST['student']))
	{
		if($_REQUEST['values']['NEXT_SCHOOL']!='')
		{
                                            $next_school = $_REQUEST['values']['NEXT_SCHOOL'];
		        unset($_REQUEST['values']['NEXT_SCHOOL']);
		}
		if($_REQUEST['values']['CALENDAR_ID'])
		{
                        $calendar = clean_param($_REQUEST['values']['CALENDAR_ID'],PARAM_INT);
			unset($_REQUEST['values']['CALENDAR_ID']);
		}
                foreach($_REQUEST['student'] as $student_id=>$yes)
		{
			if($yes=='Y')
			{
				$students .= ",'$student_id'";
				$students_count++;
			}
		}
                
		foreach($_REQUEST['values'] as $field=>$value)
		{
                
                    $arr=  explode('[', $field);
                  
                    if($arr[0]!='medical_info')
                    {
			if(isset($value) && trim($value)!='')
			{       
                                $value= paramlib_validation($field,$value);
				$update .= ','.$field."='$value'";
				$values_count++;
			}
                    }
                    else 
                    {
                        $value= paramlib_validation($field,$value);
		
                        $fields.=','.$arr[1];
                        $values.=','.$value;
			$values_count++;
                    }
		}  
                $fields=  explode(',', $fields) ;
                $values=explode(',',$values);
		$medical_student_id=  explode(',', $students);
                $check_student_avail=  DBGet(DBQuery('SELECT student_id from medical_info where SYEAR='.UserSyear() .' AND SCHOOL_ID='.UserSchool()));
                
                foreach($check_student_avail as $stu_key=>$stu_id)
                {
                    foreach($stu_id as $stu_id_k=>$stu_id_v)
                        $med_stu_id[]=$stu_id_v;
                }
                foreach($_REQUEST['student'] as $student_id=>$yes)
		{
			if($yes=='Y')
			{
				$students_m_id[]= $student_id;
				
			}
		}
                
                if($values_count && $students_count)
                    if(trim($update)!='')
			DBQuery('UPDATE students SET '.substr($update,1).' WHERE STUDENT_ID IN ('.substr($students,1).')');
                    else {
                        foreach($students_m_id as $stu_k=>$stu_id)
                       {
                          
                       if(in_array($stu_id,$med_stu_id))
                       {    
                          if($values[1].trim()!='')
                                 DBQuery('UPDATE medical_info SET '.$fields[1].'=\''.$values[1].'\' WHERE STUDENT_ID ='.$stu_id );
                        if($values[2].trim()!='')
                                 DBQuery('UPDATE medical_info SET '.$fields[2].'=\''.$values[2].'\' WHERE STUDENT_ID  ='.$stu_id);
                        if($values[3].trim()!='')
                                 DBQuery('UPDATE medical_info SET '.$fields[3].'=\''.$values[3].'\' WHERE STUDENT_ID ='.$stu_id);     
                               
                       }  
                       else {
                        DBQuery('INSERT INTO medical_info (STUDENT_ID,SYEAR,SCHOOL_ID) VALUES ('.$stu_id.','.UserSyear().','.UserSchool().')');
                       
                        if($values[1].trim()!='')
                                 DBQuery('UPDATE medical_info SET '.$fields[1].'=\''.$values[1].'\' WHERE STUDENT_ID ='.$stu_id);
                        if($values[2].trim()!='')
                                 DBQuery('UPDATE medical_info SET '.$fields[2].'=\''.$values[2].'\' WHERE STUDENT_ID  ='.$stu_id);
                        if($values[3].trim()!='')
                                 DBQuery('UPDATE medical_info SET '.$fields[3].'=\''.$values[3].'\' WHERE STUDENT_ID ='.$stu_id);     
                        
                        
                       }
                       
                       }
                   
                    }
		elseif($note)
			$note = substr($note,0,strpos($note,'. '));
		elseif($next_school=='' && !$calendar)
			$note = '<IMG SRC=assets/warning_button.gif>No data was entered.';

		if($next_school!='')
			DBQuery('UPDATE student_enrollment SET NEXT_SCHOOL='.$next_school.' WHERE SYEAR='.UserSyear().' AND STUDENT_ID IN ('.substr($students,1).') ');
		if($calendar)
			DBQuery('UPDATE student_enrollment SET CALENDAR_ID='.$calendar.' WHERE SYEAR='.UserSyear().' AND STUDENT_ID IN ('.substr($students,1).') ');

		if(!$note)
			$note = '<IMG SRC=assets/check.gif>The specified information was applied to the selected students.';
		unset($_REQUEST['modfunc']);
		unset($_REQUEST['values']);
		unset($_SESSION['_REQUEST_vars']['modfunc']);
		unset($_SESSION['_REQUEST_vars']['values']);
	}
	else
                {
                    ShowErr('You must choose at least one field and one student');
                    for_error();
}
}

DrawBC("Students > ".ProgramTitle());

if(!$_REQUEST['modfunc'])
{
	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ',CAST(NULL AS CHAR(1)) AS CHECKBOX';

	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=save METHOD=POST>";

		if($_REQUEST['category_id'])
		{
			$fields_RET = DBGet(DBQuery('SELECT ID,TITLE,TYPE,SELECT_OPTIONS FROM custom_fields WHERE CATEGORY_ID=\''.$_REQUEST[category_id].'\''),array(),array('TYPE'));
		}	
		else
		{
			$fields_RET = DBGet(DBQuery('SELECT ID,TITLE,TYPE,SELECT_OPTIONS FROM custom_fields'),array(),array('TYPE'));
		
                      
		}
		$categories_RET = DBGet(DBQuery('SELECT ID,TITLE FROM student_field_categories WHERE ID=1 OR ID=2'));
		$tmp_REQUEST = $_REQUEST;
		unset($tmp_REQUEST['category_id']);
		echo '<CENTER><TABLE align=center ><TR><TD align=center>';
		echo '<CENTER><SELECT name=category_id onchange="document.location.href=\''.PreparePHP_SELF($tmp_REQUEST).'&amp;category_id=\'+this.form.category_id.value;"><OPTION value="">All Categories</OPTION>';
		foreach($categories_RET as $category)
			echo '<OPTION value='.$category['ID'].($_REQUEST['category_id']==$category['ID']?' SELECTED':'').'>'.$category['TITLE'].'</OPTION>';
		echo '</SELECT><div class=clear ></div>';
		PopTable_wo_header ('header','');

		echo '<TABLE align=center>';
                
                
               
                
                
                if($_REQUEST[category_id]=='')
                {
                             $arr_g=array('Ethnicity','Common Name','Gender','Language','Email','Phone');
                           
                             foreach($arr_g as $v_g){
                             echo '<TR><TD class=lable>'.$v_g.'</TD><TD>'._makeTextInput($v_g).'</TD></TR>';
                       
                             }
                    
                             $arr_m=array('Physician','Physician\'s Phone','Preferred Hospital');
                            
                             foreach($arr_m as $v_m){
                                 
                                 if(trim($v_m)=='Physician')
                                 { $v_m_n='medical_info[PHYSICIAN]';
                                    echo '<TR><TD class=lable>'.$v_m.'</TD><TD>'._makeTextInput($v_m_n).'</TD></TR>';
                                    
                                 }if(trim($v_m)=='Physician\'s Phone')
                                 {
                                     $v_m_n='medical_info[PHYSICIAN_PHONE]';
                                      echo '<TR><TD class=lable>'.$v_m.'</TD><TD>'._makeTextInput($v_m_n).'</TD></TR>';
                                 }
                                    else if(trim($v_m)=='Preferred Hospital')
                                    {
                                        $v_m_n='medical_info[PREFERRED_HOSPITAL]';
                                 
                             echo '<TR><TD class=lable>'.$v_m.'</TD><TD>'._makeTextInput($v_m_n).'</TD></TR>'; 
                                    }
                                       
                       
                             }
                }
                if($_REQUEST[category_id]==1)
                {
                             $arr=array('Ethnicity','Common Name','Gender','Language','Email','Phone');

                             foreach($arr as $v){
                             echo '<TR><TD class=lable>'.$v.'</TD><TD>'._makeTextInput($v).'</TD></TR>';
                             }
                }
                if($_REQUEST[category_id]==2)
                {
                             $arr=array(' Physician','Physician\'s Phone','Preferred Hospital');
                         
                             foreach($arr as $v_m){
                                 
                                
                                 
                            if(trim($v_m)=='Physician')
                                 { $v_m_n='medical_info[PHYSICIAN]';
                                    echo '<TR><TD class=lable>'.$v_m.'</TD><TD>'._makeTextInput($v_m_n).'</TD></TR>';
                                    
                                 }if(trim($v_m)=='Physician\'s Phone')
                                 {
                                     $v_m_n='medical_info[PHYSICIAN_PHONE]';
                                      echo '<TR><TD class=lable>'.$v_m.'</TD><TD>'._makeTextInput($v_m_n).'</TD></TR>';
                                 }
                                    else if(trim($v_m)=='Preferred Hospital')
                                    {
                                        $v_m_n='medical_info[PREFERRED_HOSPITAL]';
                                 
                             echo '<TR><TD class=lable>'.$v_m.'</TD><TD>'._makeTextInput($v_m_n).'</TD></TR>'; 
                                    }
                             }
                }
                
		if(count($fields_RET['text']))
		{
			foreach($fields_RET['text'] as $field)
			 {
			 	$title=strtolower(trim($field['TITLE']));
			 	if(strpos(trim($field['TITLE']),' ')!=0)
				{
				 $p1=substr(trim($field['TITLE']),0,strpos(trim($field['TITLE']),' '));
				 $p2=substr(trim($field['TITLE']),strpos(trim($field['TITLE']),' ')+1);
				$title=strtolower($p1.'_'.$p2);
				}
				
                                $query=DBGet(DBQuery('SELECT * FROM students LIMIT 0,1'));
                                $query=$query[1];
				$f=0;
                                foreach($query as $k => $v)
                                {
                                    if(strtolower($k)==strtolower($title))
                                        $f=1;
                                }
				if($f==0)
				 {
                                    if(trim($title)=='physician')
                                        $title='medical_info[PHYSICIAN]';
                                    if(trim($title)=='physician_phone')
                                        $title='medical_info[PHYSICIAN_PHONE]';
                                    else if(trim($title)=='preferred_hospital')
                                        $title='medical_info[PREFERRED_HOSPITAL]';

				 }
				echo '<TR><TD class=lable>'.$field['TITLE'].'</TD><TD>'._makeTextInput($title).'</TD></TR>';
			 }	
		}
		if(count($fields_RET['numeric']))
		{
			foreach($fields_RET['numeric'] as $field)
				echo '<TR><TD class=lable>'.$field['TITLE'].'</TD><TD>'._makeTextInput('CUSTOM_'.$field['ID'],true).'</TD></TR>';
		}
		if(count($fields_RET['date']))
		{
			foreach($fields_RET['date'] as $field)
				echo '<TR><TD class=lable>'.$field['TITLE'].'</TD><TD>'._makeDateInput('CUSTOM_'.$field['ID']).'</TD></TR>';
		}
		if(count($fields_RET['select']))
		{
			foreach($fields_RET['select'] as $field)
			{
                            if($field[TITLE]=='Ethnicity' ||$field[TITLE]=='Gender' ||$field[TITLE]=='Language')
                            {
                                $select_options = array();
				$field['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$field['SELECT_OPTIONS']));
				$options = explode("\r",$field['SELECT_OPTIONS']);
				if(count($options))
				{
					foreach($options as $option)
						$select_options[$option] = $option;
				}

				echo "<TR><TD class=lable_right valign=top>$field[TITLE]</TD><TD>"._makeSelectInput($field[TITLE],$select_options).'</TD></TR>';
				echo "</TD></TR>";
                            }
                            else 
                            {
				$select_options = array();
				$field['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$field['SELECT_OPTIONS']));
				$options = explode("\r",$field['SELECT_OPTIONS']);
				if(count($options))
				{
					foreach($options as $option)
						$select_options[$option] = $option;
				}

				echo "<TR><TD class=lable_right valign=top>$field[TITLE]</TD><TD>"._makeSelectInput('CUSTOM_'.$field['ID'],$select_options).'</TD></TR>';
				echo "</TD></TR>";
                            }
			}
		}
		if(count($fields_RET['textarea']))
		{
			foreach($fields_RET['textarea'] as $field)
			{
				echo '<TR><TD class=lable_right valign=top>'.$field['TITLE'].'</TD>';
				echo '<TD>';
				echo _makeTextareaInput('CUSTOM_'.$field['ID']);
				echo '</TD>';
				echo '</TR>';
			}
		}
		if(!$_REQUEST['category_id'] || $_REQUEST['category_id']=='1')
		{
			echo '<TR><TD class=lable valign=top>Rolling Retention / Options</TD>';
			echo '<TD>';
			$schools_RET = DBGet(DBQuery('SELECT ID,TITLE FROM schools WHERE ID!=\''.UserSchool().'\''));
			$options = array(UserSchool()=>'Next grade at current school','0'=>'Retain','-1'=>'Do not enroll after this school year');
			if(count($schools_RET))
			{
				foreach($schools_RET as $school)
					$options[$school['ID']] = $school['TITLE'];
			}
			echo _makeSelectInput('NEXT_SCHOOL',$options);
			echo '</TD>';
			echo '</TR><TR>';

			echo '<TD class=lable_right valign=top>Calendar</TD>';
			echo '<TD>';
			$calendars_RET = DBGet(DBQuery('SELECT CALENDAR_ID,DEFAULT_CALENDAR,TITLE FROM school_calendars WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\' ORDER BY DEFAULT_CALENDAR ASC'));
			$options = array();
			if(count($calendars_RET))
			{
				foreach($calendars_RET as $calendar)
					$options[$calendar['CALENDAR_ID']] = $calendar['TITLE'];
			}
			echo _makeSelectInput('CALENDAR_ID',$options);
			echo '</TD>';
			echo '</TR>';
		}
		echo '</TABLE>';
		echo '<BR>';

		$radio_count = count($fields_RET['radio']);
		if($radio_count)
		{
			echo '<TABLE cellpadding=5>';
			echo '<TR>';
			for($i=1;$i<=$radio_count;$i++)
			{
				echo '<TD>'._makeCheckboxInput('CUSTOM_'.$fields_RET['radio'][$i]['ID'],'<b>'.$fields_RET['radio'][$i]['TITLE'].'</b>').'</TD>';
				if($i%5==0 && $i!=$radio_count)
					echo '</TR><TR>';
			}
			echo '</TD></TR>';
			echo '</TABLE>';
		}
		PopTable('footer');
		echo '</TD></TR>';
		echo '</TABLE><BR>';
	}
	elseif($note)
		DrawHeader($note);

	Widgets('activity');
	Widgets('course');
	Widgets('absences');

	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'student\');"><A>');
	$extra['new'] = true;

	Search('student_id',$extra);
        if($_REQUEST['search_modfunc']=='list' && $_SESSION['count_stu']!='0')
        {
             unset ($_SESSION['count_stu']);
             echo "<BR><CENTER>".SubmitButton('Save','','class=btn_medium')."</CENTER>";
         }
         echo '</FORM>';
}

function _makeChooseCheckbox($value,$title='')
{	global $THIS_RET;

	return "<INPUT type=checkbox name=student[".$THIS_RET['STUDENT_ID']."] value=Y>";
}

function _makeTextInput($column,$numeric=false)
{
	if($numeric===true)
		$options = 'size=3 maxlength=11 class=cell_floating';
	else
		$options = 'size=25 class=cell_floating';
if($column=='physician'|| $column=='physician_phone'|| $column=='preferred_hospital')
    return TextInput('',''.$column.'','',$options);
else
    return TextInput('','values['.$column.']','',$options);
}

function _makeTextareaInput($column,$numeric=false)
{
	return TextAreaInput('','values['.$column.']');
}

function _makeDateInput($column)
{
	return DateInput('','values['.$column.']','');
}

function _makeSelectInput($column,$options)
{
	return SelectInput('','values['.$column.']','',$options,'N/A',"style='max-width:250;'");
}

function _makeCheckboxInput($column,$name)
{
	return CheckboxInput('','values['.$column.']',$name,'',true);
}
?>