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
error_reporting(0);
session_start();
ini_set('max_execution_time','50000');
ini_set('max_input_time','50000');
include("CustomClassFnc.php");
$mysql_database=$_SESSION['db'];
$dbUser=$_SESSION['username'];
$dbPass=$_SESSION['password'];
$dbconn = mysql_connect($_SESSION['server'],$_SESSION['username'],$_SESSION['password']) or die() ;
mysql_select_db($mysql_database,$dbconn);

$q1=mysql_query("SELECT name,value FROM $mysql_database.app where name='version'");


    $q2=mysql_fetch_array(mysql_query("SELECT name,value FROM $mysql_database.app where name='version'"));

 $v=$q2['value'];
 if($v=='5.0')
 {
$opensis_tab=array('address','address_fields','address_field_categories','app','attendance_calendar','attendance_calendars','attendance_codes','attendance_code_categories','attendance_completed','attendance_day','attendance_period',
'calendar_events','calendar_events_visibility','config','courses','course_periods','course_subjects','custom_fields','eligibility','eligibility_activities','eligibility_completed','goal','gradebook_assignments','gradebook_assignment_types','gradebook_grades','grades_completed','hacking_log','history_marking_periods','honor_roll','login_message','login_records','log_maintain',
'lunch_period','marking_period_id_generator','missing_attendance','old_course_weights','people','people_fields','people_field_categories','people_join_contacts','portal_notes','profile_exceptions',
'program_config','program_user_config','progress','report_card_comments','report_card_grades','report_card_grade_scales','schedule','schedule_requests','schools','school_gradelevels','school_periods',
'school_progress_periods','school_quarters','school_semesters','school_years','staff','staff_exceptions','staff_fields','staff_field_categories','staff_school_relationship','students','students_join_address',
'students_join_people','students_join_users','student_contacts','student_eligibility_activities','student_enrollment','student_enrollment_codes','student_field_categories','student_gpa_calculated','student_gpa_running','student_medical','student_medical_alerts',
'student_medical_notes','student_medical_visits','student_mp_comments','student_mp_stats','student_report_card_comments','student_report_card_grades','system_preference','system_preference_misc','teacher_reassignment','user_profiles');
 }
 elseif($v=='5.1')
 {
$opensis_tab=array('address','address_fields','address_field_categories','app','attendance_calendar','attendance_calendars','attendance_codes','attendance_code_categories','attendance_completed','attendance_day','attendance_period',
'calendar_events','calendar_events_visibility','config','courses','course_periods','course_subjects','custom_fields','eligibility','eligibility_activities','eligibility_completed','goal','gradebook_assignments','gradebook_assignment_types','gradebook_grades','grades_completed','hacking_log','history_marking_periods','honor_roll','login_message','login_records','log_maintain',
'lunch_period','marking_period_id_generator','missing_attendance','old_course_weights','people','people_fields','people_field_categories','people_join_contacts','portal_notes','profile_exceptions',
'program_config','program_user_config','progress','report_card_comments','report_card_grades','report_card_grade_scales','schedule','schedule_requests','schools','school_gradelevels','school_periods',
'school_progress_periods','school_quarters','school_semesters','school_years','staff','staff_exceptions','staff_fields','staff_field_categories','staff_school_relationship','students','students_join_address',
'students_join_people','students_join_users','student_contacts','student_eligibility_activities','student_enrollment','student_enrollment_codes','student_field_categories','student_gpa_calculated','student_gpa_running','student_medical','student_medical_alerts',
'student_medical_notes','student_medical_visits','student_mp_comments','student_mp_stats','student_report_card_comments','student_report_card_grades','system_preference','system_preference_misc','teacher_reassignment','user_profiles');
 }
 elseif($v=='5.2')
 {
  $opensis_tab=array('address','address_fields','address_field_categories','app','attendance_calendar','attendance_calendars','attendance_codes','attendance_code_categories','attendance_completed','attendance_day','attendance_period',
'calendar_events','calendar_events_visibility','config','courses','course_periods','course_subjects','custom_fields','eligibility','eligibility_activities','eligibility_completed','goal','gradebook_assignments','gradebook_assignment_types','gradebook_grades','grades_completed','hacking_log','history_marking_periods','history_school','honor_roll','login_message','login_records','log_maintain',
'lunch_period','marking_period_id_generator','missing_attendance','old_course_weights','people','people_fields','people_field_categories','people_join_contacts','portal_notes','profile_exceptions',
'program_config','program_user_config','progress','report_card_comments','report_card_grades','report_card_grade_scales','schedule','schedule_requests','schools','school_gradelevels','school_periods',
'school_progress_periods','school_quarters','school_semesters','school_years','staff','staff_exceptions','staff_fields','staff_field_categories','staff_school_relationship','students','students_join_address',
'students_join_people','students_join_users','student_contacts','student_eligibility_activities','student_enrollment','student_enrollment_codes','student_field_categories','student_gpa_calculated','student_gpa_running','student_medical','student_medical_alerts',
'student_medical_notes','student_medical_visits','student_mp_comments','student_mp_stats','student_report_card_comments','student_report_card_grades','system_preference','system_preference_misc','teacher_reassignment','user_profiles');
 }
 elseif($v=='5.3')
 {
$opensis_tab=array('address','address_fields','address_field_categories','app','attendance_calendar','attendance_calendars','attendance_codes','attendance_code_categories','attendance_completed','attendance_day','attendance_period',
'calendar_events','calendar_events_visibility','config','courses','course_periods','course_subjects','custom_fields','eligibility','eligibility_activities','eligibility_completed','goal','gradebook_assignments','gradebook_assignment_types','gradebook_grades','grades_completed','hacking_log','history_marking_periods','history_school','honor_roll','login_message','login_records','log_maintain',
'lunch_period','marking_period_id_generator','missing_attendance','old_course_weights','people','people_fields','people_field_categories','people_join_contacts','portal_notes','profile_exceptions',
'program_config','program_user_config','progress','report_card_comments','report_card_grades','report_card_grade_scales','schedule','schedule_requests','schools','school_gradelevels','school_periods',
'school_progress_periods','school_quarters','school_semesters','school_years','staff','staff_exceptions','staff_fields','staff_field_categories','staff_school_relationship','students','students_join_address',
'students_join_people','students_join_users','student_contacts','student_eligibility_activities','student_enrollment','student_enrollment_codes','student_field_categories','student_gpa_calculated','student_gpa_running','student_medical','student_medical_alerts',
'student_medical_notes','student_medical_visits','student_mp_comments','student_mp_stats','student_report_card_comments','student_report_card_grades','system_preference','system_preference_misc','teacher_reassignment','user_profiles');
}
//----------------------------To select all extra table-------------------------------------



$qr_tab=mysql_query("show full tables where Table_Type != 'VIEW'") or die(mysql_error());
$extra_tab=array();
$fetch1=mysql_fetch_array($qr_tab);

while($fetch=mysql_fetch_array($qr_tab))
{
    

    if(!in_array($fetch[0],$opensis_tab))
    {
        
   $tab_name=$fetch[0];
        $extra_tab[]=$tab_name;
    }

}


if(count($extra_tab)>0)
{
    $_SESSION['extra_tab']=1;
    $tab_implode=implode(' ',$extra_tab);
 
$Export_FileName_ex="dump_extra_back.sql";


      exec("mysqldump --user $dbUser --password='$dbPass' $mysql_database $tab_implode > $Export_FileName_ex");
foreach($extra_tab as $vk => $vv)
      {
   
         mysql_query("drop table ".$vv) or die (mysql_error());  
      }
      
}


      
if($v!='5.3')
{
    
    $proceed=mysql_query("SELECT name,value
FROM app
WHERE value='4.6' OR value='4.7' OR value LIKE '4.8%' OR value='4.9' OR value='5.0' OR value='5.1' OR value='5.2' OR value='5.3'");
$proceed=mysql_fetch_assoc($proceed);
if(!$proceed)
{
    $proceed=mysql_query("SELECT name,value
    FROM app
    WHERE value='4.6' OR value='4.7' OR value LIKE '4.8%' OR value='4.9' OR value='5.0'");
    $proceed=mysql_fetch_assoc($proceed);
}
 $version=$proceed['value'];
$get_routines=mysql_query('SELECT routine_name,routine_type FROM information_schema.routines WHERE routine_schema=\''.$mysql_database.'\' ');
while($get_routines_arr=mysql_fetch_assoc($get_routines))
{
  
    mysql_query('DROP '.$get_routines_arr['routine_type'].' IF EXISTS '.$get_routines_arr['routine_name']);
}

$get_trigger=mysql_query('SELECT trigger_name FROM information_schema.triggers WHERE trigger_schema=\''.$mysql_database.'\' ');
while($get_trigger_arr=mysql_fetch_assoc($get_trigger))
{
  
    mysql_query('DROP TRIGGER IF EXISTS '.$get_trigger_arr['trigger_name']);
}

mysql_query('UPDATE '.table_to_upper('students',$version).' SET failed_login=0 WHERE failed_login is null');
if($version!='5.2' && $version!='5.3')
{
mysql_query('Create table staff_new as SELECT * FROM '.table_to_upper('staff',$version).'');
mysql_query('TRUNCATE TABLE staff_new');
mysql_query('ALTER TABLE `staff_new` DROP `syear`, DROP `schools`, DROP `rollover_id`');

mysql_query('DROP TABLE '.table_to_upper('staff_school_relationship',$version).'');
   mysql_query('CREATE TABLE '.table_to_upper('staff_school_relationship',$version).' (
 `staff_id` int(11) NOT NULL,
 `school_id` int(11) NOT NULL,
 `syear` int(4) NOT NULL,
 `start_date` date NOT NULL,
 `end_date` date NOT NULL,
 PRIMARY KEY (`staff_id`,`school_id`,`syear`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8');

$sql=  mysql_query('SELECT * FROM '.table_to_upper('staff',$version).' order by staff_id asc');
while($row= mysql_fetch_array($sql))
{
    if($row['username']!='')
        $staff_sql=mysql_query("SELECT staff_id FROM staff_new WHERE username='".$row['username']."' AND username IS NOT NULL");
    else 
        $staff_sql=mysql_query("SELECT staff_id FROM staff_new WHERE first_name='".$row['first_name']."' AND last_name='".$row['last_name']."' AND profile='".$row['profile']."'");
    if(mysql_num_rows($staff_sql)==0)
    {
        $staff_id=$row['staff_id'];
        mysql_query("insert into staff_new (staff_id,current_school_id,title,first_name,last_name,middle_name,username,password,phone,email,profile,homeroom,last_login,failed_login,profile_id,is_disable) values('".$row['staff_id']."','".$row['current_school_id']."'
            ,'".$row['title']."','".$row['first_name']."','".$row['last_name']."','".$row['middle_name']."','".$row['username']."','".$row['password']."'
                ,'".$row['phone']."','".$row['email']."','".$row['profile']."','".$row['homeroom']."','".$row['last_login']."','".$row['failed_login']."','".$row['profile_id']."','".$row['is_disable']."')");
    if($row['username']!='')
        $st_info_sql=mysql_query("SELECT syear,staff_id,schools FROM ".table_to_upper('staff',$version)." WHERE username='".$row['username']."' AND username IS NOT NULL");
    else
        $st_info_sql=mysql_query("SELECT syear,staff_id,schools FROM ".table_to_upper('staff',$version)." WHERE first_name='".$row['first_name']."' AND last_name='".$row['last_name']."' AND profile='".$row['profile']."' AND username IS NULL");

    while ($row1 = mysql_fetch_array($st_info_sql))
        {


            $school=  substr(substr($row1['schools'],0, -1),1);
            $all_school=  explode(',',$school);
            foreach($all_school as $key=>$value)
            {

                mysql_query('insert into '.table_to_upper('staff_school_relationship',$version).' values(\''.$staff_id.'\',\''.$value.'\',\''.$row1['syear'].'\',\'0000-00-00\',\'0000-00-00\')')or die(mysql_error());
            }
            
            
              
            mysql_query("update attendance_completed set staff_id='".$row['staff_id']."' WHERE staff_id='".$row1['staff_id']."'");
            mysql_query("update  course_periods set teacher_id='".$row['staff_id']."' WHERE teacher_id='".$row1['staff_id']."'");
            mysql_query("update  course_periods set secondary_teacher_id='".$row['staff_id']."' WHERE secondary_teacher_id='".$row1['staff_id']."'");
            mysql_query("update  login_records set staff_id='".$row['staff_id']."' WHERE staff_id='".$row1['staff_id']."'");
            mysql_query("update missing_attendance set teacher_id='".$row['staff_id']."' WHERE teacher_id='".$row1['staff_id']."'");
            mysql_query("update portal_notes set published_user='".$row['staff_id']."'WHERE published_user='".$row1['staff_id']."'");
            mysql_query("update program_user_config set user_id='".$row['staff_id']."'WHERE user_id='".$row1['staff_id']."'");
            mysql_query("update schedule_requests set with_teacher_id='".$row['staff_id']."'WHERE with_teacher_id='".$row1['staff_id']."'");
            
            mysql_query("update teacher_reassignment set teacher_id='".$row['staff_id']."'WHERE teacher_id='".$row1['staff_id']."'");
            mysql_query("update teacher_reassignment set pre_teacher_id='".$row['staff_id']."'WHERE pre_teacher_id='".$row1['staff_id']."'");
            mysql_query("update teacher_reassignment set modified_by='".$row['staff_id']."'WHERE modified_by='".$row1['staff_id']."'");
            mysql_query("update gradebook_assignments set staff_id='".$row['staff_id']."' WHERE staff_id='".$row1['staff_id']."'");
            mysql_query("update gradebook_assignment_types set staff_id='".$row['staff_id']."' WHERE staff_id='".$row1['staff_id']."'");
            mysql_query("update grades_completed set staff_id='".$row['staff_id']."' WHERE staff_id='".$row1['staff_id']."'");
            mysql_query("update student_mp_comments set staff_id='".$row['staff_id']."' WHERE staff_id='".$row1['staff_id']."'");
            mysql_query("update schedule set modified_by='".$row['staff_id']."' WHERE modified_by='".$row1['staff_id']."'");
        
    }
    }
        
    
}

mysql_query('DROP TABLE '.table_to_upper('staff',$version).'');
mysql_query('RENAME TABLE `staff_new` TO '.table_to_upper('staff',$version).'');
}
if($proceed['name']){
       
                    $dummyFile = "dummy.txt";
                    $fpt = fopen($dummyFile, 'w');

                    if ($fpt == FALSE)
                    {
                        die(show_error1().' Show Error 1');
                    }
                    else
                    {
                        unlink($dummyFile);
                    }
                    fclose($fpt);
    
	$date_time=date("m-d-Y");
         $mysql_database;
    $Export_FileName=$mysql_database.'_'.$date_time.'.sql';
	$myFile = "UpgradeInc.sql";
    executeSQL($myFile);
	
	        exec("mysqldump -n -t -c --skip-add-locks --skip-disable-keys --skip-triggers --user $dbUser --password='$dbPass' $mysql_database > $Export_FileName");

                    $res_student_field='SHOW COLUMNS FROM '.table_to_upper('students',$version).' WHERE FIELD LIKE "CUSTOM_%"';
//
	$objCustomStudents=new custom($mysql_database);
	$objCustomStudents->set($res_student_field,'students');
	
	$res_staff_field='SHOW COLUMNS FROM '.table_to_upper('staff',$version).' WHERE FIELD LIKE "CUSTOM_%"';
	$objCustomStaff=new custom($mysql_database);
	$objCustomStaff->set($res_staff_field,'staff');
	
	mysql_query("drop database $mysql_database");
     
	mysql_query("CREATE DATABASE $mysql_database CHARACTER SET utf8 COLLATE utf8_general_ci");
       
	mysql_select_db($mysql_database);
        
	
         $myFile = "OpensisUpdateSchemaMysql.sql";
        
    executeSQL($myFile);
	
	//execute custome field for student
	foreach($objCustomStudents->customQueryString as $query){
	mysql_query($query);
	}
	//execute custome field for satff
	foreach($objCustomStaff->customQueryString as $query){
	mysql_query($query);
	}
	

                    $myFile = "OpensisUpdateProcsMysql.sql";
                    executeSQL($myFile);

                    //=====================For version prior than 4.9 only====================================
                    if($version!='5.0' || $version!='5.1' || $version!='5.2' || $version!='5.3')
                    {
                        $Export_FileName=to_upper_tables_to_import($Export_FileName);
                    }
                    
                    //=========================================================

                    exec("mysql --user $dbUser --password='$dbPass' $mysql_database < $Export_FileName",$result,$status);
                  
                    if($status!=0)
                    {
                        die(show_error1('db').' Show Error 2');
                    }
                    if($version!='5.0')
                    {
                        unlink($Export_FileName);
                    }
                    $myFile = "OpensisUpdateTriggerMysql.sql";
                    executeSQL($myFile);
                    
	mysql_query("delete from app");
	$appTable="INSERT INTO `app` (`name`, `value`) VALUES
('version', '5.3'),
('date', 'December 01, 2013'),
('build', '01122013001'),
('update', '0'),
('last_updated', 'December 01, 2013')";
	mysql_query($appTable);
	$custom_insert=mysql_query("select count(*) from custom_fields where title in('Ethnicity','Common Name','Physician','Physician Phone','Preferred Hospital','Gender','Email','Phone','Language')");
	$custom_insert=mysql_fetch_array($custom_insert);
	$custom_insert=$custom_insert[0];
	if($custom_insert<9){
	$custom_insert="INSERT INTO `custom_fields` (`type`, `search`, `title`, `sort_order`, `select_options`, `category_id`, `system_field`, `required`, `default_selection`, `hide`) VALUES
('text', NULL, 'Ethnicity', 3, NULL, 1, 'Y', NULL, NULL, NULL),
('text', NULL, 'Common Name', 2, NULL, 1, 'Y', NULL, NULL, NULL),
('text', NULL, 'Physician', 6, NULL, 2, 'Y', NULL, NULL, NULL),
('text', NULL, 'Physician Phone', 7, NULL, 2, 'Y', NULL, NULL, NULL),
('text', NULL, 'Preferred Hospital', 8, NULL, 2, 'Y', NULL, NULL, NULL),
('text', NULL, 'Gender', 5, NULL, 1, 'Y', NULL, NULL, NULL),
('text', NULL, 'Email', 6, NULL, 1, 'Y', NULL, NULL, NULL),
('text', NULL, 'Phone', 9, NULL, 1, 'Y', NULL, NULL, NULL),
('text', NULL, 'Language', 8, NULL, 1, 'Y', NULL, NULL, NULL);";
mysql_query($custom_insert);
	}
	$login_msg=mysql_query("SELECT COUNT(*) FROM login_message WHERE 1");
	$login_msg=mysql_fetch_array($login_msg);
	$login_msg=$login_msg[0];
	if($login_msg<1){
	$login_msg="INSERT INTO `login_message` (`id`, `message`, `display`) VALUES
(1, 'This is a restricted network. Use of this network, its equipment, and resources is monitored at all times and requires explicit permission from the network administrator. If you do not have this permission in writing, you are violating the regulations of this network and can and will be prosecuted to the fullest extent of law. By continuing into this system, you are acknowledging that you are aware of and agree to these terms.', 'Y')";
mysql_query($login_msg);
	}
	
	$syear=mysql_fetch_assoc(mysql_query("select MAX(syear) as year, MIN(start_date) as start from school_years"));
	$_SESSION['syear']=$syear['year'];
                  $max_syear=$syear['year'];
                  $start_date=$syear['start'];
//=============================4.8.1 To 4.9===================================
if($version!='5.0' && $version!='4.9' && $version!='5.1' && $version!='5.2' && $version!='5.3')
{
        $up_sql="INSERT INTO student_enrollment_codes(syear,title,short_name,type)VALUES
        (".$max_syear.",'Transferred out','TRAN','TrnD'),
        (".$max_syear.",'Transferred in','TRAN','TrnE'),
        (".$max_syear.",'Rolled over','ROLL','Roll'); ";
        mysql_query($up_sql) or die(show_error1().' Show Error 3');

        $up_sql ="INSERT INTO profile_exceptions (profile_id, modname, can_use, can_edit) VALUES
            (3, 'scheduling/PrintSchedules.php','Y',NULL),
            (1, 'scheduling/ViewSchedule.php', 'Y', NULL),
            (2, 'scheduling/ViewSchedule.php', 'Y', NULL),
            (1, 'schoolsetup/UploadLogo.php', 'Y', 'Y'); ";
        mysql_query($up_sql) or die(show_error1().' Show Error 4');

        $up_sql ="INSERT INTO program_config (program, title, value) VALUES
            ('MissingAttendance', 'LAST_UPDATE','".$start_date."'); ";
        mysql_query($up_sql) or die(show_error1().' Show Error 5');

        $up_sql ="UPDATE profile_exceptions SET modname='scheduling/ViewSchedule.php' WHERE modname='scheduling/Schedule.php' AND (profile_id=0 OR profile_id=3);";
        mysql_query($up_sql) or die(show_error1().' Show Error 6');
}
//====================================================================
	mysql_query("UPDATE schedule SET dropped='Y' WHERE end_date IS NOT NULL AND end_date < CURDATE() AND dropped='N'");
	header('Location: Upgrade6.php');
	unset($objCustomStudents);
	unset($objCustomStaff);
}else{
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../styles/Installer.css" />
</head>
<body>
<div class="heading2">Warning
<div style="height:270px;">
<br /><br />
 <table border="0" cellspacing="6" cellpadding="3" align="center">
            <tr>
			 <td colspan="2" align="center">
		<p>	The database you have chosen is not compliant with openSIS-CE version 4.7 or 4.8X or 4.9 or 5.0 or 5.1 or 5.2 or 5.3 or 6.0 We are unable to proceed.</p>

<p>Click Retry to select another database, or Exit to quit the installation.
</p>
		</td>
			</tr>
            <tr>
            	<td colspan="2" style="height:100px;">&nbsp;</td>
            </tr>
			<tr>


  <td align="left"><a href="Selectdb.php"><img src="images/retry.png"  alt="Retry"  border="0"/></a></td>
    <td align="right"><a href="Step0.php" ><img src="images/exit.png" alt="Exit" border="0" /></a></td>


	          </tr>
	        </table>
</div>
</div>
</body>
</html>
<?php
}
}


    else
{
    header('Location: Upgrade6.php');
}
function executeSQL($myFile)
{	
	$sql = file_get_contents($myFile);
	$sqllines = explode("\n",$sql);
	$cmd = '';
	$delim = false;
	foreach($sqllines as $l)
	{
		if(preg_match('/^\s*--/',$l) == 0)
		{
			if(preg_match('/DELIMITER \$\$/',$l) != 0)
			{	
				$delim = true;
			}
			else
			{
				if(preg_match('/DELIMITER ;/',$l) != 0)
				{
					$delim = false;
				}
				else
				{
					if(preg_match('/END\$\$/',$l) != 0)
					{
						$cmd .= ' END';
					}
					else
					{
						$cmd .= ' ' . $l . "\n";
					}
				}
				if(preg_match('/.+;/',$l) != 0 && !$delim)
				{
					$result = mysql_query($cmd) or die(show_error1().' Show Error 7');
					$cmd = '';
				}
			}
		}
	}
}

function show_error1($msg='')
{
    if($msg=='')
        $msg='Application does not have permission to write into install directory.';
    elseif($msg=='db')
        $msg='Your database is not compatible with openSIS-CE<br />Please take this screen shot and send it to your openSIS representative for resolution.';
    $err .= "
<html>
<head>
<link rel='stylesheet' type='text/css' href='../styles/Installer.css' />
</head>
<body>

<div style='height:280px;'>

<br /><br /><span class='header_txt'></span>

<div align='center'>
$msg
</div>
<div style='height:50px;'>&nbsp;</div>";
$err.="<div align='center'><a href='Selectdb.php?mod=upgrade'><img src='images/retry.png' border='0' /></a> &nbsp; &nbsp; <a href='Step0.php'><img src='images/exit.png' border='0' /></a></div>";
$err.="</div></body></html>";
echo $err;
}

function table_to_upper($table,$ver)
{
    if($ver=='4.6' || $ver=='4.7' || $ver=='4.8' || $ver=='4.8.1' || $ver=='4.9')
        $return=  strtoupper ($table);
    else
        $return=$table;
    return $return;
}

function to_upper_tables_to_import($input_file)
{
    $output_file='temp_opensis5.0.sql';
    $handle = @fopen($input_file, "r"); // Open file form read.
    $str='';
    if ($handle) {
        while (!feof($handle)) // Loop til end of file.
        {
            $buffer = fgets($handle, 4096); // Read a line.
            if(substr($buffer, 0,11)=='INSERT INTO')
            {
                $arr_line=explode(' ', $buffer);
                $arr_line[2]= strtolower($arr_line[2]);
                $str_line=implode(' ', $arr_line);
                $str .= $str_line;
            }
            else
            {
                $str .=$buffer;
            }
        }
        fclose($handle); // Close the file.
       
        $f = fopen($output_file, "w"); 
    fwrite($f, $str); 
    }
    return $output_file;

}

?>
