<?php
#**************************************************************************
#  openSIS is a free student information system for publirc and non-public 
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

include('RedirectRootInc.php');
include("functions/ParamLibFnc.php");

$url=validateQueryString(curPageURL());
if($url===FALSE){
 header('Location: index.php');
 }
if($_REQUEST['modname']=='grades/Assignments.php' && $_REQUEST['assignment_id']!='' && isset($_REQUEST['tables'][$_REQUEST['assignment_id']]['DESCRIPTION']))
{
    $_SESSION['ASSIGNMENT_DESCRIPTION']=$_REQUEST['tables'][$_REQUEST['assignment_id']]['DESCRIPTION'];
}
$isajax="modules";
 $btn = optional_param('btn','',PARAM_ALPHA);
if($btn == 'Update' || $btn == ''){
	$btn = 'old';
}
$nsc = optional_param('nsc','',PARAM_SPCL);
if($_REQUEST['new_school']!='true')

{
	$ns = "NT";
}
else
{
	$ns = "TT";
}

$handle=opendir("js");
while ($file = readdir($handle)) {
$filelst = "$filelst,$file";
}
closedir($handle);
$filelist = explode(",",$filelst);

if(count($filelist)>3)
{
for ($count=1;$count<count($filelist);$count++) {
$filename=$filelist[$count];
if(($filename != ".") && ($filename != "..") && ($filename!=""))
echo "<script src='js/".$filename."'></script>";
}
}
	
echo "<script type='text/javascript'>
	function changeColors(){ 
        
        var aTags = document.getElementsByTagName(\"a\"); 
		
		
        for(i=0;i<aTags.length;i++){ 
        	if(document.getElementsByTagName('a')[i].id=='hm')
                document.getElementsByTagName('a')[i].className = 'submenuitem'; 
        } 
	} 
        function init(param,param2) {
        
        calendar.set('date_'+param);
        if(param2==2)
        {
        document.getElementById('date_'+param).style.display ='block';
        document.getElementById('date_div_'+param).style.display ='none';
        }
        document.getElementById('date_'+param).click();
        }
		
</script>";	


error_reporting(1);

$start_time = time();
include 'Warehouse.php';
$old_school = UserSchool();
$old_syear = UserSyear();





if((!$_SESSION['UserMP'] || (optional_param('school','',PARAM_SPCL) && optional_param('school','',PARAM_SPCL) != $old_school) || (optional_param('syear',0,PARAM_SPCL) && optional_param('syear',0,PARAM_SPCL) != $old_syear)) && User('PROFILE')!='parent')
	$_SESSION['UserMP'] = GetCurrentMP('QTR',DBDate());





array_rwalk($_REQUEST,'strip_tags');
//print_r($_REQUEST);
if(!isset($_REQUEST['_openSIS_PDF']))
{
	Warehouse('header');
$css = trim(getCSS());
echo "<link rel='stylesheet' type='text/css' href='themes/".trim(strtolower($css))."/".trim(ucwords($css)).".css'>";
echo "<link rel='stylesheet' type='text/css' href='styles/Help.css'>";
echo "<link rel='stylesheet' type='text/css' href='styles/CalendarMod.css'>";
if(strpos($_REQUEST['modname'],'miscellaneous/')===false)
		echo '<script language="JavaScript">if(window == top  && (!window.opener || window.opener.location.href.substring(0,(window.opener.location.href.indexOf("&")!=-1?window.opener.location.href.indexOf("&"):window.opener.location.href.replace("#","").length))!=window.location.href.substring(0,(window.location.href.indexOf("&")!=-1?window.location.href.indexOf("&"):window.location.href.replace("#","").length)))) window.location.href = "index.php";</script>';
	echo "<BODY onload='newLoad()' style='overflow-x:hidden; min-windth:900px;'>";
}		
		
echo "
<center>
  <table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"wrapper\">
    <tr>
      <td ><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
          <tr>
            <td class=\"banner\" valign=\"top\"><table width='100%' border='0' cellspacing='0' cellpadding='0'>
                <tr>
                  <td align=\"left\">&nbsp;</td>
                  <td align=\"right\"><div class=\"user_info\">".date('l F j, Y')." &nbsp;&nbsp;|&nbsp;&nbsp;".User('NAME')." &nbsp;&nbsp;|&nbsp;&nbsp; <a href='index.php?modfunc=logout' class='logout'>Log Out</a>";
/****************************************************************************************/
if(User('PROFILE')=='teacher')
{  
	echo "<br /><br />
	<table cellspacing=\"0\"><tr><td><FORM name=head_frm id=head_frm action=Side.php?modfunc=update&btnn=$btn&nsc=$ns&act=school method=POST><INPUT type=hidden name=modcat value='' id=modcat_input>";
$RET=  DBGet(DBQuery('SELECT s.ID,s.TITLE FROM schools s,staff st INNER JOIN staff_school_relationship ssr USING(staff_id) WHERE s.id=ssr.school_id AND ssr.syear=\''.UserSyear().'\' AND st.staff_id=\''.$_SESSION[STAFF_ID].'\' AND (ssr.END_DATE>=curdate() OR ssr.END_DATE=\'0000-00-00\')'));
echo "<SELECT name=school onChange='this.form.submit();'>";
foreach($RET as $school){
    echo "<OPTION style='padding-right:8px;' value=$school[ID]".((UserSchool()==$school['ID'])?' SELECTED':'').">".$school['TITLE']."</OPTION>";
}
echo "</SELECT>";
					
//===================================================================================================
echo "</FORM></td><td></td>";
echo "<td><FORM name=head_frm id=head_frm action=Side.php?modfunc=update&btnn=$btn&nsc=$ns&act=syear method=POST><INPUT type=hidden name=modcat value='' id=modcat_input>";
$school_years_RET=DBGet(DBQuery("SELECT YEAR(sy.START_DATE)AS START_DATE,YEAR(sy.END_DATE)AS END_DATE FROM school_years sy,staff st INNER JOIN staff_school_relationship ssr USING(staff_id) WHERE ssr.SYEAR=sy.SYEAR AND sy.school_id=ssr.school_id AND sy.school_id=".UserSchool()." AND st.staff_id=$_SESSION[STAFF_ID]"));
						 
echo "<SELECT name=syear onChange='this.form.submit();' style='width:80;'>";

foreach($school_years_RET as $school_years)
{
    echo "<OPTION value=$school_years[START_DATE]".((UserSyear()==$school_years['START_DATE'])?' SELECTED':'').">$school_years[START_DATE]".($school_years[END_DATE]!=$school_years[START_DATE]? "-".$school_years['END_DATE']:'').'</OPTION>';
}

echo '</SELECT>';
echo "</FORM></td><td></td>";

echo "<td><FORM name=head_frm id=head_frm action=Side.php?modfunc=update&btnn=$btn&nsc=$ns&act=mp method=POST><INPUT type=hidden name=modcat value='' id=modcat_input>";

$RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM school_quarters WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
if(!isset($_SESSION['UserMP']))
{
    $_SESSION['UserMP'] = GetCurrentMP('QTR',DBDate());
    $allMP='QTR';
}	
if(!$RET)
{
    $RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM school_semesters WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
    if(!isset($_SESSION['UserMP']))
    {
            $_SESSION['UserMP'] = GetCurrentMP('SEM',DBDate());
            $allMP='SEM';
    }	
}

if(!$RET)
{
    $RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM school_years WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
    if(!isset($_SESSION['UserMP']))
    {
            $_SESSION['UserMP'] = GetCurrentMP('FY',DBDate());
            $allMP='FY';
    }	
}
	

echo "<SELECT name=mp onChange='this.form.submit();'>";
if(count($RET))
{
    if(!UserMP())
            $_SESSION['UserMP'] = $RET[1]['MARKING_PERIOD_ID'];

    foreach($RET as $quarter)
    {
                    echo "<OPTION value=$quarter[MARKING_PERIOD_ID]".(UserMP()==$quarter['MARKING_PERIOD_ID']?' SELECTED':'').">".$quarter['TITLE']."</OPTION>";

    }
}
echo "</SELECT>";
//Marking Period

echo '</FORM></td></tr>';
echo '</table>';
echo '<table cellspacing=\"0\">';
echo '<tr><td style="color:#fff;">Subject</td><td></td>
          <td style="color:#fff">Course</td><td></td>
		  <td style="color:#fff">Course Period</td></tr>';

echo "<tr><td><FORM name=head_frm id=head_frm action=Side.php?modfunc=update&btnn=$btn&nsc=$ns&act=subject method=POST><INPUT type=hidden name=modcat value='' id=modcat_input>";

$sub = DBQuery("SELECT DISTINCT cs.TITLE, cs.SUBJECT_ID,cs.SCHOOL_ID FROM course_subjects as cs,course_details as cd WHERE cs.SUBJECT_ID=cd.SUBJECT_ID AND cd.SYEAR='".UserSyear()."' AND (cd.TEACHER_ID='".User('STAFF_ID')."' OR cd.SECONDARY_TEACHER_ID='".User('STAFF_ID')."') AND cs.SCHOOL_ID='".UserSchool()."' AND (cd.MARKING_PERIOD_ID IN (".GetAllMP($allMP,UserMP()).") OR (cd.MARKING_PERIOD_ID IS NULL ))");//AND cd.BEGIN_DATE<='".date('Y-m-d')."' AND cd.END_DATE>='".date('Y-m-d')."'))");
$RET = DBGet($sub);

if(!UserSubject()){
    $_SESSION['UserSubject']=$RET[1]['SUBJECT_ID'];
}
echo "<SELECT name=subject onChange='this.form.submit();'>";
if(count($RET)>0)
{
foreach($RET as $subject){
    echo "<OPTION id=$subject[SUBJECT_ID] value=$subject[SUBJECT_ID]".((UserSubject()==$subject['SUBJECT_ID'])?' SELECTED':'').">".$subject['TITLE']."</OPTION>";
}
}
else
{
    echo '<OPTION value="">n/a</OPTION>';
}
echo "</SELECT>";
//===================================================================================================		
echo "</FORM></td><td></td>";
echo "<td><FORM name=head_frm id=head_frm action=Side.php?modfunc=update&btnn=$btn&nsc=$ns&act=course method=POST><INPUT type=hidden name=modcat value='' id=modcat_input>";
$course = DBQuery("SELECT DISTINCT cd.COURSE_TITLE, cd.COURSE_ID,cd.SUBJECT_ID,cd.SCHOOL_ID FROM course_details cd WHERE (cd.TEACHER_ID='".User('STAFF_ID')."' OR cd.SECONDARY_TEACHER_ID='".User('STAFF_ID')."') AND cd.SYEAR='".UserSyear()."' AND cd.SCHOOL_ID='".UserSchool()."' AND cd.SUBJECT_ID='".UserSubject()."' AND (cd.MARKING_PERIOD_ID IN (".GetAllMP($allMP,UserMP()).") OR (cd.MARKING_PERIOD_ID IS NULL ))");//AND cd.BEGIN_DATE<='".date('Y-m-d')."' AND cd.END_DATE>='".date('Y-m-d')."'))");					
$RET = DBGet($course);
if(!UserCourse()){
    $_SESSION['UserCourse']=$RET[1]['COURSE_ID'];
}
echo "<SELECT name=course onChange='this.form.submit();'>";
if(count($RET)>0)
{
foreach($RET as $course){
    echo "<OPTION id=$course[COURSE_ID] value=$course[COURSE_ID]".((UserCourse()==$course['COURSE_ID'])?' SELECTED':'').">".$course['COURSE_TITLE']."</OPTION>";
}
}
else
{
    echo '<OPTION value="">n/a</OPTION>';
}
echo "</SELECT>";
//===================================================================================================							     					     
echo "</FORM></td><td></td>";

echo "<td><FORM name=head_frm id=head_frm action=Side.php?modfunc=update&btnn=$btn&nsc=$ns&act=period method=POST><INPUT type=hidden name=modcat value='' id=modcat_input>";		
						

$QI = DBQuery("SELECT cpv.ID,cp.COURSE_PERIOD_ID,cp.MARKING_PERIOD_ID,cp.COURSE_ID,cp.TITLE,cp.SCHOOL_ID,cpv.PERIOD_ID FROM course_periods cp,course_period_var cpv WHERE cp.SYEAR='".UserSyear()."' AND cp.COURSE_PERIOD_ID=cpv.COURSE_PERIOD_ID AND cp.SCHOOL_ID='".UserSchool()."' AND cp.COURSE_ID='".UserCourse()."' AND (TEACHER_ID='".User('STAFF_ID')."' OR SECONDARY_TEACHER_ID='".User('STAFF_ID')."') AND (MARKING_PERIOD_ID IN (".GetAllMP($allMP,UserMP()).") OR (MARKING_PERIOD_ID IS NULL)) group by (cp.COURSE_PERIOD_ID)");
$RET = DBGet($QI);
$user_profile_ret= DBGet(DBQuery(" SELECT PROFILE FROM staff WHERE STAFF_ID=".UserID()));

if(!CpvId())
    $_SESSION['CpvId']=$RET[1]['ID'];

$fy_id = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM school_years WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
$fy_id = $fy_id[1]['MARKING_PERIOD_ID'];

if(!UserCoursePeriod()){
    $_SESSION['UserCoursePeriod'] = $RET[1]['COURSE_PERIOD_ID'];
}	

echo "<SELECT name=period onChange='this.form.submit();'>";
if(count($RET)>0)
{
    $flag=0; 
foreach($RET as $period)
{
    $x_sel="";
    $period_det=DBGet(DBQuery('SELECT sp.TITLE as PERIOD_NAME,cpv.DAYS,cpv.COURSE_PERIOD_DATE FROM course_period_var cpv,school_periods sp WHERE cpv.ID='.$period['ID'].' AND cpv.PERIOD_ID=sp.PERIOD_ID'));
    $period_det=$period_det[1];
    $days_arr=array("Monday"=>'M',"Tuesday"=>'T',"Wednesday"=>'W',"Thursday"=>'H',"Friday"=>'F',"Saturday"=>'S',"Sunday"=>'U');
    if($period_det['DAYS']=='')
    {
        $period_det['DAYS']=date('l',strtotime($period_det['COURSE_PERIOD_DATE']));
        $period_det['DAYS']=$days_arr[$period_det['DAYS']];
    }


       if($flag == 0){
           $x_sel=" SELECTED=SELECTED"; 
           $flag = 1;
       }
  
     echo "<OPTION id=$period[COURSE_PERIOD_ID] value=$period[ID]".((CpvId()==$period['ID'])?' SELECTED':'').">".$period['TITLE']." - ".$period_det['PERIOD_NAME']." - ".$period_det['DAYS']."</OPTION>";
    $_SESSION['UserPeriod'] = $period['PERIOD_ID'];
    if(CpvId()==$period['ID'])
    {
        
        $_SESSION['CpvId']=$period['ID'];
        $_SESSION['UserCoursePeriod'] = $period['COURSE_PERIOD_ID'];
    }
    
}
}
else
{
    echo '<OPTION value="">n/a</OPTION>';
}
echo "</SELECT>";
echo "</FORM></td>";
echo '</tr></table></div>';
echo "</td></tr></table></td></tr>";
						
}  ##################Only for Teacher End##################	
if(User('PROFILE')!='teacher')
{
echo "<br><br><table cellpadding=2 cellspacing=0><tr><td><FORM name=head_frm id=head_frm action=Side.php?modfunc=update&btnn=$btn&nsc=$ns method=POST>
<INPUT type=hidden name=modcat value='' id=modcat_input>
";
	
if(User('PROFILE')=='admin')
{


    $RET=  DBGet(DBQuery("SELECT DISTINCT s.ID,s.TITLE FROM schools s,staff st INNER JOIN staff_school_relationship ssr USING(staff_id) WHERE s.id=ssr.school_id AND st.staff_id=$_SESSION[STAFF_ID] ORDER BY s.TITLE asc"));
    echo "<SELECT name=school onChange='this.form.submit();'>";
    foreach($RET as $school)
        echo "<OPTION  style='padding-right:8px;' value=$school[ID]".((UserSchool()==$school['ID'])?' SELECTED':'').">".$school['TITLE']."</OPTION>";

    echo "</SELECT>&nbsp;";
}



if(User('PROFILE')=='parent')
{
    $RET = DBGet(DBQuery("SELECT sju.STUDENT_ID, se.SCHOOL_ID FROM students s,students_join_people sju, student_enrollment se WHERE s.STUDENT_ID=sju.STUDENT_ID AND sju.PERSON_ID='".User('STAFF_ID')."' AND se.SYEAR=".UserSyear()." AND se.STUDENT_ID=sju.STUDENT_ID AND (('".DBDate()."' BETWEEN se.START_DATE AND se.END_DATE OR se.END_DATE IS NULL) AND '".DBDate()."'>=se.START_DATE)"));
    foreach($RET as $student)
        $_SESSION['UserSchool'] = $student['SCHOOL_ID'];
}
						
if(User('PROFILE')=='parent' || User('PROFILE')=='teacher')
{
    if(!$_SESSION['UserSchool'])
    {
        $sch_id = DBGet(DBQuery("SELECT CURRENT_SCHOOL_ID FROM staff WHERE STAFF_ID='".User('STAFF_ID')."'"));
        $sch_id = $sch_id[1]['CURRENT_SCHOOL_ID'];
        $_SESSION['UserSchool'] = $sch_id;
    }
}
						
echo '</FORM></td>';

echo "<td><FORM name=head_frm id=head_frm action=Side.php?modfunc=update&btnn=$btn&nsc=$ns method=POST>
<INPUT type=hidden name=modcat value='' id=modcat_input>";

$school_years_RET1=DBGet(DBQuery("SELECT START_DATE,END_DATE FROM school_years WHERE SCHOOL_ID=".UserSchool()));
$school_years_RET1=$school_years_RET1[1];
$school_years_RET1['START_DATE']=explode("-",$school_years_RET1['START_DATE']);
$school_years_RET1['START_DATE']=$school_years_RET1['START_DATE'][0];
$school_years_RET1['END_DATE']=explode("-",$school_years_RET1['END_DATE']);
$school_years_RET1['END_DATE']=$school_years_RET1['END_DATE'][0];				

echo "<SELECT name=syear onChange='this.form.submit();'>";

if($school_years_RET1['END_DATE']>$school_years_RET1['START_DATE'])
{
    if(User('PROFILE')=='student'){
        $school_years_RET=DBGet(DBQuery("SELECT DISTINCT sy.START_DATE,sy.END_DATE FROM school_years sy,student_enrollment se WHERE se.SYEAR=sy.SYEAR AND se.STUDENT_ID='$_SESSION[STUDENT_ID]' AND sy.SCHOOL_ID=".UserSchool()." "));
    }
    elseif(User('PROFILE')=='parent'){
        if(UserStudentID()=='')
        {
        $stu_ID=DBGet(DBQuery("SELECT sju.STUDENT_ID,CONCAT(s.LAST_NAME,', ',s.FIRST_NAME) AS FULL_NAME,se.SCHOOL_ID FROM students s,students_join_people sju, student_enrollment se WHERE s.STUDENT_ID=sju.STUDENT_ID AND sju.PERSON_ID='".User('STAFF_ID')."' AND se.SYEAR=".UserSyear()." AND se.STUDENT_ID=sju.STUDENT_ID AND (('".DBDate()."' BETWEEN se.START_DATE AND se.END_DATE OR se.END_DATE IS NULL) AND '".DBDate()."'>=se.START_DATE)"));    
        $stu_ID=$stu_ID[1]['STUDENT_ID'];
        }
        else
        $stu_ID=UserStudentID();    
        $school_years_RET=DBGet(DBQuery("SELECT DISTINCT sy.START_DATE,sy.END_DATE FROM school_years sy,student_enrollment se WHERE se.SYEAR=sy.SYEAR AND se.STUDENT_ID=".$stu_ID." AND sy.SCHOOL_ID=".UserSchool()." "));
    }
    else{
        $school_years_RET=DBGet(DBQuery("SELECT sy.START_DATE,sy.END_DATE FROM school_years sy ,staff s INNER JOIN staff_school_relationship ssr ON s.staff_id=ssr.staff_id WHERE sy.school_id=ssr.school_id AND sy.syear=ssr.syear AND sy.SCHOOL_ID=".UserSchool()." AND s.staff_id='$_SESSION[STAFF_ID]'"));
    }
    foreach($school_years_RET as $school_years)
    {
        $school_years['START_DATE']=explode("-",$school_years['START_DATE']);
        $school_years['START_DATE']=$school_years['START_DATE'][0];
        $school_years['END_DATE']=explode("-",$school_years['END_DATE']);
        $school_years['END_DATE']=$school_years['END_DATE'][0];
        echo "<OPTION value=$school_years[START_DATE]".((UserSyear()==$school_years['START_DATE'])?' SELECTED':'')."> $school_years[START_DATE]-".($school_years['END_DATE'])."</OPTION>";
    }
}
else if($school_years_RET1['END_DATE']==$school_years_RET1['START_DATE'])
{
    if(User('PROFILE')=='student')
        $school_years_RET=DBGet(DBQuery("SELECT DISTINCT sy.START_DATE,sy.END_DATE FROM school_years sy,student_enrollment se WHERE se.SYEAR=sy.SYEAR AND se.STUDENT_ID='$_SESSION[STUDENT_ID]' AND sy.SCHOOL_ID=".UserSchool()." "));
    elseif(User('PROFILE')=='parent'){
        if(UserStudentID()=='')
        {
        $stu_ID=DBGet(DBQuery("SELECT sju.STUDENT_ID,CONCAT(s.LAST_NAME,', ',s.FIRST_NAME) AS FULL_NAME,se.SCHOOL_ID FROM students s,students_join_people sju, student_enrollment se WHERE s.STUDENT_ID=sju.STUDENT_ID AND sju.PERSON_ID='".User('STAFF_ID')."' AND se.SYEAR=".UserSyear()." AND se.STUDENT_ID=sju.STUDENT_ID AND (('".DBDate()."' BETWEEN se.START_DATE AND se.END_DATE OR se.END_DATE IS NULL) AND '".DBDate()."'>=se.START_DATE)"));    
        $stu_ID=$stu_ID[1]['STUDENT_ID'];
        }
        else
        $stu_ID=UserStudentID();    
        $school_years_RET=DBGet(DBQuery("SELECT DISTINCT sy.START_DATE,sy.END_DATE FROM school_years sy,student_enrollment se WHERE se.SYEAR=sy.SYEAR AND se.STUDENT_ID=".$stu_ID." AND sy.SCHOOL_ID=".UserSchool()." "));
    }
    else
    {
        if(UserSchool())
            $school_years_RET=DBGet(DBQuery("SELECT sy.START_DATE,sy.END_DATE FROM school_years sy ,staff s INNER JOIN staff_school_relationship ssr ON s.staff_id=ssr.staff_id WHERE sy.school_id=ssr.school_id AND sy.syear=ssr.syear AND sy.SCHOOL_ID=".UserSchool()." AND s.staff_id='$_SESSION[STAFF_ID]'"));
        else
            $school_years_RET=DBGet(DBQuery("SELECT sy.START_DATE,sy.END_DATE FROM school_years sy ,staff s WHERE s.SYEAR=sy.SYEAR  AND s.USERNAME=(SELECT USERNAME FROM staff  WHERE STAFF_ID='$_SESSION[STAFF_ID]')"));
    }
    foreach($school_years_RET as $school_years)
    {
        $school_years['START_DATE']=explode("-",$school_years['START_DATE']);
        $school_years_RET['START_DATE']=$school_years['START_DATE'][0];
        echo "<OPTION value=$school_years_RET[START_DATE]".((UserSyear()==$school_years_RET['START_DATE'])?' SELECTED':'').">$school_years_RET[START_DATE]</OPTION>";
    }
}
						#}
echo '</SELECT>&nbsp;';
echo '</FORM></td>';

echo "<td><FORM name=head_frm id=head_frm action=Side.php?modfunc=update&btnn=$btn&nsc=$ns method=POST>
<INPUT type=hidden name=modcat value='' id=modcat_input>";
if(User('PROFILE')=='parent')
{
    $RET = DBGet(DBQuery("SELECT sju.STUDENT_ID,CONCAT(s.LAST_NAME,', ',s.FIRST_NAME) AS FULL_NAME,se.SCHOOL_ID FROM students s,students_join_people sju, student_enrollment se WHERE s.STUDENT_ID=sju.STUDENT_ID AND sju.PERSON_ID='".User('STAFF_ID')."' AND se.SYEAR=".UserSyear()." AND se.STUDENT_ID=sju.STUDENT_ID AND (('".DBDate()."' BETWEEN se.START_DATE AND se.END_DATE OR se.END_DATE IS NULL) AND '".DBDate()."'>=se.START_DATE)"));
    if(!UserStudentID())
        $_SESSION['student_id'] = $RET[1]['STUDENT_ID'];
    echo "<SELECT name=student_id onChange='this.form.submit();'>";
    if(count($RET))
    {
        foreach($RET as $student)
        {
            echo "<OPTION value=$student[STUDENT_ID]".((UserStudentID()==$student['STUDENT_ID'])?' SELECTED':'').">".$student['FULL_NAME']."</OPTION>";
            if(UserStudentID()==$student['STUDENT_ID'])
                $_SESSION['UserSchool'] = $student['SCHOOL_ID'];
        }
    }
    echo "</SELECT>&nbsp;";

    if(!UserMP())
        $_SESSION['UserMP'] = GetCurrentMP('QTR',DBDate());
}
echo '</FORM></td>';							

								
	
//For Marking Period
echo "<td><FORM name=head_frm id=head_frm action=Side.php?modfunc=update&btnn=$btn&nsc=$ns method=POST>
<INPUT type=hidden name=modcat value='' id=modcat_input>";
		
$RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM school_quarters WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
if(!isset($_SESSION['UserMP']))
    $_SESSION['UserMP'] = GetCurrentMP('QTR',DBDate());

if(!$RET)
{
    $RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM school_semesters WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
    if(!isset($_SESSION['UserMP']))
        $_SESSION['UserMP'] = GetCurrentMP('SEM',DBDate());
}

if(!$RET)
{
    $RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM school_years WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
    if(!isset($_SESSION['UserMP']))
        $_SESSION['UserMP'] = GetCurrentMP('FY',DBDate());
}
	
echo "<SELECT name=mp onChange='this.form.submit();'>";
if(count($RET))
{
    if(!UserMP())
        $_SESSION['UserMP'] = $RET[1]['MARKING_PERIOD_ID'];
    foreach($RET as $quarter)
        echo "<OPTION value=$quarter[MARKING_PERIOD_ID]".(UserMP()==$quarter['MARKING_PERIOD_ID']?' SELECTED':'').">".$quarter['TITLE']."</OPTION>";
}
echo "</SELECT>";
//Marking Period

echo '</FORM></td></tr></table></div>';
echo "</td></tr></table></td></tr>";
}##################Porfile Not Teacher End##########################################

if(UserStudentID() && User('PROFILE')!='parent' && User('PROFILE')!='student')
{
	$RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME,NAME_SUFFIX FROM students WHERE STUDENT_ID='".UserStudentID()."'"));
}
if(UserStaffID() && User('PROFILE')=='admin')
{
	if(UserStudentID())
	$RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME FROM staff WHERE STAFF_ID='".UserStaffID()."'"));
}

echo "

<tr>
            <td class=\"content\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                <tr>
                  <td align=\"center\" ><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"content_wrapper\">
                      <tr>
                        <td class=\"menubar\" style='padding-left:20px; padding-right:20px;'>";

#------------------------------------------------------------ menu Scroller _---------------------------------------------

  echo "  	  <div class='scrollable'>
	 <div id='cdnavheader'>
      <ul id='thumbs'>
";

 #------------------------------------------------------------ menu Scroller ends _---------------------------------------------

require('Menu.php');
echo "<li><a style='cursor:hand;' href='#' onmouseup='check_content(\"Ajax.php?modname=miscellaneous/Portal.php\");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"Home\";document.getElementById(\"cframe\").src = \"Bottom.php?modcat=home\"'><span>" ."Home". "</span></a></li>";
foreach($_openSIS['Menu'] as $modcat=>$programs)
{
	if(count($_openSIS['Menu'][$modcat]))
	{
		$keys = array_keys($_openSIS['Menu'][$modcat]);
		$menu = false;
		foreach($keys as $key_index=>$file)
		{
			if(!is_numeric($file))
				$menu = true;
		}
                
		if(!$menu)
			continue;

		if(User('PROFILE')!='admin' && $modcat=="schoolsetup")
		{
			echo "<li><a style='cursor:hand;' HREF=# onmouseup='check_content(\"Ajax.php?modname=$modcat/Search.php\");' target=body onmousedown='document.getElementById(\"header\").innerHTML =\"School Info\";document.getElementById(\"cframe\").src = \"Bottom.php?modcat=".$modcat."\"'><span>School Info</span></a></li>";
		}
                
		elseif(User('PROFILE')!='admin' && $modcat=="users")
		{
			echo "<li><a style='cursor:hand;' HREF=# onmouseup='check_content(\"Ajax.php?modname=$modcat/Search.php\");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"My Info\";document.getElementById(\"cframe\").src = \"Bottom.php?modcat=".$modcat."\"'><span>My Info</span></a></li>";
		}
		elseif(User('PROFILE')=='student' && $modcat=="students")
		{
                    
			echo "<li><a style='cursor:hand;' HREF=# onmouseup='check_content(\"Ajax.php?modname=$modcat/Search.php\");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"My Info\";document.getElementById(\"cframe\").src = \"Bottom.php?modcat=".$modcat."\"'><span>My Info</span></a></li>";
		}
		elseif(User('PROFILE')=='student' && $modcat=="scheduling")
		{
			echo "<li><a style='cursor:hand;' HREF=# onmouseup='check_content(\"Ajax.php?modname=$modcat/Search.php\");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".ucwords(str_replace('_',' ',$modcat))."\";document.getElementById(\"cframe\").src = \"Bottom.php?modcat=".$modcat."\"'><span>Schedule</span></a></li>";
		}
                 elseif($modcat=="messaging")
                {                   
                        echo "<li><a style='cursor:hand;' HREF=# onmouseup='check_content(\"Ajax.php?modname=$modcat/Inbox.php\");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".ucwords(str_replace('_',' ',$modcat))."\";document.getElementById(\"cframe\").src = \"Bottom.php?modcat=".$modcat."\"'><span>Messaging</span></a></li>";
                }
		else
		{
                    
                        if($modcat=='eligibility')
                        {
                        echo "<li><a style='cursor:hand;' HREF=# onmouseup='check_content(\"Ajax.php?modname=$modcat/Search.php\");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"Extracurricular\";document.getElementById(\"cframe\").src = \"Bottom.php?modcat=".$modcat."\"'><span>Extracurricular</span></a></li>";    
                        }
                        elseif($modcat=='schoolsetup')
                        echo "<li><a style='cursor:hand;' HREF=# onmouseup='check_content(\"Ajax.php?modname=$modcat/Search.php\");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"School Setup\";document.getElementById(\"cframe\").src = \"Bottom.php?modcat=".$modcat."\"'><span>School Setup</span></a></li>";
                        else
                        {
			echo "<li><a style='cursor:hand;' HREF=# onmouseup='check_content(\"Ajax.php?modname=$modcat/Search.php\");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".ucwords(str_replace('_',' ',$modcat))."\";document.getElementById(\"cframe\").src = \"Bottom.php?modcat=".$modcat."\"'><span>".ucfirst(str_replace('_',' ',$modcat))."</span></a></li>";
                        }
                        
                }


}
}

  #------------------------------------------------------------ menu Scroller _---------------------------------------------

echo"</ul></div></div>";




  echo "  </td></tr>";
 #------------------------------------------------------------ menu Scroller ends _---------------------------------------------



echo "<tr>
                        <td class=\"submenubar_bg\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                            <tr>
                              <td class=\"submenubar\" style='padding-left:10px; padding-right:10px;'>";
							  
echo "<div id='submenu_1' style='display:none;'>

<table cellspacing=0 celpadding=0 border=0 width=100%><tr><td width=60% valign=middle class='welcome'><b>Welcome to openSIS Student Information System</b></td><td width=40% class='version'>Version : ".$openSISVersion." | Release Date : ".$builddate."</td></tr></table> 

</div>"; 
$i = 2;
foreach($_openSIS['Menu'] as $modcat=>$programs)
{

	if(count($_openSIS['Menu'][$modcat]))
	{
		$keys = array_keys($_openSIS['Menu'][$modcat]);
		$menu = false;
		foreach($keys as $key_index=>$file)
		{
			if(!is_numeric($file))
				$menu = true;
		}
		if(!$menu)
			continue;



echo "<div id='submenu_".$i."' style='display:none'>"; 


		
		$int=0;
		$mm = 0;
		foreach($keys as $key_index=>$file)
		{
			
				
			$int = $int+1; 
			
			
		 				
			
			if(optional_param('student_id','',PARAM_ALPHANUM)=="new")
			
			{
				if($modcat=="students")
		 	{	
		 	 	if($int==2)
		 	 	{
				$style="class='submenu_link'";
				}
				else
				{
				$style="class='submenuitem'";
				}
			}	
			
			}
			
			elseif(optional_param("staff_id",'',PARAM_ALPHANUM)=="new")
			{
			 
			 if($modcat=="users")
		 	{	
		 	 	if($int==2)
		 	 	{
				$style="class='submenu_link'";
				}
				else
				{
				$style="class='submenuitem'";
				}
			}	
			
			 
			 
			 
			
			}else
			{
				
				if(optional_param('modname','',PARAM_URL)==$file)
			{	
				$style="class='submenu_link'";

			}else
			{
			$style="class='submenuitem'";
			}
		
		}
		
		 	
		 	
			$title = $_openSIS['Menu'][$modcat][$file];

		
			
					
			if($mm==0)
			{
			if(substr($file,0,7)=='http://')
				echo "<A ".$style." HREF=$file target=body >$title</A> &nbsp;&nbsp;|&nbsp;&nbsp;";
			elseif(substr($file,0,7)=='HTTP://')
				echo "<A ".$style." HREF=$file target=_blank>$title</A> &nbsp;&nbsp;|&nbsp;&nbsp;";
			elseif(!is_numeric($file))
				if(User('PROFILE')=='student' && $title=="Student Info"){

				echo "<A ".$style." id=hm HREF=# onClick='check_content(\"Ajax.php?modname=".$file." \");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".ucwords($modcat)." >> "."$title\"' onmouseup=\"changeColors(); this.className='submenu_link'; document.getElementById('cframe').src='Bottom.php?modname=".$file."';\">My Info</A> &nbsp;&nbsp;|&nbsp;&nbsp;";	
                                
                                }
				elseif(User('PROFILE')=='student' && $title=="Schedule"){
				echo "<A ".$style." id=hm HREF=# onClick='check_content(\"Ajax.php?modname=".$file." \");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".ucwords($modcat)." >> "."$title\"' onmouseup=\"changeColors(); this.className='submenu_link'; document.getElementById('cframe').src='Bottom.php?modname=".$file."';\">My Schedule</A> &nbsp;&nbsp;|&nbsp;&nbsp;";	
				}				
				elseif(User('PROFILE')=='student' && $title=="Student Requests"){
				echo "<A ".$style." id=hm HREF=# onClick='check_content(\"Ajax.php?modname=".$file." \");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".ucwords($modcat)." >> "."$title\"' onmouseup=\"changeColors(); this.className='submenu_link'; document.getElementById('cframe').src='Bottom.php?modname=".$file."';\">My Requests</A> &nbsp;&nbsp;|&nbsp;&nbsp;";	
				}
				else
				{

                                    if($modcat=='eligibility')
                                        echo "<A ".$style." id=hm HREF=# onClick='check_content(\"Ajax.php?modname=".$file." \");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"Extracurricular >> "."$title\"' onmouseup=\"changeColors(); this.className='submenu_link'; document.getElementById('cframe').src='Bottom.php?modname=".str_replace('&', '?',$file)."';\">$title</A> &nbsp;&nbsp;|&nbsp;&nbsp;";    
                                    else
                                    {
                                        if(User('PROFILE_ID')!=0 && User('PROFILE')=='admin')
                                        {
                                            if($modcat=='tools' && $title!='Backup Database')
                                                echo "<A ".$style." id=hm HREF=# onClick='check_content(\"Ajax.php?modname=".$file." \");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".ucwords($modcat)." >> "."$title\"' onmouseup=\"changeColors(); this.className='submenu_link'; document.getElementById('cframe').src='Bottom.php?modname=".str_replace('&', '?',$file)."';\">$title</A> &nbsp;&nbsp;|&nbsp;&nbsp;";
                                            if($modcat!='tools')
                                                echo "<A ".$style." id=hm HREF=# onClick='check_content(\"Ajax.php?modname=".$file." \");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".($modcat=='schoolsetup'?'School Setup':  ucwords($modcat))." >> "."$title\"' onmouseup=\"changeColors(); this.className='submenu_link'; document.getElementById('cframe').src='Bottom.php?modname=".str_replace('&', '?',$file)."';\">$title</A> &nbsp;&nbsp;|&nbsp;&nbsp;";
                                        }
                                        else
                                            echo "<A ".$style." id=hm HREF=# onClick='check_content(\"Ajax.php?modname=".$file." \");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".($modcat=='schoolsetup'?'School Setup':  ucwords($modcat))." >> "."$title\"' onmouseup=\"changeColors(); this.className='submenu_link'; document.getElementById('cframe').src='Bottom.php?modname=".str_replace('&', '?',$file)."';\">$title</A> &nbsp;&nbsp;|&nbsp;&nbsp;";
                                    }
				}
			elseif($keys[$key_index+1] && !is_numeric($keys[$key_index+1]))
			{
                            $mm=$mm+1;
                            if(User('PROFILE_ID')!=0 && User('PROFILE')=='admin')
                            {
                                if($modcat=='tools' &&  $title!='Reports')
                                    echo '<label class="dd_menuitem" id="mm_'.$modcat.'_'.$mm.'"><b>'.$title.'</b>&nbsp;<img src="themes/'.trim(strtolower ($css)).'/mnu_drpdwn.gif" />&nbsp;&nbsp;|&nbsp;&nbsp;</label>&nbsp;'.'<div id="menu_child_'.$modcat.'_'.$mm.'" style="position: absolute; visibility: hidden; width:200px;">';
    
                                if($modcat!='tools')
                                    echo '<label class="dd_menuitem" id="mm_'.$modcat.'_'.$mm.'"><b>'.$title.'</b>&nbsp;<img src="themes/'.trim(strtolower ($css)).'/mnu_drpdwn.gif" />&nbsp;&nbsp;|&nbsp;&nbsp;</label>&nbsp;'.'<div id="menu_child_'.$modcat.'_'.$mm.'" style="position: absolute; visibility: hidden; width:200px;">';
                            }
                            else
                                echo '<label class="dd_menuitem" id="mm_'.$modcat.'_'.$mm.'"><b>'.$title.'</b>&nbsp;<img src="themes/'.trim(strtolower ($css)).'/mnu_drpdwn.gif" />&nbsp;&nbsp;|&nbsp;&nbsp;</label>&nbsp;'.'<div id="menu_child_'.$modcat.'_'.$mm.'" style="position: absolute; visibility: hidden; width:200px;">';
			}

			}elseif($mm>0)
			{
			$menumm = $mm; 
			if(substr($file,0,7)=='http://')
				echo "<A id=dd class='dd_submenuitem' HREF=$file target=body >$title</A>";
			elseif(substr($file,0,7)=='HTTP://')
				echo "<A id=dd class='dd_submenuitem' HREF=$file target=_blank>$title</A>";
			elseif(!is_numeric($file))
                        {
                                if($modcat=='eligibility')
                                echo "<A id=dd class='dd_submenuitem' HREF=# onClick='check_content(\"Ajax.php?modname=".$file." \");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"Extracurricular >> "."$title\"' onmouseup=\"document.getElementById('cframe').src='Bottom.php?modname=".$file."';\">$title</A>";
                                else
                                {
                                    
                                        if(User('PROFILE_ID')!=0 && User('PROFILE')=='admin')
                                        {
                                            if($modcat=='tools' && $title!='At a Glance' && $title!='Institute Reports' && $title!='Institute Custom Field Reports')
                                                echo "<A id=dd class='dd_submenuitem' HREF=# onClick='check_content(\"Ajax.php?modname=".$file." \");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"". ucwords($modcat)." >> "."$title\"' onmouseup=\"document.getElementById('cframe').src='Bottom.php?modname=".$file."';\">$title</A>";
//                                            
                                            if($modcat!='tools')
                                            {
                                                
                                                echo "<A id=dd class='dd_submenuitem' HREF=# onClick='check_content(\"Ajax.php?modname=".$file." \");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".($modcat=='schoolsetup'?'School Setup':  ucwords($modcat))." >> "."$title\"' onmouseup=\"document.getElementById('cframe').src='Bottom.php?modname=".$file."';\">$title</A>";
                                            }
                                                }
                                        else
                                            echo "<A id=dd class='dd_submenuitem' HREF=# onClick='check_content(\"Ajax.php?modname=".$file." \");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".($modcat=='schoolsetup'?'School Setup':  ucwords($modcat))." >> "."$title\"' onmouseup=\"document.getElementById('cframe').src='Bottom.php?modname=".$file."';\">$title</A>";
                                    
                                }
                                
                        }
			elseif($keys[$key_index+1] && !is_numeric($keys[$key_index+1]))
			{
			 $mm=$mm+1;
			echo '</div><label class="dd_menuitem" id="mm_'.$modcat.'_'.$mm.'"><b>'.$title.'</b>&nbsp;<img src="themes/'.trim(strtolower ($css)).'/mnu_drpdwn.gif" />&nbsp;&nbsp;|&nbsp;&nbsp;</label>'.'<div id="menu_child_'.$modcat.'_'.$mm.'" style="position: absolute; visibility: hidden; width=200px;">';
			}
				
				echo '<script type="text/javascript">
createmenu("mm_'.$modcat.'_'.$menumm.'", "menu_child_'.$modcat.'_'.$menumm.'", "hover", "y", "pointer");
</script>';
			}
		
					
						
			

			
			
			echo "</b>";
	
		}
		
		

		
		echo "</div></div></DIV>";
		$i=$i+1;
	}
	
	
}	
echo "	</div> ";
echo '</tr></table></td></tr>';	
$append='';
if($_REQUEST['page_display'])
    $append='?page_display='.$_REQUEST['page_display'];
if($_REQUEST['include'] && $_REQUEST['modname']=='students/Student.php')
    $append='?include='.$_REQUEST['include'];
	
echo "
<tr >
                        <td class=\"pageheading_bg\"><div class=heading><table width='100%' cellpadding='0' cellspacing='0'><tr><td valign='top'>";


					
echo "				<div class=\"page_heading_breadcrumb\"><label id='header' name='header'></label>&nbsp;</td><td>";

echo '<div id="showhelp" style="padding-top:33px;"><a href="javascript:void(0);" onclick="inter=setInterval(\'ShowBox(helpdiv, 380, 499, 499, 211, showhelp)\',1);return false;"><b>Help</b></a></div></td></tr></table>';

echo '
	<div style="height:0px; margin-top:-6px; width=0px; position: absolute; overflow:hidden; visibility: hidden; text-align:left; " id="helpdiv">

<div class="help_div">
<table width=100% border=0 cellspacing=0 cellpadding=0><tr><td align=right style=" height:25px;  padding-right:5px;"><a href="javascript:void(0);" onclick="inter=setInterval(\'HideBox(helpdiv, showhelp)\',1);return false;"><b>Hide Help</b></a></td></tr></table>
</div>
<div style="background-image:url(themes/black/help_top.gif); width:495px; height:17px;"></div>
<iframe id="cframe" src="Bottom.php?modname='.$_REQUEST['modname'].$append.'" width="493" height=194px frameborder="0" scrolling="no" style="background-image:url(themes/black/help_bg.gif); width:495px; background-repeat:repeat-y; background-color:transparent; text-align:left " >
</iframe>
<div style="background-image:url(themes/black/help_bottom.gif); background-repeat:no-repeat; width:495px; height:10px;"></div>

</div>
';


echo "</div></div>";


echo "</td>
                      </tr>
<tr>
                        <td  valign=\"top\" class=\"txt_container_bg\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                            <tr>
                              <td class=\"txt_bg\">
							  
							  <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                              <tr>
                              <td class=\"txt_container\" valign=\"top\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                              <tr>
                              <td class=\"txt_padding\">
"	;

echo "<div id='content' name='content'>";
//For Student or User Informat

//=====================   For changing student id =====================


 
 if(User('PROFILE')=='admin')
	{

                $admin_COMMON_FROM=" FROM students s, student_address a,student_enrollment ssm ";
	   $admin_COMMON_WHERE=" WHERE s.STUDENT_ID=ssm.STUDENT_ID  AND a.STUDENT_ID=s.STUDENT_ID AND a.TYPE='Home Address' AND ssm.SYEAR=".UserSyear()." AND ssm.SCHOOL_ID=".UserSchool()." ";
	  
	   if(optional_param('mp_comment','',PARAM_NOTAGS) || $_SESSION['smc'])
		{
			$admin_COMMON_FROM .=" ,student_mp_comments smc";
			$admin_COMMON_WHERE .=" AND smc.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smc']='1';
		}
		  
		  if(optional_param('goal_description','',PARAM_NOTAGS) || optional_param('goal_title','',PARAM_NOTAGS) || $_SESSION['g'])
		{
			$admin_COMMON_FROM .=" ,student_goal g ";
			$admin_COMMON_WHERE .=" AND g.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['g']='1';
		}
		  
		  if(optional_param('progress_name','',PARAM_NOTAGS) ||optional_param('progress_description','',PARAM_NOTAGS) || $_SESSION['p'])
		{
			$admin_COMMON_FROM .=" ,student_goal_progress p ";
			$admin_COMMON_WHERE .=" AND p.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['p']='1';
		}
		 
		  if(optional_param('doctors_note_comments','',PARAM_NOTAGS) || optional_param('med_day','',PARAM_NOTAGS) || optional_param('med_month','',PARAM_NOTAGS) || optional_param('med_year','',PARAM_NOTAGS) || $_SESSION['smn'])
		{
			$admin_COMMON_FROM .=" ,student_medical_notes smn ";
			$admin_COMMON_WHERE .=" AND smn.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smn']='1';
		}
		  
		  if(optional_param('type','',PARAM_NOTAGS) || optional_param('imm_comments','',PARAM_NOTAGS) || optional_param('imm_day','',PARAM_NOTAGS) || optional_param('imm_month','',PARAM_NOTAGS) || optional_param('imm_year','',PARAM_NOTAGS) || $_SESSION['sm'])
		{

                        $admin_COMMON_FROM .=" ,student_immunization sm ";
			$admin_COMMON_WHERE .=" AND sm.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['sm']='1';
	
		}
		
		  
		  if(optional_param('ma_day','',PARAM_NOTAGS) || optional_param('ma_month','',PARAM_NOTAGS) ||optional_param('ma_year','',PARAM_NOTAGS) || optional_param('med_alrt_title','',PARAM_NOTAGS) || $_SESSION['sma'])
		{
			$admin_COMMON_FROM .=" ,student_medical_alerts sma  ";
			$admin_COMMON_WHERE .=" AND sma.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['sma']='1';
	
		}
		
		  if(optional_param('nv_day','',PARAM_NOTAGS) || optional_param('nv_month','',PARAM_NOTAGS) ||optional_param('nv_year','',PARAM_NOTAGS) || optional_param('reason','',PARAM_NOTAGS) || optional_param('result','',PARAM_NOTAGS)||  optional_param('med_vist_comments','',PARAM_NOTAGS) || $_SESSION['smv'])
		{
			$admin_COMMON_FROM .=" ,student_medical_visits smv   ";
			$admin_COMMON_WHERE .=" AND smv.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smv']='1';
		}
		$admin_COMMON= $admin_COMMON_FROM . $admin_COMMON_WHERE;
  }
 
if(User('PROFILE')=='teacher')
	{

                   $teacher_COMMON_FROM=" FROM students s, student_enrollment ssm, course_periods cp,
	schedule ss,student_address a ";
	   $teacher_COMMON_WHERE=" WHERE a.STUDENT_ID=s.STUDENT_ID  AND a.TYPE='Home Address' AND s.STUDENT_ID=ssm.STUDENT_ID AND ssm.STUDENT_ID=ss.STUDENT_ID AND ssm.SYEAR=cp.SYEAR AND ssm.SYEAR=ss.SYEAR AND cp.COURSE_ID=ss.COURSE_ID AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND ss.MARKING_PERIOD_ID IN (".GetAllMP('',$queryMP).")
						AND (cp.TEACHER_ID='".User('STAFF_ID')."' OR cp.SECONDARY_TEACHER_ID='".User('STAFF_ID')."') AND cp.COURSE_PERIOD_ID='".UserCoursePeriod()."' AND (ssm.START_DATE IS NOT NULL AND ('".DBDate()."'<=ssm.END_DATE OR ssm.END_DATE IS NULL)) AND ssm.SYEAR=".UserSyear()." AND ssm.SCHOOL_ID=".UserSchool()." ";
						
	  
	   if(optional_param('mp_comment','',PARAM_SPCL) || $_SESSION['smc'])
		{
			$teacher_COMMON_FROM .=" ,student_mp_comments smc";
			$teacher_COMMON_WHERE .=" AND smc.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smc']='1';
		}
		 
		  if(optional_param('goal_description','',PARAM_SPCL) || optional_param('goal_title','',PARAM_SPCL) || $_SESSION['g'])
		{
			$teacher_COMMON_FROM .=" ,student_goal g ";
			$teacher_COMMON_WHERE .=" AND g.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['g']='1';
		}
		  
		  if(optional_param('progress_name','',PARAM_NOTAGS) || optional_param('progress_description','',PARAM_NOTAGS) || $_SESSION['p'])
		{
			$teacher_COMMON_FROM .=" ,student_goal_progress p ";
			$teacher_COMMON_WHERE .=" AND p.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['p']='1';
		}
		  
		   if(optional_param('doctors_note_comments','',PARAM_NOTAGS) || optional_param('med_day','',PARAM_NOTAGS) || optional_param('med_month','',PARAM_NOTAGS) ||optional_param('med_year','',PARAM_NOTAGS) || $_SESSION['smn'])
		{
			$teacher_COMMON_FROM .=" ,student_medical_notes smn ";
			$teacher_COMMON_WHERE .=" AND smn.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smn']='1';
		}
		  
		  if(optional_param('type','',PARAM_NOTAGS) || optional_param('imm_comments','',PARAM_NOTAGS) ||optional_param('imm_day','',PARAM_NOTAGS) || optional_param('imm_month','',PARAM_NOTAGS) ||optional_param('imm_year','',PARAM_NOTAGS) || $_SESSION['sm'])
		{
		
                      $teacher_COMMON_FROM .=" ,student_immunization sm ";
			$teacher_COMMON_WHERE .=" AND sm.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['sm']='1';
	
		}
		
		  if(optional_param('ma_day','',PARAM_NOTAGS) || optional_param('ma_month','',PARAM_NOTAGS) || optional_param('ma_year','',PARAM_NOTAGS) || optional_param('med_alrt_title','',PARAM_NOTAGS) || $_SESSION['sma'])
		{
			$teacher_COMMON_FROM .=" ,student_medical_alerts sma  ";
			$teacher_COMMON_WHERE .=" AND sma.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['sma']='1';
	
		}
		
		  if(optional_param('nv_day','',PARAM_NOTAGS) || optional_param('nv_month','',PARAM_NOTAGS) || optional_param('nv_year','',PARAM_NOTAGS) || optional_param('reason','',PARAM_NOTAGS) || optional_param('result','',PARAM_NOTAGS) || optional_param('med_vist_comments','',PARAM_NOTAGS) || $_SESSION['smv'])
		{
			$teacher_COMMON_FROM .=" ,student_medical_visits smv   ";
			$teacher_COMMON_WHERE .=" AND smv.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smv']='1';
		}
		$teacher_COMMON= $teacher_COMMON_FROM . $teacher_COMMON_WHERE;
 }
 
	
	
 
 
 
//===================== End =============================================
 


//
	

echo "<div id='update_panel'>";
echo "<center><div id='divErr'></div></center>";

	
if(!isset($_REQUEST['_openSIS_PDF']))
{
		
	echo '<DIV id="Migoicons" style="visibility:hidden;position:absolute;z-index:1000;top:-100;"></DIV>';
	echo "<TABLE width=100% border=0 cellpadding=0><TR><TD valign=top align=center>";
}


if($_REQUEST['modname'])
{       /*******************back to list****************************/
        if($_REQUEST['bottom_back'] && $_SESSION['staff_id'])
                unset($_SESSION['staff_id']);
	if($_REQUEST['bottom_back'] && $_SESSION['student_id'])
            unset($_SESSION['student_id']);
        /************************************************/
        if($_REQUEST['_openSIS_PDF']=='true')
		ob_start();
	if(strpos($_REQUEST['modname'],'?')!==false)
	{
		
		$modname = substr(optional_param('modname','',PARAM_NOTAGS),0,strpos(optional_param('modname','',PARAM_NOTAGS),'?'));
		
		$vars = substr(optional_param('modname','',PARAM_NOTAGS),(strpos(optional_param('modname','',PARAM_NOTAGS),'?')+1));

		$vars = explode('?',$vars);
		foreach($vars as $code)
		{
			$code = explode('=',$code);
			$_REQUEST[$code[0]] = $code[1];
		}
	}
	else
		
		$modname = optional_param('modname','',PARAM_NOTAGS);

	
	if(optional_param('LO_save','',PARAM_INT)!='1' && !isset($_REQUEST['_openSIS_PDF']) && (strpos($modname,'miscellaneous/')===false || $modname=='miscellaneous/Registration.php' || $modname=='miscellaneous/Export.php' || $modname=='miscellaneous/Portal.php'))
		$_SESSION['_REQUEST_vars'] = $_REQUEST;

	$allowed = false;
	include 'Menu.php';
       
	foreach($_openSIS['Menu'] as $modcat=>$programs)
	{  
		
		if(optional_param('modname','',PARAM_NOTAGS)==$modcat.'/Search.php')
		{
			$allowed = true;
			break;
		}
		foreach($programs as $program=>$title)
		{   
			
			if(optional_param('modname','',PARAM_NOTAGS)==$program)
			{
				$allowed = true;
				break;
			}
		}
	}  
        if(optional_param('modname','',PARAM_NOTAGS)=='users/TeacherPrograms.php?include=attendance/TakeAttendance.php')
                $allowed=true;
	if(optional_param('modname','',PARAM_NOTAGS)=='schoolsetup/UploadLogo.php' && User('PROFILE')=='admin')
                $allowed=true;
        if(optional_param('modname','',PARAM_NOTAGS)=='users/UploadUserPhoto.php')
                $allowed=true;
        if(optional_param('modname','',PARAM_NOTAGS)=='users/UploadUserPhoto.php?modfunc=edit')
                $allowed=true;
        if(optional_param('modname','',PARAM_NOTAGS)=='students/Upload.php')
                $allowed=true;
        if(optional_param('modname','',PARAM_NOTAGS)=='students/Upload.php?modfunc=edit')
                $allowed=true;
	if(substr(optional_param('modname','',PARAM_NOTAGS),0,14)=='miscellaneous/' || substr(optional_param('modname','',PARAM_NOTAGS),0,7)=='grades/')
		$allowed = true;
      if(optional_param('modname','',PARAM_NOTAGS)=='messaging/AddMember.php')
                $allowed=true;
	if($allowed || $_SESSION['take_mssn_attn'])
	{  
		
		if(Preferences('SEARCH')!='Y' && substr(clean_param($modname,PARAM_NOTAGS),0,6)!='users/')
			$_REQUEST['search_modfunc'] = 'list';
		include('modules/'.$modname);
	}
	else
	{
		if(User('USERNAME'))
		{
			echo "You're not allowed to use this program! This attempted violation has been logged and your IP address was captured.";
			Warehouse('footer');
			
			if ($_SERVER['HTTP_X_FORWARDED_FOR']){
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			
			if($openSISNotifyAddress)
				mail($openSISNotifyAddress,'HACKING ATTEMPT',"INSERT INTO hacking_log (HOST_NAME,IP_ADDRESS,LOGIN_DATE,VERSION,PHP_SELF,DOCUMENT_ROOT,SCRIPT_NAME,MODNAME,USERNAME) values('$_SERVER[SERVER_NAME]','$ip','".date('Y-m-d')."','$openSISVersion','$_SERVER[PHP_SELF]','$_SERVER[DOCUMENT_ROOT]','$_SERVER[SCRIPT_NAME]','$_REQUEST[modname]','".User('USERNAME')."')");
			if(false && function_exists('query'))
			{
				
			if ($_SERVER['HTTP_X_FORWARDED_FOR']){
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			
                         $connection = new mysqli('os4ed.com','openSIS_log','openSIS_log','openSIS_log');
				
			$connection->query("INSERT INTO hacking_log (HOST_NAME,IP_ADDRESS,LOGIN_DATE,VERSION,PHP_SELF,DOCUMENT_ROOT,SCRIPT_NAME,MODNAME,USERNAME) values('$_SERVER[SERVER_NAME]','$ip','".date('Y-m-d')."','$openSISVersion','$_SERVER[PHP_SELF]','$_SERVER[DOCUMENT_ROOT]','$_SERVER[SCRIPT_NAME]','".optional_param('modname','',PARAM_CLEAN)."','".User('USERNAME')."')");	
			mysqli_close($link);
                        
                        }
		}
		exit;
	}

	if($_SESSION['unset_student'])
	{
		unset($_SESSION['unset_student']);
		unset($_SESSION['staff_id']);
	}
}


if(!isset($_REQUEST['_openSIS_PDF']))
{
	echo '</TD></TR></TABLE>';
	for($i=1;$i<=$_openSIS['PrepareDate'];$i++)
	{
		echo '<script type="text/javascript">
    Calendar.setup({
        monthField     :    "monthSelect'.$i.'",
        dayField       :    "daySelect'.$i.'",
        yearField      :    "yearSelect'.$i.'",
        ifFormat       :    "%d-%b-%y",
        button         :    "trigger'.$i.'",
        align          :    "Tl",
        singleClick    :    true
    });
</script>';
	}


echo "</div>";

	echo "</td>
                                        </tr>
                                      </table></td>
                                  </tr>
                                </table></td>
                            </tr>
                          </table></td>
                      </tr>
                    </table></td>
                </tr>
              </table></td>
          </tr>

			<tr>
            <td class=\"footer\">
			<table width=\"100%\" border=\"0\">
  <tr>
    <td align='center' class='copyright'>
       <center>openSIS is a product of Open Solutions for Education, Inc. (<a href='http://www.os4ed.com' target='_blank'>OS4Ed</a>).
                and is licensed under the <a href='http://www.gnu.org/licenses/gpl.html' target='_blank'>GPL License</a>.
                </center></td>
  </tr>
</table>
			</td>
          	</tr>
        </table></td>
    </tr>
  </table>
</center>
<div id='cal' style='position:absolute;'> </div>
</body>
</html>
	
	";

}


?>
