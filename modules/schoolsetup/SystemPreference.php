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
DrawBC("School Setup >> ".ProgramTitle());
if($_REQUEST['page_display']){
	
echo '<style type="text/css">
.back_preference { padding:2px 0px 10px 8px; text-align:left; margin:5px 5px; }
</style>';
echo "<div class=back_preference><a href=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."><strong>&laquo; Back to System Preference</strong>
</a></div><br/>";
}
if(clean_param($_REQUEST['page_display'],PARAM_ALPHAMOD)=='SystemPreference'){
if((clean_param($_REQUEST['action'],PARAM_ALPHAMOD) == 'update') && (clean_param($_REQUEST['button'],PARAM_ALPHAMOD)=='Save') && clean_param($_REQUEST['values'],PARAM_NOTAGS) && $_POST['values'] && User('PROFILE')=='admin')
{

	$sql = 'UPDATE system_preference SET ';
	foreach($_REQUEST['values'] as $column=>$value)
	{
        $value=paramlib_validation($column,$value);
                if($column=='FULL_DAY_MINUTE' && $value=='')
                 $value=NULL;
                if($column=='HALF_DAY_MINUTE' && $value=='')
                 $value=NULL;
		$sql .= $column.'=\''.str_replace("\'","''",$value).'\',';
	}
	$sql = substr($sql,0,-1) . ' WHERE SCHOOL_ID=\''.UserSchool().'\'';
	DBQuery($sql);
	
}
elseif((clean_param($_REQUEST['action'],PARAM_ALPHAMOD) == 'insert') && (clean_param($_REQUEST['button'],PARAM_ALPHAMOD)=='Save') && clean_param($_REQUEST['values'],PARAM_NOTAGS) && $_POST['values'] && User('PROFILE')=='admin')
{

	$sql = 'INSERT INTO system_preference SET ';
	foreach($_REQUEST['values'] as $column=>$value)
	{
        $value=paramlib_validation($column,$value);
		$sql .= $column.'=\''.str_replace("\'","''",$value).'\',';
	}
	$sql = substr($sql,0,-1) . ',school_id=\''.UserSchool().'\'';
	DBQuery($sql);
	
}

$sys_pref = DBGet(DBQuery('SELECT * FROM system_preference WHERE SCHOOL_ID='.UserSchool()));
$sys_pref = $sys_pref[1];

PopTable('header','Half-day and full-day minutes');
if($sys_pref==''){
    echo "<FORM name=sys_pref id=sys_pref action=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&action=insert&page_display=SystemPreference method=POST>";
}else{

echo "<FORM name=sys_pref id=sys_pref action=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&action=update&page_display=SystemPreference method=POST>";
    
    
}
echo "<table width=300px><tr><td><table border=0 cellpadding=4 align=center>";
echo "<tr><td><strong>Full day minutes :</strong> </td><td>".TextInput($sys_pref['FULL_DAY_MINUTE'],'values[FULL_DAY_MINUTE]', '','class=cell_floating size=5')."</td></tr><tr><td><strong>Half day minutes :</strong></td><td>".TextInput($sys_pref['HALF_DAY_MINUTE'],'values[HALF_DAY_MINUTE]', '','class=cell_floating size=5')."</td></tr>";
echo "</table></td></tr></table>";

DrawHeader('','',"<INPUT TYPE=SUBMIT name=button id=button class=btn_medium onclick='return formcheck_halfday_fullday();'  VALUE='Save'></CENTER>");
echo "</FORM>";
PopTable('footer');
}else if(clean_param($_REQUEST['page_display'],PARAM_ALPHAMOD)=='MAINTENANCE'){
if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='update'){

if(clean_param($_REQUEST['maintain'],PARAM_NOTAGS)){
    
    $check_sys_pref_misc=DBGet(DBQuery('SELECT COUNT(1) as TOTAL FROM system_preference_misc'));
    if($check_sys_pref_misc[1]['TOTAL']>0)
    {
$sql='UPDATE system_preference_misc SET ';
foreach($_REQUEST['maintain'] as $column_name=>$value)
					{ 
					$sql .= ''.$column_name.'=\''.str_replace("\'","''",str_replace("`","''",$value)).'\',';

}
$sql= substr($sql,0,-1) .' WHERE 1=1';

}
else
$sql='INSERT INTO system_preference_misc (SYSTEM_MAINTENANCE_SWITCH) VALUES (\''.$_REQUEST['maintain']['SYSTEM_MAINTENANCE_SWITCH'].'\') ';
DBQuery($sql);
}
foreach($_REQUEST['values'] as $id=>$columns)
	{
		if($id!='new')
		{
			$sql = 'UPDATE login_message SET ';
			foreach($columns as $column=>$value)
		{

                   
		if($value=='DISPLAY')
			$sql .= $column.'=\'Y\',';
			else
			$sql .= $column.'=\''.str_replace("\'","''",$value).'\',';
		}
			$sql = substr($sql,0,-1) . ' WHERE ID=\''.$id.'\'';
			DBQuery($sql);
		}
		else
		{
			$sql = 'INSERT INTO login_message ';
			$go = 0;
			foreach($columns as $column=>$value)
			{
				if($value)
				{
					if($value=='DISPLAY')
					 {
 					$fields .= $column.',';
					$values .= '\''.str_replace("\'","''",'Y').'\',';
					 }
					else
					 {
					$fields .= $column.',';
					$values .= '\''.str_replace("\'","''",$value).'\',';
					 }
					$go = true;
				}
			}
			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
			if($go)
			{
				DBQuery($sql);
			}
		}
	}
foreach($_REQUEST['val'] as $col=>$val)
	{
		$id=trim(substr($val,0,strpos($val,',')));
		$value=trim(substr($val,strpos($val,',')+1));
		if($id!='new')
		{
		$ID=DBGet(DBQuery('SELECT ID FROM login_message'));
		foreach($ID as $get_ID)
		 {
		    if($get_ID['ID']==$id)
			$sql = 'UPDATE login_message SET '.$col.'=\'Y\' WHERE ID='.$get_ID['ID'];
			else
			$sql = 'UPDATE login_message SET '.$col.'=\'N\' WHERE ID='.$get_ID['ID'];
			DBQuery($sql);
		 }
			
		}
		else
		{
		    $ID=DBGet(DBQuery('SELECT ID FROM login_message'));
			foreach($ID as $get_ID)
			 {
				if($get_ID['ID']==$id)
				$sql = 'UPDATE login_message SET '.$col.'=\'Y\' WHERE ID='.$get_ID['ID'];
				else
				$sql = 'UPDATE login_message SET '.$col.'=\'N\' WHERE ID='.$get_ID['ID'];
				DBQuery($sql);
			 }
			$max_ID=DBGet(DBQuery('SELECT MAX(ID) AS ID FROM login_message'));
			$login_VAL=DBGet(DBQuery('SELECT ID,MESSAGE FROM login_message WHERE ID='.$max_ID[1]['ID'].' '));
			$sql='UPDATE login_message SET ';
			if($login_VAL[1]['MESSAGE'] !='')
			{
				$sql .= $col.'=\'Y\' ';
				$sql .=  ' WHERE ID='.$max_ID[1]['ID'].'';
			}
				DBQuery($sql);
		}
	}	
unset($_REQUEST['maintain']);
}
if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='remove')
{
	if(DeletePrompt_sys_maintain('login message'))
	{
		DBQuery("DELETE FROM login_message WHERE ID='$_REQUEST[id]'");
		unset($_REQUEST['modfunc']);
	}
	
}
if($_REQUEST['modfunc']!='remove')
 {
	$maintain_RET=DBGet(DBQuery("SELECT SYSTEM_MAINTENANCE_SWITCH FROM system_preference_misc LIMIT 1"));
	$maintain=$maintain_RET[1];
	echo "<FORM name=maintenance id=maintenance action=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&modfunc=update&page_display=MAINTENANCE method=POST>";
	echo '<table>';
	echo '<tr><td align=left><span style="font-size:12px; font-weight:bold;">Under Maintenance :</td><td><span style="font-weight:bold;">'.CheckboxInput($maintain['SYSTEM_MAINTENANCE_SWITCH'],'maintain[SYSTEM_MAINTENANCE_SWITCH]').'</span></td></tr>';
	$sql = 'SELECT ID,MESSAGE,DISPLAY FROM login_message ORDER BY ID';
	$QI = DBQuery($sql);
	$login_MESSAGE=DBGet($QI,array('MESSAGE'=>'_makeContentInput','DISPLAY'=>'_makeRadio'));
	$link['add']['html'] = array('MESSAGE'=>_makeContentInput('','MESSAGE'),'DISPLAY'=>_makeRadio('','DISPLAY'));
	$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove&page_display=MAINTENANCE";
	$link['remove']['variables'] = array('id'=>'ID');
	$columns = array('MESSAGE'=>'Login Message','DISPLAY'=>'Display');
	ListOutput($login_MESSAGE,$columns,'Message','Messages',$link, true, array('search'=>false));
	
	echo '<tr><td><CENTER>'.SubmitButton('Save','','class=btn_medium').'</CENTER></td></tr>';
	echo '</table>';
	echo '</FORM>';
 }
}else if(clean_param($_REQUEST['page_display'],PARAM_ALPHAMOD)=='INACTIVITY'){
PopTable('header','User Inactivity Days');
include("UserActivityDays.php");
PopTable('footer');
}else if(clean_param($_REQUEST['page_display'],PARAM_ALPHAMOD)=='FAILURE'){
PopTable('header','Login Failure Allowance');
include("FailureCount.php");
PopTable('footer');
}else if(clean_param($_REQUEST['page_display'],PARAM_ALPHAMOD)=='CURRENCY'){
PopTable('header','Currency');
include("SetCurrency.php");
PopTable('footer');
}else if(clean_param($_REQUEST['page_display'],PARAM_ALPHAMOD)=='CLASSRANK'){
PopTable('header','Class Rank');

if($_REQUEST['modfunc']=='update'){
if(isset($_REQUEST['display_rank'])){
$rank_RET=DBGet(DBQuery('SELECT VALUE FROM program_config WHERE school_id=\''.  UserSchool().'\' AND program=\'class_rank\' AND title=\'display\' LIMIT 0, 1'));
if(count($rank_RET)==0){
DBQuery('INSERT INTO program_config (school_id,program,title,value) VALUES(\''.  UserSchool().'\',\'class_rank\',\'display\',\'Y\')');
}else{
DBQuery('UPDATE program_config SET value=\''.$_REQUEST['display_rank'].'\' WHERE school_id=\''.  UserSchool().'\' AND program=\'class_rank\' AND title=\'display\'');
}
unset($_REQUEST['display_rank']);
unset($_SESSION['_REQUEST_vars']['display_rank']);
}
}
$rank_RET=DBGet(DBQuery('SELECT VALUE FROM program_config WHERE school_id=\''.  UserSchool().'\' AND program=\'class_rank\' AND title=\'display\' LIMIT 0, 1'));
$rank=$rank_RET[1];
echo "<FORM name=failure id=failure action=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&modfunc=update&page_display=CLASSRANK method=POST>";
echo '<table width="330px;" cellpadding="4">';
echo '<tr><td width="92%" align="center">Display Class Rank?</td><td align="left">'. CheckboxInput($rank['VALUE'],'display_rank','','class=cell_floating').'</td></tr>';
echo '<tr><td colspan="2"></td></tr>';
echo '<tr><td colspan="2"><CENTER>'.SubmitButton('Save','','class=btn_medium').'</CENTER></td></tr>';
echo '</table>';
echo '</FORM>';

PopTable('footer');
}else if(clean_param($_REQUEST['page_display'],PARAM_ALPHAMOD)=='UPDATENOTIFY'){
PopTable('header','Display Notifications');

if($_REQUEST['modfunc']=='update'){

    if(isset($_REQUEST['display_notify']))
    {
        $notify_RET=DBGet(DBQuery('SELECT VALUE FROM program_config WHERE school_id=\''.  UserSchool().'\' AND program=\'UPDATENOTIFY\' AND title=\'display\' LIMIT 0, 1'));
        if(count($notify_RET)==0)
        {
        DBQuery('INSERT INTO program_config (SYEAR,SCHOOL_ID,PROGRAM,TITLE,VALUE) VALUES(\''.  UserSyear().'\',\''.  UserSchool().'\',\'UPDATENOTIFY\',\'display\',\'Y\')');
        }
        else
        {
        DBQuery('UPDATE program_config SET VALUE=\''.$_REQUEST['display_notify'].'\' WHERE SCHOOL_ID=\''.  UserSchool().'\' AND PROGRAM=\'UPDATENOTIFY\' AND TITLE=\'display\'');
        }
        unset($_REQUEST['display_notify']);
        unset($_SESSION['_REQUEST_vars']['display_notify']);
    }
    if(isset($_REQUEST['display_school_notify']))
    {
        $notify_RET=DBGet(DBQuery('SELECT VALUE FROM program_config WHERE school_id=\''.  UserSchool().'\' AND program=\'UPDATENOTIFY\' AND title=\'display_school\' LIMIT 0, 1'));
        if(count($notify_RET)==0)
        {
        DBQuery('INSERT INTO program_config (SYEAR,SCHOOL_ID,PROGRAM,TITLE,VALUE) VALUES(\''.  UserSyear().'\',\''.  UserSchool().'\',\'UPDATENOTIFY\',\'display_school\',\'Y\')');
        }
        else
        {
        DBQuery('UPDATE program_config SET VALUE=\''.$_REQUEST['display_school_notify'].'\' WHERE SCHOOL_ID=\''.  UserSchool().'\' AND PROGRAM=\'UPDATENOTIFY\' AND TITLE=\'display_school\'');
        }
        unset($_REQUEST['display_notify']);
        unset($_SESSION['_REQUEST_vars']['display_notify']);
    }
}
$notify_RET=DBGet(DBQuery('SELECT VALUE FROM program_config WHERE school_id=\''.  UserSchool().'\' AND program=\'UPDATENOTIFY\' AND TITLE=\'display\' LIMIT 0, 1'));
$notify_RET=$notify_RET[1];
$notify_RET_school=DBGet(DBQuery('SELECT VALUE FROM program_config WHERE school_id=\''.  UserSchool().'\' AND program=\'UPDATENOTIFY\' AND TITLE=\'display_school\' LIMIT 0, 1'));
$notify_RET_school=$notify_RET_school[1];
echo "<FORM name=failure id=failure action=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&modfunc=update&page_display=UPDATENOTIFY method=POST>";
echo '<table width="330px;" cellpadding="4">';
echo '<tr><td width="92%" align="center">Notify when latest version is available?</td><td align="left">'. CheckboxInput($notify_RET['VALUE'],'display_notify','','class=cell_floating').'</td></tr>';
echo '<tr><td width="92%" align="center">Notify when school setup is incomplete?</td><td align="left">'. CheckboxInput($notify_RET_school['VALUE'],'display_school_notify','','class=cell_floating').'</td></tr>';
echo '<tr><td colspan="2"></td></tr>';
echo '<tr><td colspan="2"><CENTER>'.SubmitButton('Save','','class=btn_medium').'</CENTER></td></tr>';
echo '</table>';
echo '</FORM>';

PopTable('footer');
}
else{

echo '
<style type="text/css">
.time_schedule { background:url(assets/time_schedule.png) no-repeat 0px 0px; padding:10px 0px 10px 45px; text-align:left; margin:14px 280px; }
.login_failure { background:url(assets/login_failure.png) no-repeat 0px 0px; padding:10px 0px 10px 45px; text-align:left; margin:14px 280px; }
.user_inactivity { background:url(assets/user_inactivity.png) no-repeat 0px 0px; padding:10px 0px 10px 45px; text-align:left; margin:14px 280px; }
.maintenance { background:url(assets/maintenance.png) no-repeat 0px 0px; padding:10px 0px 10px 45px; text-align:left; margin:14px 280px; }
.currency { background:url(assets/currency.png) no-repeat 0px 0px; padding:10px 0px 10px 45px; text-align:left; margin:14px 280px; }
.class_rank { background:url(assets/class_rank.png) no-repeat 0px 0px; padding:15px 0px 10px 45px; text-align:left; margin:14px 280px; }
.updatenotify { background:url(assets/notify.png) no-repeat 0px 0px; padding:15px 0px 10px 45px; text-align:left; margin:14px 280px; }
</style>

<div style=padding:20px 0px 0px 0px;>';
echo "<div class=time_schedule><a href=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&page_display=SystemPreference><strong>Set half-day and full-day minutes</strong></a></div>";
echo "<div class=login_failure><a href=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&page_display=FAILURE><strong>Set login failure allowance count</strong></a></div>";
echo "<div class=user_inactivity><a href=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&page_display=INACTIVITY><strong>Set  allowable user inactivity days</strong></a></div>";
echo "<div class=maintenance><a href=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&page_display=MAINTENANCE><strong>Put system in maintenance mode</strong></a></div>";
echo "<div class=currency><a href=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&page_display=CURRENCY><strong>Set Currency</strong></a></div>";
echo "<div class=class_rank><a href=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&page_display=CLASSRANK><strong>Display Class Rank</strong></a></div>";
echo "<div class=updatenotify><a href=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&page_display=UPDATENOTIFY><strong>Display Notifications</strong></a></div>";
echo '</div>';
}
function _makeContentInput($value,$name)
{	global $THIS_RET;

	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';
		$THIS_RET['ID'];
	return TextareaInput($value,"values[$id][$name]",'','rows=8 cols=55');
}
function makeTextInput($value,$name)
{	global $THIS_RET;
	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';
	
	if($name!='MESSAGE')
		$extra = 'size=5 maxlength=2 class=cell_floating';
		else 
	$extra = 'class=cell_floating ';
	
	if($name=='SORT_ORDER')
		$comment = '<!-- '.$value.' -->';

	return $comment.TextInput($value,'values['.$id.']['.$name.']','',$extra);
}
function _makeRadio($value,$name)
{	global $THIS_RET;
	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';
	
	if($THIS_RET[$name]=='Y')
		return "<TABLE align=center><TR><TD><INPUT type=radio name=val[".$name."] value=".$id.",".$name." CHECKED></TD></TR></TABLE>";
	else
		return "<TABLE align=center><TR><TD><INPUT type=radio name=val[".$name."] value=".$id.",".$name."".(AllowEdit()?'':' ')."></TD></TR></TABLE>";
}

?>