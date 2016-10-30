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
DrawBC("Users > ".ProgramTitle());
$_openSIS['allow_edit'] = true;

if(clean_param($_REQUEST['tables'],PARAM_NOTAGS) && ($_POST['tables'] || $_REQUEST['ajax']))
{
	$table = strtolower($_REQUEST['table']);
	foreach($_REQUEST['tables'] as $id=>$columns)
	{
		if($id!='new')
		{
			if($columns['CATEGORY_ID'] && $columns['CATEGORY_ID']!=$_REQUEST['category_id'])
				$_REQUEST['category_id'] = $columns['CATEGORY_ID'];

			$sql = "UPDATE $table SET ";

			foreach($columns as $column=>$value){
                            if($column=='TITLE' && $value!='')
                            {
                            $value=str_replace("'","''",clean_param(trim($value),PARAM_SPCL));    
                            }
                            $sql .= $column."='".$value."',";
                            
                            
                        }
			$sql = substr($sql,0,-1) . " WHERE ID='$id'";
			$go = true;
		}
		else
		{
			$sql = "INSERT INTO $table ";

			if($table=='staff_fields')
			{
				if($columns['CATEGORY_ID'])
				{
					$_REQUEST['category_id'] = $columns['CATEGORY_ID'];
					unset($columns['CATEGORY_ID']);
				}
				
                                $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'staff_fields'"));
                                $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
				$id = $id[1]['ID'];

                                $fields = "CATEGORY_ID,";
				$values = "'".$_REQUEST['category_id']."',";
				$_REQUEST['id'] = $id;
                                
				switch($columns['TYPE'])
				{
					case 'radio':
						$Sql_add_column="ALTER TABLE staff ADD CUSTOM_$id VARCHAR(1) ";
					break;

					case 'text':
					case 'select':
					case 'autos':
					case 'edits':
					$Sql_add_column="ALTER TABLE staff ADD CUSTOM_$id VARCHAR(255) ";
					break;

					case 'codeds':
						$Sql_add_column="ALTER TABLE staff ADD CUSTOM_$id VARCHAR(15)";
					break;

					case 'multiple':
					$Sql_add_column="ALTER TABLE staff ADD CUSTOM_$id VARCHAR(1000)";
					break;

					case 'numeric':
				$Sql_add_column="ALTER TABLE staff ADD CUSTOM_$id NUMERIC(10,2) ";
					break;

					case 'date':
						$Sql_add_column="ALTER TABLE staff ADD CUSTOM_$id VARCHAR(128)";
					break;

					case 'textarea':
					$Sql_add_column="ALTER TABLE staff ADD CUSTOM_$id VARCHAR(5000) ";
					break;
				}
			if($columns['DEFAULT_SELECTION']){
		$Sql_add_column.=" NOT NULL DEFAULT  '".$columns['DEFAULT_SELECTION']."' ";
				}elseif($columns['REQUIRED']){
		$Sql_add_column.=" NOT NULL ";
				}
				DBQuery($Sql_add_column);
				DBQuery("CREATE INDEX CUSTOM_IND$id ON staff (CUSTOM_$id)");
unset($table);
			}
			elseif($table=='staff_field_categories')
			{
				
                                $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'staff_field_categories'"));
                                $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
				$id = $id[1]['ID'];
                                if($id==5)
                                {
                                    $id++;
                                   DBQuery("ALTER TABLE staff_field_categories AUTO_INCREMENT =$id");
                                }
				$fields = "";
				$values ="";
                                
                                $id=DBGet(DBQuery('SELECT MAX(ID) as MAX_ID FROM staff_field_categories'));
                                $id=$id[1]['MAX_ID']+1;
				$_REQUEST['category_id'] = $id;
                                $fields.= "ID,";
				$values.=$id.',';
				if(User('PROFILE_ID')!='')
					DBQuery("INSERT INTO profile_exceptions (PROFILE_ID,MODNAME,CAN_USE,CAN_EDIT) values('".User('PROFILE_ID')."','users/Staff.php&category_id=$id','Y','Y')");
				else
                                {
                                    $profile_id_mod=DBGet(DBQuery("SELECT PROFILE_ID FROM staff WHERE USER_ID='".User('STAFF_ID')));
                                    $profile_id_mod=$profile_id_mod[1]['PROFILE_ID'];   
                                    if($profile_id_mod!='') 
                                    DBQuery("INSERT INTO profile_exceptions (PROFILE_ID,MODNAME,CAN_USE,CAN_EDIT) values('".$profile_id_mod."','users/Staff.php&category_id=$id','Y','Y')");
                                }
			}

			$go = false;

			foreach($columns as $column=>$value)
			{
				if($value)
				{  
                                        if($column=='TITLE' && $value!='')
                                        {
                                        $value=str_replace("'","''",clean_param(trim($value),PARAM_SPCL));    
                                        }
					$fields .= $column.',';
					$values .= "'".$value."',";
					$go = true;
				}
			}
			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
		}

		if($go)
			DBQuery($sql);
	}
	unset($_REQUEST['tables']);
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='delete')
{
	if(clean_param($_REQUEST['id'],PARAM_INT))
	{
            $fid=$_REQUEST['id'];
            $has_assigned_RET=DBGet(DBQuery("SELECT COUNT(CUSTOM_$fid) AS TOTAL_ASSIGNED FROM staff WHERE CUSTOM_$fid<>'' OR CUSTOM_$fid IS NULL"));
            $has_assigned=$has_assigned_RET[1]['TOTAL_ASSIGNED'];
            if($has_assigned>0){
            UnableDeletePromptMod('Cannot delete becauses this staff field is associated.');
            }
            else{
		if(DeletePromptMod('staff field'))
		{
			$id = clean_param($_REQUEST['id'],PARAM_INT);
			DBQuery("DELETE FROM staff_fields WHERE ID='$id'");
			DBQuery("ALTER TABLE staff DROP COLUMN CUSTOM_$id");
			$_REQUEST['modfunc'] = '';
			unset($_REQUEST['id']);
		}
            }
	}
	elseif(clean_param($_REQUEST['category_id'],PARAM_INT))
	{
            $has_assigned_RET=DBGet(DBQuery('SELECT COUNT(*) AS TOTAL_ASSIGNED FROM staff_fields WHERE CATEGORY_ID=\''.$_REQUEST['category_id'].'\''));
            $has_assigned=$has_assigned_RET[1]['TOTAL_ASSIGNED'];
            if($has_assigned>0){
            UnableDeletePromptMod('Cannot delete becauses this staff field category is associated.');
            }
            else{
                    if(DeletePromptMod('staff field category and all fields in the category'))
                    {
                            $fields = DBGet(DBQuery("SELECT ID FROM staff_fields WHERE CATEGORY_ID='$_REQUEST[category_id]'"));
                            foreach($fields as $field)
                            {
                                    DBQuery("DELETE FROM staff_fields WHERE ID='$field[ID]'");
                                    DBQuery("ALTER TABLE staff DROP COLUMN CUSTOM_$field[ID]");
                            }
                            DBQuery("DELETE FROM staff_field_categories WHERE ID='$_REQUEST[category_id]'");
                            // remove from profiles and permissions
                            DBQuery("DELETE FROM profile_exceptions WHERE MODNAME='users/User/Student.php&category_id=$_REQUEST[category_id]'");

                            $_REQUEST['modfunc'] = '';
                            unset($_REQUEST['category_id']);
                    }
            }
	}
}

if(!$_REQUEST['modfunc'])
{
	// CATEGORIES
	$sql = "SELECT ID,TITLE,SORT_ORDER FROM staff_field_categories ORDER BY SORT_ORDER,TITLE";
	$QI = DBQuery($sql);
	$RET = DBGet($QI);

        $new_tabs=array();
         unset($new_tabs);
        unset($ti);
        unset($td);
        $swap_tabs='n';
        foreach($RET as $ti=>$td)
        {
            if($td['TITLE']=='School Information')
                $swap_tabs='y';
        }

        if($swap_tabs=='y')
        {$c=3;
        foreach($RET as $ti=>$td)
        {
            if($td['TITLE']=='Demographic Info')
                
               $new_tabs[1]=$td;
            elseif($td['TITLE']=='School Information')
               $new_tabs[2]=$td;

            else
            {
               $new_tabs[$c]=$td;
               $c=$c+1;
            }
               
        
           
        }
        }
        
        echo '<br><br>';
        if(count($new_tabs))
        {
        unset($RET);
        $tabs=$new_tabs;
        ksort($tabs);
        }
     
        
        unset($new_tabs);
        unset($ti);
        unset($td);
     
        $swap_tabs='n';
        $si=1;
                foreach($tabs as $ci=>$cd)
                {
                    $categories_RET2[$si]=$cd;
                    $categories_RET2[$si]['SORT_ORDER']=$si;
                    $si++;
                }
 $categories_RET =$categories_RET2;
	if(AllowEdit() && $_REQUEST['id']!='new' && $_REQUEST['category_id']!='new' && ($_REQUEST['id'] || $_REQUEST['category_id']>4))
		$delete_button = "<INPUT type=button class=btn_medium value=Delete onClick='javascript:window.location=\"Modules.php?modname=$_REQUEST[modname]&modfunc=delete&category_id=$_REQUEST[category_id]&id=$_REQUEST[id]\"'> ";

	// ADDING & EDITING FORM
	if($_REQUEST['id'] && $_REQUEST['id']!='new')
	{
		$sql = "SELECT CATEGORY_ID,TITLE,TYPE,SELECT_OPTIONS,DEFAULT_SELECTION,SORT_ORDER,REQUIRED,REQUIRED,(SELECT TITLE FROM staff_field_categories WHERE ID=CATEGORY_ID) AS CATEGORY_TITLE FROM staff_fields WHERE ID='$_REQUEST[id]'";
		$RET = DBGet(DBQuery($sql));
		$RET = $RET[1];
		$title = $RET['CATEGORY_TITLE'].' - '.$RET['TITLE'];
	}
	elseif($_REQUEST['category_id'] && $_REQUEST['category_id']!='new' && $_REQUEST['id']!='new')
	{
		$sql = "SELECT ID,TITLE,ADMIN,TEACHER,PARENT,NONE,SORT_ORDER,INCLUDE
				FROM staff_field_categories
				WHERE ID='$_REQUEST[category_id]'";
		$RET = DBGet(DBQuery($sql));
		$RET = $RET[1];
		$title = $RET['TITLE'];
	}
	elseif($_REQUEST['id']=='new')
		$title = 'New Staff Field';
	elseif($_REQUEST['category_id']=='new')
		$title = 'New Staff Field Category';

	if($_REQUEST['id'])
	{
		echo "<FORM name=F1 id=F1 action=Modules.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]";
		if($_REQUEST['id']!='new')
			echo "&id=$_REQUEST[id]";
		echo "&table=STAFF_FIELDS method=POST>";

		DrawHeaderHome($title,$delete_button.SubmitButton('Save','','class=btn_medium onclick="formcheck_user_userfields_F1();"')); //'<INPUT type=submit value=Save>');
		$header .= '<TABLE cellpadding=3 width=100%>';
                $header .= '<TR><TD><input type=hidden id=f_id value="'.$_REQUEST['id'].'"/></TD></TR>';
		$header .= '<TR>';

		$header .= '<TD>' . TextInput($RET['TITLE'],'tables['.$_REQUEST['id'].'][TITLE]','Field Name') . '</TD>';

		// You can't change a student field type after it has been created
		// mab - allow changing between select and autos and edits and text
		if($_REQUEST['id']!='new')
		{

				$type_options = array('select'=>'Pull-Down','autos'=>'Auto Pull-Down','edits'=>'Edit Pull-Down','text'=>'Text','radio'=>'Checkbox','codeds'=>'Coded Pull-Down','numeric'=>'Number','multiple'=>'Select Multiple from Options','date'=>'Date','textarea'=>'Long Text');
			
		}
		else
			$type_options = array('select'=>'Pull-Down','autos'=>'Auto Pull-down','edits'=>'Edit Pull-Down','text'=>'Text','radio'=>'Checkbox','codeds'=>'Coded Pull-Down','numeric'=>'Number','multiple'=>'Select Multiple from Options','date'=>'Date','textarea'=>'Long Text');

		$header .= '<TD>' . SelectInput($RET['TYPE'],'tables['.$_REQUEST['id'].'][TYPE]','Data Type',$type_options,false) . '</TD>';
		if($_REQUEST['id']!='new' && $RET['TYPE']!='multiple' && $RET['TYPE']!='codeds' && $RET['TYPE']!='select' && $RET['TYPE']!='autos' && $RET['TYPE']!='edits' && $RET['TYPE']!='text')
		{
			$_openSIS['allow_edit'] = $allow_edit;
			$_openSIS['AllowEdit'][$modname] = $AllowEdit;
		}
		foreach($categories_RET as $type)
			$categories_options[$type['ID']] = $type['TITLE'];

		$header .= '<TD>' . SelectInput($RET['CATEGORY_ID']?$RET['CATEGORY_ID']:$_REQUEST['category_id'],'tables['.$_REQUEST['id'].'][CATEGORY_ID]','User Field Category',$categories_options,false) . '</TD>';

		$header .= '<TD>' . TextInput($RET['SORT_ORDER'],'tables['.$_REQUEST['id'].'][SORT_ORDER]','Sort Order') . '</TD>';

		$header .= '</TR><TR>';
		$colspan = 2;
		if($RET['TYPE']=='autos' || $RET['TYPE']=='edits' || $RET['TYPE']=='select' || $RET['TYPE']=='codeds' || $RET['TYPE']=='multiple' || $_REQUEST['id']=='new')
		{
			$header .= '<TD colspan=2>'.TextAreaInput($RET['SELECT_OPTIONS'],'tables['.$_REQUEST['id'].'][SELECT_OPTIONS]','Pull-Down/Auto Pull-Down/Coded Pull-Down/Select Multiple Choices<BR>* one per line','rows=7 cols=40') . '</TD>';
			$colspan = 1;
		}
		$header .= '<TD valign=bottom colspan='.$colspan.'>'.TextInput($RET['DEFAULT_SELECTION'],'tables['.$_REQUEST['id'].'][DEFAULT_SELECTION]','Default').'<small><BR>* for dates: YYYY-MM-DD,<BR> for checkboxes: Y</small></TD>';

		$new = ($_REQUEST['id']=='new');
		$header .= '<TD>' . CheckboxInput($RET['REQUIRED'],'tables['.$_REQUEST['id'].'][REQUIRED]','Required','',$new) . '</TD>';

		$header .= '</TR>';
		$header .= '</TABLE>';
	}
	elseif($_REQUEST['category_id'])
	{
		echo "<FORM name=F2 id=F2 action=Modules.php?modname=$_REQUEST[modname]&table=STAFF_FIELD_CATEGORIES";
		if($_REQUEST['category_id']!='new')
			echo "&category_id=$_REQUEST[category_id]";
		echo " method=POST>";
		DrawHeaderHome($title,$delete_button.SubmitButton('Save','','class=btn_medium onclick="formcheck_user_stafffields_F2();"')); //'<INPUT type=submit value=Save>');
		$header .= '<TABLE cellpadding=3 width=100%>';
                $header .= '<TR><TD><input type=hidden id=t_id value="'.$_REQUEST['category_id'].'"/></TD></TR>';
		$header .= '<TR>';
                
		$header .= '<TD>' . (($RET['ID']>5 || $RET['ID']=='')?TextInput($RET['TITLE'],'tables['.$_REQUEST['category_id'].'][TITLE]','Title'):NoInput($RET['TITLE'],'Title')). '</TD>';


          
            $header .= '<TD>' . (($RET['SORT_ORDER']>5 || $RET['SORT_ORDER']=='')?TextInput($RET['SORT_ORDER'],'tables['.$_REQUEST['category_id'].'][SORT_ORDER]','Sort Order'):NoInput($RET['SORT_ORDER'],'Sort Order')) . '</TD>';
		$new = ($_REQUEST['category_id']=='new');
		$header .= '<TD><TABLE><TR>';
		$header .= '<TD>' . (($RET['ID']>5 || $RET['ID']=='')?CheckboxInput($RET['ADMIN'],'tables['.$_REQUEST['category_id'].'][ADMIN]',($_REQUEST['category_id']=='1'&&!$RET['ADMIN']?'<FONT color=red>':'').'Administrator'.($_REQUEST['category_id']=='1'&&!$RET['ADMIN']?'</FONT>':''),'',$new,'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>'): NoInput(($RET['ADMIN']=='Y'?'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>':'<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>'),'Administrator')) . '</TD>';
		$header .= '<TD>' . (($RET['ID']>5 || $RET['ID']=='')?CheckboxInput($RET['TEACHER'],'tables['.$_REQUEST['category_id'].'][TEACHER]',($_REQUEST['category_id']=='1'&&!$RET['TEACHER']?'<FONT color=red>':'').'Teacher'.($_REQUEST['category_id']=='1'&&!$RET['TEACHER']?'</FONT>':''),'',$new,'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>'): NoInput(($RET['TEACHER']=='Y'?'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>':'<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>'),'Teacher')) . '</TD>';
		$header .= '<TD>' . (($RET['ID']>5 || $RET['ID']=='')?CheckboxInput($RET['PARENT'],'tables['.$_REQUEST['category_id'].'][PARENT]',($_REQUEST['category_id']=='1'&&!$RET['PARENT']?'<FONT color=red>':'').'Parent'.($_REQUEST['category_id']=='1'&&!$RET['TEACHER']?'</FONT>':''),'',$new,'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>'): NoInput(($RET['PARENT']=='Y'?'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>':'<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>'),'Parent')) . '</TD>';
		$header .= '<TD>' . (($RET['ID']>5 || $RET['ID']=='')?CheckboxInput($RET['NONE'],'tables['.$_REQUEST['category_id'].'][NONE]',($_REQUEST['category_id']=='1'&&!$RET['NONE']?'<FONT color=red>':'').'No Access'.($_REQUEST['category_id']=='1'&&!$RET['TEACHER']?'</FONT>':''),'',$new,'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>') : NoInput(($RET['NONE']=='Y'?'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>':'<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>'),'No Access')). '</TD>';
		$header .= '</TR>';
		$header .= '<TR><TD colspan=4><small><FONT color='.Preferences('TITLES').'>Profiles</FONT></small></TD></TR>';
		$header .= '</TABLE></TD>';

		if($_REQUEST['category_id']>2 || $new)
		{
			$header .= '</TR><TR>';
			$header .= '<TD colspan=2></TD>';
			$header .= '<TD>' . ($RET['ID']>4?TextInput($RET['INCLUDE'],'tables['.$_REQUEST['category_id'].'][INCLUDE]','Include (should be left blank for most categories)'):''). '</TD>';
		}

		$header .= '</TR>';
		$header .= '</TABLE>';
	}
	else
		$header = false;

	if($header)
	{
		DrawHeaderHome($header);
		echo '</FORM>';
	}

	// DISPLAY THE MENU
	$LO_options = array('save'=>false,'search'=>false,'add'=>true);

	echo '<TABLE><TR>';

	if(count($categories_RET))
	{
		if($_REQUEST['category_id'])
		{
			foreach($categories_RET as $key=>$value)
			{
				if($value['ID']==$_REQUEST['category_id'])
					$categories_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
			}
		}
	}

	echo '<TD valign=top width=48%>';
	$columns = array('TITLE'=>'Category','SORT_ORDER'=>'Order');
	$link = array();
	$link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]";

	$link['TITLE']['variables'] = array('category_id'=>'ID');

	$link['add']['link'] = "#"." onclick='check_content(\"Ajax.php?modname=$_REQUEST[modname]&category_id=new\");'";

	ListOutput($categories_RET,$columns,'Staff Field Category','Staff Field Categories',$link,array(),$LO_options);
	echo '</TD>';

	// FIELDS
	if($_REQUEST['category_id'] && $_REQUEST['category_id']!='new' && count($categories_RET))
	{

                $sql = "SELECT ID,TITLE,TYPE,SORT_ORDER FROM staff_fields WHERE CATEGORY_ID='".$_REQUEST['category_id']."' ORDER BY SORT_ORDER,TITLE";
		$fields_RET = DBGet(DBQuery($sql),array('TYPE'=>'_makeType'));

		if(count($fields_RET))
		{
			if($_REQUEST['id'] && $_REQUEST['id']!='new')
			{
				foreach($fields_RET as $key=>$value)
				{
					if($value['ID']==$_REQUEST['id'])
						$fields_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
				}
			}
		}

		echo '<td class=vbreak></td><TD valign=top  width=48%>';
		$columns = array('TITLE'=>'User Field','SORT_ORDER'=>'Order','TYPE'=>'Data Type');
		$link = array();
		$link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]";
	
		
		$link['TITLE']['variables'] = array('id'=>'ID');
		$link['add']['link'] = "#"." onclick='check_content(\"Ajax.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]&id=new\");'";


                
                $count=0;
                $count++;
                switch ($_REQUEST[category_id]) {
                    case 1:
                             $arr=array('Name','Staff Id','Alternate Id','Gender','Date of Birth','Ethnicity','Primary Language','Second Language','Third Language','Email','Physical Disablity');
                             $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]";
                             $link['add']['link'] = "#"." onclick='check_content(\"Ajax.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]&id=new\");'";
                             $link['TITLE']['variables'] = array('id'=>'ID');

                        break;
                    case 2:
                             $arr=array('Street Address 1','Street Address 2','City','State','Zip Code','Home Phone'
                                 ,'Mobile Phone','Office Phone','Work Email','Personal Email','Relationship to Staff');
                             $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]";
                             $link['add']['link'] = "#"." onclick='check_content(\"Ajax.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]&id=new\");'";
                             $link['TITLE']['variables'] = array('id'=>'ID');

                        break;
                     case 3:
                             $arr=array('Category','Job Title','Joining Date','End Date','Profile','Username','Password','Disable User');

                        break;
                    case 4:
                             $arr=array('Certificate Name','Certificate Code','Certificate Short Name','Primary Certification Indicator','Certification Date','Certification Expiry Date','Certification Details');
                        break;
                    
                    default:
                            $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]";
                   

                            $link['TITLE']['variables'] = array('id'=>'ID');
                    
                            $link['add']['link'] = "#"." onclick='check_content(\"Ajax.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]&id=new\");'";


                        break;
                }
                foreach ($arr as $key => $value) {
                    $fields_RET1[$count]=array('ID'=>'','SORT_ORDER'=>($key+1),'TITLE'=>$value,'TYPE'=>'<span style="color:#ea8828;">Default</span>');
                    $count++;
                }
                $count2=1;
                foreach ($fields_RET1 as $key2) {
                    $dd[$count2]=$key2;
                    $count2++;
                }
                foreach ($fields_RET as  $row) {
                        $dd[$count2]=$row;
                    $count2++;
                       
                    }
		ListOutput($dd,$columns,'Staff Field','Staff Fields',$link,array(),$LO_options);
                
		echo '</TD>';
	}

	echo '</TR></TABLE>';
}

function _makeType($value,$name)
{
	$options = array('radio'=>'Checkbox','text'=>'Text','autos'=>'Auto Pull-Down','edits'=>'Edit Pull-Down','select'=>'Pull-Down','codeds'=>'Coded Pull-Down','date'=>'Date','numeric'=>'Number','textarea'=>'Long Text','multiple'=>'Select Multiple');
	return $options[$value];
}




?>
