<?php

error_reporting(0);
session_start();
ini_set('max_execution_time','50000');
ini_set('max_input_time','50000');
include("CustomClassFnc.php");
$mysql_database=$_SESSION['db'];
$dbUser=$_SESSION['username'];
$dbPass=$_SESSION['password'];
$dbconn = mysql_connect($_SESSION['server'],$_SESSION['username'],$_SESSION['password']) or die() ;
mysql_select_db($mysql_database);

	$date_time=date("m-d-Y");
         
$Export_FileName=$mysql_database.'Backup'.$date_time.'.sql';
	
	
	        exec("mysqldump -n -t -c --skip-add-locks --skip-disable-keys --skip-triggers --user $dbUser --password='$dbPass' $mysql_database > $Export_FileName");
                
                $school_caledar="CREATE TABLE school_calendars (
    school_id numeric,
    title character varying(100),
    syear numeric(4,0),
    calendar_id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    default_calendar character varying(1),
    days VARCHAR( 7 ),
    rollover_id numeric,
 `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_by` varchar(255) DEFAULT NULL
)ENGINE=MyISAM";
mysql_query($school_caledar);
$school_caledar_alter="ALTER TABLE school_calendars AUTO_INCREMENT=1";
mysql_query($school_caledar_alter);
$qr=mysql_query("select * from attendance_calendars");
while($res=mysql_fetch_array($qr))
{
    $da_ar=array();
    $cal_id =$res['calendar_id'];
    $cal_title =$res['title '];

    $qs=mysql_query("select school_date from attendance_calendar where calendar_id='$cal_id' limit 0,365 ");
    while($res1=mysql_fetch_array($qs))
    {
        $day=date('l',strtotime($res1['school_date']));
        if(strtolower($day)==strtolower('Thursday'))
            array_push($da_ar,'H');
        else {
        array_push($da_ar,substr($day,0,1));    
        }
        
    }
    $k=array_unique($da_ar);
     $calendar_day=implode('',$k);
    
    mysql_query("insert into school_calendars(syear,school_id,title,days,default_calendar,calendar_id) values('$res[syear]','$res[school_id]','$res[title]','$calendar_day','$res[default_calendar]','$cal_id')");
    
}

$qr_room="CREATE TABLE IF NOT EXISTS rooms (
 room_id int(11) NOT NULL AUTO_INCREMENT,
 school_id int(11) NOT NULL,
 title varchar(50) NOT NULL,
 capacity int(11) DEFAULT NULL,
 description text,
 sort_order int(11) DEFAULT NULL,
 `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_by` varchar(255) DEFAULT NULL,
 PRIMARY KEY (room_id)
) ENGINE=MyISAM";
mysql_query($qr_room);
mysql_query("CREATE TABLE course_periods_new (
    syear int(4) NOT NULL,
    school_id numeric NOT NULL,
    course_period_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    course_id numeric NOT NULL,
    course_weight character varying(10),
    title character varying(100),
    short_name text,
    mp character varying(3),
    marking_period_id integer NULL DEFAULT NULL,
    begin_date date NULL DEFAULT NULL,
    end_date date NULL DEFAULT NULL,
    teacher_id int,
    secondary_teacher_id int,
    total_seats int,
    filled_seats numeric NOT NULL default 0,
    does_honor_roll character varying(1),
    does_class_rank character varying(1),
    gender_restriction character varying(1),
    house_restriction character varying(1),
    availability int,
    parent_id int,
    calendar_id int,
    half_day character varying(1),
    does_breakoff character varying(1),
    rollover_id int,
    grade_scale_id int,
    credits decimal(10,3) null default null,
    schedule_type ENUM( 'FIXED', 'VARIABLE', 'BLOCKED' ),
    last_updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified_by int(11) NOT NULL,
 `updated_by` varchar(255) DEFAULT NULL
)ENGINE=MyISAM");

mysql_query("CREATE TABLE course_period_var (
 id INT NOT NULL AUTO_INCREMENT,
 course_period_id int(11) NOT NULL,
 days varchar(7) DEFAULT NULL,
 course_period_date date NULL DEFAULT NULL,
 period_id int(11) NOT NULL,
 start_time TIME NOT NULL,
 end_time TIME NOT NULL,
 room_id int(11) NOT NULL,
 does_attendance varchar(1) DEFAULT NULL,
 `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_by` varchar(255) DEFAULT NULL,
 PRIMARY KEY (id)
) ENGINE=MyISAM");
mysql_query("ALTER TABLE course_periods AUTO_INCREMENT=1");
$qr=mysql_query("select * from course_periods group by room,school_id");
$qr2=mysql_query("select * from course_periods");
$qr1=mysql_query("select max(total_seats) as tot from course_periods");
$res_seat=  mysql_fetch_array($qr1);
$total_seat=$res_seat['tot'] + 10;
while($res= mysql_fetch_array($qr))
{
    
    $room= $res['room'];
    $sc_id=$res['school_id'];
   
    mysql_query("insert into rooms(school_id, title, capacity) values ('$sc_id','$room','$total_seat')");
     $mp_id=$res['marking_period_id'];
 
}

while($res= mysql_fetch_array($qr2))
{
    
    $room= $res['room'];
    $sc_id=$res['school_id'];
     $mp_id=$res['marking_period_id'];
  

   $qr4=mysql_query("select room_id from rooms where title='$res[room]' and school_id='$res[school_id]'");
   $r_rom=mysql_fetch_array($qr4);
   $room_id= $r_rom['room_id'];
    $qr3=mysql_query("select start_date,end_date from marking_periods where marking_period_id='$mp_id'");
    $res1=mysql_fetch_array($qr3);
     $start_date=$res1['start_date'];
     $end_date=$res1['end_date'];
      $query="insert into course_periods_new(syear,school_id,course_period_id,course_id,course_weight,title,short_name,mp,marking_period_id,teacher_id,secondary_teacher_id,total_seats,filled_seats,does_honor_roll,does_class_rank,gender_restriction,house_restriction,availability,parent_id,calendar_id,half_day,does_breakoff,rollover_id,grade_scale_id,credits,begin_date,end_date)values('$res[syear]','$sc_id','$res[course_period_id]','$res[course_id]','$res[course_weight]','$res[title]','$res[short_name]','$res[mp]','$res[marking_period_id]','$res[teacher_id]','$res[secondary_teacher_id]','$res[total_seats]','$res[filled_seats]','$res[does_honor_roll]','$res[does_class_rank]','$res[gender_restriction]','$res[house_restriction]','$res[availability]','$res[parent_id]','$res[calendar_id]','$res[half_day]','$res[does_breakoff]','$res[rollover_id]','$res[grade_scale_id]','$res[credits]','$start_date','$end_date')";        
  $query_var="insert into course_period_var(course_period_id,days,period_id,start_time,end_time,room_id,does_attendance)values('$res[course_period_id]','$res[days]','$res[period_id]','$start_date','$end_date','$room_id','$res[does_attendance]')";
mysql_query($query);
mysql_query( $query_var);
}


$qr_user_profile="CREATE TABLE user_profiles_new (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    profile character varying(30),
    title character varying(100),
 `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_by` varchar(255) DEFAULT NULL
)ENGINE=MyISAM;";

mysql_query($qr_user_profile);
$up_in1="INSERT INTO `user_profiles_new` (`profile`, `title`, `last_updated`, `updated_by`) VALUES
('admin', 'Super Administrator', '2015-07-28 00:26:33', NULL)";
mysql_query($up_in1);
$up_in2="UPDATE  `user_profiles_new` SET  `id` =  '0'" ;
mysql_query($up_in2);
$up_in3="ALTER TABLE  `user_profiles_new` AUTO_INCREMENT=1";
mysql_query($up_in3);
$up_in4="INSERT INTO `user_profiles_new` (`profile`, `title`, `last_updated`, `updated_by`) VALUES
('admin', 'Administrator', '2015-07-28 00:26:33', NULL),
('teacher', 'Teacher', '2015-07-28 00:26:33', NULL),
('student', 'Student', '2015-07-28 00:26:33', NULL),
('parent', 'Parent', '2015-07-28 00:26:33', NULL),
('admin', 'Admin Asst', '2015-07-28 00:26:33', NULL)";
mysql_query($up_in4);

$qr_custom_user=mysql_query("SELECT * FROM `user_profiles` WHERE title<>'Super Administrator' and title<>'Administrator' and title<> 'Teacher' and title<> 'Student' and title<>'Admin Asst' and title<>'parent'");
while($custom_user_fetch=mysql_fetch_array($qr_custom_user))
{
 $custom_user_qr="INSERT INTO `user_profiles_new` (`profile`, `title`) VALUES('$custom_user_fetch[profile]', '$custom_user_fetch[title]')";   
mysql_query($custom_user_qr);
 
}
//------------------student table start--------------------------------------//
$stu_cr="CREATE TABLE IF NOT EXISTS students_new (
    student_id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    last_name character varying(50) NOT NULL,
    first_name character varying(50) NOT NULL,
    middle_name character varying(50),
    name_suffix character varying(3),
    gender character varying(255),
    ethnicity character varying(255),
    common_name character varying(255),
    social_security character varying(255),
    birthdate character varying(255),
    language character varying(255),
    estimated_grad_date character varying(255),
    alt_id character varying(50),
    email character varying(50),
    phone character varying(30),
    is_disable varchar(10) default NULL,
 `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_by` varchar(255) DEFAULT NULL
)ENGINE=MyISAM";
mysql_query($stu_cr);
//------------------student table end-------------------------------------------//
//-----------------------------staff table start--------------------------------//
$qr_staff_create="CREATE TABLE IF NOT EXISTS `staff_new` (
  `staff_id` int(8) NOT NULL AUTO_INCREMENT,
  `current_school_id` decimal(10,0) DEFAULT NULL,
  `title` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `first_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `middle_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `phone` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `profile` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  `homeroom` varchar(5) CHARACTER SET utf8 DEFAULT NULL,
  `profile_id` decimal(10,0) DEFAULT NULL,
  `primary_language_id` int(8) DEFAULT NULL,
  `gender` varchar(8) CHARACTER SET utf8 DEFAULT NULL,
  `ethnicity_id` int(8) DEFAULT NULL,
  `birthdate` date  NULL,
  `alternate_id` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `name_suffix` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `second_language_id` int(8) DEFAULT NULL,
  `third_language_id` int(8) DEFAULT NULL,
  `is_disable` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `physical_disability` varchar(1) CHARACTER SET utf8 DEFAULT NULL,
  `disability_desc` VARCHAR( 225 ) DEFAULT NULL,
 `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_by` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`staff_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
mysql_query($qr_staff_create);
//---------------------------staff table end--------------------------------------------------------------//

//--------------------------------staff fields & students fields start --------------------------------//


$qr_staff_field_qr="CREATE TABLE IF NOT EXISTS `staff_field_categories_new` (
 `id` int(8) NOT NULL DEFAULT '0',
 `title` varchar(100) DEFAULT NULL,
 `sort_order` decimal(10,0) DEFAULT NULL,
 `include` varchar(100) DEFAULT NULL,
 `admin` char(1) DEFAULT NULL,
 `teacher` char(1) DEFAULT NULL,
 `parent` char(1) DEFAULT NULL,
 `none` char(1) DEFAULT NULL,
 `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
mysql_query($qr_staff_field_qr);
$qr_staff_field_alter="ALTER TABLE staff_field_categories_new AUTO_INCREMENT=1;";
mysql_query($qr_staff_field_alter);
mysql_query("CREATE TABLE IF NOT EXISTS staff_fields_new (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    type character varying(10),
    search character varying(1),
    title character varying(30),
    sort_order numeric,
    select_options character varying(10000),
    category_id numeric,
    system_field character(1),
    required character varying(1),
    default_selection character varying(255),
 `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_by` varchar(255) DEFAULT NULL
)ENGINE=MyISAM");

$dummyFile2 = "CustomField.sql";
                    $fpt = fopen($dummyFile2, 'w') or die("Unable to open file!");

mysql_query("ALTER TABLE staff_fields_new AUTO_INCREMENT=1");
$qr_staff_field_alter_default_insert=mysql_query("INSERT INTO `staff_field_categories_new` (`id`, `title`, `sort_order`, `include`, `admin`, `teacher`, `parent`, `none`) VALUES
(1, 'Demographic Info', '1', NULL, 'Y', 'Y', 'Y', 'Y'),
(2, 'Addresses & Contacts', '2', NULL, 'Y', 'Y', 'Y', 'Y'),
(3, 'School Information', '3', NULL, 'Y', 'Y', 'Y', 'Y'),
(4, 'Certification Information', '4', NULL, 'Y', 'Y', 'Y', 'Y'),
(5, 'Schedule', '5', NULL, 'Y', 'Y', NULL, NULL)");

$qr_staff_field_custom="select id,title,sort_order,include,admin,teacher,parent,none from staff_field_categories where title<>'Demographic Info' and title<>'Addresses & Contacts' and title<> 'School Information' and title<>'Certification Information' and title<>'Schedule' and title<>'General Info'";
$sf_qr=mysql_query($qr_staff_field_custom) or die(mysql_error().'error at 1242');
if(mysql_num_rows($sf_qr)>0)
{
    while($sf_rq=mysql_fetch_array($sf_qr))
    {
        $max_qr=mysql_fetch_array(mysql_query('select max(id) as mid,id from staff_field_categories_new')) or die(mysql_error().'error at 1244');
    $mid=$max_qr['mid']+1;
    if($mid==6)
        $mid=7;
    $cat_id=$sf_rq['id'];
        mysql_query("insert into staff_field_categories_new(id,title,sort_order,admin,teacher,parent,none) values('$mid','$sf_rq[title]','$sf_rq[sort_order]','$sf_rq[admin]','$sf_rq[teacher]','$sf_rq[parent]','$sf_rq[none]')") or die(mysql_error().'error at 280' );
$mod="users/Staff.php&category_id=".$mid;
        $qr_wr="insert into profile_exceptions(profile_id,modname,can_use,can_edit) values('0','$mod','Y','Y');";
        fwrite($fpt,$qr_wr);
        mysql_query('insert into staff_fields_new(type,search,title,sort_order,select_options,category_id,system_field,required,default_selection)select type,search,title,sort_order,select_options,\''.$mid.'\',system_field,required,default_selection from staff_fields where category_id ='.$cat_id.'') or die(mysql_error().'error at 1483' );
    }
}
mysql_query('DROP TABLE staff_field_categories');
mysql_query("RENAME TABLE staff_field_categories_new TO staff_field_categories") ;
mysql_query('DROP TABLE staff_fields');
mysql_query("RENAME TABLE staff_fields_new TO staff_fields") ;
mysql_query("CREATE TABLE student_field_categories_new (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title character varying(100),
    sort_order numeric,
    include character varying(100),
 `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_by` varchar(255) DEFAULT NULL
)ENGINE=MyISAM");


mysql_query("ALTER TABLE student_field_categories_new AUTO_INCREMENT=1");
mysql_query("CREATE TABLE  IF NOT EXISTS custom_fields_new (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    type character varying(10),
    search character varying(1),
    title character varying(30),
    sort_order numeric,
    select_options character varying(10000),
    category_id numeric,
    system_field character(1),
    required character varying(1),
    default_selection character varying(255),
	hide varchar(1),
 `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_by` varchar(255) DEFAULT NULL
)ENGINE=MyISAM");
mysql_query("ALTER TABLE custom_fields_new AUTO_INCREMENT=1");
mysql_query("INSERT INTO `student_field_categories_new` (`id`, `title`, `sort_order`, `include`, `last_updated`, `updated_by`) VALUES
(1, 'General Info', '1', NULL, '2015-07-11 15:23:28', NULL),
(2, 'Medical', '3', NULL, '2015-07-11 15:23:28', NULL),
(3, 'Addresses & Contacts', '2', NULL, '2015-07-11 15:23:28', NULL),
(4, 'Comments', '4', NULL, '2015-07-11 15:23:28', NULL),
(5, 'Goals', '5', NULL, '2015-07-11 15:23:28', NULL),
(6, 'Enrollment Info', '6', NULL, '2015-07-11 15:23:28', NULL),
(7, 'Files', '7', NULL, '2015-07-11 15:23:28', NULL)");

$qr_student_field_custom="select id,title,sort_order,include from student_field_categories where title<>'General Info' and  title<>'Medical' and title<> 'Addresses & Contacts' and title<>'Comments' and title<>'Goals' and  title<> 'Enrollment Info' and  title<>'Files'";
$su_qr=mysql_query($qr_student_field_custom);

if(mysql_num_rows($su_qr)>0)
{
    
    while($sf_rq=mysql_fetch_array($su_qr))
    {
        $max_qr=mysql_fetch_array(mysql_query('select max(id) as mid,id from student_field_categories_new')) or die(mysql_error().'error at 1243');
    $mid=$max_qr['mid']+1;
    $cat_id=$sf_rq['id'];
        mysql_query("insert into student_field_categories_new(id,title,sort_order) values('$mid','$sf_rq[title]','$sf_rq[sort_order]')") or die(mysql_error().'error at 281' );
  $mod="students/Student.php&category_id=".$mid;
        $qr_wr="insert into profile_exceptions(profile_id,modname,can_use,can_edit) values('0','$mod','Y','Y');";
        fwrite($fpt,$qr_wr);
        $qr_custom=mysql_query('select * from custom_fields  where category_id ='.$cat_id.'') or die(mysql_error().'error at 286' );
     while($qr_custom_fetch=mysql_fetch_array($qr_custom))
    {
         $id=$qr_custom_fetch['id'];
     
  mysql_query('insert into custom_fields_new(id,type,search,title,sort_order,select_options,category_id,system_field,required,default_selection,hide)select \''.$id.'\', type,search,title,sort_order,select_options,\''.$mid.'\',system_field,required,default_selection,hide from custom_fields where id ='.$id.'') or die(mysql_error().'error at 1537' );
   

  
  
  }    
    }
}
 $res_student_field='SHOW COLUMNS FROM '.'students'.' WHERE FIELD LIKE "CUSTOM_%"';

	$objCustomStudents=new custom($mysql_database);
	$objCustomStudents->set($res_student_field,'students_new');
	
	$res_staff_field='SHOW COLUMNS FROM '.'staff'.' WHERE FIELD LIKE "CUSTOM_%"';
	$objCustomStaff=new custom($mysql_database);
	$objCustomStaff->set($res_staff_field,'staff_new');

                    $qr="RENAME TABLE staff TO staff_new;";
                    fwrite($fpt,$qr);
                    $qr1="RENAME TABLE students TO students_new;";
                    fwrite($fpt,$qr1);

                   
	foreach($objCustomStudents->customQueryString as $query){
	mysql_query($query);
        $query1=$query.";";
        fwrite($fpt,$query1);
	}
	//execute custome field for satff
	foreach($objCustomStaff->customQueryString as $query){
	mysql_query($query);
        $query1=$query.";";
        fwrite($fpt,$query1);
	}
         $qr3="RENAME TABLE staff_new TO staff;";
         fwrite($fpt,$qr3);
                    $qr4="RENAME TABLE students_new TO students;";
                    fwrite($fpt,$qr4);
         fclose($fpt);
mysql_query('DROP TABLE student_field_categories');
mysql_query("RENAME TABLE student_field_categories_new TO student_field_categories") ;
mysql_query('DROP TABLE custom_fields');
mysql_query("RENAME TABLE custom_fields_new TO custom_fields") ;

//-------------------staff fields & students fields end-------------------------------------------------//
//----------------------------------------staff insert start------------------------------------------------------------------------------------------------------------//

$qr_log_auth="CREATE TABLE `login_authentication_new` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) NOT NULL,
 `profile_id` int(11) NOT NULL,
 `username` varchar(255) NOT NULL,
 `password` varchar(255) NOT NULL,
 `last_login` datetime DEFAULT NULL,
 `failed_login` int(3) NOT NULL DEFAULT '0',
 `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_by` varchar(255) DEFAULT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `COMPOSITE` (`user_id`,`profile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
mysql_query($qr_log_auth);
$staff_info_school_qr="CREATE TABLE IF NOT EXISTS `staff_school_info` (
  `staff_school_info_id` int(8) NOT NULL AUTO_INCREMENT,
  `staff_id` int(8) NOT NULL,
  `category` varchar(255) NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `joining_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `home_school` int(8) NOT NULL,
  `opensis_access` char(1) NOT NULL DEFAULT 'N',
  
  `opensis_profile` varchar(255) DEFAULT NULL,
  `school_access` varchar(255) DEFAULT NULL,
 `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date and time staff school info was modified',
 `updated_by` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`staff_school_info_id`),
  UNIQUE KEY `staff_id` (`staff_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
mysql_query($staff_info_school_qr);
$staff_qr=mysql_query("select * from staff where profile <>'parent'");
$staff_c_arr=array();
$res_staff_field=mysql_query('SHOW COLUMNS FROM '.'staff'.' WHERE FIELD LIKE "CUSTOM_%"');
while($st_ff=mysql_fetch_array($res_staff_field))
{
    array_push($staff_c_arr,$st_ff['Field']);
}
if(empty($staff_c_arr))
{
   $custom_staff=""; 
}
 else {
   $custom_staff=",".implode(',',$staff_c_arr); 
}
while($res=mysql_fetch_array($staff_qr))
{

  $profile_id=$res['profile_id'];
   
   $staff_id=$res['staff_id'];
    $qr_profile= mysql_query("select title from user_profiles where id='$profile_id'");
    $f=mysql_fetch_array($qr_profile);
    
 $title=$f['title'];
 
  $qr_profile_new=  mysql_fetch_array(mysql_query("select id from user_profiles_new where title='$title'"));
  $p=$qr_profile_new['id'];
  $qr_staff_insert='INSERT INTO staff_new (staff_id,current_school_id,title,first_name,last_name,middle_name,phone,email,profile,homeroom,profile_id,is_disable '.$custom_staff.') SELECT staff_id,current_school_id,title,first_name,last_name,middle_name,phone,email,profile,homeroom,profile_id,is_disable '.$custom_staff.' FROM staff where staff_id ='.$staff_id.'';
     $qr_staff_insert_log_auth="insert into login_authentication_new (user_id,profile_id,username,password,last_login,failed_login) values('$res[staff_id]','$p','$res[username]','$res[password]','$res[last_login]','$res[failed_login]')";
 
     mysql_query($qr_staff_insert);
  if($res['username']!='' && $res['password']!='')
  mysql_query($qr_staff_insert_log_auth);
  $qr_staff_info=mysql_query("select * from staff_school_relationship where staff_id='$staff_id'");
  $qr_staff_num=mysql_num_rows($qr_staff_info);
  if($qr_staff_num>0)
  {
while($res_info=mysql_fetch_array($qr_staff_info))
{
    $staff_id=$res_info['staff_id'];
    $qr1=mysql_fetch_array(mysql_query('select * from staff where staff_id='.$staff_id.''));
    $profile=$qr1['profile'];
   $school_access=','.$res_info['school_id'].',';

    if($profile=='teacher')
    {
         $category='Teacher';
        $title='Teacher';
    }
    elseif($profile=='admin')
    {
         $category='Administrator';
        $title='Administrator';
    }
 else {
      $category=$profile;
        $title=$profile;  
    }
    

    $query_info_insert="insert into staff_school_info(home_school,staff_id,category,job_title,joining_date,end_date,opensis_access,opensis_profile,school_access)values('$res_info[school_id]','$staff_id','$category','$title','$res_info[start_date]','$res_info[end_date]','Y','$p','$school_access')";
 
  mysql_query($query_info_insert);
}
  }
}

//----------------------------------------staff insert End-------------------------------------------------------------------------------------------------------------//




//-------------------------------------------------------stu2nd-------------------------//


$stu_al="ALTER TABLE students AUTO_INCREMENT=1";
mysql_query($stu_al);

$stu_join_cr="CREATE TABLE IF NOT EXISTS students_join_people_new (
   id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
   student_id numeric NOT NULL,
   person_id numeric(10,0) NOT NULL,
   is_emergency varchar(10) DEFAULT NULL,
   emergency_type varchar(100) DEFAULT NULL,
   relationship varchar(100) NOT NULL,
 `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_by` varchar(255) DEFAULT NULL
)ENGINE=MyISAM";
mysql_query($stu_join_cr);
$stu_al_qr="ALTER TABLE students_join_people AUTO_INCREMENT=1";
mysql_query($stu_al_qr);
$stu_people_qr="CREATE TABLE IF NOT EXISTS `people_new` (
 `staff_id` int(11) NOT NULL AUTO_INCREMENT,
 `current_school_id` decimal(10,0) DEFAULT NULL,
 `title` varchar(5) DEFAULT NULL,
 `first_name` varchar(100) DEFAULT NULL,
 `last_name` varchar(100) DEFAULT NULL,
 `middle_name` varchar(100) DEFAULT NULL,
 `home_phone` varchar(255) DEFAULT NULL,
 `work_phone` varchar(255) DEFAULT NULL,
 `cell_phone` varchar(255) DEFAULT NULL,
 `email` varchar(100) DEFAULT NULL,
 `custody` varchar(1) DEFAULT NULL,
 `profile` varchar(30) DEFAULT NULL,
 `profile_id` decimal(10,0) DEFAULT NULL,
 `is_disable` varchar(10) DEFAULT NULL,
 `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_by` varchar(255) DEFAULT NULL,
 PRIMARY KEY (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
mysql_query($stu_people_qr);
$add_qr="CREATE TABLE IF NOT EXISTS `student_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `syear` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `street_address_1` varchar(5000) DEFAULT NULL,
  `street_address_2` varchar(5000) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zipcode` varchar(255) DEFAULT NULL,
  `bus_pickup` varchar(1) DEFAULT NULL,
  `bus_dropoff` varchar(1) DEFAULT NULL,
  `bus_no` varchar(255) DEFAULT NULL,
  `type` varchar(500) NOT NULL,
  `people_id` int(11) DEFAULT NULL,
 `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_by` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1" ;

mysql_query($add_qr);


$stu_c_arr=array();
$res_student_field=mysql_query('SHOW COLUMNS FROM '.'students'.' WHERE FIELD LIKE "CUSTOM_%"')or die(mysql_error().'error at 606' );
while($st_ff=mysql_fetch_array($res_student_field))
{
    array_push($stu_c_arr,$st_ff['Field']);
}
if(empty($stu_c_arr))
{
   $custom_stu=""; 
}
else
$custom_stu=",".implode(',',$stu_c_arr);
  $qr1="insert into students_new(student_id,last_name,first_name,middle_name,name_suffix,gender,ethnicity,common_name,social_security,birthdate,language,estimated_grad_date,alt_id,email,phone,is_disable ".$custom_stu.") select student_id,last_name,first_name,middle_name,name_suffix,gender,ethnicity,common_name,social_security,birthdate,language,estimated_grad_date,alt_id,email,phone,is_disable".$custom_stu." from students";
mysql_query($qr1) or die(mysql_error().'error at 1ss' );
$qr_student_login_auth="insert into login_authentication_new(user_id,profile_id,username,password,last_login,failed_login) select student_id,'3',username,password,last_login,failed_login from students";
mysql_query($qr_student_login_auth) or die(mysql_error().'error at 1000' );
$qr=mysql_query("select * from people") or die(mysql_error().'error at 2' );
while($res=mysql_fetch_array($qr))
{
     $per_id=$res['person_id'];
    $st_jq=mysql_query("select * from students_join_people where person_id='$per_id'") or die(mysql_error().'error at 3' );
    $st_f=mysql_fetch_array($st_jq);
    
    $qr1=mysql_query("SELECT school_id FROM student_enrollment WHERE student_id=(select student_id from students_join_people where person_id='$per_id') order by id desc limit 0,1")or die(mysql_error().'error at 4' );;
$res1=mysql_fetch_array($qr1);

    $crnt_sch_id=$res1['school_id'];

    $qr2='insert into people_new(staff_id,current_school_id,last_name,first_name,middle_name)select person_id,\''.$crnt_sch_id.'\',first_name,last_name,middle_name from people where person_id='.$per_id.'';
mysql_query($qr2) or die(mysql_error().'error at 5' );
 $wq='insert into student_addres(student_id,syear,school_id,street_address_1,street_address_2,city,state,zipcode,type,people_id,bus_pickup,bus_dropoff,bus_no)select \''.$student_id.'\',\''.$syear.'\',\''.$crnt_sch_id.'\',addn_address,addn_street,addn_city,addn_state,addn_zipcode,\'Other\',addn_bus_pickup,addn_bus_dropoff,addn_busno from students_join_people where id='.$join_id.''; 
mysql_query($wq);
}
$qr1=mysql_query("select * from people_new") or die(mysql_error().'error at 6' );
while($res=mysql_fetch_array($qr1))
{
     $per_id=$res['staff_id'];
    $st_jq=mysql_query("select * from students_join_people where person_id='$per_id'") or die(mysql_error().'error at 7' );
    $st_f=mysql_fetch_array($st_jq);
   $qr1=mysql_query("SELECT school_id FROM student_enrollment WHERE student_id=(select student_id from students_join_people where person_id='$per_id') order by id desc limit 0,1") or die(mysql_error().'error at 8' );
$res1=mysql_fetch_array($qr1);

    $crnt_sch_id=$res1['school_id'];

    $qr="update people_new set";
  if($st_f['addn_home_phone']!='') 
      $qr.=" home_phone='$st_f[addn_home_phone]',";
   if($st_f['addn_work_phone']!='')  
        $qr.=" work_phone='$st_f[addn_work_phone]',";
       if($st_f['addn_mobile_phone']!='') 
            $qr.=" cell_phone='$st_f[addn_mobile_phone]',";
   if($st_f[addn_email]!='') 
       $qr.=" email='$st_f[addn_email]',";
       
   $qr.="profile='parent',profile_id=4 where staff_id='$per_id'";
   
 mysql_query($qr)  or die(mysql_error().'error at 9' );
   
}
$emeregncy='Other';
  $qr2='insert into students_join_people_new(student_id,person_id,emergency_type,relationship)select student_id,person_id,\''.$emeregncy.'\',student_relation from students_join_people';
mysql_query($qr2)  or die(mysql_error().'error at 10' );
$qr_add=mysql_query("select * from address")  or die(mysql_error().'error at 11' );
while($res=mysql_fetch_array($qr_add))
{
   
   $add_id=$res['address_id'];
     $student_id=$res['student_id'];
    
     $qr1=mysql_query("SELECT school_id,syear FROM student_enrollment WHERE student_id='$student_id' order by id desc limit 0,1")  or die(mysql_error().'error at 12' );
$res1=mysql_fetch_array($qr1);

    $crnt_sch_id=$res1['school_id'];
   $syear=$res1['syear'];
    $pe_qr=mysql_query("select max(person_id) as pid from students_join_people_new")  or die(mysql_error().'error at 13' );
    $pe_f=mysql_fetch_array($pe_qr);
    $p_p_id=$pe_f['pid']+1;
    $type='parent';
    $po_id='4';
    $qr2_pe='insert into people_new(staff_id,current_school_id,last_name,first_name,home_phone,work_phone,cell_phone,email,profile,profile_id,custody) select \''.$p_p_id.'\',\''.$crnt_sch_id.'\',pri_last_name,pri_first_name,home_phone,work_phone,mobile_phone,email,\''.$type.'\',\''.$po_id.'\',prim_custody from address where address_id='.$add_id.'';

    $qr2_join='insert into students_join_people_new(student_id,person_id,emergency_type,relationship) select \''.$student_id.'\',\''.$p_p_id.'\',\'Primary\',prim_student_relation from address where address_id='.$add_id.'';
$qr2_add='insert into student_address(student_id,syear,school_id,street_address_1,street_address_2,city,state,zipcode,type,people_id) select \''.$student_id.'\',\''.$syear.'\',\''.$crnt_sch_id.'\',prim_address,prim_street,prim_city,prim_state,prim_zipcode,\'Primary\',\''.$p_p_id.'\' from address where address_id='.$add_id.'';    
mysql_query($qr2_pe)  or die(mysql_error().'error at 14' );
mysql_query($qr2_join)  or die(mysql_error().'error at 15' );
mysql_query($qr2_add)  or die(mysql_error().'error at 16' );
$pe_qr=mysql_query("select max(person_id) as pid from students_join_people_new");
    $pe_f=mysql_fetch_array($pe_qr);
    $s_p_id=$pe_f['pid']+1;
   
     $qr2s_pe='insert into people_new(staff_id,current_school_id,last_name,first_name,home_phone,work_phone,cell_phone,email,profile,profile_id,custody) select \''.$s_p_id.'\',\''.$crnt_sch_id.'\',sec_last_name,sec_first_name,sec_home_phone,sec_work_phone,sec_mobile_phone,sec_email,\''.$type.'\',\''.$po_id.'\',sec_custody from address where address_id='.$add_id.'';

$qr2s_join='insert into students_join_people_new(student_id,person_id,emergency_type,relationship) select \''.$student_id.'\',\''.$s_p_id.'\',\'Secondary\',sec_student_relation from address where address_id='.$add_id.'';
$qr2s_add='insert into student_address(student_id,syear,school_id,street_address_1,street_address_2,city,state,zipcode,type,people_id)select \''.$student_id.'\',\''.$syear.'\',\''.$crnt_sch_id.'\',sec_address,sec_street,sec_city,sec_state,sec_zipcode,\'Secondary\',\''.$s_p_id.'\' from address where address_id='.$add_id.'';    
mysql_query($qr2s_pe)  or die(mysql_error().'error at 17' );
mysql_query($qr2s_join)  or die(mysql_error().'error at 18' );
mysql_query($qr2s_add)  or die(mysql_error().'error at 19' );
$t=mysql_query("select * from students_join_people where student_id='$student_id'")  or die(mysql_error().'error at 20' );
$k=  mysql_num_rows($t);

 if($k>0)
 {
while($tf=mysql_fetch_array($t))
{
    $person_id=$tf[person_id];
    $join_id=$tf['id'];
    $wq='insert into student_address(student_id,syear,school_id,street_address_1,street_address_2,city,state,zipcode,type,people_id,bus_pickup,bus_dropoff,bus_no)select \''.$student_id.'\',\''.$syear.'\',\''.$crnt_sch_id.'\',addn_address,addn_street,addn_city,addn_state,addn_zipcode,\'Other\',\''.$person_id.'\',addn_bus_pickup,addn_bus_dropoff,addn_busno from students_join_people where id='.$join_id.''; 
mysql_query($wq);
    

}
 }
$wq1='insert into student_address(student_id,syear,school_id,street_address_1,street_address_2,city,state,zipcode,type)select \''.$student_id.'\',\''.$syear.'\',\''.$crnt_sch_id.'\',address,street,city,state,zipcode,\'Home Address\' from address where address_id='.$add_id.''; 
$wq2='insert into student_address(student_id,syear,school_id,street_address_1,street_address_2,city,state,zipcode,type)select\''.$student_id.'\',\''.$syear.'\',\''.$crnt_sch_id.'\',mail_address,mail_street,mail_city,mail_state,mail_zipcode,\'Mail\'from address where address_id='.$add_id.''; 
mysql_query($wq1)  or die(mysql_error().'error at 21' );
mysql_query($wq2)  or die(mysql_error().'error at 22' );
}

//------------------------------------------------------stu2nd--------------------------//

//--------------------------------Associate parent start--------------------------------------//
$staff_qr_assoc=mysql_query("select * from staff where profile ='parent'")  or die(mysql_error().'error at 23' );

while($res_assoc=mysql_fetch_array($staff_qr_assoc))
{
$per_id=$res_assoc['staff_id'];
    $assoc_stu=mysql_fetch_array(mysql_query("select student_id from  students_join_users where staff_id ='$res_assoc[staff_id]'"));
 
    $assoc_student_id=$assoc_stu['student_id'];

    $qr_staff_in=mysql_fetch_array(mysql_query("select * from staff_school_relationship where staff_id='$per_id'")) or die(mysql_error().'error at 699' );
    $syear=$qr_staff_in['syear'];
    $crnt_sch_id=$qr_staff_in['school_id'];
     $qr_sch=mysql_query("SELECT school_id FROM student_enrollment WHERE student_id=(select student_id from students_join_people where person_id='$per_id') order by id desc limit 0,1") or die(mysql_error().'error at 24' );
    $pe_qr=mysql_query("select max(person_id) as pid from students_join_people_new")  or die(mysql_error().'error at 24' );
    $pe_f=mysql_fetch_array($pe_qr);
     $p_p_id=$pe_f['pid']+1;
    

 $qr2_pe='insert into people_new(last_name,staff_id,current_school_id,first_name,middle_name,email,profile,profile_id,home_phone)select last_name,\''.$p_p_id.'\',\''.$crnt_sch_id.'\',first_name,middle_name,email,\'parent\',\'4\',phone from staff where staff_id='.$per_id.'';

    mysql_query($qr2_pe) or die(mysql_error().'error at 25' );
       $qr2_join="insert into students_join_people_new(student_id,person_id,emergency_type,relationship) values('$assoc_student_id','$p_p_id','Other','Legal Guardian')";
mysql_query($qr2_join);
         $wq1='insert into student_address(student_id,syear,school_id,street_address_1,street_address_2,city,state,zipcode,people_id,type)select \''.$assoc_student_id.'\',\''.$syear.'\',\''.$crnt_sch_id.'\',address,street,city,state,zipcode,\''.$p_p_id.'\',\'Other\' from address where student_id='.$assoc_student_id.''; 
 

 mysql_query($wq1) or die(mysql_error().'error at 2576' );
   
    



 $qr_staff_insert_log_auth="insert into login_authentication_new(user_id,profile_id,username,password,last_login,failed_login) values('$p_p_id','4','$res_assoc[username]','$res_assoc[password]','$res_assoc[last_login]','$res_assoc[failed_login]')";

  if($res_assoc['username']!='' && $res_assoc['password']!='')
  mysql_query($qr_staff_insert_log_auth) or die(mysql_error().'error at 26' );

}
//--------------------------------Associate parent End--------------------------------------//
//-------------------------------------Portal Notes start-------------------------------------//
$qr_por=mysql_query("select * from portal_notes");
$qr2_por="CREATE TABLE portal_notes_new (
    id int(8) not null auto_increment primary key,
    school_id numeric,
    syear numeric(4,0),
    title character varying(255),
    content longtext,
    sort_order numeric,
    published_user numeric,
   last_updated timestamp ,
    start_date date,
    end_date date,
    published_profiles character varying(255),
 `updated_by` varchar(255) DEFAULT NULL
)ENGINE=MyISAM";;
mysql_query($qr2_por);

$qr3_por="ALTER TABLE portal_notes_new AUTO_INCREMENT=1";
mysql_query($qr3_por);
while($res_por=mysql_fetch_array($qr_por))
{
    $id=$res_por['id'];
 
     $final_arr=array();
    $pub=explode(',',$res_por['published_profiles']);
   $final_arr[0]=',';
    foreach($pub as $k => $v)
    {
if($v==0)
//       
   {
       $p=3;
   
       
   }
  if($v==3)
       $p=4;
   elseif($v==4)
       $p=6;
   elseif($v==5)
       $p=7;
   elseif($v==6)
       $p=8;
   elseif($v==7)
       $p=9;
   
   else
       $p=$v;
   
   array_push($final_arr,$p);
    }
    if(count($final_arr)>0)
     $publish_user=implode(',',$final_arr);
    else
       $publish_user=''; 
     $res_por_insert="insert into portal_notes_new(school_id,syear,title,content,sort_order,published_user,published_profiles,start_date,end_date)select school_id,syear,title,content,sort_order,published_user,'$publish_user',start_date,end_date from portal_notes where id='$id'";

   mysql_query($res_por_insert) or die(mysql_error().'error at 27' );
    
    
    
}
//-----------------------------Portal Notes END----------------------------------------//

//-----------------------------app start-------------------------------//
$app_qr="CREATE TABLE app_new (
    name character varying(100) NOT NULL,
    value character varying(100) NOT NULL
)ENGINE=MyISAM;";
    
mysql_query($app_qr);
$app_insert="INSERT INTO `app_new` (`name`, `value`) VALUES
('version', '6.0'),
('date', 'July 20, 2015'),
('build', '20150619001'),
('update', '0'),
('last_updated', 'July 20, 2015')";
mysql_query($app_insert) or die(mysql_error().'error at 28' );
//-----------------------------app end--------------------------------//
//-----------------------------student gpa calculated start----------------//
$qr_student_gpa_create="CREATE TABLE student_gpa_calculated_new (
    student_id numeric,
    marking_period_id integer,
    mp character varying(4),
    gpa decimal(10,2),
    weighted_gpa decimal(10,2),
    unweighted_gpa decimal(10,2),
    class_rank numeric,
    grade_level_short character varying(100) default NULL,
    cgpa decimal(10,2) DEFAULT NULL,
    cum_unweighted_factor decimal(10,6),
 `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_by` varchar(255) DEFAULT NULL
)ENGINE=MyISAM";
mysql_query($qr_student_gpa_create);
$qr_gpa_calcultate=mysql_query('select * from student_gpa_calculated') or die(mysql_error().'error at 29' );
while($gpa_fetch=mysql_fetch_array($qr_gpa_calcultate))
{
    $stu_id=$gpa_fetch[student_id];
   
   $marking_period_id=$gpa_fetch[marking_period_id];

    $gpa_cal_insert='insert into student_gpa_calculated_new(student_id,marking_period_id,mp,gpa,weighted_gpa,unweighted_gpa,class_rank,grade_level_short)select \''.$stu_id.'\',\''.$marking_period_id.'\',mp,gpa,weighted_gpa,unweighted_gpa,class_rank,grade_level_short from student_gpa_calculated where student_id='.$stu_id.' and marking_period_id='.$marking_period_id.'';
    mysql_query($gpa_cal_insert) or die(mysql_error().'error at 30' );
    $cum_qr='select cum_unweighted_factor from  student_mp_stats where student_id='.$gpa_fetch[student_id].' and marking_period_id='.$gpa_fetch[marking_period_id].'';
    if(mysql_num_rows(mysql_query($cum_qr))>0)
    {
    $cum_unweighted_factor_qr=mysql_fetch_array(mysql_query($cum_qr));
$cum_unweighted_factor=$cum_unweighted_factor_qr['cum_unweighted_factor'];
   
mysql_query('update student_gpa_calculated_new set cum_unweighted_factor='.$cum_unweighted_factor.' where student_id='.$stu_id.' and marking_period_id='.$marking_period_id.'');

}

     $cgp_qr='select cgpa from student_gpa_running where student_id='.$gpa_fetch[student_id].' and marking_period_id='.$gpa_fetch[marking_period_id].'';
  if(mysql_num_rows(mysql_query($cgp_qr))>0)
    {
    $cgpa_qr=mysql_fetch_array(mysql_query($cgp_qr));
$cgpa=$cgpa_qr['cgpa'];  
mysql_query('update student_gpa_calculated_new set cgpa='.$cgpa.' where student_id='.$stu_id.' and marking_period_id='.$marking_period_id.'') or die(mysql_error().'error at 31' );
    }
}

//-----------------------------student gpa calculated end----------------//
//-----------------------------school start---------------------------//
mysql_query('ALTER TABLE schools DROP ceeb');
////-----------------------------school end---------------------------//
//-----------------------------making super admin start-------------------------------------//
$qr_log=mysql_query("select * from login_authentication_new where username='os4ed'") or die(mysql_error().'error at 32' );
$pass='f7658b271318b97a17e625f875ea5a24';
if(mysql_num_rows($qr_log)>0)
{
    
$super_query=mysql_fetch_array($qr_log);
 $staff_id=$super_query['user_id'];
$id=$super_query['id'];

mysql_query("update staff_new set profile_id='0' where staff_id='$staff_id'") or die(mysql_error().'error at 33' ) ;
mysql_query("update login_authentication_new set profile_id=0,password='$pass' where id='$id'") or die(mysql_error().'error at 34' );
mysql_query("update staff_school_info set category='Super Administrator',job_title='Super Administrator' where staff_id='$staff_id'") or die(mysql_error().'error at 35' ) ;
}
else
{
   
    $qr_school1=mysql_query('select min(id) as sch_id from schools');
    $qr_sch_f=mysql_fetch_array($qr_school1);
    $crnt_sch_id=$qr_sch_f['sch_id'];
    $qr_staf=mysql_query('select max(staff_id) as sid from staff') or die(mysql_error().'error at 675' );
   $st_f=mysql_fetch_array($qr_staf);
   $staff_id=$st_f['sid']+1;
   mysql_query("INSERT INTO `staff_new` (`staff_id`,`current_school_id`, `title`, `first_name`, `last_name`, `middle_name`, `phone`, `email`, `profile`, `homeroom`, `profile_id`, `primary_language_id`, `gender`, `ethnicity_id`, `birthdate`, `alternate_id`, `name_suffix`, `second_language_id`, `third_language_id`, `is_disable`, `physical_disability`, `disability_desc`,`updated_by`) values ( '$staff_id','$crnt_sch_id', '', 'osfored', 'admin', '', '770-555-1212', 'info@os4ed.com', 'admin', '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL)" ) or die(mysql_error().'error at 674' );

   $qr_school=mysql_query('select * from schools');
   while($f=mysql_fetch_array($qr_school))
   {
       
       $years_qr=mysql_query("select * from school_years where school_id='$f[id]'") or die(mysql_error() .'error at 682');
       
       if(mysql_num_rows($years_qr)>0)
       {
          
           while($f1=mysql_fetch_array($years_qr))
           {
               $start_qr=mysql_query("select start_date from marking_periods where marking_period_id='$f1[marking_period_id]'") or die(mysql_error() .'error at 687');
           $start_date=$start_qr['start_date'];
           
           mysql_query("insert into staff_school_relationship(staff_id,school_id,syear,start_date)values('$staff_id','$f1[school_id]','$f1[syear]','$start_date')" ) or die(mysql_error().'error at 689');    
           
              
           }
       }
   }
   $jo_date_qr=mysql_query("select start_date from marking_periods where school_id='$school_id' and syear='$syear'and mp_type='year'") or die(mysql_error().'error at 1647');
$jo_d_f=mysql_fetch_array($jo_date_qr);
$start_date=$jo_d_f['start_date'];
   mysql_query("insert into login_authentication_new (user_id,username,password,profile_id) values('$staff_id','os4ed','$pass','0')") or die(mysql_error().'error at 679' );
$jo_qr=mysql_query("select min(start_date) as j_date from marking_periods") or die(mysql_error().'error at 697' );
  $jo_f=mysql_fetch_array($jo_qr);
  $joining_date=$jo_f['j_date'];
mysql_query("insert into staff_school_info (staff_id,category,job_title,joining_date,opensis_access) values('$staff_id','Super Administrator','Super Administrator','$joining_date','Y')") or die(mysql_error().'error at 680' );
   
}

//-----------------------------making super admin end-------------------------------------//
//------------------------------drop table start----------------------//
mysql_query('DROP TABLE attendance_calendars') ;
mysql_query('DROP TABLE course_periods');
mysql_query('DROP TABLE user_profiles')  ;
mysql_query('DROP TABLE staff') ;
mysql_query('DROP TABLE login_authentication') ;
mysql_query('DROP TABLE students') ;
mysql_query('DROP TABLE students_join_people') ;
mysql_query('DROP TABLE address');
mysql_query('DROP TABLE address_fields');
mysql_query('DROP TABLE address_field_categories');
mysql_query('DROP TABLE people') ;
mysql_query('DROP TABLE portal_notes') ;
mysql_query('DROP TABLE app') ;
mysql_query('DROP TABLE student_gpa_calculated') ;
mysql_query('DROP TABLE student_mp_stats') ;
mysql_query('DROP TABLE student_gpa_running');
mysql_query('DROP TABLE staff_exceptions');
mysql_query('DROP TABLE  students_join_address');
mysql_query('DROP TABLE  students_join_address');
mysql_query('DROP TABLE  students_join_users');
mysql_query('DROP VIEW student_contacts');

//------------------------------drop table end------------------------//
//------------------------------rename table start--------------------//
mysql_query("RENAME TABLE attendance_calendars_new TO attendance_calendars") ;
mysql_query("RENAME TABLE course_periods_new TO course_periods") ;
mysql_query("RENAME TABLE user_profiles_new TO user_profiles") ;
mysql_query("RENAME TABLE staff_new TO staff")  ;
mysql_query("RENAME TABLE login_authentication_new TO login_authentication")  ;
mysql_query("RENAME TABLE students_new TO students") ;
mysql_query("RENAME TABLE students_join_people_new TO students_join_people")  ;
mysql_query("RENAME TABLE people_new TO people") ;
mysql_query("RENAME TABLE portal_notes_new TO portal_notes") ;
mysql_query("RENAME TABLE app_new TO app")  ;
mysql_query("RENAME TABLE student_gpa_calculated_new TO student_gpa_calculated")  ;
mysql_query("RENAME TABLE progress TO student_goal_progress") ;
mysql_query("RENAME TABLE goal TO student_goal")  ;
mysql_query("RENAME TABLE student_medical TO student_immunization")  ;
//------------------------------rename table end--------------------//
//------------------------------truncate---------------------------//
mysql_query("TRUNCATE profile_exceptions");
mysql_query("INSERT INTO `profile_exceptions` (`profile_id`, `modname`, `can_use`, `can_edit`) VALUES
('2', 'students/Student.php&category_id=6', 'Y', NULL),
('3', 'students/Student.php&category_id=6', 'Y', NULL),
('4', 'students/Student.php&category_id=6', 'Y', NULL),
('2', 'users/User.php&category_id=5', 'Y', NULL),
('3', 'schoolsetup/Schools.php', 'Y', NULL),
('3', 'schoolsetup/Calendar.php', 'Y', NULL),
('3', 'students/Student.php', 'Y', NULL),
('3', 'students/Student.php&category_id=1', 'Y', 'Y'),
('3', 'students/Student.php&category_id=3', 'Y', 'Y'),
('3', 'students/ChangePassword.php', 'Y', NULL),
('3', 'scheduling/ViewSchedule.php', 'Y', NULL),
('3', 'scheduling/PrintSchedules.php', 'Y', NULL),
('3', 'scheduling/Requests.php', 'Y', 'Y'),
('3', 'grades/StudentGrades.php', 'Y', NULL),
('3', 'grades/FinalGrades.php', 'Y', NULL),
('3', 'grades/ReportCards.php', 'Y', NULL),
('3', 'grades/Transcripts.php', 'Y', NULL),
('3', 'grades/GPARankList.php', 'Y', NULL),
('3', 'attendance/StudentSummary.php', 'Y', NULL),
('3', 'attendance/DailySummary.php', 'Y', NULL),
('3', 'eligibility/Student.php', 'Y', NULL),
('3', 'eligibility/StudentList.php', 'Y', NULL),
('2', 'schoolsetup/Schools.php', 'Y', NULL),
('2', 'schoolsetup/MarkingPeriods.php', 'Y', NULL),
('2', 'schoolsetup/Calendar.php', 'Y', NULL),
('2', 'students/Student.php', 'Y', NULL),
('2', 'students/AddUsers.php', 'Y', NULL),
('2', 'students/AdvancedReport.php', 'Y', NULL),
('2', 'students/Student.php&category_id=1', 'Y', NULL),
('2', 'students/Student.php&category_id=3', 'Y', NULL),
('2', 'students/Student.php&category_id=4', 'Y', 'Y'),
('2', 'users/User.php', 'Y', NULL),
('2', 'users/Staff.php&category_id=2', 'Y', NULL),
('2', 'users/Staff.php&category_id=1', 'Y', NULL),
('2', 'users/Preferences.php', 'Y', NULL),
('2', 'scheduling/Schedule.php', 'Y', NULL),
('2', 'scheduling/PrintSchedules.php', 'Y', NULL),
('2', 'scheduling/PrintClassLists.php', 'Y', NULL),
('2', 'scheduling/PrintClassPictures.php', 'Y', NULL),
('2', 'grades/InputFinalGrades.php', 'Y', NULL),
('2', 'grades/ReportCards.php', 'Y', NULL),
('2', 'grades/grades.php', 'Y', NULL),
('2', 'grades/Assignments.php', 'Y', NULL),
('2', 'grades/AnomalousGrades.php', 'Y', NULL),
('2', 'grades/Configuration.php', 'Y', NULL),
('2', 'grades/ProgressReports.php', 'Y', NULL),
('2', 'grades/StudentGrades.php', 'Y', NULL),
('2', 'grades/FinalGrades.php', 'Y', NULL),
('2', 'grades/ReportCardGrades.php', 'Y', NULL),
('2', 'grades/ReportCardComments.php', 'Y', NULL),
('2', 'attendance/TakeAttendance.php', 'Y', NULL),
('2', 'attendance/DailySummary.php', 'Y', NULL),
('2', 'attendance/StudentSummary.php', 'Y', NULL),
('2', 'eligibility/EnterEligibility.php', 'Y', NULL),
('2', 'scheduling/ViewSchedule.php', 'Y', NULL),
('4', 'attendance/StudentSummary.php', 'Y', NULL),
('4', 'attendance/DailySummary.php', 'Y', NULL),
('4', 'eligibility/Student.php', 'Y', NULL),
('4', 'eligibility/StudentList.php', 'Y', NULL),
('4', 'schoolsetup/Schools.php', 'Y', NULL),
('4', 'schoolsetup/Calendar.php', 'Y', NULL),
('4', 'students/Student.php', 'Y', NULL),
('4', 'students/Student.php&category_id=1', 'Y', NULL),
('4', 'students/Student.php&category_id=3', 'Y', 'Y'),
('4', 'users/User.php', 'Y', NULL),
('4', 'users/User.php&category_id=1', 'Y', 'Y'),
('4', 'users/Preferences.php', 'Y', NULL),
('4', 'scheduling/ViewSchedule.php', 'Y', NULL),
('4', 'scheduling/Requests.php', 'Y', 'Y'),
('4', 'grades/StudentGrades.php', 'Y', NULL),
('4', 'grades/FinalGrades.php', 'Y', NULL),
('4', 'grades/ReportCards.php', 'Y', NULL),
('4', 'grades/Transcripts.php', 'Y', NULL),
('4', 'grades/GPARankList.php', 'Y', NULL),
('4', 'users/User.php&category_id=2', 'Y', NULL),
('4', 'users/User.php&category_id=3', 'Y', NULL),
('2', 'schoolsetup/Courses.php', 'Y', NULL),
('2', 'schoolsetup/CourseCatalog.php', 'Y', NULL),
('2', 'schoolsetup/PrintCatalog.php', 'Y', NULL),
('2', 'schoolsetup/PrintAllCourses.php', 'Y', NULL),
('2', 'students/Student.php&category_id=5', 'Y', 'Y'),
('4', 'students/ChangePassword.php', 'Y', NULL),
('4', 'scheduling/StudentScheduleReport.php', 'Y', NULL),
('2', 'students/Student.php&category_id=2', 'Y', NULL),
('4', 'students/Student.php&category_id=4', 'Y', NULL),
('3', 'scheduling/StudentScheduleReport.php', 'Y', NULL),
('3', 'Billing/Fee.php', 'Y', NULL),
('3', 'Billing/Balance_Report.php', 'Y', NULL),
('3', 'Billing/DailyTransactions.php', 'Y', NULL),
('4', 'Billing/Fee.php', 'Y', NULL),
('4', 'Billing/Balance_Report.php', 'Y', NULL),
('4', 'Billing/DailyTransactions.php', 'Y', NULL),
('5', 'schoolsetup/PortalNotes.php', 'Y', 'Y'),
('5', 'schoolsetup/MarkingPeriods.php', 'Y', NULL),
('5', 'schoolsetup/Calendar.php', 'Y', 'Y'),
('5', 'schoolsetup/Periods.php', 'Y', NULL),
('5', 'schoolsetup/GradeLevels.php', 'Y', NULL),
('5', 'schoolsetup/Schools.php', 'Y', NULL),
('5', 'schoolsetup/UploadLogo.php', 'Y', NULL),
('5', 'schoolsetup/Schools.php?new_school=true', 'Y', NULL),
('5', 'schoolsetup/CopySchool.php', 'Y', NULL),
('5', 'schoolsetup/SystemPreference.php', 'Y', NULL),
('5', 'schoolsetup/Courses.php', 'Y', NULL),
('5', 'schoolsetup/CourseCatalog.php', 'Y', NULL),
('5', 'schoolsetup/PrintCatalog.php', 'Y', NULL),
('5', 'schoolsetup/PrintCatalogGradeLevel.php', 'Y', NULL),
('5', 'schoolsetup/PrintAllCourses.php', 'Y', NULL),
('5', 'schoolsetup/TeacherReassignment.php', 'Y', NULL),
('5', 'students/Student.php', 'Y', 'Y'),
('5', 'students/Student.php&include=GeneralInfoInc&student_id=new', 'Y', 'Y'),
('5', 'students/AssignOtherInfo.php', 'Y', 'Y'),
('5', 'students/AddUsers.php', 'Y', 'Y'),
('5', 'students/AdvancedReport.php', 'Y', 'Y'),
('5', 'students/AddDrop.php', 'Y', 'Y'),
('5', 'students/Letters.php', 'Y', 'Y'),
('5', 'students/MailingLabels.php', 'Y', 'Y'),
('5', 'students/StudentLabels.php', 'Y', 'Y'),
('5', 'students/PrintStudentInfo.php', 'Y', 'Y'),
('5', 'students/PrintStudentContactInfo.php', 'Y', 'Y'),
('5', 'students/GoalReport.php', 'Y', 'Y'),
('5', 'students/StudentFields.php', 'Y', NULL),
('5', 'students/EnrollmentCodes.php', 'Y', 'Y'),
('5', 'students/Upload.php', 'Y', 'Y'),
('5', 'students/Upload.php?modfunc=edit', 'Y', 'Y'),
('5', 'students/Student.php&category_id=1', 'Y', 'Y'),
('5', 'students/Student.php&category_id=2', 'Y', 'Y'),
('5', 'students/Student.php&category_id=3', 'Y', 'Y'),
('5', 'students/Student.php&category_id=4', 'Y', 'Y'),
('5', 'students/Student.php&category_id=5', 'Y', 'Y'),
('5', 'users/User.php', 'Y', 'Y'),
('5', 'users/User.php&staff_id=new', 'Y', NULL),
('5', 'users/AddStudents.php', 'Y', NULL),
('5', 'users/Preferences.php', 'Y', NULL),
('5', 'users/Profiles.php', 'Y', NULL),
('5', 'users/Exceptions.php', 'Y', NULL),
('5', 'users/UserFields.php', 'Y', NULL),
('5', 'users/TeacherPrograms.php?include=grades/InputFinalGrades.php', 'Y', NULL),
('5', 'users/TeacherPrograms.php?include=grades/grades.php', 'Y', NULL),
('5', 'users/TeacherPrograms.php?include=grades/ProgressReports.php', 'Y', 'Y'),
('5', 'users/TeacherPrograms.php?include=attendance/TakeAttendance.php', 'Y', 'Y'),
('5', 'users/TeacherPrograms.php?include=attendance/Missing_Attendance.php', 'Y', 'Y'),
('5', 'users/TeacherPrograms.php?include=eligibility/EnterEligibility.php', 'Y', NULL),
('5', 'users/User.php&category_id=1', 'Y', 'Y'),
('5', 'users/User.php&category_id=2', 'Y', 'Y'),
('5', 'scheduling/Schedule.php', 'Y', NULL),
('5', 'scheduling/ViewSchedule.php', 'Y', NULL),
('5', 'scheduling/Requests.php', 'Y', NULL),
('5', 'scheduling/MassSchedule.php', 'Y', NULL),
('5', 'scheduling/MassRequests.php', 'Y', NULL),
('5', 'scheduling/MassDrops.php', 'Y', NULL),
('5', 'scheduling/PrintSchedules.php', 'Y', 'Y'),
('5', 'scheduling/PrintClassLists.php', 'Y', 'Y'),
('5', 'scheduling/PrintClassPictures.php', 'Y', NULL),
('5', 'scheduling/PrintRequests.php', 'Y', NULL),
('5', 'scheduling/ScheduleReport.php', 'Y', NULL),
('5', 'scheduling/RequestsReport.php', 'Y', NULL),
('5', 'scheduling/UnfilledRequests.php', 'Y', NULL),
('5', 'scheduling/IncompleteSchedules.php', 'Y', NULL),
('5', 'scheduling/AddDrop.php', 'Y', NULL),
('5', 'scheduling/Scheduler.php', 'Y', NULL),
('5', 'grades/ReportCards.php', 'Y', 'Y'),
('5', 'grades/CalcGPA.php', 'Y', 'Y'),
('5', 'grades/Transcripts.php', 'Y', 'Y'),
('5', 'grades/TeacherCompletion.php', 'Y', NULL),
('5', 'grades/GradeBreakdown.php', 'Y', NULL),
('5', 'grades/FinalGrades.php', 'Y', NULL),
('5', 'grades/GPARankList.php', 'Y', NULL),
('5', 'grades/AdminProgressReports.php', 'Y', NULL),
('5', 'grades/HonorRoll.php', 'Y', NULL),
('5', 'grades/ReportCardGrades.php', 'Y', 'Y'),
('5', 'grades/ReportCardComments.php', 'Y', 'Y'),
('5', 'grades/HonorRollSetup.php', 'Y', 'Y'),
('5', 'grades/FixGPA.php', 'Y', NULL),
('5', 'grades/EditReportCardGrades.php', 'Y', NULL),
('5', 'grades/EditHistoryMarkingPeriods.php', 'Y', NULL),
('5', 'attendance/Administration.php', 'Y', 'Y'),
('5', 'attendance/AddAbsences.php', 'Y', 'Y'),
('5', 'attendance/AttendanceData.php?list_by_day=true', 'Y', 'Y'),
('5', 'attendance/Percent.php', 'Y', 'Y'),
('5', 'attendance/Percent.php?list_by_day=true', 'Y', 'Y'),
('5', 'attendance/DailySummary.php', 'Y', 'Y'),
('5', 'attendance/StudentSummary.php', 'Y', 'Y'),
('5', 'attendance/TeacherCompletion.php', 'Y', 'Y'),
('5', 'attendance/FixDailyAttendance.php', 'Y', 'Y'),
('5', 'attendance/DuplicateAttendance.php', 'Y', 'Y'),
('5', 'attendance/AttendanceCodes.php', 'Y', 'Y'),
('5', 'eligibility/Student.php', 'Y', NULL),
('5', 'eligibility/AddActivity.php', 'Y', NULL),
('5', 'eligibility/StudentList.php', 'Y', NULL),
('5', 'eligibility/TeacherCompletion.php', 'Y', NULL),
('5', 'eligibility/Activities.php', 'Y', NULL),
('5', 'eligibility/EntryTimes.php', 'Y', NULL),
('5', 'Billing/LedgerCard.php', 'Y', 'Y'),
('5', 'Billing/Balance_Report.php', 'Y', 'Y'),
('5', 'Billing/DailyTransactions.php', 'Y', 'Y'),
('5', 'Billing/PaymentHistory.php', 'Y', 'Y'),
('5', 'Billing/Fee.php', 'Y', 'Y'),
('5', 'Billing/StudentPayments.php', 'Y', 'Y'),
('5', 'Billing/MassAssignFees.php', 'Y', 'Y'),
('5', 'Billing/MassAssignPayments.php', 'Y', 'Y'),
('5', 'Billing/SetUp.php', 'Y', 'Y'),
('5', 'Billing/SetUp_FeeType.php', 'Y', 'Y'),
('5', 'Billing/SetUp_PayPal.php', 'Y', 'Y'),
('5', 'tools/LogDetails.php', 'Y', 'Y'),
('5', 'tools/DeleteLog.php', 'Y', 'Y'),
('5', 'tools/Rollover.php', 'Y', 'Y'),
('5', 'tools/Backup.php', 'Y', 'Y'),
('1', 'schoolsetup/SchoolCustomFields.php', 'Y', 'Y'),
('1', 'students/Student.php&category_id=6', 'Y', 'Y'),
('1', 'users/User.php&category_id=5', 'Y', 'Y'),
('1', 'schoolsetup/PortalNotes.php', 'Y', 'Y'),
('1', 'schoolsetup/Schools.php', 'Y', 'Y'),
('1', 'schoolsetup/Schools.php?new_school=true', 'Y', 'Y'),
('1', 'schoolsetup/CopySchool.php', 'Y', 'Y'),
('1', 'schoolsetup/MarkingPeriods.php', 'Y', 'Y'),
('1', 'schoolsetup/Calendar.php', 'Y', 'Y'),
('1', 'schoolsetup/Periods.php', 'Y', 'Y'),
('1', 'schoolsetup/GradeLevels.php', 'Y', 'Y'),
('1', 'schoolsetup/Rollover.php', 'Y', 'Y'),
('1', 'schoolsetup/Courses.php', 'Y', 'Y'),
('1', 'schoolsetup/CourseCatalog.php', 'Y', 'Y'),
('1', 'schoolsetup/PrintCatalog.php', 'Y', 'Y'),
('1', 'schoolsetup/PrintCatalogGradeLevel.php', 'Y', 'Y'),
('1', 'schoolsetup/PrintAllCourses.php', 'Y', 'Y'),
('1', 'schoolsetup/UploadLogo.php', 'Y', 'Y'),
('1', 'schoolsetup/TeacherReassignment.php', 'Y', 'Y'),
('1', 'students/Student.php', 'Y', 'Y'),
('1', 'students/Student.php&include=GeneralInfoInc&student_id=new', 'Y', 'Y'),
('1', 'students/AssignOtherInfo.php', 'Y', 'Y'),
('1', 'students/AddUsers.php', 'Y', 'Y'),
('1', 'students/AdvancedReport.php', 'Y', 'Y'),
('1', 'students/AddDrop.php', 'Y', 'Y'),
('1', 'students/Letters.php', 'Y', 'Y'),
('1', 'students/MailingLabels.php', 'Y', 'Y'),
('1', 'students/StudentLabels.php', 'Y', 'Y'),
('1', 'students/PrintStudentInfo.php', 'Y', 'Y'),
('1', 'students/PrintStudentContactInfo.php', 'Y', 'Y'),
('1', 'students/GoalReport.php', 'Y', 'Y'),
('1', 'students/StudentFields.php', 'Y', 'Y'),
('1', 'students/AddressFields.php', 'Y', 'Y'),
('1', 'students/PeopleFields.php', 'Y', 'Y'),
('1', 'students/EnrollmentCodes.php', 'Y', 'Y'),
('1', 'students/Upload.php?modfunc=edit', 'Y', 'Y'),
('1', 'students/Upload.php', 'Y', 'Y'),
('1', 'students/Student.php&category_id=1', 'Y', 'Y'),
('1', 'students/Student.php&category_id=3', 'Y', 'Y'),
('1', 'students/Student.php&category_id=2', 'Y', 'Y'),
('1', 'students/Student.php&category_id=4', 'Y', 'Y'),
('1', 'students/StudentReenroll.php', 'Y', 'Y'),
('1', 'students/EnrollmentReport.php', 'Y', 'Y'),
('1', 'users/User.php', 'Y', 'Y'),
('1', 'users/User.php&category_id=1', 'Y', 'Y'),
('1', 'users/User.php&category_id=2', 'Y', 'Y'),
('1', 'users/User.php&staff_id=new', 'Y', 'Y'),
('1', 'users/AddStudents.php', 'Y', 'Y'),
('1', 'users/Preferences.php', 'Y', 'Y'),
('1', 'users/Profiles.php', 'Y', 'Y'),
('1', 'users/Exceptions.php', 'Y', 'Y'),
('1', 'users/UserFields.php', 'Y', 'Y'),
('1', 'users/TeacherPrograms.php?include=grades/InputFinalGrades.php', 'Y', 'Y'),
('1', 'users/TeacherPrograms.php?include=grades/grades.php', 'Y', 'Y'),
('1', 'users/TeacherPrograms.php?include=attendance/TakeAttendance.php', 'Y', 'Y'),
('1', 'users/TeacherPrograms.php?include=attendance/Missing_Attendance.php', 'Y', 'Y'),
('1', 'users/TeacherPrograms.php?include=eligibility/EnterEligibility.php', 'Y', 'Y'),
('1', 'users/UploadUserPhoto.php', 'Y', 'Y'),
('1', 'users/UploadUserPhoto.php?modfunc=edit', 'Y', 'Y'),
('1', 'users/UserAdvancedReport.php', 'Y', 'Y'),
('1', 'scheduling/Schedule.php', 'Y', 'Y'),
('1', 'scheduling/Requests.php', 'Y', 'Y'),
('1', 'scheduling/MassSchedule.php', 'Y', 'Y'),
('1', 'scheduling/MassRequests.php', 'Y', 'Y'),
('1', 'scheduling/MassDrops.php', 'Y', 'Y'),
('1', 'scheduling/ScheduleReport.php', 'Y', 'Y'),
('1', 'scheduling/RequestsReport.php', 'Y', 'Y'),
('1', 'scheduling/UnfilledRequests.php', 'Y', 'Y'),
('1', 'scheduling/IncompleteSchedules.php', 'Y', 'Y'),
('1', 'scheduling/AddDrop.php', 'Y', 'Y'),
('1', 'scheduling/PrintSchedules.php', 'Y', 'Y'),
('1', 'scheduling/PrintRequests.php', 'Y', 'Y'),
('1', 'scheduling/PrintClassLists.php', 'Y', 'Y'),
('1', 'scheduling/PrintClassPictures.php', 'Y', 'Y'),
('1', 'scheduling/Courses.php', 'Y', 'Y'),
('1', 'scheduling/Scheduler.php', 'Y', 'Y'),
('1', 'scheduling/ViewSchedule.php', 'Y', 'Y'),
('1', 'grades/ReportCards.php', 'Y', 'Y'),
('1', 'grades/CalcGPA.php', 'Y', 'Y'),
('1', 'grades/Transcripts.php', 'Y', 'Y'),
('1', 'grades/TeacherCompletion.php', 'Y', 'Y'),
('1', 'grades/GradeBreakdown.php', 'Y', 'Y'),
('1', 'grades/FinalGrades.php', 'Y', 'Y'),
('1', 'grades/GPARankList.php', 'Y', 'Y'),
('1', 'grades/ReportCardGrades.php', 'Y', 'Y'),
('1', 'grades/ReportCardComments.php', 'Y', 'Y'),
('1', 'grades/FixGPA.php', 'Y', 'Y'),
('1', 'grades/EditReportCardGrades.php', 'Y', 'Y'),
('1', 'grades/EditHistoryMarkingPeriods.php', 'Y', 'Y'),
('1', 'grades/HistoricalReportCardGrades.php', 'Y', 'Y'),
('1', 'attendance/Administration.php', 'Y', 'Y'),
('1', 'attendance/AddAbsences.php', 'Y', 'Y'),
('1', 'attendance/AttendanceData.php?list_by_day=true', 'Y', 'Y'),
('1', 'attendance/Percent.php', 'Y', 'Y'),
('1', 'attendance/Percent.php?list_by_day=true', 'Y', 'Y'),
('1', 'attendance/DailySummary.php', 'Y', 'Y'),
('1', 'attendance/StudentSummary.php', 'Y', 'Y'),
('1', 'attendance/TeacherCompletion.php', 'Y', 'Y'),
('1', 'attendance/DuplicateAttendance.php', 'Y', 'Y'),
('1', 'attendance/AttendanceCodes.php', 'Y', 'Y'),
('1', 'attendance/FixDailyAttendance.php', 'Y', 'Y'),
('1', 'eligibility/Student.php', 'Y', 'Y'),
('1', 'eligibility/AddActivity.php', 'Y', 'Y'),
('1', 'eligibility/StudentList.php', 'Y', 'Y'),
('1', 'eligibility/TeacherCompletion.php', 'Y', 'Y'),
('1', 'eligibility/Activities.php', 'Y', 'Y'),
('1', 'eligibility/EntryTimes.php', 'Y', 'Y'),
('1', 'tools/LogDetails.php', 'Y', 'Y'),
('1', 'tools/DeleteLog.php', 'Y', 'Y'),
('2', 'users/Staff.php&category_id=5', 'Y', NULL),
('1', 'tools/Rollover.php', 'Y', 'Y'),
('1', 'students/Upload.php', 'Y', 'Y'),
('1', 'students/Upload.php?modfunc=edit', 'Y', 'Y'),
('1', 'schoolsetup/SystemPreference.php', 'Y', 'Y'),
('1', 'students/Student.php&category_id=5', 'Y', 'Y'),
('1', 'grades/HonorRoll.php', 'Y', 'Y'),
('1', 'users/TeacherPrograms.php?include=grades/ProgressReports.php', 'Y', 'Y'),
('1', 'users/User.php&category_id=2', 'Y', 'Y'),
('1', 'grades/HonorRollSetup.php', 'Y', 'Y'),
('1', 'grades/AdminProgressReports.php', 'Y', 'Y'),
('1', 'Billing/LedgerCard.php', 'Y', 'Y'),
('1', 'Billing/Balance_Report.php', 'Y', 'Y'),
('1', 'Billing/DailyTransactions.php', 'Y', 'Y'),
('1', 'Billing/PaymentHistory.php', 'Y', 'Y'),
('1', 'Billing/Fee.php', 'Y', 'Y'),
('1', 'Billing/StudentPayments.php', 'Y', 'Y'),
('1', 'Billing/MassAssignFees.php', 'Y', 'Y'),
('1', 'Billing/MassAssignPayments.php', 'Y', 'Y'),
('1', 'Billing/SetUp.php', 'Y', 'Y'),
('1', 'Billing/SetUp_FeeType.php', 'Y', 'Y'),
('1', 'Billing/SetUp_PayPal.php', 'Y', 'Y'),
('1', 'users/Staff.php', 'Y', 'Y'),
('1', 'users/Staff.php&staff_id=new', 'Y', 'Y'),
('1', 'users/Exceptions_staff.php', 'Y', 'Y'),
('1', 'users/StaffFields.php', 'Y', 'Y'),
('1', 'users/Staff.php&category_id=1', 'Y', 'Y'),
('1', 'users/Staff.php&category_id=2', 'Y', 'Y'),
('1', 'users/Staff.php&category_id=3', 'Y', 'Y'),
('1', 'users/Staff.php&category_id=4', 'Y', 'Y'),
('1', 'messaging/Inbox.php', 'Y', 'Y'),
('1', 'messaging/Compose.php', 'Y', 'Y'),
('1', 'messaging/SentMail.php', 'Y', 'Y'),
('1', 'messaging/Trash.php', 'Y', 'Y'),
('1', 'messaging/Group.php', 'Y', 'Y'),
('4', 'messaging/Inbox.php', 'Y', NULL),
('4', 'messaging/Compose.php', 'Y', NULL),
('4', 'messaging/SentMail.php', 'Y', NULL),
('4', 'messaging/Trash.php', 'Y', NULL),
('4', 'messaging/Group.php', 'Y', NULL),
('2', 'messaging/Inbox.php', 'Y', NULL),
('2', 'messaging/Compose.php', 'Y', NULL),
('2', 'messaging/SentMail.php', 'Y', NULL),
('2', 'messaging/Trash.php', 'Y', NULL),
('2', 'messaging/Group.php', 'Y', NULL),
('3', 'messaging/Inbox.php', 'Y', NULL),
('3', 'messaging/Compose.php', 'Y', NULL),
('3', 'messaging/SentMail.php', 'Y', NULL),
('3', 'messaging/Trash.php', 'Y', NULL),
('3', 'messaging/Group.php', 'Y', NULL),
('0', 'students/Student.php&category_id=6', 'Y', 'Y'),
('0', 'users/User.php&category_id=5', 'Y', 'Y'),
('0', 'schoolsetup/PortalNotes.php', 'Y', 'Y'),
('0', 'schoolsetup/Schools.php', 'Y', 'Y'),
('0', 'schoolsetup/Schools.php?new_school=true', 'Y', 'Y'),
('0', 'schoolsetup/CopySchool.php', 'Y', 'Y'),
('0', 'schoolsetup/MarkingPeriods.php', 'Y', 'Y'),
('0', 'schoolsetup/Calendar.php', 'Y', 'Y'),
('0', 'schoolsetup/Periods.php', 'Y', 'Y'),
('0', 'schoolsetup/GradeLevels.php', 'Y', 'Y'),
('0', 'schoolsetup/Rollover.php', 'Y', 'Y'),
('0', 'schoolsetup/Courses.php', 'Y', 'Y'),
('0', 'schoolsetup/CourseCatalog.php', 'Y', 'Y'),
('0', 'schoolsetup/PrintCatalog.php', 'Y', 'Y'),
('0', 'schoolsetup/PrintCatalogGradeLevel.php', 'Y', 'Y'),
('0', 'schoolsetup/PrintAllCourses.php', 'Y', 'Y'),
('0', 'schoolsetup/UploadLogo.php', 'Y', 'Y'),
('0', 'schoolsetup/TeacherReassignment.php', 'Y', 'Y'),
('0', 'students/Student.php', 'Y', 'Y'),
('0', 'students/Student.php&include=GeneralInfoInc&student_id=new', 'Y', 'Y'),
('0', 'students/AssignOtherInfo.php', 'Y', 'Y'),
('0', 'students/AddUsers.php', 'Y', 'Y'),
('0', 'students/AdvancedReport.php', 'Y', 'Y'),
('0', 'students/AddDrop.php', 'Y', 'Y'),
('0', 'students/Letters.php', 'Y', 'Y'),
('0', 'students/MailingLabels.php', 'Y', 'Y'),
('0', 'students/StudentLabels.php', 'Y', 'Y'),
('0', 'students/PrintStudentInfo.php', 'Y', 'Y'),
('0', 'students/PrintStudentContactInfo.php', 'Y', 'Y'),
('0', 'students/GoalReport.php', 'Y', 'Y'),
('0', 'students/StudentFields.php', 'Y', 'Y'),
('0', 'students/AddressFields.php', 'Y', 'Y'),
('0', 'students/PeopleFields.php', 'Y', 'Y'),
('0', 'students/EnrollmentCodes.php', 'Y', 'Y'),
('0', 'students/Upload.php?modfunc=edit', 'Y', 'Y'),
('0', 'students/Upload.php', 'Y', 'Y'),
('0', 'students/Student.php&category_id=1', 'Y', 'Y'),
('0', 'students/Student.php&category_id=3', 'Y', 'Y'),
('0', 'students/Student.php&category_id=2', 'Y', 'Y'),
('0', 'students/Student.php&category_id=4', 'Y', 'Y'),
('0', 'students/StudentReenroll.php', 'Y', 'Y'),
('0', 'students/EnrollmentReport.php', 'Y', 'Y'),
('0', 'users/User.php', 'Y', 'Y'),
('0', 'users/User.php&category_id=1', 'Y', 'Y'),
('0', 'users/User.php&category_id=2', 'Y', 'Y'),
('0', 'users/User.php&staff_id=new', 'Y', 'Y'),
('0', 'users/AddStudents.php', 'Y', 'Y'),
('0', 'users/Preferences.php', 'Y', 'Y'),
('0', 'users/Profiles.php', 'Y', 'Y'),
('0', 'users/Exceptions.php', 'Y', 'Y'),
('0', 'users/UserFields.php', 'Y', 'Y'),
('0', 'users/TeacherPrograms.php?include=grades/InputFinalGrades.php', 'Y', 'Y'),
('0', 'users/TeacherPrograms.php?include=grades/grades.php', 'Y', 'Y'),
('0', 'users/TeacherPrograms.php?include=attendance/TakeAttendance.php', 'Y', 'Y'),
('0', 'users/TeacherPrograms.php?include=attendance/Missing_Attendance.php', 'Y', 'Y'),
('0', 'users/TeacherPrograms.php?include=eligibility/EnterEligibility.php', 'Y', 'Y'),
('0', 'users/UploadUserPhoto.php', 'Y', 'Y'),
('0', 'users/UploadUserPhoto.php?modfunc=edit', 'Y', 'Y'),
('0', 'users/UserAdvancedReport.php', 'Y', 'Y'),
('0', 'scheduling/Schedule.php', 'Y', 'Y'),
('0', 'scheduling/Requests.php', 'Y', 'Y'),
('0', 'scheduling/MassSchedule.php', 'Y', 'Y'),
('0', 'scheduling/MassRequests.php', 'Y', 'Y'),
('0', 'scheduling/MassDrops.php', 'Y', 'Y'),
('0', 'scheduling/ScheduleReport.php', 'Y', 'Y'),
('0', 'scheduling/RequestsReport.php', 'Y', 'Y'),
('0', 'scheduling/UnfilledRequests.php', 'Y', 'Y'),
('0', 'scheduling/IncompleteSchedules.php', 'Y', 'Y'),
('0', 'scheduling/AddDrop.php', 'Y', 'Y'),
('0', 'scheduling/PrintSchedules.php', 'Y', 'Y'),
('0', 'scheduling/PrintRequests.php', 'Y', 'Y'),
('0', 'scheduling/PrintClassLists.php', 'Y', 'Y'),
('0', 'scheduling/PrintClassPictures.php', 'Y', 'Y'),
('0', 'scheduling/Courses.php', 'Y', 'Y'),
('0', 'scheduling/Scheduler.php', 'Y', 'Y'),
('0', 'scheduling/ViewSchedule.php', 'Y', 'Y'),
('0', 'grades/ReportCards.php', 'Y', 'Y'),
('0', 'grades/CalcGPA.php', 'Y', 'Y'),
('0', 'grades/Transcripts.php', 'Y', 'Y'),
('0', 'grades/TeacherCompletion.php', 'Y', 'Y'),
('0', 'grades/GradeBreakdown.php', 'Y', 'Y'),
('0', 'grades/FinalGrades.php', 'Y', 'Y'),
('0', 'grades/GPARankList.php', 'Y', 'Y'),
('0', 'grades/ReportCardGrades.php', 'Y', 'Y'),
('0', 'grades/ReportCardComments.php', 'Y', 'Y'),
('0', 'grades/FixGPA.php', 'Y', 'Y'),
('0', 'grades/EditReportCardGrades.php', 'Y', 'Y'),
('0', 'grades/EditHistoryMarkingPeriods.php', 'Y', 'Y'),
('0', 'grades/HistoricalReportCardGrades.php', 'Y', 'Y'),
('0', 'attendance/Administration.php', 'Y', 'Y'),
('0', 'attendance/AddAbsences.php', 'Y', 'Y'),
('0', 'attendance/AttendanceData.php?list_by_day=true', 'Y', 'Y'),
('0', 'attendance/Percent.php', 'Y', 'Y'),
('0', 'attendance/Percent.php?list_by_day=true', 'Y', 'Y'),
('0', 'attendance/DailySummary.php', 'Y', 'Y'),
('0', 'attendance/StudentSummary.php', 'Y', 'Y'),
('0', 'attendance/TeacherCompletion.php', 'Y', 'Y'),
('0', 'attendance/DuplicateAttendance.php', 'Y', 'Y'),
('0', 'attendance/AttendanceCodes.php', 'Y', 'Y'),
('0', 'attendance/FixDailyAttendance.php', 'Y', 'Y'),
('0', 'eligibility/Student.php', 'Y', 'Y'),
('0', 'eligibility/AddActivity.php', 'Y', 'Y'),
('0', 'eligibility/StudentList.php', 'Y', 'Y'),
('0', 'eligibility/TeacherCompletion.php', 'Y', 'Y'),
('0', 'eligibility/Activities.php', 'Y', 'Y'),
('0', 'eligibility/EntryTimes.php', 'Y', 'Y'),
('0', 'tools/LogDetails.php', 'Y', 'Y'),
('0', 'tools/DeleteLog.php', 'Y', 'Y'),
('0', 'tools/Backup.php', 'Y', 'Y'),
('0', 'tools/Rollover.php', 'Y', 'Y'),
('0', 'students/Upload.php', 'Y', 'Y'),
('0', 'students/Upload.php?modfunc=edit', 'Y', 'Y'),
('0', 'schoolsetup/SystemPreference.php', 'Y', 'Y'),
('0', 'students/Student.php&category_id=5', 'Y', 'Y'),
('0', 'grades/HonorRoll.php', 'Y', 'Y'),
('0', 'users/TeacherPrograms.php?include=grades/ProgressReports.php', 'Y', 'Y'),
('0', 'users/User.php&category_id=2', 'Y', 'Y'),
('0', 'grades/HonorRollSetup.php', 'Y', 'Y'),
('0', 'grades/AdminProgressReports.php', 'Y', 'Y'),
('0', 'Billing/LedgerCard.php', 'Y', 'Y'),
('0', 'Billing/Balance_Report.php', 'Y', 'Y'),
('0', 'Billing/DailyTransactions.php', 'Y', 'Y'),
('0', 'Billing/PaymentHistory.php', 'Y', 'Y'),
('0', 'Billing/Fee.php', 'Y', 'Y'),
('0', 'Billing/StudentPayments.php', 'Y', 'Y'),
('0', 'Billing/MassAssignFees.php', 'Y', 'Y'),
('0', 'Billing/MassAssignPayments.php', 'Y', 'Y'),
('0', 'Billing/SetUp.php', 'Y', 'Y'),
('0', 'Billing/SetUp_FeeType.php', 'Y', 'Y'),
('0', 'Billing/SetUp_PayPal.php', 'Y', 'Y'),
('0', 'users/Staff.php', 'Y', 'Y'),
('0', 'users/Staff.php&staff_id=new', 'Y', 'Y'),
('0', 'users/Exceptions_staff.php', 'Y', 'Y'),
('0', 'users/StaffFields.php', 'Y', 'Y'),
('0', 'users/Staff.php&category_id=1', 'Y', 'Y'),
('0', 'users/Staff.php&category_id=2', 'Y', 'Y'),
('0', 'users/Staff.php&category_id=3', 'Y', 'Y'),
('0', 'users/Staff.php&category_id=4', 'Y', 'Y'),
('0', 'schoolsetup/SchoolCustomFields.php', 'Y', 'Y'),
('0', 'messaging/Inbox.php', 'Y', 'Y'),
('0', 'messaging/Compose.php', 'Y', 'Y'),
('0', 'messaging/SentMail.php', 'Y', 'Y'),
('0', 'messaging/Trash.php', 'Y', 'Y'),
('0', 'messaging/Group.php', 'Y', 'Y'),
('0', 'schoolsetup/Rooms.php', 'Y', 'Y'),
('0', 'schoolsetup/school_specific_standards.php', 'Y', 'Y'),
('0', 'users/TeacherPrograms.php?include=grades/AdminProgressReports.php', 'Y', 'Y'),
('0', 'tools/Reports.php?func=Basic', 'Y', 'Y'),
('0', 'tools/Reports.php?func=Ins_r', 'Y', 'Y'),
('0', 'tools/Reports.php?func=Ins_cf', 'Y', 'Y'),
('0', 'schoolsetup/us_common_standards.php', 'Y', 'Y'),
('0', 'schoolsetup/EffortGradeLibrary.php', 'Y', 'Y'),
('0', 'grades/EffortGradeSetup.php', 'Y', 'Y'),
('0', 'users/TeacherPrograms.php?include=attendance/MissingAttendance.php', 'Y', 'Y'),
('2', 'students/StudentLabels.php', 'Y', NULL),
('2', 'users/Staff.php', 'Y', NULL),
('2', 'users/Staff.php&category_id=3', 'Y', NULL),
('2', 'users/Staff.php&category_id=4', 'Y', NULL),
('2', 'grades/Grades.php', 'Y', NULL),
('1', 'schoolsetup/Rooms.php', 'Y', 'Y'),
('1', 'users/TeacherPrograms.php?include=attendance/MissingAttendance.php', 'Y', 'Y'),
('0', 'users/Staff.php&category_id=5', 'Y', 'Y')");

//------------------------------truncate---------------------------//

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
$sql = "SHOW FULL TABLES IN `$mysql_database` WHERE TABLE_TYPE LIKE 'VIEW';";
$result = mysql_query($sql);
$views=array();
while ($row = mysql_fetch_row($result)) {

    mysql_query('DROP VIEW '.$row[0]);
}
$date_time=date("m-d-Y");
         $mysql_database;

    $Export_FileName=$mysql_database.'_'.$date_time.'_6.0_update.sql';

	    

 exec("mysqldump -n -t -c --skip-add-locks --skip-disable-keys --skip-triggers --user $dbUser --password='$dbPass' $mysql_database > $Export_FileName");
    
	
    mysql_query("drop database $mysql_database");
     
	mysql_query("CREATE DATABASE $mysql_database CHARACTER SET utf8 COLLATE utf8_general_ci");
    
	mysql_select_db($mysql_database);
        
          $myFile = "OpensisSchemaMysqlIncUpdate.sql";
        
    executeSQL($myFile);

     
   if(file_exists("CustomField.sql"))
   {
    $dummyFile3 = "CustomField.sql";


                        exec("mysql --user $dbUser --password='$dbPass' $mysql_database < $dummyFile3",$result,$status);
                        unlink( $dummyFile3);

   }
    $myFile_pro = "OpensisProcsMysqlInc.sql";
                    executeSQL($myFile_pro);
                   
                    exec("mysql --user $dbUser --password='$dbPass' $mysql_database < $Export_FileName",$result,$status);
                    unlink($Export_FileName);
                    $myFile_tr = "OpensisTriggerMysqlInc.sql";
                    executeSQL($myFile_tr);
                     
                   if(isset($_SESSION['extra_tab']) && $_SESSION['extra_tab']==1)
{
//                       
                   $Export_FileName_ex="dump_extra_back.sql";
                   exec("mysql --user $dbUser --password='$dbPass' $mysql_database < $Export_FileName_ex",$result,$status);
          unset($_SESSION['extra_tab']);
          $new_name="ExtraTableBackup".$mysql_database.$date_time.".sql";
         rename($Export_FileName_ex,$new_name);
}

//--------------------------------------staff joining date start-----------------------------------//
$qr_jo=mysql_query('select * from staff_school_info') or die(mysql_error().'error at 1640');
while($jo_fe=mysql_fetch_array($qr_jo))
{
     $staff_id=$jo_fe['staff_id'];
   
   
    $sch_qrr=mysql_query("select school_id,syear from staff_school_relationship where staff_id='$staff_id' limit 0,1") or die(mysql_error().'error at 1451');
$sch_qr=mysql_fetch_array($sch_qrr);
    $school_id=$sch_qr['school_id'];
$syear=$sch_qr['syear'];
$jo_date_qr=mysql_query("select start_date from marking_periods where school_id='$school_id' and syear='$syear'and mp_type='year'") or die(mysql_error().'error at 1454');
$jo_d_f=mysql_fetch_array($jo_date_qr);
$start_date=$jo_d_f['start_date'];
mysql_query("update staff_school_info set joining_date='$start_date' where staff_id='$staff_id'") or die(mysql_error().'error at 1457');
    
}
//----------------------------------------staff joining date end-----------------------------------//

//-----------------set sort order for staff_field_catagorey and student_field start------------------------------//
$stf_qrr=mysql_query("select * from staff_field_categories where sort_order=0");
while($f=mysql_fetch_array($stf_qrr))
{
    $max_qr=mysql_fetch_array(mysql_query("select max(sort_order) as mid from staff_field_categories"));
    $mid=$max_qr['mid']+1;
    mysql_query("update staff_field_categories set sort_order='$mid' where id='$f[id]'");
}
$stf_qr=mysql_query("select * from student_field_categories where sort_order=0");
while($f=mysql_fetch_array($stf_qr))
{
    $max_qr=mysql_fetch_array(mysql_query("select max(sort_order) as mid from student_field_categories"));
    $mid=$max_qr['mid']+1;
    mysql_query("update student_field_categories set sort_order='$mid' where id='$f[id]'");
}
//-----------------set sort order for staff_field_catagorey and student_field end------------------------------//
                header('Location: Step5.php');
              
                
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
                ?>
