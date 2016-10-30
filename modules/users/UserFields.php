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
$not_default=false;
if(clean_param($_REQUEST['tables'],PARAM_NOTAGS) && ($_POST['tables'] || $_REQUEST['ajax']))
{
	$table = $_REQUEST['table'];
	foreach($_REQUEST['tables'] as $id=>$columns)
	{
		if($id!='new')
		{
                           if(isset($columns['TITLE']) && trim($columns['TITLE'])=='')
                           {
                               echo "<font color='red'><b>Unable to save data, because title can not be blank</b></font>";
                           }
                           else
                           {
                                        if($columns['CATEGORY_ID'] && $columns['CATEGORY_ID']!=$_REQUEST['category_id'])
                                        $_REQUEST['category_id'] = $columns['CATEGORY_ID'];

                                        $sql = 'UPDATE '.$table.' SET ';

                                        foreach($columns as $column=>$value){

                                            if($column=='TITLE' && $value!='')
                                            {
                                            $value=str_replace("'","''",clean_param(trim($value),PARAM_SPCL));    
                                            }
                                            $sql .= $column.'=\''.$value.'\',';
                                        }
                                        $sql = substr($sql,0,-1) . ' WHERE ID=\''.$id.'\'';
                                        $go = true;
                                        if($table=='people_fields')
                                        $custom_field_id=$id;
                           }
		}
		else
		{
			$sql = 'INSERT INTO '.$table .'';

			if($table=='people_fields')
			{
				if($columns['CATEGORY_ID'])
				{
					$_REQUEST['category_id'] = $columns['CATEGORY_ID'];
					unset($columns['CATEGORY_ID']);
				}
				
                                $id = DBGet(DBQuery('SHOW TABLE STATUS LIKE \''.'people_fields'.'\''));
                                $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
				$id = $id[1]['ID'];
				$fields = 'CATEGORY_ID,';
				$values = '\''.$_REQUEST['category_id'].'\',';
				$_REQUEST['id'] = $id;

				switch($columns['TYPE'])
				{
					case 'radio':
						$Sql_add_column='ALTER TABLE people ADD CUSTOM_'.$id.' VARCHAR(1) ';
					break;

					case 'text':
                                                                                            $Sql_add_column='ALTER TABLE people ADD CUSTOM_'.$id.' VARCHAR(255) ';
                                                                                            break;
					case 'select':
					case 'autos':
					case 'edits':
					$Sql_add_column='ALTER TABLE people ADD CUSTOM_'.$id.' VARCHAR(100) ';
					break;

					case 'codeds':
						$Sql_add_column='ALTER TABLE people ADD CUSTOM_'.$id.' VARCHAR(15)';
					break;

					case 'multiple':
					$Sql_add_column='ALTER TABLE people ADD CUSTOM_'.$id.' VARCHAR(255)';
					break;

					case 'numeric':
				$Sql_add_column='ALTER TABLE people ADD CUSTOM_'.$id.' NUMERIC(20,2) ';
                                                                               if(!is_numeric($columns['DEFAULT_SELECTION'])){
                                                                                                                $not_default=true;
                                                                                                                $columns['DEFAULT_SELECTION']='';
                                                                                                            }
					break;

					case 'date':
						$Sql_add_column='ALTER TABLE people ADD CUSTOM_'.$id.' DATE ';
                                                                                        if(preg_match("/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/", $columns['DEFAULT_SELECTION']) === 0){
                                                                                                                            $not_default=true;
                                                                                                                            $columns['DEFAULT_SELECTION']='';
                                                                                                                        }
					break;

					case 'textarea':
					$Sql_add_column='ALTER TABLE people ADD CUSTOM_'.$id.' LONGTEXT ';
                                                                                            $not_default=true;
					break;
				}
                                                                        if($columns['REQUIRED']){
		$Sql_add_column.=' NOT NULL ';
                                                                        }else{
                                                                            $Sql_add_column.=' NULL ';
				}
                                                                        if($columns['DEFAULT_SELECTION']  && $not_default==false){
                                                                            $Sql_add_column.=' DEFAULT  \''.$columns['DEFAULT_SELECTION'].'\' ';
                                                                        }
				DBQuery($Sql_add_column);
                                                                        if($columns['TYPE']!='textarea')
				DBQuery('CREATE INDEX CUSTOM_IND'.$id.' ON people (CUSTOM_'.$id.')');
unset($table);
			}
			elseif($table=='people_field_categories')
			{
				
                                $id = DBGet(DBQuery('SHOW TABLE STATUS LIKE \''.'people_field_categories'.'\''));
                                $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
				$id = $id[1]['ID'];
				$fields = '';
				$values ='';
				$_REQUEST['category_id'] = $id;
				// add to profile or permissions of user creating it
				if(User('PROFILE_ID')!='')
					DBQuery('INSERT INTO profile_exceptions (PROFILE_ID,MODNAME,CAN_USE,CAN_EDIT) values(\''.User('PROFILE_ID').'\',\'users/User.php&category_id='.$id.'\',\'Y\',\'Y\')');
				else
					DBQuery('INSERT INTO staff_exceptions (USER_ID,MODNAME,CAN_USE,CAN_EDIT) values(\''.User('STAFF_ID').'\',\'users/User.php&category_id='.$id.'\',\'Y\',\'Y\')');
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
					$values .= '\''.$value.'\',';
					$go = true;
				}
			}
			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
		}

		if($go)
                                        DBQuery($sql);
                if($custom_field_id)
                {
                    $custom_update=DBGet(DBQuery('SELECT TYPE,REQUIRED,DEFAULT_SELECTION FROM people_fields WHERE ID=\''.$custom_field_id.'\''));
                    $custom_update=$custom_update[1];
                    switch($custom_update['TYPE'])
                    {
                            case 'radio':
                            $Sql_modify_column='ALTER TABLE people MODIFY CUSTOM_'.$id.' VARCHAR(1) ';
                            break;

                            case 'text':
                            $Sql_modify_column='ALTER TABLE people MODIFY CUSTOM_'.$id.' VARCHAR(255)';
                            break;

                            case 'select':
                            case 'autos':
                            case 'edits':
                            $Sql_modify_column='ALTER TABLE people MODIFY CUSTOM_'.$id.' VARCHAR(100)';
                            break;
                            
                            case 'codeds':
                            $Sql_modify_column='ALTER TABLE people MODIFY CUSTOM_'.$id.' VARCHAR(15)';
                            break;

                            case 'multiple':
                            $Sql_modify_column='ALTER TABLE people MODIFY CUSTOM_'.$id.' VARCHAR(255)';
                            break;

                            case 'numeric':
                            $Sql_modify_column='ALTER TABLE people MODIFY CUSTOM_'.$id.' NUMERIC(20,2)';
                            if(!is_numeric($columns['DEFAULT_SELECTION'])){
                                $not_default=true;
                    }
                            break;

                            case 'date':
                            $Sql_modify_column='ALTER TABLE people MODIFY CUSTOM_'.$id.'  DATE';
                            if(preg_match("/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/", $columns['DEFAULT_SELECTION']) === 0){
                                $not_default=true;
                    }
                            break;

                            case 'textarea':
                                    $Sql_modify_column='ALTER TABLE people MODIFY CUSTOM_'.$id.' LONGTEXT';
                                    $not_default=true;
                            break;
                }
                
                if($custom_update['REQUIRED']){
                        $Sql_modify_column.=' NOT NULL ';
                }else{
                    $Sql_modify_column.=' NULL ';
	}
                if($custom_update['DEFAULT_SELECTION'] && $not_default==false){
                    $Sql_modify_column.=' DEFAULT  \''.$custom_update['DEFAULT_SELECTION'].'\' ';
                }
                    DBQuery($Sql_modify_column);
            }
                
	}
	unset($_REQUEST['tables']);
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='delete')
{
	if(clean_param($_REQUEST['id'],PARAM_INT))
	{
            $fid=$_REQUEST['id'];
            $has_assigned_RET=DBGet(DBQuery("SELECT COUNT(CUSTOM_$fid) AS TOTAL_ASSIGNED FROM people WHERE CUSTOM_$fid<>'' OR CUSTOM_$fid IS NULL"));
            $has_assigned=$has_assigned_RET[1]['TOTAL_ASSIGNED'];
            if($has_assigned>0){
            UnableDeletePromptMod('Cannot delete becauses this people field is associated.');
            }
            else{
		if(DeletePromptMod('staff field'))
		{
			$id = clean_param($_REQUEST['id'],PARAM_INT);
			DBQuery('DELETE FROM people_fields WHERE ID=\''.$id.'\'');
			DBQuery('ALTER TABLE people DROP COLUMN CUSTOM_'.$id.'');
			$_REQUEST['modfunc'] = '';
			unset($_REQUEST['id']);
		}
	}
	}
	elseif(clean_param($_REQUEST['category_id'],PARAM_INT))
	{
            $has_assigned_RET=DBGet(DBQuery('SELECT COUNT(*) AS TOTAL_ASSIGNED FROM people_fields WHERE CATEGORY_ID=\''.$_REQUEST['category_id'].'\''));
            $has_assigned=$has_assigned_RET[1]['TOTAL_ASSIGNED'];
            if($has_assigned>0){
            UnableDeletePromptMod('Cannot delete becauses this people field category is associated.');
            }
            else{
                    if(DeletePromptMod('staff field category and all fields in the category'))
		{
			$fields = DBGet(DBQuery('SELECT ID FROM people_fields WHERE CATEGORY_ID=\''.$_REQUEST[category_id].'\''));
			foreach($fields as $field)
			{
				DBQuery('DELETE FROM people_fields WHERE ID=\''.$field[ID].'\'');
				DBQuery('ALTER TABLE people DROP COLUMN CUSTOM_'.$field[ID].'');
			}
			DBQuery('DELETE FROM people_field_categories WHERE ID=\''.$_REQUEST[category_id].'\'');
			// remove from profiles and permissions
			DBQuery('DELETE FROM profile_exceptions WHERE MODNAME=\'users/User/Student.php&category_id='.$_REQUEST[category_id].'\'');
			$_REQUEST['modfunc'] = '';
			unset($_REQUEST['category_id']);
		}
	}
}
}

if(!$_REQUEST['modfunc'])
{
	// CATEGORIES
	$sql = 'SELECT ID,TITLE,SORT_ORDER FROM people_field_categories ORDER BY SORT_ORDER,TITLE';
	$QI = DBQuery($sql);
	$categories_RET = DBGet($QI);

	if(AllowEdit() && $_REQUEST['id']!='new' && $_REQUEST['category_id']!='new' && ($_REQUEST['id'] || $_REQUEST['category_id']>2))
		$delete_button = "<INPUT type=button class=btn_medium value=Delete onClick='javascript:window.location=\"Modules.php?modname=$_REQUEST[modname]&modfunc=delete&category_id=$_REQUEST[category_id]&id=$_REQUEST[id]\"'> ";

	// ADDING & EDITING FORM
	if($_REQUEST['id'] && $_REQUEST['id']!='new')
	{
		$sql = 'SELECT CATEGORY_ID,TITLE,TYPE,SELECT_OPTIONS,DEFAULT_SELECTION,SORT_ORDER,REQUIRED,REQUIRED,(SELECT TITLE FROM people_field_categories WHERE ID=CATEGORY_ID) AS CATEGORY_TITLE FROM people_fields WHERE ID=\''.$_REQUEST[id].'\'';
		$RET = DBGet(DBQuery($sql));
		$RET = $RET[1];
		$title = $RET['CATEGORY_TITLE'].' - '.$RET['TITLE'];
	}
	elseif($_REQUEST['category_id'] && $_REQUEST['category_id']!='new' && $_REQUEST['id']!='new')
	{
		$sql = 'SELECT ID, TITLE,ADMIN,TEACHER,PARENT,NONE,SORT_ORDER,INCLUDE
				FROM people_field_categories
				WHERE ID=\''.$_REQUEST[category_id].'\'';
		$RET = DBGet(DBQuery($sql));
		$RET = $RET[1];
		$title = $RET['TITLE'];
	}
	elseif($_REQUEST['id']=='new')
		$title = 'New User Field';
	elseif($_REQUEST['category_id']=='new')
		$title = 'New User Field Category';

	if($_REQUEST['id'])
	{
		echo "<FORM name=F1 id=F1 action=Modules.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]";
		if($_REQUEST['id']!='new')
			echo "&id=$_REQUEST[id]";
		echo "&table=people_fields method=POST>";

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

		$header .= '<TD>' . SelectInput($RET['TYPE'],'tables['.$_REQUEST['id'].'][TYPE]','Data Type',$type_options,false,'id=type onchange="formcheck_student_studentField_F1_defalut();"') . '</TD>';
		if($_REQUEST['id']!='new'  && $RET['TYPE']!='multiple'  && $RET['TYPE']!='codeds' && $RET['TYPE']!='select' && $RET['TYPE']!='autos' && $RET['TYPE']!='edits' && $RET['TYPE']!='text')
		{
			$_openSIS['allow_edit'] = $allow_edit;
			$_openSIS['AllowEdit'][$modname] = $AllowEdit;
		}
		foreach($categories_RET as $type)
			$categories_options[$type['ID']] = $type['TITLE'];

		$header .= '<TD>' . SelectInput($RET['CATEGORY_ID']?$RET['CATEGORY_ID']:$_REQUEST['category_id'],'tables['.$_REQUEST['id'].'][CATEGORY_ID]','User Field Category',$categories_options,false) . '</TD>';

		if($_REQUEST['id']=='new')
		$header .= '<TD>' . TextInput($RET['SORT_ORDER'],'tables['.$_REQUEST['id'].'][SORT_ORDER]','Sort Order','onkeydown="return numberOnly(event);"') . '</TD>';
                else
		$header .= '<TD>' . TextInput($RET['SORT_ORDER'],'tables['.$_REQUEST['id'].'][SORT_ORDER]','Sort Order','onkeydown=\"return numberOnly(event);\"') . '</TD>';

		$header .= '</TR><TR>';
		$colspan = 2;
		if($RET['TYPE']=='autos' || $RET['TYPE']=='edits' || $RET['TYPE']=='select' || $RET['TYPE']=='codeds' || $RET['TYPE']=='multiple' || $_REQUEST['id']=='new')
		{
			$header .= '<TD colspan=2>'.TextAreaInput($RET['SELECT_OPTIONS'],'tables['.$_REQUEST['id'].'][SELECT_OPTIONS]','Pull-Down/Auto Pull-Down/Coded Pull-Down/Select Multiple Choices<BR>* one per line','rows=7 cols=40') . '</TD>';
			$colspan = 1;
		}
		$header .= '<TD valign=bottom colspan='.$colspan.'>'.TextInput_mod_a($RET['DEFAULT_SELECTION'],'tables['.$_REQUEST['id'].'][DEFAULT_SELECTION]','Default').'<small><BR>* for dates: YYYY-MM-DD,<BR> for checkboxes: Y<BR> for long text it will be ignored</small></TD>';

		$new = ($_REQUEST['id']=='new');
		$header .= '<TD>' . CheckboxInput($RET['REQUIRED'],'tables['.$_REQUEST['id'].'][REQUIRED]','Required','',$new) . '</TD>';

		$header .= '</TR>';
		$header .= '</TABLE>';
	}
	elseif($_REQUEST['category_id'])
	{
		echo "<FORM name=F2 id=F2 action=Modules.php?modname=$_REQUEST[modname]&table=people_field_categories";
		if($_REQUEST['category_id']!='new')
			echo "&category_id=$_REQUEST[category_id]";
		echo " method=POST>";
		DrawHeaderHome($title,$delete_button.SubmitButton('Save','','class=btn_medium onclick="formcheck_user_userfields_F2();"')); //'<INPUT type=submit value=Save>');
		
                $header .= '<TABLE cellpadding=3 width=100%>';
                $header .= '<TR><TD><input type=hidden id=t_id value="'.$_REQUEST['category_id'].'"/></TD></TR>';
		$header .= '<TR>';


		$header .= '<TD>' . (($RET['ID']>2 || $RET['ID']=='')?TextInput($RET['TITLE'],'tables['.$_REQUEST['category_id'].'][TITLE]','Title'):NoInput($RET['TITLE'],'Title')). '</TD>';

                $header .= '<TD>' . (($RET['SORT_ORDER']>2 || $RET['SORT_ORDER']=='')?TextInput($RET['SORT_ORDER'],'tables['.$_REQUEST['category_id'].'][SORT_ORDER]','Sort Order'):NoInput($RET['SORT_ORDER'],'Sort Order')) . '</TD>';
		$new = ($_REQUEST['category_id']=='new');

                $header .= '<TD><TABLE><TR>';
		$header .= '<TD>' . (($RET['ID']>2 || $RET['ID']=='')?CheckboxInput($RET['ADMIN'],'tables['.$_REQUEST['category_id'].'][ADMIN]',($_REQUEST['category_id']=='1'&&!$RET['ADMIN']?'<FONT color=red>':'').'Administrator'.($_REQUEST['category_id']=='1'&&!$RET['ADMIN']?'</FONT>':''),'',$new,'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>'): NoInput(($RET['ADMIN']=='Y'?'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>':'<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>'),'Administrator')) . '</TD>';
		$header .= '<TD>' . (($RET['ID']>2|| $RET['ID']=='')?CheckboxInput($RET['TEACHER'],'tables['.$_REQUEST['category_id'].'][TEACHER]',($_REQUEST['category_id']=='1'&&!$RET['TEACHER']?'<FONT color=red>':'').'Teacher'.($_REQUEST['category_id']=='1'&&!$RET['TEACHER']?'</FONT>':''),'',$new,'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>'): NoInput(($RET['TEACHER']=='Y'?'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>':'<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>'),'Teacher')) . '</TD>';
		$header .= '<TD>' . (($RET['ID']>2 || $RET['ID']=='')?CheckboxInput($RET['PARENT'],'tables['.$_REQUEST['category_id'].'][PARENT]',($_REQUEST['category_id']=='1'&&!$RET['PARENT']?'<FONT color=red>':'').'Parent'.($_REQUEST['category_id']=='1'&&!$RET['TEACHER']?'</FONT>':''),'',$new,'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>'): NoInput(($RET['PARENT']=='Y'?'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>':'<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>'),'Parent')) . '</TD>';
		$header .= '<TD>' . (($RET['ID']>2 || $RET['ID']=='')?CheckboxInput($RET['NONE'],'tables['.$_REQUEST['category_id'].'][NONE]',($_REQUEST['category_id']=='1'&&!$RET['NONE']?'<FONT color=red>':'').'No Access'.($_REQUEST['category_id']=='1'&&!$RET['TEACHER']?'</FONT>':''),'',$new,'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>') : NoInput(($RET['NONE']=='Y'?'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>':'<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>'),'No Access')). '</TD>';
		$header .= '</TR>';
		$header .= '<TR><TD colspan=4><small><FONT color='.Preferences('TITLES').'>Profiles</FONT></small></TD></TR>';
		$header .= '</TABLE></TD>';

		if($_REQUEST['category_id']>2 || $new)
		{
			$header .= '</TR><TR>';
			$header .= '<TD colspan=2></TD>';
			$header .= '<TD>' . TextInput($RET['INCLUDE'],'tables['.$_REQUEST['category_id'].'][INCLUDE]','Include (should be left blank for most categories)') . '</TD>';
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

	ListOutput($categories_RET,$columns,'User Field Category','User Field Categories',$link,array(),$LO_options);
	echo '</TD>';

	// FIELDS
	if($_REQUEST['category_id'] && $_REQUEST['category_id']!='new' && count($categories_RET))
	{
		$sql = 'SELECT ID,TITLE,TYPE,SORT_ORDER FROM people_fields WHERE CATEGORY_ID=\''.$_REQUEST['category_id'].'\' ORDER BY SORT_ORDER,TITLE';
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
                             $arr=array('Name','Email Address','Disable User','User Id','Home Phone','Work Phone','Cell Phone','User Profile','Username','Password');
                             $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]";
                             $link['add']['link'] = "#"." onclick='check_content(\"Ajax.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]&id=new\");'";
                             $link['TITLE']['variables'] = array('id'=>'ID');

                        break;
                    case 2:
                             $arr=array('Address','Street','City','State','Zip Code');
                             $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]";
                             $link['add']['link'] = "#"." onclick='check_content(\"Ajax.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]&id=new\");'";
                             $link['TITLE']['variables'] = array('id'=>'ID');

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
		ListOutput($dd,$columns,'User Field','User Fields',$link,array(),$LO_options);
                


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