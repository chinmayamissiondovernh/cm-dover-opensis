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
if(User('PROFILE')=='admin')
{
	
    	if(($_REQUEST['modfunc']=='search_fnc' || !$_REQUEST['modfunc']) &&  !$_REQUEST['search_modfunc'])
	{
            
		if($_SESSION['staff_id'])
		{
			unset($_SESSION['staff_id']);
			echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
		}
		echo '<BR>';
		PopTable('header','Find a Staff');

		echo "<FORM name=search action=Modules.php?modname=$_REQUEST[modname]&modfunc=list&next_modname=$_REQUEST[next_modname] method=POST>";
		echo '<TABLE>';
		echo '<TR><TD align=right>Last Name</TD><TD><INPUT type=text class=cell_floating name=last></TD></TR>';
		echo '<TR><TD align=right>First Name</TD><TD><INPUT type=text class=cell_floating name=first></TD></TR>';
		echo '<TR><TD align=right>Username</TD><TD><INPUT type=text class=cell_floating name=username></TD></TR>';
                if(User('PROFILE_ID')==1)
                $qry1=DBGet(DBQuery('SELECT * FROM user_profiles WHERE profile <> \''.'student'.'\' AND profile <> \''.'parent'.'\' AND id !=0'));
                if(User('PROFILE_ID')==0)
                $qry1=DBGet(DBQuery('SELECT * FROM user_profiles WHERE profile <> \''.'student'.'\' AND profile <> \''.'parent'.'\''));
             
                $options['']='N/A';
                foreach($qry1 as $index=>$value)
                {
                    $options[$value['ID']]=$value['TITLE'];
                    if($value['ID']=='2')
                        $t=$value['ID'];
                }
                $options['admin']='Administrator w/Custom';
                $options['teacher']='Teacher w/Custom';
                $options['none']='No Access';
                

                        if($extra['profile'])
                        {
                        if($extra['profile']=='teachers_option')
                        $options=array($t=>'Teacher','teacher'=>'Teacher w/Custom');
                        elseif($extra['profile']=='teachers_option_all')
                        {
                        $options=array();
                        foreach($qry1 as $index=>$value)
                        {
                            if($value['PROFILE']=='teacher')
                            $options[$value['ID']]=$value['TITLE'];
                        }
                        $options['teacher']='Teacher w/Custom';   
                        
                        }
			else
                        $options = array($extra['profile']=>$options[$extra['profile']]);
                        
                        }
                        
		echo '<TR><TD align=right>Profile</TD><TD><SELECT name=profile>';
		foreach($options as $key=>$val)
			echo '<OPTION value="'.$key.'">'.$val;
		echo '</SELECT></TD></TR>';
		if($extra['search'])
			echo $extra['search'];
		echo '<TR><TD colspan=2 align=center>';

		if(User('PROFILE')=='admin')
			echo '<INPUT type=checkbox name=_search_all_schools value=Y'.(Preferences('DEFAULT_ALL_SCHOOLS')=='Y'?' CHECKED':'').'>Search All Schools<BR>';
			echo '<INPUT type=checkbox name=_dis_user value=Y>Include Disabled Staff<BR><br>';
		
		echo "<INPUT type=SUBMIT class=btn_medium value='Submit' onclick='formload_ajax(\"search\");'>&nbsp<INPUT type=RESET class=btn_medium value='Reset'>";
		echo '</TD></TR>';
		echo '</TABLE>';
		/********************for Back to user***************************/
                    echo '<input type=hidden name=sql_save_session_staf value=true />';
                /************************************************/
		echo '</FORM>';
		// set focus to last name text box
		echo '<script type="text/javascript"><!--
			document.search.last.focus();
			--></script>';
		PopTable('footer');
	}

	
	else
	{
		if(!$_REQUEST['next_modname'])
			$_REQUEST['next_modname'] = 'users/Staff.php';

		if(!isset($_openSIS['DrawHeader'])) DrawHeaderHome('Please select a Staff');

		$staff_RET = GetUserStaffList($extra);
                $last_log=DBGet(DBQuery('SELECT DISTINCT CONCAT(s.LAST_NAME,  \' \' ,s.FIRST_NAME) AS FULL_NAME,
					s.PROFILE,s.PROFILE_ID,ssr.END_DATE,s.STAFF_ID,\' \' as LAST_LOGIN FROM staff s INNER JOIN staff_school_relationship ssr USING(staff_id) WHERE
					((s.PROFILE_ID!=4 AND s.PROFILE_ID!=3) OR s.PROFILE_ID IS NULL) AND ssr.SYEAR=\''.UserSyear().'\'  AND s.STAFF_ID NOT IN (SELECT USER_ID FROM login_authentication WHERE PROFILE_ID NOT IN (3,4))'));
                foreach($last_log as $li=>$ld)
                {
                    $staff_RET[]=$ld;
                }
                $_SESSION['count_stf'] =  count($staff_RET);
		if($extra['profile'])
		{
			$options = array('admin'=>'Administrator','teacher'=>'Teacher','parent'=>'Parent','none'=>'No Access');
			if($extra['profile']=='teachers_option')
                        {
                        $singular = 'Teacher';
			$plural = 'Teachers';
                        }
                        elseif($extra['profile']=='teachers_option_all')
                        {
                        $singular = 'Teacher';
			$plural = 'Teachers';
                        }
                        else 
                        {
                        $singular = $options[$extra['profile']];
			$plural = $singular.($options[$extra['profile']]=='none'?'es':'s');
                        }
                        
			$columns = array('FULL_NAME'=>$singular,'STAFF_ID'=>'Staff ID');
		}
		else
		{
			$singular = 'Staff';
			$plural = 'Staffs';
                        if($_REQUEST['_dis_user'])
			$columns = array('FULL_NAME'=>'Staff Member','PROFILE'=>'Profile','STAFF_ID'=>'Staff ID','Status'=>'Status');
                        else
			$columns = array('FULL_NAME'=>'Staff Member','PROFILE'=>'Profile','STAFF_ID'=>'Staff ID');
		}
		if(is_array($extra['columns_before']))
			$columns = $extra['columns_before'] + $columns;
		if(is_array($extra['columns_after']))
			$columns += $extra['columns_after'];
		if(is_array($extra['link']))
			$link = $extra['link'];
		else
		{
			$link['FULL_NAME']['link'] = "Modules.php?modname=$_REQUEST[next_modname]";
		
			$link['FULL_NAME']['variables'] = array('staff_id'=>'STAFF_ID');
		}
		
		
                if($_REQUEST['_dis_user'])
                {
                    foreach($staff_RET as $i=>$d)
                    {
                       
                        
                        if(($d['END_DATE']=='0000-00-00' || $d['END_DATE']>=date('Y-m-d') || $d['END_DATE']==NULL) && $d['IS_DISABLE']!='Y')
                            $staff_RET[$i]['Status']='<font style="color:green">Active</font>';
                        else
                            $staff_RET[$i]['Status']='<font style="color:red">Inactive</font>';
                    }
                }
		ListOutput($staff_RET,$columns,$singular,$plural,$link,false,$extra['options']);
	}
}

function makeLogin($value)
{
	return ProperDate(substr($value,0,10)).substr($value,10);
}
?>