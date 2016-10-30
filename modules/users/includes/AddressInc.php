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


if(count($_REQUEST['values']['ADDRESS']))
    foreach($_REQUEST['values']['ADDRESS'] as $index=>$data)
        $_REQUEST['values']['ADDRESS'][$index]=str_replace("'","\'",$data);
if(count($_REQUEST['values']['EMERGENCY_CONTACT']))
    foreach($_REQUEST['values']['EMERGENCY_CONTACT'] as $index=>$data)
        $_REQUEST['values']['EMERGENCY_CONTACT'][$index]=str_replace("'","\'",$data);

if($_REQUEST['values'] && ($_POST['values'] || $_REQUEST['ajax']))
{
	

	if($_REQUEST['values'])
	{
		if($_REQUEST['address_id']!='new')
		{



                    if($_REQUEST['values']['ADDRESS']){
                        $sql = "UPDATE staff_address  SET ";
		
       foreach($_REQUEST['values']['ADDRESS'] as $column=>$value)
			{
				if(!is_array($value))
					$sql .= $column."='".str_replace("\'","''",$value)."',";
				else
				{
					$sql .= $column."='||";
					foreach($value as $val)
					{
						if($val)
							$sql .= str_replace('&quot;','"',$val).'||';
					}
					$sql .= "',";
				}
	
                        }
                        $sql = substr($sql,0,-1) . " WHERE STAFF_ADDRESS_ID='$_REQUEST[address_id]'";

			DBQuery($sql);

                    }






                    if($_REQUEST['values']['CONTACT']){
                        $sql = "UPDATE staff_contact  SET ";

       foreach($_REQUEST['values']['CONTACT'] as $column=>$value)
			{
				if(!is_array($value))
					$sql .= $column."='".str_replace("\'","''",$value)."',";
				else
				{
					$sql .= $column."='||";
					foreach($value as $val)
					{
						if($val)
							$sql .= str_replace('&quot;','"',$val).'||';
					}
					$sql .= "',";
				}

                        }
                        $sql = substr($sql,0,-1) . " WHERE STAFF_ID=".UserStaffID();

			DBQuery($sql);

                    }




 if($_REQUEST['values']['EMERGENCY_CONTACT']){
                        $sql = "UPDATE staff_emergency_contact  SET ";

       foreach($_REQUEST['values']['EMERGENCY_CONTACT'] as $column=>$value)
			{
				if(!is_array($value))
					$sql .= $column."='".str_replace("\'","''",$value)."',";
				else
				{
					$sql .= $column."='||";
					foreach($value as $val)
					{
						if($val)
							$sql .= str_replace('&quot;','"',$val).'||';
					}
					$sql .= "',";
				}

                        }
                        $sql = substr($sql,0,-1) . " WHERE STAFF_ID=".UserStaffID();

			DBQuery($sql);

                    }
	
		}
		else
		{
                    
			

     if($_REQUEST['values']['ADDRESS']){

         if($_REQUEST['r4']=='Y'){
         $_REQUEST['values']['ADDRESS']['STAFF_ADDRESS1_MAIL']=$_REQUEST['values']['ADDRESS']['STAFF_ADDRESS1_PRIMARY'];
         $_REQUEST['values']['ADDRESS']['STAFF_ADDRESS2_MAIL']=$_REQUEST['values']['ADDRESS']['STAFF_ADDRESS2_PRIMARY'];
         $_REQUEST['values']['ADDRESS']['STAFF_CITY_MAIL']=$_REQUEST['values']['ADDRESS']['STAFF_CITY_PRIMARY'];
         $_REQUEST['values']['ADDRESS']['STAFF_STATE_MAIL']=$_REQUEST['values']['ADDRESS']['STAFF_STATE_PRIMARY'];
         $_REQUEST['values']['ADDRESS']['STAFF_ZIP_MAIL']=$_REQUEST['values']['ADDRESS']['STAFF_ZIP_PRIMARY'];
                                    }

                        $sql = "INSERT INTO staff_address ";
			$fields = 'STAFF_ID,';
			$values = "'".UserStaffID()."',";
       foreach($_REQUEST['values']['ADDRESS'] as $column=>$value)
			{
				if($value)
				{
					$fields .= $column.',';
					
					$values .= "'".str_replace("\'","''",$value)."',";
				}
			}
			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
                    
			DBQuery($sql);

               $id=DBGet(DBQuery("select max(staff_address_id) as ADDRESS_ID  from staff_address"));
                              $id=$id[1]['ADDRESS_ID'];
                              $_REQUEST['address_id'] = $id;
                   }

                         if($_REQUEST['values']['CONTACT']){
                        $sql = "INSERT INTO staff_contact ";
			$fields = 'STAFF_ID,';
			$values = "'".UserStaffID()."',";
       foreach($_REQUEST['values']['CONTACT'] as $column=>$value)
			{
				if($value)
				{
					$fields .= $column.',';

					$values .= "'".str_replace("\'","''",$value)."',";
				}
			}
			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
                    
			DBQuery($sql);

                        }

                         if($_REQUEST['values']['EMERGENCY_CONTACT']){
                        $sql = "INSERT INTO staff_emergency_contact ";
			$fields = 'STAFF_ID,';
			$values = "'".UserStaffID()."',";
       foreach($_REQUEST['values']['EMERGENCY_CONTACT'] as $column=>$value)
			{
				if($value)
				{
					$fields .= $column.',';

					$values .= "'".str_replace("\'","''",$value)."',";
				}
			}
			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
                     
			DBQuery($sql);

                        }

		}
	}

	

	

	

	

	unset($_REQUEST['modfunc']);
	unset($_REQUEST['values']);
}



if(!$_REQUEST['modfunc'])
{
    
   if($_REQUEST['address_id']!='' && $_REQUEST['address_id']!='new'){
	$this_address_RET = DBGet(DBQuery("SELECT * FROM staff_address
        WHERE STAFF_ADDRESS_ID=".$_REQUEST['address_id']." AND STAFF_ID=".UserStaffID()));
        $this_address=$this_address_RET[1];

        $this_contact_RET = DBGet(DBQuery("SELECT * FROM staff_contact
        WHERE STAFF_ID=".UserStaffID()));
        $this_contact=$this_contact_RET[1];

        $this_emer_contact_RET = DBGet(DBQuery("SELECT * FROM staff_emergency_contact
        WHERE STAFF_ID=".UserStaffID()));
        $this_emer_contact=$this_emer_contact_RET[1];

   }
	

	echo '<TABLE border=0><TR><TD valign=top>'; // table 1
	echo '<TABLE border=0><TR><TD valign=top>'; // table 2
	echo '<TABLE border=0 cellpadding=0 cellspacing=0>'; // table 3
	
		
	############################################################################################
		
		$style = '';
		
		
			
			
			
	############################################################################################	
	
	// New Address

	echo '</TABLE>';
	echo '</TD>';
	echo '<TD class=vbreak>&nbsp;</TD><TD valign=top>';

	if(isset($_REQUEST['address_id']))
	{
		echo "<INPUT type=hidden name=address_id value=$_REQUEST[address_id]>";

		if($_REQUEST['address_id']!='0' && $_REQUEST['address_id']!=='old')
		{
			if($_REQUEST['address_id']=='new')
				$size = true;
			else
				$size = false;

			
			echo '<TABLE width=100%><TR><TD>'; // open 3a
			echo '<FIELDSET><LEGEND><FONT color=gray>Home Address</FONT></LEGEND><TABLE width=100%>';
			echo '<TR><td width="100"><span class=red></span>Street Address 1</td><td width="10">:</td><TD style=\"white-space:nowrap\"><table cellspacing=0 cellpadding=0 cellspacing=0 cellpadding=0 border=0><tr><td>'.TextInput($this_address['STAFF_ADDRESS1_PRIMARY'],'values[ADDRESS][STAFF_ADDRESS1_PRIMARY]','','class=cell_medium').'</td><td>';
			if($_REQUEST['address_id']!='new' && $_REQUEST['address_id']!='0')
			{
				$display_address = urlencode($this_address['STAFF_ADDRESS1_PRIMARY'].', '.($this_address['STAFF_CITY_PRIMARY']?' '.$this_address['STAFF_CITY_PRIMARY'].', ':'').$this_address['STAFF_STATE_PRIMARY'].($this_address['STAFF_ZIP_PRIMARY']?' '.$this_address['STAFF_ZIP_PRIMARY']:''));
				$link = 'http://google.com/maps?q='.$display_address;
				echo '&nbsp;<A class=red HREF=# onclick=\'window.open("'.$link.'","","scrollbars=yes,resizable=yes,width=800,height=700");\'>Map it</A>';
			}
			echo '</td></tr></table></TD></tr>';
			echo '<TR><td>Street Address 2</td><td>:</td><TD>'.TextInput($this_address['STAFF_ADDRESS2_PRIMARY'],'values[ADDRESS][STAFF_ADDRESS2_PRIMARY]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td><span class=red></span>City</td><td>:</td><TD>'.TextInput($this_address['STAFF_CITY_PRIMARY'],'values[ADDRESS][STAFF_CITY_PRIMARY]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td><span class=red></span>State</td><td>:</td><TD>'.TextInput($this_address['STAFF_STATE_PRIMARY'],'values[ADDRESS][STAFF_STATE_PRIMARY]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td><span class=red></span>Zip/Postal Code</td><td>:</td><TD>'.TextInput($this_address['STAFF_ZIP_PRIMARY'],'values[ADDRESS][STAFF_ZIP_PRIMARY]','','class=cell_medium').'</TD></tr>';
			
			echo '</TABLE></FIELDSET>';
			echo'</TD></TR>';
			echo '</TABLE>'; //close 3a

			if($_REQUEST['address_id']=='new')
			{
				$new = true;
				$this_address['RESIDENCE'] = 'Y';
				$this_address['MAILING'] = 'Y';
				
			}


			echo '<TABLE border=0 width=100%><TR><TD>'; //open 3b
			echo '<FIELDSET><LEGEND><FONT color=gray>Mailing Address</FONT></LEGEND>';
			if($_REQUEST['address_id']=='new')
			echo '<table><TR><TD><span class=red></span><input type="radio" id="r4" name="r4" value="Y" onClick="hidediv();" checked>&nbsp;Same as Home Address &nbsp;&nbsp; <input type="radio" id="r4" name="r4" value="N" onClick="showdiv();">&nbsp;Add New Address</TD></TR></TABLE>'; 
			if($_REQUEST['address_id']=='new')
			echo '<div id="hideShow" style="display:none">';
			else
			echo '<div id="hideShow">';
			echo '<TABLE>';
			echo '<TR><td style=width:100px>Street Address 1</td><td>:</td><TD>'.TextInput($this_address['STAFF_ADDRESS1_MAIL'],'values[ADDRESS][STAFF_ADDRESS1_MAIL]','','class=cell_medium').'</TD>';
			echo '<TR><td>Street Address 2</td><td>:</td><TD>'.TextInput($this_address['STAFF_ADDRESS2_MAIL'],'values[ADDRESS][STAFF_ADDRESS2_MAIL]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>City</td><td>:</td><TD>'.TextInput($this_address['STAFF_CITY_MAIL'],'values[ADDRESS][STAFF_CITY_MAIL]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>State</td><td>:</td><TD>'.TextInput($this_address['STAFF_STATE_MAIL'],'values[ADDRESS][STAFF_STATE_MAIL]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>Zip/Postal Code</td><td>:</td><TD>'.TextInput($this_address['STAFF_ZIP_MAIL'],'values[ADDRESS][STAFF_ZIP_MAIL]','','class=cell_medium').'</TD></tr>';
			
			echo '</TABLE>';
			echo '</div>';

			echo '</FIELDSET>';
			echo'</TD></TR>';
			echo '</TABLE>'; // close 3b
			
			
			echo '<TABLE border=0 width=100%><TR><TD>'; //open 3c
			echo '<FIELDSET><LEGEND><FONT color=gray>Contact Information</FONT></LEGEND><TABLE width=100%><tr><td>';
			echo '<table border=0 width=100%>';
			
			echo '<TR><td width="100"><span class=red></span>Home Phone</td><td width="10">:</td><TD>'.TextInput($this_contact['STAFF_HOME_PHONE'],'values[CONTACT][STAFF_HOME_PHONE]','','class=cell_medium').'</TD></tr>';
			
			echo '<TR><td>Mobile Phone</td><td>:</td><TD>'.TextInput($this_contact['STAFF_MOBILE_PHONE'],'values[CONTACT][STAFF_MOBILE_PHONE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td><span class=red></span>Office Phone</td><td>:</td><TD>'.TextInput($this_contact['STAFF_WORK_PHONE'],'values[CONTACT][STAFF_WORK_PHONE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td><span class=red></span>Work Email</td><td>:</td><TD>'.TextInput($this_contact['STAFF_WORK_EMAIL'],'values[CONTACT][STAFF_WORK_EMAIL]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>Personal Email</td><td>:</td><TD>'.TextInput($this_contact['STAFF_PERSONAL_EMAIL'],'values[CONTACT][STAFF_PERSONAL_EMAIL]','','class=cell_medium').'</TD></tr>';
			
			
			
			
			echo '</table></td></tr></table></FIELDSET>';
			echo'</TD></TR>';
			echo '</TABLE>'; // close 3c
			
############################################################################################		
			echo '<TABLE border=0 width=100%><TR><TD>'; // open 3d
			echo '<FIELDSET><LEGEND><FONT color=gray>Emergency Contact Information</FONT></LEGEND><TABLE width=100%><tr><td>';
			
			
			echo '<TR><td width="100"><span class=red></span>First Name</td><td width="10">:</td><TD>'.TextInput($this_emer_contact['STAFF_EMERGENCY_FIRST_NAME'],'values[EMERGENCY_CONTACT][STAFF_EMERGENCY_FIRST_NAME]','','class=cell_medium').'</TD></tr>';
			
			
			echo '<TR><td><span class=red></span>Last Name</td><td>:</td><TD>'.TextInput($this_emer_contact['STAFF_EMERGENCY_LAST_NAME'],'values[EMERGENCY_CONTACT][STAFF_EMERGENCY_LAST_NAME]','','class=cell_medium').'</TD></tr>';
			echo '<tr><td><span class=red></span>Relationship to Staff</TD><td>:</td><td>'._makeAutoSelectInputX($this_emer_contact['STAFF_EMERGENCY_RELATIONSHIP'],'STAFF_EMERGENCY_RELATIONSHIP','EMERGENCY_CONTACT','',$relation_options).'</TD></tr>';
			echo '<TR><td><span class=red></span>Home Phone</td><td>:</td><TD>'.TextInput($this_emer_contact['STAFF_EMERGENCY_HOME_PHONE'],'values[EMERGENCY_CONTACT][STAFF_EMERGENCY_HOME_PHONE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td><span class=red></span>Work Phone</td><td>:</td><TD>'.TextInput($this_emer_contact['STAFF_EMERGENCY_WORK_PHONE'],'values[EMERGENCY_CONTACT][STAFF_EMERGENCY_WORK_PHONE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>Mobile Phone</td><td>:</td><TD>'.TextInput($this_emer_contact['STAFF_EMERGENCY_MOBILE_PHONE'],'values[EMERGENCY_CONTACT][STAFF_EMERGENCY_MOBILE_PHONE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>Email</td><td>:</td><TD>'.TextInput($this_emer_contact['STAFF_EMERGENCY_EMAIL'],'values[EMERGENCY_CONTACT][STAFF_EMERGENCY_EMAIL]','','class=cell_medium').'</TD></tr>';
			
		
			
			
			echo '</table></td></tr></table>';
                        $_REQUEST['category_id'] = 2;
                        $_REQUEST['custom']='staff';
                        include('modules/users/includes/OtherInfoInc.inc.php');
			#echo '</FIELDSET>';
			echo'</TD></TR>';
			echo '</TABLE>';  // close 3d
			
			
############################################################################################			
			
		}

	}
	else
		echo '';
		
	
	$separator = '<HR>';
}



		
	echo '</TD></TR>';
	echo '</TABLE>'; // end of table 1

	




function _makeAutoSelectInputX($value,$column,$table,$title,$select,$id='',$div=true)
{
	if($column=='CITY' || $column=='MAIL_CITY')
		$options = 'maxlength=60';
	if($column=='STATE' || $column=='MAIL_STATE')
		$options = 'size=3 maxlength=10';
	elseif($column=='ZIPCODE' || $column=='MAIL_ZIPCODE')
		$options = 'maxlength=10';
	else
		$options = 'maxlength=100';

	if($value!='---' && count($select)>1)
		return SelectInput($value,"values[$table]".($id?"[$id]":'')."[$column]",$title,$select,'N/A','',$div);
	else
		return TextInput($value=='---'?array('---','<FONT color=red>---</FONT>'):$value,"values[$table]".($id?"[$id]":'')."[$column]",$title,$options,$div);
}
?>