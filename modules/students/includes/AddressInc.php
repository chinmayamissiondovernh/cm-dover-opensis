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
include 'modules/students/ConfigInc.php';

if(clean_param($_REQUEST['values'],PARAM_NOTAGS) && ($_POST['values'] || $_REQUEST['ajax']))
{

    if($_REQUEST['r7']=='Y')
    {    
        $get_home_add=DBGet(DBQuery('SELECT street_address_1,street_address_2,city,state,zipcode,bus_pickup,bus_dropoff,bus_no FROM student_address WHERE STUDENT_ID=\''.UserStudentID().'\' AND SYEAR=\''.UserSyear().'\' AND SCHOOL_ID= \''.  UserSchool().'\' AND TYPE=\'Home Address\' '));
        if(count($get_home_add)>0)
        {
           foreach($get_home_add[1] as $gh_i=>$gh_d)
           {
               if($gh_d!='')
               $_REQUEST['values']['student_address']['OTHER'][$gh_i]=$gh_d;
           }
        }
        else
        {
            echo "<script>show_home_error();</script>";
            unset($_REQUEST['values']);
        }
    }
    
    if($_REQUEST['r4']=='Y')
    {    
        $_REQUEST['values']['student_address']['MAIL']['STREET_ADDRESS_1']=$_REQUEST['values']['student_address']['HOME']['STREET_ADDRESS_1'];
        $_REQUEST['values']['student_address']['MAIL']['STREET_ADDRESS_2']=$_REQUEST['values']['student_address']['HOME']['STREET_ADDRESS_2'];
        $_REQUEST['values']['student_address']['MAIL']['CITY']=$_REQUEST['values']['student_address']['HOME']['CITY'];
        $_REQUEST['values']['student_address']['MAIL']['ZIPCODE']=$_REQUEST['values']['student_address']['HOME']['ZIPCODE'];
        $_REQUEST['values']['student_address']['MAIL']['STATE']=$_REQUEST['values']['student_address']['HOME']['STATE'];
    }
    if($_REQUEST['same_addr']=='Y')
    {    

        $address_details=DBGEt(DBQuery('SELECT STREET_ADDRESS_1 as ADDRESS,STREET_ADDRESS_2 as STREET,CITY,STATE,ZIPCODE FROM  student_address WHERE STUDENT_ID='.$_REQUEST['student_id'].' AND type=\'Home Address\' '));
        if(isset($_REQUEST['values']['student_address']['HOME']['STREET_ADDRESS_1'])&& !isset($_REQUEST['values']['student_address']['MAIL']['STREET_ADDRESS_1']))
        $_REQUEST['values']['student_address']['MAIL']['STREET_ADDRESS_1']=$_REQUEST['values']['student_address']['HOME']['STREET_ADDRESS_1'];
        elseif(isset($_REQUEST['values']['student_address']['MAIL']['STREET_ADDRESS_1']))
       $_REQUEST['values']['student_address']['MAIL']['STREET_ADDRESS_1']=$_REQUEST['values']['student_address']['MAIL']['STREET_ADDRESS_1'];
            else
        $_REQUEST['values']['student_address']['MAIL']['STREET_ADDRESS_1']=$address_details[1]['ADDRESS'];
        
        if(isset($_REQUEST['values']['student_address']['HOME']['STREET_ADDRESS_2']) && !isset($_REQUEST['values']['student_address']['MAIL']['STREET_ADDRESS_2']))
        $_REQUEST['values']['student_address']['MAIL']['STREET_ADDRESS_2']=$_REQUEST['values']['student_address']['HOME']['STREET_ADDRESS_1'];
        elseif(isset($_REQUEST['values']['student_address']['MAIL']['STREET_ADDRESS_2']))
        {
            $_REQUEST['values']['student_address']['MAIL']['STREET_ADDRESS_2']=$_REQUEST['values']['student_address']['MAIL']['STREET_ADDRESS_2'];
            
        }else
            {
        $_REQUEST['values']['student_address']['MAIL']['STREET_ADDRESS_2']=$address_details[1]['STREET'];
        }
        if(isset($_REQUEST['values']['student_address']['HOME']['CITY']) && !isset($_REQUEST['values']['student_address']['MAIL']['CITY']))
        $_REQUEST['values']['student_address']['MAIL']['CITY']=$_REQUEST['values']['student_address']['HOME']['CITY'];
       elseif(isset($_REQUEST['values']['student_address']['MAIL']['CITY']))
           $_REQUEST['values']['student_address']['MAIL']['CITY']=$_REQUEST['values']['student_address']['MAIL']['CITY'];
        else
        $_REQUEST['values']['student_address']['MAIL']['CITY']=$address_details[1]['CITY'];
        
        if(isset($_REQUEST['values']['student_address']['HOME']['ZIPCODE']) && !isset($_REQUEST['values']['student_address']['MAIL']['ZIPCODE']))
        $_REQUEST['values']['student_address']['MAIL']['ZIPCODE']=$_REQUEST['values']['student_address']['HOME']['ZIPCODE'];
      elseif(isset($_REQUEST['values']['student_address']['MAIL']['ZIPCODE']))
          $_REQUEST['values']['student_address']['MAIL']['ZIPCODE']=$_REQUEST['values']['student_address']['MAIL']['ZIPCODE'];
        else
        $_REQUEST['values']['student_address']['MAIL']['ZIPCODE']=$address_details[1]['ZIPCODE'];
        
        if(isset($_REQUEST['values']['student_address']['HOME']['STATE']) && !isset($_REQUEST['values']['student_address']['MAIL']['STATE']))
        $_REQUEST['values']['student_address']['MAIL']['STATE']=$_REQUEST['values']['student_address']['HOME']['STATE'];
       elseif(isset($_REQUEST['values']['student_address']['MAIL']['STATE']))
           $_REQUEST['values']['student_address']['MAIL']['STATE']=$_REQUEST['values']['student_address']['MAIL']['STATE'];
        else
        $_REQUEST['values']['student_address']['MAIL']['STATE']=$address_details[1]['STATE'];
    }
    
      if($_REQUEST['sec_addr']=='Y')
    {    

        $address_details=DBGEt(DBQuery('SELECT STREET_ADDRESS_1 as ADDRESS,STREET_ADDRESS_2 as STREET,CITY,STATE,ZIPCODE FROM  student_address WHERE STUDENT_ID='.$_REQUEST['student_id'].' AND type=\'Home Address\' '));
        if(isset($_REQUEST['values']['student_address']['HOME']['STREET_ADDRESS_1']) &&!isset($_REQUEST['values']['student_address']['SECONDARY']['STREET_ADDRESS_1']))
        $_REQUEST['values']['student_address']['SECONDARY']['STREET_ADDRESS_1']=$_REQUEST['values']['student_address']['HOME']['STREET_ADDRESS_1'];
       elseif(isset($_REQUEST['values']['student_address']['SECONDARY']['STREET_ADDRESS_1']))
           $_REQUEST['values']['student_address']['SECONDARY']['STREET_ADDRESS_1']=$_REQUEST['values']['student_address']['SECONDARY']['STREET_ADDRESS_1'];
        else
        $_REQUEST['values']['student_address']['SECONDARY']['STREET_ADDRESS_1']=$address_details[1]['ADDRESS'];
        
        if(isset($_REQUEST['values']['student_address']['HOME']['STREET_ADDRESS_2']) && !isset($_REQUEST['values']['student_address']['SECONDARY']['STREET_ADDRESS_2']))
        $_REQUEST['values']['student_address']['SECONDARY']['STREET_ADDRESS_2']=$_REQUEST['values']['student_address']['HOME']['STREET_ADDRESS_1'];
        elseif(isset($_REQUEST['values']['student_address']['SECONDARY']['STREET_ADDRESS_2']))
            $_REQUEST['values']['student_address']['SECONDARY']['STREET_ADDRESS_2']=$_REQUEST['values']['student_address']['SECONDARY']['STREET_ADDRESS_2'];
        else
        $_REQUEST['values']['student_address']['SECONDARY']['STREET_ADDRESS_2']=$address_details[1]['STREET'];
        
        if(isset($_REQUEST['values']['student_address']['HOME']['CITY']) && !isset($_REQUEST['values']['student_address']['SECONDARY']['CITY']))
        $_REQUEST['values']['student_address']['SECONDARY']['CITY']=$_REQUEST['values']['student_address']['HOME']['CITY'];
       elseif(isset($_REQUEST['values']['student_address']['SECONDARY']['CITY']))
           $_REQUEST['values']['student_address']['SECONDARY']['CITY']=$_REQUEST['values']['student_address']['SECONDARY']['CITY'];
        else
        $_REQUEST['values']['student_address']['SECONDARY']['CITY']=$address_details[1]['CITY'];
        
        if(isset($_REQUEST['values']['student_address']['HOME']['ZIPCODE']) &&!isset($_REQUEST['values']['student_address']['SECONDARY']['ZIPCODE']))
        $_REQUEST['values']['student_address']['SECONDARY']['ZIPCODE']=$_REQUEST['values']['student_address']['HOME']['ZIPCODE'];
       elseif(isset($_REQUEST['values']['student_address']['SECONDARY']['ZIPCODE']))
           $_REQUEST['values']['student_address']['SECONDARY']['ZIPCODE']=$_REQUEST['values']['student_address']['SECONDARY']['ZIPCODE'];
        else
        $_REQUEST['values']['student_address']['SECONDARY']['ZIPCODE']=$address_details[1]['ZIPCODE'];
        
        if(isset($_REQUEST['values']['student_address']['HOME']['STATE']) && !isset($_REQUEST['values']['student_address']['SECONDARY']['STATE']))
        $_REQUEST['values']['student_address']['SECONDARY']['STATE']=$_REQUEST['values']['student_address']['HOME']['STATE'];
        elseif(isset($_REQUEST['values']['student_address']['SECONDARY']['STATE']))
            $_REQUEST['values']['student_address']['SECONDARY']['STATE']=$_REQUEST['values']['student_address']['SECONDARY']['STATE'];
        else
        $_REQUEST['values']['student_address']['SECONDARY']['STATE']=$address_details[1]['STATE'];
    }
        if($_REQUEST['prim_addr']=='Y')
    {    

        $address_details=DBGEt(DBQuery('SELECT STREET_ADDRESS_1 as ADDRESS,STREET_ADDRESS_2 as STREET,CITY,STATE,ZIPCODE FROM  student_address WHERE STUDENT_ID='.$_REQUEST['student_id'].' AND type=\'Home Address\' '));
        if(isset($_REQUEST['values']['student_address']['HOME']['STREET_ADDRESS_1']) && !isset($_REQUEST['values']['student_address']['PRIMARY']['STREET_ADDRESS_1']))
        $_REQUEST['values']['student_address']['PRIMARY']['STREET_ADDRESS_1']=$_REQUEST['values']['student_address']['HOME']['STREET_ADDRESS_1'];
        elseif(isset($_REQUEST['values']['student_address']['PRIMARY']['STREET_ADDRESS_1']))
            $_REQUEST['values']['student_address']['PRIMARY']['STREET_ADDRESS_1']=$_REQUEST['values']['student_address']['PRIMARY']['STREET_ADDRESS_1'];
        else
        $_REQUEST['values']['student_address']['PRIMARY']['STREET_ADDRESS_1']=$address_details[1]['ADDRESS'];
        
        if(isset($_REQUEST['values']['student_address']['HOME']['STREET_ADDRESS_2']) && !isset($_REQUEST['values']['student_address']['PRIMARY']['STREET_ADDRESS_2']))
        $_REQUEST['values']['student_address']['PRIMARY']['STREET_ADDRESS_2']=$_REQUEST['values']['student_address']['HOME']['STREET_ADDRESS_2'];
        elseif(isset($_REQUEST['values']['student_address']['PRIMARY']['STREET_ADDRESS_2']))
            $_REQUEST['values']['student_address']['PRIMARY']['STREET_ADDRESS_2']=$_REQUEST['values']['student_address']['PRIMARY']['STREET_ADDRESS_2'];
        else
        $_REQUEST['values']['student_address']['PRIMARY']['STREET_ADDRESS_2']=$address_details[1]['STREET'];
        
        if(isset($_REQUEST['values']['student_address']['HOME']['CITY']) & !isset($_REQUEST['values']['student_address']['PRIMARY']['CITY']))
        $_REQUEST['values']['student_address']['PRIMARY']['CITY']=$_REQUEST['values']['student_address']['HOME']['CITY'];
        elseif(isset($_REQUEST['values']['student_address']['PRIMARY']['CITY']))
            $_REQUEST['values']['student_address']['PRIMARY']['CITY']=$_REQUEST['values']['student_address']['PRIMARY']['CITY'];
        else
       $_REQUEST['values']['student_address']['PRIMARY']['CITY']=$address_details[1]['CITY'];
        
        if(isset($_REQUEST['values']['student_address']['HOME']['ZIPCODE']) && !isset($_REQUEST['values']['student_address']['PRIMARY']['ZIPCODE']))
        $_REQUEST['values']['student_address']['PRIMARY']['ZIPCODE']=$_REQUEST['values']['student_address']['HOME']['ZIPCODE'];
      elseif(isset($_REQUEST['values']['student_address']['PRIMARY']['ZIPCODE']))
          $_REQUEST['values']['student_address']['PRIMARY']['ZIPCODE']=$_REQUEST['values']['student_address']['PRIMARY']['ZIPCODE'];
        else
        $_REQUEST['values']['student_address']['PRIMARY']['ZIPCODE']=$address_details[1]['ZIPCODE'];
        
        if(isset($_REQUEST['values']['student_address']['HOME']['STATE']) && !isset($_REQUEST['values']['student_address']['PRIMARY']['STATE']))
        $_REQUEST['values']['student_address']['PRIMARY']['STATE']=$_REQUEST['values']['student_address']['HOME']['STATE'];
       elseif(isset($_REQUEST['values']['student_address']['PRIMARY']['STATE']))
           $_REQUEST['values']['student_address']['PRIMARY']['STATE']=$_REQUEST['values']['student_address']['PRIMARY']['STATE'];
        else
        $_REQUEST['values']['student_address']['PRIMARY']['STATE']=$address_details[1]['STATE'];
    }
    
    if($_REQUEST['r6']=='Y')
    {    
        $_REQUEST['values']['student_address']['SECONDARY']['STREET_ADDRESS_1']=$_REQUEST['values']['student_address']['HOME']['STREET_ADDRESS_1'];
        $_REQUEST['values']['student_address']['SECONDARY']['STREET_ADDRESS_2']=$_REQUEST['values']['student_address']['HOME']['STREET_ADDRESS_2'];
        $_REQUEST['values']['student_address']['SECONDARY']['CITY']=$_REQUEST['values']['student_address']['HOME']['CITY'];
        $_REQUEST['values']['student_address']['SECONDARY']['ZIPCODE']=$_REQUEST['values']['student_address']['HOME']['ZIPCODE'];
        $_REQUEST['values']['student_address']['SECONDARY']['STATE']=$_REQUEST['values']['student_address']['HOME']['STATE'];
    }
    
    if($_REQUEST['r5']=='Y')
    {    
        $_REQUEST['values']['student_address']['PRIMARY']['STREET_ADDRESS_1']=$_REQUEST['values']['student_address']['HOME']['STREET_ADDRESS_1'];
        $_REQUEST['values']['student_address']['PRIMARY']['STREET_ADDRESS_2']=$_REQUEST['values']['student_address']['HOME']['STREET_ADDRESS_2'];
        $_REQUEST['values']['student_address']['PRIMARY']['CITY']=$_REQUEST['values']['student_address']['HOME']['CITY'];
        $_REQUEST['values']['student_address']['PRIMARY']['ZIPCODE']=$_REQUEST['values']['student_address']['HOME']['ZIPCODE'];
        $_REQUEST['values']['student_address']['PRIMARY']['STATE']=$_REQUEST['values']['student_address']['HOME']['STATE'];
    }
    
    
    
    foreach($_REQUEST['values'] as $table=>$type)
    {
        foreach($type as $ind=>$val)
        {
           if($val['ID']!='new')
           {
               $go='false';
               $cond_go='false';
               
               if($val['PROFILE_ID']!='')
               {
                   $staff_info_sql = "SELECT PROFILE_ID FROM people WHERE STAFF_ID=".$val['ID'];
                   $staff_info = DBGet(DBQuery($staff_info_sql));
                   $staff_prof_id = $staff_info[1]['PROFILE_ID'];
                   
                   $update_sql = 'UPDATE login_authentication SET PROFILE_ID =\''.$val['PROFILE_ID'].'\' WHERE USER_ID = '.$val['ID'].' AND PROFILE_ID = '.$staff_prof_id;
                   DBQuery($update_sql);
               }
               foreach($val as $col=>$col_v)
               {   

                        if($col!='ID')
                        {
                            if($col=='PASSWORD' && $col_v!='')
                            {
 
                                $password=md5(str_replace("'","''",$col_v));
                            }
                            elseif ($col=='USER_NAME' && $col_v!='') 
                            {
                                $user_name_val=str_replace("'","''",$col_v);
                            }
                            elseif($col=='RELATIONSHIP' && $col_v!='')
                                $rel_stu[]=$col.'=\''.str_replace("'","''",str_replace("'","\'",$col_v)).'\'';
                            elseif($col=='IS_EMERGENCY_HIDDEN' && $col_v=='Y')
                                $rel_stu[]='IS_EMERGENCY=\''.str_replace("'","''",str_replace("'","\'",$col_v)).'\'';
                             elseif($col=='IS_EMERGENCY_HIDDEN' && $col_v=='N')
                                $rel_stu[]='IS_EMERGENCY=NULL';
                            else 
                            {
                                if($col!='USER_NAME' && $col!='RELATIONSHIP' && $col!='PASSWORD' && $col!='IS_EMERGENCY' && $col!='IS_EMERGENCY_HIDDEN')
                                 $set_arr[]=$col."='".str_replace("'","''",$col_v)."'";                            
                            }  
                            $go='true';
                        }

                   if($col=='ID' && $col_v!='')
                   {
                       if($table=='people')
                       {
                            $where='STAFF_ID='.$col_v;
                            
                            if($ind=='PRIMARY')
                            {
                                if($_REQUEST['selected_pri_parent']!='' && $col_v!=$_REQUEST['selected_pri_parent'])
                                    $rel_stu[]='PERSON_ID=\''.$_REQUEST['selected_pri_parent'].'\'';
                                $pri_up_pl_id=$col_v;
                            }
                            if($ind=='SECONDARY')
                            {
                                if($_REQUEST['selected_pri_parent']!='' && $col_v!=$_REQUEST['selected_pri_parent'])
                                    $rel_stu[]='PERSON_ID=\''.$_REQUEST['selected_sec_parent'].'\'';
                                $sec_up_pl_id=$col_v;
                            }
                            if($ind=='OTHER')
                            {
                                if($_REQUEST['selected_pri_parent']!='' && $col_v!=$_REQUEST['selected_pri_parent'])
                                    $rel_stu[]='PERSON_ID=\''.$_REQUEST['selected_oth_parent'].'\'';
                                $oth_up_pl_id=$col_v;
                            }
                       }
                       else
                            $where=' ID='.$col_v;
                       $cond_go='true';
                   }
               }
               
               
               $set_arr=implode(',',$set_arr);
               $rel_stu=implode(',',$rel_stu);
               if($set_arr!='')
              $qry='UPDATE '.$table.' SET '.$set_arr.' WHERE '.$where;
              
               //codes to be inserted
                    DBQuery($qry);
               if($ind=='PRIMARY' && $rel_stu!='')
               {
                   DBQuery('UPDATE students_join_people SET '.$rel_stu.' WHERE EMERGENCY_TYPE=\'Primary\' AND PERSON_ID='.$pri_up_pl_id.' AND STUDENT_ID='.UserStudentID());
               }
               if($ind=='SECONDARY' && $rel_stu!='')
               {
                   DBQuery('UPDATE students_join_people SET '.$rel_stu.' WHERE EMERGENCY_TYPE=\'Secondary\' AND PERSON_ID='.$sec_up_pl_id.' AND STUDENT_ID='.UserStudentID());
               }
               if($ind=='OTHER' && $rel_stu!='')
               {
                   DBQuery('UPDATE students_join_people SET '.$rel_stu.' WHERE EMERGENCY_TYPE=\'Other\' AND PERSON_ID='.$oth_up_pl_id.' AND STUDENT_ID='.UserStudentID());
               }
               if($table=='people' && $ind=='PRIMARY')
                { 
                    if(clean_param($_REQUEST['primary_portal'],PARAM_ALPHAMOD)=='Y' && $password!='' )
                    {

                            $res_pass_chk = DBQuery('SELECT * FROM login_authentication WHERE PASSWORD=\''.$password.'\'');
                            $res_user_chk = DBQuery('SELECT * FROM login_authentication WHERE USERNAME=\''. $user_name_val.'\'');
                            $num_user=DBGet($res_user_chk);
                            $num_pass = DBGet($res_pass_chk);
                            
                          if(count($num_user)==0)
                          {
                            if(count($num_pass)==0)
                            {
                                

                                DBQuery('INSERT INTO login_authentication (USER_ID,USERNAME,PASSWORD,PROFILE_ID) VALUES ('.$pri_up_pl_id.',\''.$user_name_val.'\',\''.$password.'\',4)');
                            }
                            else
                            { 
                                echo "<script>document.getElementById('divErr').innerHTML='<font color=red><b>Password already exists.</b></font>';</script>";
                            }
                          }
                        else
                            { 
                                echo "<script>document.getElementById('divErr').innerHTML='<font color=red><b>Username already exists.</b></font>';</script>";
                            }  

                    }
                }
               if($table=='people' && $ind=='SECONDARY')
               { 
                   if(clean_param($_REQUEST['secondary_portal'],PARAM_ALPHAMOD)=='Y' && $password!='')
                   {
                            $res_user_chk = DBQuery('SELECT * FROM login_authentication WHERE USERNAME=\''. $user_name_val.'\'');
                            $num_user=DBGet($res_user_chk);
                            $res_pass_chk = DBQuery('SELECT * FROM login_authentication WHERE PASSWORD=\''.$password.'\'');
                            $num_pass = DBGet($res_pass_chk);
                            
                           if(count($num_user)==0)
                          {
                            if(count($num_pass)==0)
                            {

                                DBQuery('INSERT INTO login_authentication (USER_ID,USERNAME,PASSWORD,PROFILE_ID) VALUES ('.$sec_up_pl_id.',\''.$user_name_val.'\',\''.$password.'\',4)');
                            }
                            else
                            {
   
                               echo "<script>document.getElementById('divErr').innerHTML='<font color=red><b>Password already exists.</b></font>';</script>";
                            }
                          }
                            else
                            { 
                               echo "<script>document.getElementById('divErr').innerHTML='<font color=red><b>Username already exists.</b></font>';</script>";
                            } 

                    }
               }
               if($table=='people' && $ind=='OTHER')
               { 
                   if(clean_param($_REQUEST['other_portal'],PARAM_ALPHAMOD)=='Y' && $password!='')
                   {
  $res_user_chk = DBQuery('SELECT * FROM login_authentication WHERE USERNAME=\''. $user_name_val.'\'');
                            $num_user=DBGet($res_user_chk);
                            $res_pass_chk = DBQuery('SELECT * FROM login_authentication WHERE PASSWORD=\''.$password.'\'');
                            $num_pass = DBGet($res_pass_chk);
                          if(count($num_user)==0)
                          {
                            if(count($num_pass)==0)
                            {
  
                                DBQuery('INSERT INTO login_authentication (USER_ID,USERNAME,PASSWORD,PROFILE_ID) VALUES ('.$oth_up_pl_id.',\''.$user_name_val.'\',\''.$password.'\',4)');

                            }
                            else
                            {
   
                               echo "<script>document.getElementById('divErr').innerHTML='<font color=red><b>Password already exists.</b></font>';</script>";
                            }
                   }
                      else
                            { 
                               echo "<script>document.getElementById('divErr').innerHTML='<font color=red><b>Username already exists.</b></font>';</script>";
                            } 
                    }
               }
               unset($set_arr);
               unset($where);
               unset($col);
               unset($col_v);
               unset($go);
               unset($cond_go);
               unset($password);
               unset($user_name_val);
               unset($rel_stu);
               
              $get_person_ids=DBGet(DBQuery('SELECT * FROM students_join_people WHERE STUDENT_ID='.UserStudentID().''));
              foreach($get_person_ids as $gpi=>$gpd)
              {
                  if($gpd['EMERGENCY_TYPE']!='Other')
                    DBQuery('UPDATE student_address SET PEOPLE_ID='.$gpd['PERSON_ID'].' WHERE TYPE=\''.$gpd['EMERGENCY_TYPE'].'\' AND STUDENT_ID='.  UserStudentID());
              }
           }
           else
           {    
               $pri_pep_exists='N';
               $sec_pep_exists='N';
               $oth_pep_exists='N';
               if($ind=='PRIMARY' || $ind=='SECONDARY')
               {
                    $pri_people_exists=  DBGet (DBQuery ('SELECT * FROM people WHERE FIRST_NAME=\''.str_replace("'","''",$_REQUEST['values']['people']['PRIMARY']['FIRST_NAME']).'\' AND LAST_NAME=\''.str_replace("'","''",$_REQUEST['values']['people']['PRIMARY']['LAST_NAME']).'\' AND EMAIL=\''.$_REQUEST['values']['people']['PRIMARY']['EMAIL'].'\''));
                    if(count($pri_people_exists)>0)
                    {
                        $pri_person_id=$pri_people_exists[1]['STAFF_ID'];
                        $pri_pep_exists='Y';
                    }
                    else
                    {
                         $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'people'"));
                         $pri_person_id= $id[1]['AUTO_INCREMENT'];
                    }
                    $sec_people_exists=  DBGet (DBQuery ('SELECT * FROM people WHERE FIRST_NAME=\''.str_replace("'","''",str_replace("\'","'",$_REQUEST['values']['people']['SECONDARY']['FIRST_NAME'])).'\' AND LAST_NAME=\''.str_replace("'","''",str_replace("\'","'",$_REQUEST['values']['people']['SECONDARY']['LAST_NAME'])).'\' AND EMAIL=\''.str_replace("'","''",$_REQUEST['values']['people']['SECONDARY']['EMAIL']).'\''));
                    if(count($sec_people_exists)>0)
                    {
                        $sec_person_id=$sec_people_exists[1]['STAFF_ID'];
                        $sec_pep_exists='Y';
                    }
                    else
                    {
                        if($pri_pep_exists=='Y')
                        {
                             $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'people'"));
                             $sec_person_id= $id[1]['AUTO_INCREMENT'];
                        }
                        else
                         $sec_person_id = $pri_person_id;
                    }
               }
               if($ind=='OTHER' && $table=='people')
               {
                   $oth_people_exists=  DBGet (DBQuery ('SELECT * FROM people WHERE FIRST_NAME=\''.str_replace("\'","'",str_replace("'","''",$_REQUEST['values']['people']['OTHER']['FIRST_NAME'])).'\' AND LAST_NAME=\''.str_replace("\'","'",str_replace("'","''",$_REQUEST['values']['people']['OTHER']['LAST_NAME'])).'\' AND EMAIL=\''.str_replace("'","''",$_REQUEST['values']['people']['OTHER']['EMAIL']).'\''));
                    if(count($oth_people_exists)>0)
                    {
                        $oth_person_id=$oth_people_exists[1]['STAFF_ID'];
                        $oth_pep_exists='Y';
                    }
                    else
                    {
                         $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'people'"));
                         $oth_person_id= $id[1]['AUTO_INCREMENT'];
                    }
               }
               $go='false';
               $log_go=false;
               
               foreach($val as $col=>$col_v)
               {
                   if($table=='student_address')
                   {
                        if($col!='ID' && $col_v!='')
                        {
                            $fields[]=$col;

                                $field_vals[]="'".str_replace("'","\'",$col_v)."'";
                   
                            $go='true';
                        }
                   }
                   if($table=='people')
                   {
                       if($col!='ID' && $col_v!='')
                        {
                           if($col=='RELATIONSHIP' || $col=='IS_EMERGENCY')
                           {
                               $sjp_field.=$col.',';
                               $sjp_value.="'".$col_v."',";
                           }
                           else
                           {
                               if($col!='PASSWORD' && $col!='USER_NAME' && $col!='IS_EMERGENCY_HIDDEN')
                               {
                                    $peo_fields[]=$col;
                                    $peo_field_vals[]="'".str_replace("'","\'",$col_v)."'";
                                    $log_go=true;
                               }
                           }
                        }
                   }
               }
               $fields=implode(',',$fields);
               $field_vals=implode(',',$field_vals);
               $peo_fields=implode(',',$peo_fields);
               $peo_field_vals=implode(',',$peo_field_vals);
               if($table=='student_address')
               {
                   if($ind=='PRIMARY' || $ind=='SECONDARY' || $ind=='OTHER')
                    $type_n='type,people_id';
                   else
                       $type_n='type';
               }
               if($ind=='HOME')
               $ind_n="'Home Address'";
               if($ind=='PRIMARY')
               $ind_n="'Primary',".$pri_person_id."";
               if($ind=='SECONDARY')
               $ind_n="'Secondary',".$sec_person_id."";
               if($ind=='OTHER')
               $ind_n="'Other',".$oth_person_id."";
               if($ind=='MAIL')
               $ind_n="'Mail'";
               

               if($table=='student_address')
               {
                   if($ind=='HOME' || $ind=='MAIL')
                       $qry='INSERT INTO '.$table.' (student_id,syear,school_id,'.$fields.','.$type_n.') VALUES ('.UserStudentID().','.UserSyear().','.UserSchool().','.$field_vals.','.$ind_n.') ';
                   if(($ind=='PRIMARY' && $pri_pep_exists=='N') || ($ind=='SECONDARY' && $sec_pep_exists=='N') || ($ind=='OTHER' && $oth_pep_exists=='N'))
                       $qry='INSERT INTO '.$table.' (student_id,syear,school_id,'.$fields.','.$type_n.') VALUES ('.UserStudentID().','.UserSyear().','.UserSchool().','.$field_vals.','.$ind_n.') ';
               }
               if($table=='people')
               {
                  $sql_sjp='INSERT INTO students_join_people ('.$sjp_field.'student_id,emergency_type,person_id) VALUES ('.$sjp_value.UserStudentID().','.$ind_n.')';
                  if(($ind=='PRIMARY' && $pri_pep_exists=='N') || ($ind=='SECONDARY' && $sec_pep_exists=='N') || ($ind=='OTHER' && $oth_pep_exists=='N'))
                  {
                    $peo_fields_ar = explode(',',$peo_fields);
                    if(!in_array('PROFILE_ID',$peo_fields_ar)){
                    $sql_peo='INSERT INTO people (CURRENT_SCHOOL_ID,profile,profile_id,'.$peo_fields.') VALUES ('.UserSchool().',\'parent\',4,'.$peo_field_vals.')';
                    } else {
                    $sql_peo='INSERT INTO people (CURRENT_SCHOOL_ID,profile,'.$peo_fields.') VALUES ('.UserSchool().',\'parent\','.$peo_field_vals.')';
                      
                    }
                    
                  }
               }
               if($go=='true' & $qry!='')
                    DBQuery($qry);

               if($log_go)
               {
                   DBQuery($sql_sjp);

                   if(($ind=='PRIMARY' && $pri_pep_exists=='N') || ($ind=='SECONDARY' && $sec_pep_exists=='N') || ($ind=='OTHER' && $oth_pep_exists=='N'))
                        DBQuery($sql_peo);

               }

                if($table=='people' && $ind=='PRIMARY' && $type['PRIMARY']['USER_NAME']!='' && $pri_pep_exists=='N')
                { 
                    if(clean_param($_REQUEST['primary_portal'],PARAM_ALPHAMOD)=='Y')
                    {
                        $res_pass_chk = DBQuery('SELECT * FROM login_authentication WHERE PASSWORD = \''.md5($type['PRIMARY']['PASSWORD']).'\'');
                        $num_pass = DBGet($res_pass_chk);
                        $res_user_chk = DBQuery('SELECT * FROM login_authentication WHERE USERNAME = \''.$type['PRIMARY']['USER_NAME'].'\'');
                        $num_user = DBGet($res_user_chk);
                        
                      if(count($num_user)==0)
                      {
                        if(count($num_pass)==0)
                        {      
                            if($_REQUEST['values']['people']['PRIMARY']['PROFILE_ID']!='')
                                $pri_prof_id = $_REQUEST['values']['people']['PRIMARY']['PROFILE_ID'];
                            else
                                $pri_prof_id = 4;
                            
                            DBQuery('INSERT INTO login_authentication (USER_ID,USERNAME,PASSWORD,PROFILE_ID) VALUES ('.$pri_person_id.',\''.$type['PRIMARY']['USER_NAME'].'\',\''.md5($type['PRIMARY']['PASSWORD']).'\','.$pri_prof_id.')');

                        }
                        else
                        {
   
                               echo "<script>document.getElementById('divErr').innerHTML='<font color=red><b>Password already exists.</b></font>';</script>";
                        }
                    }
                    else
                        {
                               echo "<script>document.getElementById('divErr').innerHTML='<font color=red><b>Username already exists.</b></font>';</script>";
                        }
                    }
                    
               }
               if($table=='people' && $ind=='SECONDARY' && $sec_pep_exists=='N')
               { 
                  
                   if(clean_param($_REQUEST['secondary_portal'],PARAM_ALPHAMOD)=='Y' && $type['SECONDARY']['USER_NAME']!='')
                   {
                        $res_pass_chk = DBQuery('SELECT * FROM login_authentication WHERE PASSWORD = \''.md5($type['SECONDARY']['PASSWORD']).'\'');
                        $num_pass = DBGet($res_pass_chk);
                       
                       $res_user_chk = DBQuery('SELECT * FROM login_authentication WHERE USERNAME = \''.$type['SECONDARY']['USER_NAME'].'\'');
                        $num_user = DBGet($res_user_chk);
                       if(count($num_user)==0)
                      {
                        if(count($num_pass)==0)
                        {
                            if($_REQUEST['values']['people']['SECONDARY']['PROFILE_ID']!='')
                                $sec_prof_id = $_REQUEST['values']['people']['SECONDARY']['PROFILE_ID'];
                            else
                                $sec_prof_id = 4;
                            
                            DBQuery('INSERT INTO login_authentication (USER_ID,USERNAME,PASSWORD,PROFILE_ID) VALUES ('.$sec_person_id.',\''.$type['SECONDARY']['USER_NAME'].'\',\''.md5($type['SECONDARY']['PASSWORD']).'\','.$sec_prof_id.')');
                        }
                        else
                        {

                               echo "<script>document.getElementById('divErr').innerHTML='<font color=red><b>Password already exists.</b></font>';</script>";
                        }
                      }
                        else
                        {
                               echo "<script>document.getElementById('divErr').innerHTML='<font color=red><b>Username already exists.</b></font>';</script>";
                        }
                   }
                   
               }
               if($table=='people' && $ind=='OTHER' && $oth_pep_exists=='N')
                { 
                    if(clean_param($_REQUEST['other_portal'],PARAM_ALPHAMOD)=='Y' && $type['OTHER']['USER_NAME']!='')
                    {
                        $res_pass_chk = DBQuery('SELECT * FROM login_authentication WHERE PASSWORD = \''.md5($type['OTHER']['PASSWORD']).'\'');
                        $num_pass = DBGet($res_pass_chk);
                        $res_user_chk = DBQuery('SELECT * FROM login_authentication WHERE USERNAME = \''.$type['OTHER']['USER_NAME'].'\'');
                        $num_user = DBGet($res_user_chk);
                        if(count($num_user)==0)
                      {
                        if(count($num_pass)==0)
                        {
                            if($_REQUEST['values']['people']['OTHER']['PROFILE_ID']!='')
                                $oth_prof_id = $_REQUEST['values']['people']['OTHER']['PROFILE_ID'];
                            else
                                $oth_prof_id = 4;
                            
                            DBQuery('INSERT INTO login_authentication (USER_ID,USERNAME,PASSWORD,PROFILE_ID) VALUES ('.$oth_person_id.',\''.$type['OTHER']['USER_NAME'].'\',\''.md5($type['OTHER']['PASSWORD']).'\','.$oth_prof_id.')');

                        }
                        else
                        {
                         
                               echo "<script>document.getElementById('divErr').innerHTML='<font color=red><b>Password already exists.</b></font>';</script>";
                        }
                      }
                         else
                        {
                               echo "<script>document.getElementById('divErr').innerHTML='<font color=red><b>Username already exists.</b></font>';</script>";
                        }
                    }
                    
               }

               unset($fields);
               unset($qry);
               unset($field_vals);
               unset($peo_fields);
               unset($peo_field_vals);
               unset($sjp_field);
               unset($sjp_value);
               unset($log_go);
               unset($where);
               unset($col);
               unset($col_v);
               unset($type_n);
               unset($ind_n);
               unset($go);
               $get_person_ids=DBGet(DBQuery('SELECT * FROM students_join_people WHERE STUDENT_ID='.UserStudentID().''));
              foreach($get_person_ids as $gpi=>$gpd)
              {
                  if($gpd['EMERGENCY_TYPE']!='Other')
                    DBQuery('UPDATE student_address SET PEOPLE_ID='.$gpd['PERSON_ID'].' WHERE TYPE=\''.$gpd['EMERGENCY_TYPE'].'\' AND STUDENT_ID='.  UserStudentID());
                  
              }
           }
        }
    }
}


if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='delete')
{
    if($_REQUEST['person_id'])
    {
        if(DeletePromptModContacts('contact'))
        {
            $tot_people=  DBGet(DBQuery('SELECT COUNT(*) AS TOTAL FROM students_join_people WHERE PERSON_ID='.$_REQUEST['person_id'].''));
            $tot_people=$tot_people[1]['TOTAL'];
            if($tot_people>1)
            {        
                           DBQuery('DELETE FROM students_join_people WHERE PERSON_ID=\''.$_REQUEST['person_id'].'\' AND STUDENT_ID='.UserStudentID());
                           unset($_REQUEST['modfunc']);
            }
            else
            {
                DBQuery('DELETE FROM student_address WHERE PEOPLE_ID=\''.$_REQUEST['person_id'].'\' AND STUDENT_ID='.UserStudentID());
                DBQuery('DELETE FROM students_join_people WHERE PERSON_ID=\''.$_REQUEST['person_id'].'\' AND STUDENT_ID='.UserStudentID());
                DBQuery('DELETE FROM people WHERE STAFF_ID='.$_REQUEST['person_id']);
                DBQuery('DELETE FROM login_authentication WHERE USER_ID='.$_REQUEST['person_id'].' AND PROFILE_ID=4');
                unset($_REQUEST['modfunc']);
            }
        }
    }

}

if(!$_REQUEST['modfunc'])
{
    $addres_id=DBGet(DBQuery('SELECT ID AS ADDRESS_ID FROM student_address WHERE STUDENT_ID=\''.UserStudentID().'\' AND SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\' AND TYPE=\'Home Address\' '));
    if(count($addres_id)==1 && $addres_id[1]['ADDRESS_ID']!='')
    $_REQUEST['address_id'] = $addres_id[1]['ADDRESS_ID'];    


	echo '<TABLE border=0><TR><TD valign=top>'; // table 1
	echo '<TABLE border=0><TR><TD valign=top>'; // table 2
	echo '<TABLE border=0 cellpadding=0 cellspacing=0>'; // table 3

		echo '';
		
	############################################################################################
		
		$style = '';
		if($_REQUEST['person_id']=='new')
		{
			if($_REQUEST['address_id']!='new')
			echo '<TR onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id='.$_REQUEST['address_id'].'\';" ><TD>';
			else
			echo '<TR onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id=new\';" ><TD>';
			echo '<A style="cursor:pointer"><b>Student\'s Address </b></A>';
		}
		else
		{
			echo '<TR onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id=$_REQUEST[address_id]\';" onmouseover=\'this.style.color="white";\'><TD>';
			if($_REQUEST['person_id']==$contact['PERSON_ID'])
			echo '<A style="cursor:pointer;color:#FF0000"><b>Student\'s Address </b></A>';
			elseif($_REQUEST['person_id']!=$contact['PERSON_ID'])
			echo '<A style="cursor:pointer"><b>Student\'s Address </b></A>';
			else
			echo '<A style="cursor:pointer;color:#FF0000"><b>Student\'s Address </b></A>';
		}
		echo '</TD>';
		echo '<TD><A><IMG SRC=assets/arrow_right.gif></A></TD>';
		echo '</TR><tr><td colspan=2 class=break></td></tr>';
			
			

                        $contacts_RET = DBGet(DBQuery('SELECT PERSON_ID,RELATIONSHIP AS STUDENT_RELATION FROM students_join_people WHERE STUDENT_ID=\''.UserStudentID().'\' AND EMERGENCY_TYPE=\'Other\' ORDER BY STUDENT_RELATION'));
			$i = 1;
			if(count($contacts_RET))
			{
				foreach($contacts_RET as $contact)
				{
					$THIS_RET = $contact;

					$style .= ' ';

					$i++;
					$link = 'onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id='.$_REQUEST['address_id'].'&person_id='.$contact['PERSON_ID'].'&con_info=old\';"';
					if(AllowEdit())
						$remove_button = button('remove','',"Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&modfunc=delete&address_id=$_REQUEST[address_id]&person_id=$contact[PERSON_ID]",20);
					else
						$remove_button = '';
					if($_REQUEST['person_id']==$contact['PERSON_ID'])
						echo '<TR><td><table border=0><TR><TD width=20 align=right'.$style.'>'.$remove_button.'</TD><TD '.$link.' '.$style.'>';
					else
						echo '<TR><td><table border=0><TR><TD width=20 align=right'.$style.'>'.$remove_button.'</TD><TD '.$link.' '.$style.' style=white-space:nowrap>';

					$images = '';

					

					if($contact['CUSTODY']=='Y')
						$images .= ' <IMG SRC=assets/gavel_button.gif>';
					if($contact['EMERGENCY']=='Y')
						$images .= ' <IMG SRC=assets/emergency_button.gif>';
if ($_REQUEST['person_id']==$contact['PERSON_ID']) {
					echo '<A style="cursor:pointer; font-weight:bold;color:#ff0000" >'.($contact['STUDENT_RELATION']?$contact['STUDENT_RELATION']:'---').''.$images.'</A>';
					} else {
					echo '<A style="cursor:pointer; font-weight:bold;" >'.($contact['STUDENT_RELATION']?$contact['STUDENT_RELATION']:'---').''.$images.'</A>';
					}
					echo '</TD>';
					echo '<TD valign=middle align=right> &nbsp; <A style="cursor: pointer;"><IMG SRC=assets/arrow_right.gif></A></TD>';
					echo '</TR></table></td></tr>';
				}
			}
	############################################################################################	
	
	// New Address
	if(AllowEdit())
	{
		if($_REQUEST['address_id']!=='new' && $_REQUEST['address_id']!=='old')
		{

			echo '<TABLE width=100%><TR><TD>';
			if($_REQUEST['address_id']==0)
				echo '<TABLE border=0 cellpadding=0 cellspacing=0 width=100%>';
			else
				echo '<TABLE border=0 cellpadding=0 cellspacing=0 width=100%>';
			// New Contact
			if(AllowEdit())
			{
				$style = 'class=break';
			}

			echo '</TABLE>';
		}
                
                $check_address=DBGet(DBQuery('SELECT COUNT(*) as REC_EX FROM students_join_people WHERE STUDENT_ID='.UserStudentID()));
		if($check_address[1]['REC_EX']>1)
                {
                    if(clean_param($_REQUEST['person_id'],PARAM_ALPHAMOD)=='new')
                    {
                            echo '<TR onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id='.$_REQUEST['address_id'].'&person_id=new&con_info=old\';" onmouseover=\'this.style.color="white";\' ><TD>';
                            echo '<A style="cursor: pointer;color:#FF0000"><b>Add New Contact</b></A>';
                    }
                    else
                    {
                            echo '<TR onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id='.$_REQUEST['address_id'].'&person_id=new&con_info=old\';" onmouseover=\'this.style.color="white";\' ><TD>';
                            echo '<A style="cursor: pointer;"><b>Add New Contact</b></A>';
                    }
                
		echo '</TD>';
		echo '<TD><IMG SRC=assets/arrow_right.gif></TD>';
		echo '</TR>';
                }

	}
	echo '</TABLE>';
	echo '</TD>';
	echo '<TD class=vbreak>&nbsp;</TD><TD valign=top>';

	if(isset($_REQUEST['address_id']) && $_REQUEST['con_info']!='old')
	{
            $h_addr=DBGet(DBQuery(' SELECT sa.ID AS ADDRESS_ID,sa.STREET_ADDRESS_1 as ADDRESS,sa.STREET_ADDRESS_2 as STREET,sa.CITY,sa.STATE,sa.ZIPCODE,sa.BUS_PICKUP,sa.BUS_DROPOFF,sa.BUS_NO from student_address sa WHERE 
                                   sa.TYPE=\'Home Address\' AND sa.STUDENT_ID=\''.  UserStudentID().'\' AND sa.SCHOOL_ID=\''.  UserSchool().'\' '));
            
            $pri_par_id=  DBGet(DBQuery('SELECT * FROM students_join_people WHERE STUDENT_ID='.UserStudentID().' AND EMERGENCY_TYPE=\'Primary\''));
            if(count($pri_par_id)>0)
            {
               $p_addr=DBGet(DBQuery('SELECT p.STAFF_ID as CONTACT_ID,p.FIRST_NAME,p.MIDDLE_NAME,p.LAST_NAME,p.HOME_PHONE,p.WORK_PHONE,p.CELL_PHONE,p.EMAIL,p.CUSTODY,p.PROFILE_ID,
                                  sa.ID AS ADDRESS_ID,sa.STREET_ADDRESS_1 as ADDRESS,sa.STREET_ADDRESS_2 as STREET,sa.CITY,sa.STATE,sa.ZIPCODE,sa.BUS_PICKUP,sa.BUS_DROPOFF,sa.BUS_NO from people p,student_address sa WHERE p.STAFF_ID=sa.PEOPLE_ID  AND p.STAFF_ID=\''.$pri_par_id[1]['PERSON_ID'].'\'  AND sa.PEOPLE_ID IS NOT NULL '));
               $p_addr[1]['RELATIONSHIP']=$pri_par_id[1]['RELATIONSHIP'];
               
                $primary_user_profs_ids_arr = array();
                $primary_user_profs_ids=  DBGet(DBQuery('SELECT id FROM user_profiles WHERE profile = \''.'parent'.'\''));

                foreach($primary_user_profs_ids as $k=>$v){
                 $primary_user_profs_ids_arr[] = $primary_user_profs_ids[$k]['ID'];
                }
                               
                $primary_user_profs_id = implode(',',$primary_user_profs_ids_arr);
               
               $p_log_addr=DBGet(DBQuery('SELECT USERNAME AS USER_NAME ,PASSWORD FROM login_authentication WHERE USER_ID=\''.$pri_par_id[1]['PERSON_ID'].'\' AND PROFILE_ID in ('.$primary_user_profs_id.')'));
               $p_addr[1]['USER_NAME']=$p_log_addr[1]['USER_NAME'];
               $p_addr[1]['PASSWORD']=$p_log_addr[1]['PASSWORD'];
            }
            $m_addr=DBGet(DBQuery(' SELECT sa.ID AS ADDRESS_ID,sa.STREET_ADDRESS_1 as ADDRESS,sa.STREET_ADDRESS_2 as STREET,sa.CITY,sa.STATE,sa.ZIPCODE,sa.BUS_PICKUP,sa.BUS_DROPOFF,sa.BUS_NO from student_address sa WHERE 
                                   sa.TYPE=\'Mail\' AND sa.STUDENT_ID=\''.  UserStudentID().'\'  AND sa.SYEAR=\''.UserSyear().'\' AND sa.SCHOOL_ID=\''.  UserSchool().'\' '));
            $sec_par_id=  DBGet(DBQuery('SELECT * FROM students_join_people WHERE STUDENT_ID='.UserStudentID().' AND EMERGENCY_TYPE=\'Secondary\''));
        
            if(count($sec_par_id)>0)
            {  
                $s_addr=DBGet(DBQuery('SELECT p.STAFF_ID as CONTACT_ID,p.FIRST_NAME,p.MIDDLE_NAME,p.LAST_NAME,p.HOME_PHONE,p.WORK_PHONE,p.CELL_PHONE,p.EMAIL,p.CUSTODY,p.PROFILE_ID,
                                  sa.ID AS ADDRESS_ID,sa.STREET_ADDRESS_1 as ADDRESS,sa.STREET_ADDRESS_2 as STREET,sa.CITY,sa.STATE,sa.ZIPCODE,sa.BUS_PICKUP,sa.BUS_DROPOFF,sa.BUS_NO from people p,student_address sa WHERE p.STAFF_ID=sa.PEOPLE_ID  AND p.STAFF_ID=\''.$sec_par_id[1]['PERSON_ID'].'\'  AND sa.PEOPLE_ID IS NOT NULL '));                 
             
                
                $s_addr[1]['RELATIONSHIP']=$sec_par_id[1]['RELATIONSHIP'];
                
                $s_user_profs_ids_arr = array();
                $s_user_profs_ids=  DBGet(DBQuery('SELECT id FROM user_profiles WHERE profile = \''.'parent'.'\''));
                foreach($s_user_profs_ids as $k=>$v){
                 $s_user_profs_ids_arr[] = $s_user_profs_ids[$k]['ID'];
                }
                               
                $s_user_profs_id = implode(',',$s_user_profs_ids_arr);
                
                $p_log_addr=DBGet(DBQuery('SELECT USERNAME AS USER_NAME ,PASSWORD FROM login_authentication WHERE USER_ID=\''.$sec_par_id[1]['PERSON_ID'].'\' AND PROFILE_ID in ('.$s_user_profs_id.')'));
               $s_addr[1]['USER_NAME']=$p_log_addr[1]['USER_NAME'];
               $s_addr[1]['PASSWORD']=$p_log_addr[1]['PASSWORD'];
              
            }
            else
            {
                $s_addr=DBGet(DBQuery('SELECT ID AS ADDRESS_ID from student_address WHERE STUDENT_ID='.UserStudentID().' AND TYPE=\'Secondary\' '));                 
            }
            echo "<INPUT type=hidden name=address_id value=$_REQUEST[address_id]>";

		if($_REQUEST['address_id']!='0' && $_REQUEST['address_id']!=='old')
		{
                    $profiles_options = DBGet(DBQuery('SELECT PROFILE ,TITLE, ID FROM user_profiles WHERE profile = \'parent\' ORDER BY ID'));
                    $i = 1;
                    foreach($profiles_options as $options)
                    {

                            $option[$options['ID']] = $options['TITLE'];
                            $i++;
                    }
                        if($h_addr[1]['ADDRESS_ID']==0)
				$size = true;
			else
				$size = false;

			$city_options = _makeAutoSelect('CITY','student_address','',array(array('CITY'=>$h_addr[1]['CITY']),array('CITY'=>$h_addr[1]['CITY'])),$city_options);
			$state_options = _makeAutoSelect('STATE','student_address','',array(array('STATE'=>$h_addr[1]['STATE']),array('STATE'=>$h_addr[1]['STATE'])),$state_options);
			$zip_options = _makeAutoSelect('ZIPCODE','student_address','',array(array('ZIPCODE'=>$h_addr[1]['ZIPCODE']),array('ZIPCODE'=>$h_addr[1]['ZIPCODE'])),$zip_options);
                        
                        

                        if($h_addr[1]['BUS_PICKUP']=='N')
                            unset($h_addr[1]['BUS_PICKUP']);
                        if($h_addr[1]['BUS_DROPOFF']=='N')
                            unset($h_addr[1]['BUS_DROPOFF']);
                        if($h_addr[1]['BUS_NO']=='N')
                            unset($h_addr[1]['BUS_NO']);
                         if($p_addr[1]['CUSTODY']=='N')
                            unset($p_addr[1]['CUSTODY']);
                        if($s_addr[1]['CUSTODY']=='N')
                            unset($s_addr[1]['CUSTODY']);
                        
                        //hidden fields//
                        if($h_addr[1]['ADDRESS_ID']!='')
                        echo '<input type=hidden name="values[student_address][HOME][ID]" id=pri_person_id value='.$h_addr[1]['ADDRESS_ID'].' />';
                        else
                        echo '<input type=hidden name="values[student_address][HOME][ID]" id=pri_person_id value=new />';    
                        
                        if($m_addr[1]['ADDRESS_ID']!='')
                        echo '<input type=hidden name="values[student_address][MAIL][ID]" value='.$m_addr[1]['ADDRESS_ID'].' />';
                        else
                        echo '<input type=hidden name="values[student_address][MAIL][ID]" value=new />';
                        
                        if($s_addr[1]['ADDRESS_ID']!='')
                        echo '<input type=hidden name="values[student_address][SECONDARY][ID]" value='.$s_addr[1]['ADDRESS_ID'].' />';
                        else
                        echo '<input type=hidden name="values[student_address][SECONDARY][ID]" value=new />';    
                        
                        
                        if($p_addr[1]['ADDRESS_ID']!='')
                        echo '<input type=hidden name="values[student_address][PRIMARY][ID]" value='.$p_addr[1]['ADDRESS_ID'].' />';
                        else
                        echo '<input type=hidden name="values[student_address][PRIMARY][ID]" value=new />';
                        
                        echo '<br>';
                        
                        
                        if($p_addr[1]['CONTACT_ID']!='')
                        echo '<input type=hidden name="values[people][PRIMARY][ID]" value='.$p_addr[1]['CONTACT_ID'].' />';
                        else
                        echo '<input type=hidden name="values[people][PRIMARY][ID]" value=new />';
                        
                        if($s_addr[1]['CONTACT_ID']!='')
                        echo '<input type=hidden name="values[people][SECONDARY][ID]" value='.$s_addr[1]['CONTACT_ID'].' />';
                        else
                        echo '<input type=hidden name="values[people][SECONDARY][ID]" value=new />';
                        
			echo '<TABLE width=100%><TR><TD>'; // open 3a
			echo '<FIELDSET><LEGEND><FONT color=gray>Student\'s Home Address</FONT></LEGEND><TABLE width=100%>';
			echo '<TR><td><span class=red>*</span>Address Line 1</td><td>:</td><TD style=\"white-space:nowrap\"><table cellspacing=0 cellpadding=0 cellspacing=0 cellpadding=0 border=0><tr><td>'.TextInput($h_addr[1]['ADDRESS'],'values[student_address][HOME][STREET_ADDRESS_1]','','class=cell_medium').'</td><td>';
//			
                        if($h_addr[1]['ADDRESS_ID']!='0')
			{
				$display_address = urlencode($h_addr[1]['ADDRESS'].', '.($h_addr[1]['CITY']?' '.$h_addr[1]['CITY'].', ':'').$h_addr[1]['STATE'].($h_addr[1]['ZIPCODE']?' '.$h_addr[1]['ZIPCODE']:''));
				$link = 'http://google.com/maps?q='.$display_address;
				echo '&nbsp;<A class=red HREF=# onclick=\'window.open("'.$link.'","","scrollbars=yes,resizable=yes,width=800,height=700");\'>Map it</A>';
			}
			echo '</td></tr></table></TD></tr>';
			echo '<TR><td>Address Line 2</td><td>:</td><TD>'.TextInput($h_addr[1]['STREET'],'values[student_address][HOME][STREET_ADDRESS_2]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td><span class=red>*</span>City</td><td>:</td><TD>'.TextInput($h_addr[1]['CITY'],'values[student_address][HOME][CITY]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td><span class=red>*</span>State</td><td>:</td><TD>'.TextInput($h_addr[1]['STATE'],'values[student_address][HOME][STATE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td><span class=red>*</span>Zip/Postal Code</td><td>:</td><TD>'.TextInput($h_addr[1]['ZIPCODE'],'values[student_address][HOME][ZIPCODE]','','class=cell_medium').'</TD></tr>';
			echo '<tr><TD>School Bus Pick-up</td><td>:</td><td>'.CheckboxInputMod($h_addr[1]['BUS_PICKUP'],'values[student_address][HOME][BUS_PICKUP]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></tr>';
			echo '<TR><TD>School Bus Drop-off</td><td>:</td><td>'.CheckboxInputMod($h_addr[1]['BUS_DROPOFF'],'values[student_address][HOME][BUS_DROPOFF]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></tr>';
			echo '<TR><td>Bus No</td><td>:</td><td>'.TextInput($h_addr[1]['BUS_NO'],'values[student_address][HOME][BUS_NO]','','class=cell_small').'</TD></tr>';
			echo '</TABLE></FIELDSET>';
			echo'</TD></TR>';
			echo '</TABLE>'; //close 3a

			
			echo '<TABLE border=0 width=100%><TR><TD>'; //open 3b
			echo '<FIELDSET><LEGEND><FONT color=gray>Student\'s Mailing Address</FONT></LEGEND>';
			

                        
                        if($m_addr[1]['ADDRESS_ID']!='' && $h_addr[1]['ADDRESS_ID']!='')
                        {    
                        $s_mail_address=DBGet(DBQuery('SELECT COUNT(1) as TOTAL FROM student_address WHERE ID!=\''.$m_addr[1]['ADDRESS_ID'].'\' AND STREET_ADDRESS_1=\''.str_replace("'","\'",$m_addr[1]['ADDRESS']).'\' AND STREET_ADDRESS_1=\''.str_replace("'","\'",$p_addr[1]['ADDRESS']).'\' AND STREET_ADDRESS_2=\''.str_replace("'","\'",$m_addr[1]['STREET']).'\'  AND CITY=\''.str_replace("'","\'",$m_addr[1]['CITY']).'\' AND STATE=\''.str_replace("'","\'",$m_addr[1]['STATE']).'\' AND ZIPCODE=\''.$m_addr[1]['ZIPCODE'].'\' AND TYPE=\'Home Address\' '));
                        if($s_mail_address[1]['TOTAL']!=0)
                           $m_checked=" CHECKED=CHECKED ";
                        else
                            $m_checked=" ";
                        }
                        
                        if($h_addr[1]['ADDRESS_ID']!=0)
                            echo '<div id="check_addr"><input type="checkbox" '.$m_checked.' id="same_addr" name="same_addr" set_check_value value="Y">&nbsp;Same as Home Address &nbsp;</div><br>';
			if($h_addr[1]['ADDRESS_ID']==0)
			echo '<table><TR><TD><span class=red>*</span><input type="radio" id="r4" name="r4" value="Y" onClick="hidediv();" checked>&nbsp;Same as Home Address &nbsp;&nbsp; <input type="radio" id="r4" name="r4" value="N" onClick="showdiv();">&nbsp;Add New Address</TD></TR></TABLE>'; 
			
			if($h_addr[1]['ADDRESS_ID']==0)
			echo '<div id="hideShow" style="display:none">';
			else
			echo '<div id="hideShow">';
			echo '<TABLE>';
			echo '<TR><td style=width:120px>Address Line 1</td><td>:</td><TD>'.TextInput($m_addr[1]['ADDRESS'],'values[student_address][MAIL][STREET_ADDRESS_1]','','class=cell_medium').'</TD>';
			echo '<TR><td>Address Line 2</td><td>:</td><TD>'.TextInput($m_addr[1]['STREET'],'values[student_address][MAIL][STREET_ADDRESS_2]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>City</td><td>:</td><TD>'.TextInput($m_addr[1]['CITY'],'values[student_address][MAIL][CITY]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>State</td><td>:</td><TD>'.TextInput($m_addr[1]['STATE'],'values[student_address][MAIL][STATE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>Zip/Postal Code</td><td>:</td><TD>'.TextInput($m_addr[1]['ZIPCODE'],'values[student_address][MAIL][ZIPCODE]','','class=cell_medium').'</TD></tr>';
			
			echo '</TABLE>';
			echo '</div>';

			echo '</FIELDSET>';
			echo'</TD></TR>';
			echo '</TABLE>'; // close 3b
			
			
			echo '<TABLE border=0 width=100%><TR><TD>'; //open 3c
			echo '<FIELDSET><LEGEND><FONT color=gray>Primary Emergency Contact</FONT></LEGEND><TABLE width=100%><tr><td>';
			echo '<table border=0 width=100%>';
                       
                        $prim_relation_options = _makeAutoSelect('RELATIONSHIP','students_join_people','PRIMARY',$p_addr['RELATIONSHIP'],$relation_options);
                        
                         if(User('PROFILE')!='teacher')
                             if(User('PROFILE')=='student' || User('PROFILE')=='parent' ){
                                echo '<tr><td style=width:120px><span class=red>*</span>Relationship to Student</TD><td>:</td><td><table><tr><td>'._makeAutoSelectInputX($p_addr[1]['RELATIONSHIP'],'RELATIONSHIP','people','PRIMARY','',$prim_relation_options).'</td></tr></table></TD></tr>';
                              }  else {
                                 echo '<tr><td style=width:120px><span class=red>*</span>Relationship to Student</TD><td>:</td><td><table><tr><td>'._makeAutoSelectInputX($p_addr[1]['RELATIONSHIP'],'RELATIONSHIP','people','PRIMARY','',$prim_relation_options).'</td><td><input type="button" name="lookup" value="Lookup" onclick="javascript:window.open(\'ForWindow.php?modname='.$_REQUEST['modname'].'&modfunc=lookup&type=primary&ajax='.$_REQUEST['ajax'].'&address_id='.$_REQUEST['address_id'].'\',\'blank\',\'resizable=yes,scrollbars=yes,width=600,height=400\');return false;"></td></tr></table></TD></tr>';
                             }
                            echo '<TR><td><span class=red>*</span>First Name</td><td>:</td><TD>'.TextInput($p_addr[1]['FIRST_NAME'],'values[people][PRIMARY][FIRST_NAME]','','id=pri_fname class=cell_medium').'</TD></tr>';
                            echo '<TR><td><span class=red>*</span>Last Name</td><td>:</td><TD>'.TextInput($p_addr[1]['LAST_NAME'],'values[people][PRIMARY][LAST_NAME]','','id=pri_lname class=cell_medium').'</TD></tr>';
                            echo '<TR><td>Home Phone</td><td>:</td><TD>'.TextInput($p_addr[1]['HOME_PHONE'],'values[people][PRIMARY][HOME_PHONE]','','id=pri_hphone class=cell_medium').'</TD></tr>';
                            echo '<TR><td>Work Phone</td><td>:</td><TD>'.TextInput($p_addr[1]['WORK_PHONE'],'values[people][PRIMARY][WORK_PHONE]','','id=pri_wphone class=cell_medium').'</TD></tr>';
                            echo '<TR><td>Cell/Mobile Phone</td><td>:</td><TD>'.TextInput($p_addr[1]['CELL_PHONE'],'values[people][PRIMARY][CELL_PHONE]','','id=pri_cphone class=cell_medium').'</TD></tr>';
                            if($p_addr[1]['CONTACT_ID']=='')
                          
                                 echo '<TR><td><span class=red>*</span>Emaill</td><td>:</td><td>'.TextInput($p_addr[1]['EMAIL'],'values[people][PRIMARY][EMAIL]','','autocomplete=off id=pri_email class=cell_medium onkeyup=peoplecheck_email(this,1,0) ').'</td><td> <span id="email_1"></span></td></tr></tr>';
                            else
                            echo '<TR><td><span class=red>*</span>Email</td><td>:</td><TD><table><tr><td>'.TextInput($p_addr[1]['EMAIL'],'values[people][PRIMARY][EMAIL]','','autocomplete=off id=pri_email class=cell_medium onkeyup=peoplecheck_email(this,1,'.$p_addr[1]['CONTACT_ID'].') ').'</td><td> <span id="email_1"></span></td></tr></table></TD></tr>';    
                            echo '<TR><TD>Custody of Student</TD><td>:</td><TD>'.CheckboxInputMod($p_addr[1]['CUSTODY'],'values[people][PRIMARY][CUSTODY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></TR>';
                        
                           
                            if($p_addr[1]['USER_NAME']=='')
                            {    
                            $portal_check='';    
                            $style='style="display:none"';
                            }
                            else
                            {
                            $portal_check='checked="checked"';
                            $style='';
                            }
                            echo '<input type="hidden" id=pri_val_pass value="Y">';
                            echo '<input type="hidden" name=selected_pri_parent id=selected_pri_parent value="">';
                            echo '<input type="hidden" id=pri_val_user value="Y">';
                            echo '<input type="hidden" id=val_email_1 name=val_email_1 value="Y">';
                            if($portal_check=='')                         
                                echo '<tr><td>Portal User</td><td>:</td><td><input type="checkbox" width="25" name="primary_portal" value="Y" id="portal_1" onClick="portal_toggle(1);" '.$portal_check.'/></td></tr>';   
                            else
                                echo '<TR><TD>Portal User</TD><td>:</td><TD><div id=checked_1><IMG SRC=assets/check.gif width=15></div></TD></TR>';   
                                echo '<tr><td colspan=3><div id="portal_div_1" '.$style.'><TABLE>';
                            if($p_addr[1]['USER_NAME']=='' && $p_addr[1]['PASSWORD']=='')
                            {
                                echo '<TR><TD>Username</TD><td>:</td><TD>'.TextInput($p_addr[1]['USER_NAME'],'values[people][PRIMARY][USER_NAME]','','id=primary_username class=cell_medium onblur="usercheck_init_mod(this,1)" ').'<div id="ajax_output_1"></div></TD></TR>';   
                                echo '<TR><TD>Password</TD><td>:</td><TD>'.TextInput($p_addr[1]['PASSWORD'],'values[people][PRIMARY][PASSWORD]','','id=primary_password class=cell_medium onkeyup="passwordStrengthMod(this.value,1);" onblur="validate_password_mod(this.value,1);"').'<span id="passwordStrength1"></span></TD></TR>';   
                            }
                            else
                            {
                                echo '<TR><TD>Username</TD><td>:</td><TD><div id=uname1>'.$p_addr[1]['USER_NAME'].'</div></TD></TR>';
                                echo '<TR><TD>Password</TD><td>:</td><TD><div id=pwd1>'.str_repeat('*',strlen($p_addr[1]['PASSWORD'])).'</div></TD></TR>';
                            }
                            
                            $user_profs=  DBGet(DBQuery('SELECT * FROM user_profiles WHERE profile = \''.'parent'.'\''));
                            
                            foreach($user_profs as $user_profs_value)
                            {
                                $user_prof_options[$user_profs_value['ID']]=$user_profs_value['TITLE'];
                            }
                            
                            echo '<TR><TD>User Profile</TD><td>:</td><TD>'.SelectInput($p_addr[1]['PROFILE_ID'],'values[people][PRIMARY][PROFILE_ID]',(!$p_addr[1]['PROFILE']?'<FONT color=red>':'').''.(!$p_addr[1]['PROFILE']?'</FONT>':''),$user_prof_options,FALSE,' id=pri_prof_id').'</TD></TR>';
                        
                        echo '</TABLE></td></tr></div>';
                        echo '<tr><td colspan=3><div id="portal_hidden_div_1" ><TABLE>';
                        echo '</TABLE></td></tr></div>';
		
			if($h_addr[1]['ADDRESS_ID']==0)
			echo '<tr><td colspan=3><table><TR><TD><TD><span class=red>*</span><input type="radio" id="rps" name="r5" value="Y" onClick="prim_hidediv();" checked>&nbsp;Same as Student\'s Home Address &nbsp;&nbsp; <input type="radio" id="rpn" name="r5" value="N" onClick="prim_showdiv();">&nbsp;Add New Address</TD></TR></TABLE></td></tr>'; 

			if($h_addr[1]['ADDRESS_ID']==0)
			echo '<tr><td colspan=3><div id="prim_hideShow" style="display:none">';
			else
			echo '<tr><td colspan=5><div id="prim_hideShow">';
			echo '<div class=break></div>';
                        
                      
                        if($h_addr[1]['ADDRESS_ID']!='' && $p_addr[1]['ADDRESS_ID']!='')
                        {
                           
                        $s_prim_address=DBGet(DBQuery('SELECT COUNT(1) as TOTAL FROM student_address WHERE ID!=\''.$p_addr[1]['ADDRESS_ID'].'\' AND STREET_ADDRESS_1=\''.str_replace("'","\'",$p_addr[1]['ADDRESS']).'\' AND STREET_ADDRESS_2=\''.str_replace("'","\'",$p_addr[1]['STREET']).'\' AND CITY=\''.str_replace("'","\'",$p_addr[1]['CITY']).'\' AND STATE=\''.str_replace("'","\'",$p_addr[1]['STATE']).'\' AND ZIPCODE=\''.$p_addr[1]['ZIPCODE'].'\' AND TYPE=\'Home Address\' '));
                
                      
                        if($s_prim_address[1]['TOTAL']!=0)
                           $p_checked=" CHECKED=CHECKED ";
                        else
                            $p_checked=" ";
                         if($p_addr[1]['ADDRESS_ID']!=0)
                            echo '<div id="check_addr"><input type="checkbox" '.$p_checked.' id="prim_addr" name="prim_addr" value="Y">&nbsp;Same as Home Address &nbsp;</div><br>';
                        }
			echo '<table><TR><td style=width:120px>Address Line 1</td><td>:</td><TD><table cellspacing=0 cellpadding=0><tr><td>'.TextInput($p_addr[1]['ADDRESS'],'values[student_address][PRIMARY][STREET_ADDRESS_1]','','id=pri_address class=cell_medium').'</TD><td>';
			
			if($p_addr[1]['ADDRESS_ID']!=0)
			{
				$display_address = urlencode($p_addr[1]['ADDRESS'].', '.($p_addr[1]['CITY']?' '.$p_addr[1]['CITY'].', ':'').$p_addr[1]['STATE'].($p_addr[1]['ZIPCODE']?' '.$p_addr[1]['ZIPCODE']:''));
				$link = 'http://google.com/maps?q='.$display_address;
				echo '&nbsp;<A class=red HREF=# onclick=\'window.open("'.$link.'","","scrollbars=yes,resizable=yes,width=800,height=700");\'>Map it</A>';
			}
			echo '</td></tr></table></td></tr>';
			echo '<TR><td>Address Line 2</td><td>:</td><TD>'.TextInput($p_addr[1]['STREET'],'values[student_address][PRIMARY][STREET_ADDRESS_2]','','id=pri_street class=cell_medium').'</TD></tr>';
			echo '<TR><td>City</td><td>:</td><TD>'.TextInput($p_addr[1]['CITY'],'values[student_address][PRIMARY][CITY]','','id=pri_city class=cell_medium').'</TD></tr>';
			echo '<TR><td>State</td><td>:</td><TD>'.TextInput($p_addr[1]['STATE'],'values[student_address][PRIMARY][STATE]','','id=pri_state class=cell_medium').'</TD></tr>';
			echo '<TR><td>Zip/Postal Code</td><td>:</td><TD>'.TextInput($p_addr[1]['ZIPCODE'],'values[student_address][PRIMARY][ZIPCODE]','','id=pri_zip class=cell_medium').'</TD>';
			echo '</table>';
			echo '</div></td></tr>';

			echo '</table></td></tr></table></FIELDSET>';
			echo'</TD></TR>';
			echo '</TABLE>'; // close 3c
			
############################################################################################		
			echo '<TABLE border=0 width=100%><TR><TD>'; // open 3d
			echo '<FIELDSET><LEGEND><FONT color=gray>Secondary Emergency Contact</FONT></LEGEND><TABLE width=100%><tr><td>';
                        echo '<table border=0 width=100%>';
			$sec_relation_options = _makeAutoSelect('RELATIONSHIP','students_join_people','SECONDARY',$s_addr[1]['RELATIONSHIP'],$relation_options);
                         if(User('PROFILE')!='teacher')
                             if(User('PROFILE')=='student' || User('PROFILE')=='parent' ) {
                        echo '<tr><td style=width:120px>Relationship to Student</td><td>:</td><TD><table><tr><td>'._makeAutoSelectInputX($s_addr[1]['RELATIONSHIP'],'RELATIONSHIP','people','SECONDARY','',$sec_relation_options).'</td></tr></table></TD></tr>';
                             } else {
                        echo '<tr><td style=width:120px>Relationship to Student</td><td>:</td><TD><table><tr><td>'._makeAutoSelectInputX($s_addr[1]['RELATIONSHIP'],'RELATIONSHIP','people','SECONDARY','',$sec_relation_options).'</td><td><input type="button" name="lookup" value="Lookup" onclick="javascript:window.open(\'ForWindow.php?modname='.$_REQUEST['modname'].'&modfunc=lookup&type=secondary&ajax='.$_REQUEST['ajax'].'&address_id='.$_REQUEST['address_id'].'\',\'blank\',\'resizable=yes,scrollbars=yes,width=600,height=400\');return false;"></td></tr></table></TD></tr>';
                         }

                            echo '<TR><td>First Name</td><td>:</td><TD>'.TextInput($s_addr[1]['FIRST_NAME'],'values[people][SECONDARY][FIRST_NAME]','','id=sec_fname class=cell_medium').'</TD></tr>';


                            echo '<TR><td>Last Name</td><td>:</td><TD>'.TextInput($s_addr[1]['LAST_NAME'],'values[people][SECONDARY][LAST_NAME]','','id=sec_lname class=cell_medium').'</TD></tr>';
                            echo '<TR><td>Home Phone</td><td>:</td><TD>'.TextInput($s_addr[1]['HOME_PHONE'],'values[people][SECONDARY][HOME_PHONE]','','id=sec_hphone class=cell_medium').'</TD></tr>';
                            echo '<TR><td>Work Phone</td><td>:</td><TD>'.TextInput($s_addr[1]['WORK_PHONE'],'values[people][SECONDARY][WORK_PHONE]','','id=sec_wphone class=cell_medium').'</TD></tr>';
                            echo '<TR><td>Cell/Mobile Phone</td><td>:</td><TD>'.TextInput($s_addr[1]['CELL_PHONE'],'values[people][SECONDARY][CELL_PHONE]','','id=sec_cphone class=cell_medium').'</TD></tr>';
                            if($s_addr[1]['CONTACT_ID']=='')
                                 echo '<TR><td>Email</td><td>:</td><td>'.TextInput($s_addr[1]['EMAIL'],'values[people][SECONDARY][EMAIL]','','autocomplete=off id=sec_email class=cell_medium onkeyup=peoplecheck_email(this,2,0) ').'</td><td><span id="email_2"></span></td></tr>';
                            else
                            echo '<TR><td>Email</td><td>:</td><TD><table><tr><td>'.TextInput($s_addr[1]['EMAIL'],'values[people][SECONDARY][EMAIL]','','autocomplete=off id=sec_email class=cell_medium onkeyup=peoplecheck_email(this,2,'.$s_addr[1]['CONTACT_ID'].') ').'</td><td><span id="email_2"></span></td></tr></table></TD></tr>';    
                            echo '<TR><TD>Custody of Student</TD><td>:</td><TD>'.CheckboxInputMod($s_addr[1]['CUSTODY'],'values[people][SECONDARY][CUSTODY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></TR>';

                            if($s_addr[1]['USER_NAME']=='')
                            {    
                            $portal_check='';    
                            $style='style="display:none"';
                            }
                            else
                            {
                            $portal_check='checked="checked"';
                            $style='';
                            }
                            echo '<input type="hidden" id=sec_val_pass value="Y">';
                            echo '<input type="hidden" id=sec_val_user value="Y">';
                            echo '<input type="hidden" name=selected_sec_parent id=selected_sec_parent value="">';
                            echo '<input type="hidden" id=val_email_2 name=val_email_2 value="Y">';
                            if($portal_check=='')
                            echo '<TR><TD>Portal User</TD><td>:</td><TD><input type="checkbox" name="secondary_portal" value="Y" id="portal_2" onClick="portal_toggle(2);" '.$portal_check.'/></TD></TR>';   
                            else
                                echo '<TR><TD>Portal User</TD><td>:</td><TD><div id=checked_2><IMG SRC=assets/check.gif width=15></div></TD></TR>';
                            echo '<tr><td colspan=3><div id="portal_div_2" '.$style.'><TABLE>';
                            if($s_addr[1]['USER_NAME']=='' && $s_addr[1]['PASSWORD']=='')
                            {
                                echo '<TR><TD>Username</TD><td>:</td><TD>'.TextInput($s_addr[1]['USER_NAME'],'values[people][SECONDARY][USER_NAME]','','id=secondary_username class=cell_medium onkeyup="usercheck_init_mod(this,2)" ').'<div id="ajax_output_2"></div></TD></TR>';   
                                echo '<TR><TD>Password</TD><td>:</td><TD>'.TextInput($s_addr[1]['PASSWORD'],'values[people][SECONDARY][PASSWORD]','','id=secondary_password class=cell_medium onkeyup="passwordStrengthMod(this.value,2);validate_password_mod(this.value,2);"').'<span id="passwordStrength2"></span></TD></TR>';   
                            }
                            else
                            {
                                echo '<TR><TD>Username</TD><td>:</td><TD><div id=uname2>'.$s_addr[1]['USER_NAME'].'</div></TD></TR>';
                                echo '<TR><TD>Password</TD><td>:</td><TD><div id=pwd2>'.str_repeat('*',strlen($s_addr[1]['PASSWORD'])).'</div></TD></TR>';
                            }
                            
                            
                            echo '<TR><TD>User Profile</TD><td>:</td><TD>'.SelectInput($s_addr[1]['PROFILE_ID'],'values[people][SECONDARY][PROFILE_ID]',(!$s_addr[1]['PROFILE']?'<FONT color=red>':'').''.(!$s_addr[1]['PROFILE']?'</FONT>':''),$user_prof_options,FALSE,' id=sec_prof_id').'</TD></TR>';

                        
                        echo '</TABLE></td></tr></div>';
                        
                        echo '<tr><td colspan=3><div id="portal_hidden_div_2" ><TABLE>';
                        echo '</TABLE></td></tr></div>';
     
			if($h_addr[1]['ADDRESS_ID']==0)
			echo '<tr><td colspan=3><table><TR><TD><span class=red ></span><input type="radio" id="rss" name="r6" value="Y" onClick="sec_hidediv();" checked>&nbsp;Same as Student\'s Home Address &nbsp;&nbsp; <input type="radio" id="rsn" name="r6" value="N" onClick="sec_showdiv();">&nbsp;Add New Address</TD></TR></TABLE></td></tr>';

			if($h_addr[1]['ADDRESS_ID']==0)
			echo '<tr><td colspan=3><div id="sec_hideShow" style="display:none">';
			else
			echo '<tr><td colspan=3><div id="sec_hideShow">';
			echo '<div class=break></div>';
                        $s_sec_address=DBGet(DBQuery('SELECT COUNT(1) as TOTAL FROM student_address WHERE ID!=\''.$s_addr[1]['ADDRESS_ID'].'\' AND STREET_ADDRESS_1=\''.str_replace("'","\'",$s_addr[1]['ADDRESS']).'\' AND STREET_ADDRESS_1=\''.str_replace("'","\'",$p_addr[1]['ADDRESS']).'\' AND STREET_ADDRESS_2=\''.str_replace("'","\'",$s_addr[1]['STREET']).'\'  AND CITY=\''.str_replace("'","\'",$s_addr[1]['CITY']).'\' AND STATE=\''.str_replace("'","\'",$s_addr[1]['STATE']).'\' AND ZIPCODE=\''.$s_addr[1]['ZIPCODE'].'\' AND TYPE=\'Home Address\' '));
                        if($s_sec_address[1]['TOTAL']!=0)
                           $s_checked=" CHECKED=CHECKED ";
                        else
                            $s_checked=" ";
                         if($h_addr[1]['ADDRESS_ID']!=0)
                            echo '<div id="check_addr"><input type="checkbox" '.$s_checked.' id="sec_addr" name="sec_addr" value="Y">&nbsp;Same as Home Address &nbsp;</div><br>';
                         
			echo '<table><TR><td style=width:120px>Address Line 1</td><td>:</td><TD><table cellspacing=0 cellpadding=0><tr><td>'.TextInput($s_addr[1]['ADDRESS'],'values[student_address][SECONDARY][STREET_ADDRESS_1]','','id=sec_address class=cell_medium').'</TD><td>';
			
			if($h_addr[1]['ADDRESS_ID']!=0)
			{
				$display_address = urlencode($s_addr[1]['ADDRESS'].', '.($s_addr[1]['CITY']?' '.$s_addr[1]['CITY'].', ':'').$s_addr[1]['STATE'].($s_addr[1]['ZIPCODE']?' '.$s_addr[1]['ZIPCODE']:''));
				$link = 'http://google.com/maps?q='.$display_address;
				echo '&nbsp;<A class=red HREF=# onclick=\'window.open("'.$link.'","","scrollbars=yes,resizable=yes,width=800,height=700");\'>Map it</A>';
			}
			echo '</td></tr></table></td></tr>';
			echo '<TR><td>Address Line 2</td><td>:</td><TD>'.TextInput($s_addr[1]['STREET'],'values[student_address][SECONDARY][STREET_ADDRESS_2]','','id=sec_street class=cell_medium').'</TD></tr>';
			echo '<TR><td>City</td><td>:</td><TD>'.TextInput($s_addr[1]['CITY'],'values[student_address][SECONDARY][CITY]','','id=sec_city class=cell_medium').'</TD></tr>';
			echo '<TR><td>State</td><td>:</td><TD>'.TextInput($s_addr[1]['STATE'],'values[student_address][SECONDARY][STATE]','','id=sec_state class=cell_medium').'</TD></tr>';
			echo '<TR><td>Zip/Postal Code</td><td>:</td><TD>'.TextInput($s_addr[1]['ZIPCODE'],'values[student_address][SECONDARY][ZIPCODE]','','id=sec_zip class=cell_medium').'</TD>';
			echo '</TABLE>';
			echo '</div></td></tr></table></td></tr></table>';

			
			echo'</TD></TR>';
			echo '</TABLE>';  // close 3d
			
			
############################################################################################			
			
		}

	}
	else
		echo '';
		
	
	$separator = '<HR>';
}

$parent_user_profs=  DBGet(DBQuery('SELECT * FROM user_profiles WHERE profile = \''.'parent'.'\''));
                            
foreach($parent_user_profs as $parent_user_profs_value)
{
    $parent_user_prof_options[$parent_user_profs_value['ID']]=$parent_user_profs_value['TITLE'];
}

if($_REQUEST['person_id'] && $_REQUEST['con_info']=='old')
{       
			echo "<INPUT type=hidden name=person_id value=$_REQUEST[person_id]>";
                        
			if($_REQUEST['person_id']!='old')
			{
                            if($_REQUEST['person_id']!='new')
                            {
                                $other_par_id=  DBGet(DBQuery('SELECT * FROM students_join_people WHERE STUDENT_ID='.UserStudentID().' AND PERSON_ID='.$_REQUEST['person_id'].' AND EMERGENCY_TYPE=\'Other\''));
                            
                               $o_addr=DBGet(DBQuery('SELECT p.STAFF_ID as PERSON_ID,p.FIRST_NAME,p.MIDDLE_NAME,p.LAST_NAME,p.HOME_PHONE,p.WORK_PHONE,p.CELL_PHONE,p.EMAIL,p.CUSTODY,p.PROFILE_ID,
                                                  sa.ID AS ADDRESS_ID,sa.STREET_ADDRESS_1 as ADDRESS,sa.STREET_ADDRESS_2 as STREET,sa.CITY,sa.STATE,sa.ZIPCODE,sa.BUS_PICKUP,sa.BUS_DROPOFF,sa.BUS_NO from people p,student_address sa WHERE p.STAFF_ID=sa.PEOPLE_ID  AND p.STAFF_ID=\''.$_REQUEST['person_id'].'\'  AND sa.PEOPLE_ID IS NOT NULL '));
                               $o_addr[1]['RELATIONSHIP']=$other_par_id[1]['RELATIONSHIP'];
                               $o_addr[1]['IS_EMERGENCY']=$other_par_id[1]['IS_EMERGENCY'];
                               
                               $parent_user_profs_ids_arr = array();
                               $parent_user_profs_ids=  DBGet(DBQuery('SELECT id FROM user_profiles WHERE profile = \''.'parent'.'\''));
                               
                               foreach($parent_user_profs_ids as $k=>$v){
                                $parent_user_profs_ids_arr[] = $parent_user_profs_ids[$k]['ID'];
                               }
                               
                               $parent_user_profs_id = implode(',',$parent_user_profs_ids_arr);
                               
                               $p_log_addr=DBGet(DBQuery('SELECT USERNAME AS USER_NAME ,PASSWORD FROM login_authentication WHERE USER_ID=\''.$_REQUEST['person_id'].'\' AND PROFILE_ID in ('.$parent_user_profs_id.')'));
                                $o_addr[1]['USER_NAME']=$p_log_addr[1]['USER_NAME'];
                                $o_addr[1]['PASSWORD']=$p_log_addr[1]['PASSWORD'];
              
                            } 
                                 
                                if($o_addr[1]['PERSON_ID']!='')
                                echo '<input type=hidden name="values[people][OTHER][ID]" id=oth_person_id value='.$o_addr[1]['PERSON_ID'].' />';
                                else
                                echo '<input type=hidden name="values[people][OTHER][ID]" id=oth_person_id value=new />';
                                
                                if($o_addr[1]['ADDRESS_ID']!='')
                                echo '<input type=hidden name="values[student_address][OTHER][ID]" value='.$o_addr[1]['ADDRESS_ID'].' />';
                                else
                                echo '<input type=hidden name="values[student_address][OTHER][ID]" value=new />';
                                
                                $relation_options = _makeAutoSelect('RELATIONSHIP','students_join_people','OTHER',$o_addr[1]['RELATIONSHIP'],$relation_options);
                             
                                if($o_addr[1]['IS_EMERGENCY']=='N')
                                    unset($o_addr[1]['IS_EMERGENCY']);
                                if($o_addr[1]['CUSTODY']=='N')
                                    unset($o_addr[1]['CUSTODY']);
                                if($o_addr[1]['BUS_PICKUP']=='N')
                                    unset($o_addr[1]['BUS_PICKUP']);
                                if($o_addr[1]['BUS_DROPOFF']=='N')
                                    unset($o_addr[1]['BUS_DROPOFF']);
                                if($o_addr[1]['BUS_NO']=='N')
                                    unset($o_addr[1]['BUS_NO']);
				echo '<TABLE><TR><TD><FIELDSET><LEGEND><FONT color=gray>Additional Contact</FONT></LEGEND><TABLE width=100% border=0>'; // open 3e
				if($_REQUEST['person_id']!='new' && $_REQUEST['con_info']=='old')
				{
					echo '<TR><TD colspan=3><table><tr><td><input type=hidden name=values[people][OTHER][IS_EMERGENCY_HIDDEN] id=IS_EMERGENCY_HIDDEN value="N">'.CheckboxInputMod($o_addr[1]['IS_EMERGENCY'],'values[people][OTHER][IS_EMERGENCY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD><TD> This is an Emergency Contact</TD></TR></table></td></tr>';
					echo '<tr><td colspan=3 class=break></td></tr>';
					echo '<TR><td style="width:120px">Relationship to Student</td><td>:</td><TD><table><tr><td>'._makeAutoSelectInputX($o_addr[1]['RELATIONSHIP'],'RELATIONSHIP','people','OTHER','',$relation_options).'</td><td><input type="button" name="lookup" value="Lookup" onclick="javascript:window.open(\'ForWindow.php?modname='.$_REQUEST['modname'].'&modfunc=lookup&type=other&ajax='.$_REQUEST['ajax'].'&add_id='.$o_addr[1]['PERSON_ID'].'&address_id='.$_REQUEST['address_id'].'\',\'blank\',\'resizable=yes,scrollbars=yes,width=600,height=400\');return false;"></td></tr></table></TD>';
                                        echo '<TR><TD><span class=red>*</span>First Name</td><td>:</td><td><DIV id=person_f_'.$o_addr[1]['PERSON_ID'].'><div onclick=\'addHTML("<table><TR><TD>'.str_replace('"','\"',_makePeopleInput($o_addr[1]['FIRST_NAME'],'people','FIRST_NAME','OTHER','','class=cell_medium')).'</TD></TR></TABLE>","person_f_'.$o_addr[1]['PERSON_ID'].'",true);\'>'.$o_addr[1]['FIRST_NAME'].'</div></DIV></TD></TR>';
                                        echo '<TR><TD><span class=red>*</span>Last Name</td><td>:</td><td><DIV id=person_l_'.$o_addr[1]['PERSON_ID'].'><div onclick=\'addHTML("<table><TR><TD>'.str_replace('"','\"',_makePeopleInput($o_addr[1]['LAST_NAME'],'people','LAST_NAME','OTHER','','class=cell_medium')).'</TD></TR></TABLE>","person_l_'.$o_addr[1]['PERSON_ID'].'",true);\'>'.$o_addr[1]['LAST_NAME'].'</div></DIV></TD></TR>';
					echo '<tr><TD>Home Phone</td><td>:</td><td> '.TextInput($o_addr[1]['HOME_PHONE'],'values[people][OTHER][HOME_PHONE]','','class=cell_medium').'</TD></tr>';
					echo '<tr><TD>Work Phone</td><td>:</td><td>'.TextInput($o_addr[1]['WORK_PHONE'],'values[people][OTHER][WORK_PHONE]','','class=cell_medium').'</TD></tr>';
					echo '<tr><TD>Mobile Phone</td><td>:</td><td> '.TextInput($o_addr[1]['CELL_PHONE'],'values[people][OTHER][CELL_PHONE]','','class=cell_medium').'</TD></tr>';
					if($o_addr[1]['PERSON_ID']=='')
                                        echo '<tr><TD><span class=red>*</span>Email </td><td>:</td><td><table><tr><td>'.TextInput($o_addr[1]['EMAIL'],'values[people][OTHER][EMAIL]','','autocomplete=off class=cell_medium onkeyup=peoplecheck_email(this,2,0) ').'</td><td> <span id="email_2"></span></td></tr></table></TD></tr>';
                                        else
                                        echo '<tr><TD><span class=red>*</span>Email </td><td>:</td><td><table><tr><td>'.TextInput($o_addr[1]['EMAIL'],'values[people][OTHER][EMAIL]','','autocomplete=off class=cell_medium onkeyup=peoplecheck_email(this,2,'.$o_addr[1]['PERSON_ID'].') ').'</td><td> <span id="email_2"></span></td></tr></table></TD></tr>';    
					echo '<TR><TD>Custody</TD><td>:</td><TD>'.CheckboxInputMod($o_addr[1]['CUSTODY'],'values[people][OTHER][CUSTODY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></TR>';
					if($o_addr[1]['USER_NAME']=='')
                                        {    
                                        $portal_check='';    
                                        $style='style="display:none"';
                                        }
                                        else
                                        {
                                        $portal_check='checked="checked"';
                                        $style='';
                                        }
                                        echo '<input type="hidden" id=oth_val_pass value="Y">';
                                        echo '<input type="hidden" id=oth_val_user value="Y">';
                                        echo '<input type="hidden" name=selected_oth_parent id=selected_oth_parent value="">';
                                        echo '<input type="hidden" id=val_email_2 name=val_email_2 value="Y">';
                                        if($portal_check=='')
                                        echo '<TR><TD>Portal User</TD><td>:</td><TD><input type="checkbox" name="other_portal" value="Y" id="portal_2" onClick="portal_toggle(2);" '.$portal_check.'/></TD></TR>';   
                                        else
                                            echo '<TR><TD>Portal User</TD><td>:</td><TD><div id=checked_2><IMG SRC=assets/check.gif width=15></div></TD></TR>';
                                        echo '<tr><td colspan=3><div id="portal_div_2" '.$style.'><TABLE>';
                                        if($o_addr[1]['USER_NAME']=='' && $o_addr[1]['PASSWORD']=='')
                                        {
                                            echo '<TR><TD>Username</TD><td>:</td><TD>'.TextInput($o_addr[1]['USER_NAME'],'values[people][OTHER][USER_NAME]','','id=primary_username class=cell_medium onkeyup="usercheck_init_mod(this,2)" ').'<div id="ajax_output_2"></div></TD></TR>';   
                                            echo '<TR><TD>Password</TD><td>:</td><TD>'.TextInput($o_addr[1]['PASSWORD'],'values[people][OTHER][PASSWORD]','','id=primary_password class=cell_medium onkeyup="passwordStrengthMod(this.value,2);validate_password_mod(this.value,2);"').'<span id="passwordStrength2"></span></TD></TR>';   
                                        }
                                        else
                                        {
                                            echo '<TR><TD>Username</TD><td>:</td><TD><div id=uname2>'.$o_addr[1]['USER_NAME'].'</div></TD></TR>';
                                            echo '<TR><TD>Password</TD><td>:</td><TD><div id=pwd2>'.str_repeat('*',strlen($o_addr[1]['PASSWORD'])).'</div></TD></TR>';
                                        }
                                        
                                        echo '<TR><TD>User Profile</TD><td>:</td><TD>'.SelectInput($o_addr[1]['PROFILE_ID'],'values[people][OTHER][PROFILE_ID]',(!$o_addr[1]['PROFILE']?'<FONT color=red>':'').''.(!$o_addr[1]['PROFILE']?'</FONT>':''),$parent_user_prof_options,FALSE,' id=oth_prof_id').'</TD></TR>';
                                        
                                        echo '</TABLE></td></tr></div>';

                                        echo '<tr><td colspan=3><div id="portal_hidden_div_2" ><TABLE>';
                                        echo '</TABLE></td></tr></div>';
                                        echo '<tr><td colspan=3 class=break></td></tr>';	
					echo '<tr><td style="width:120px">Address Line 1</td><td>:</td><TD><table cellspacing=0 cellpadding=0><tr><td>'.TextInput($o_addr[1]['ADDRESS'],'values[student_address][OTHER][STREET_ADDRESS_1]','','class=cell_medium').'</TD><td>';
					if($o_addr[1]['ADDRESS_ID']!='' && $o_addr[1]['ADDRESS_ID']!='0')
					{
						$display_address = urlencode($o_addr[1]['ADDRESS'].', '.($o_addr[1]['CITY']?' '.$o_addr[1]['CITY'].', ':'').$o_addr[1]['STATE'].($o_addr[1]['ZIPCODE']?' '.$o_addr[1]['ZIPCODE']:''));
						$link = 'http://google.com/maps?q='.$display_address;
						echo '&nbsp;<A class=red HREF=# onclick=\'window.open("'.$link.'","","scrollbars=yes,resizable=yes,width=800,height=700");\'>Map it</A>';
					}
					echo '</td></tr></table></td></tr>';
					echo '<TR><td>Address Line 2</td><td>:</td><TD>'.TextInput($o_addr[1]['STREET'],'values[student_address][OTHER][STREET_ADDRESS_2]','','class=cell_medium').'</TD></tr>';
					echo '<TR><td>City</td><td>:</td><TD>'.TextInput($o_addr[1]['CITY'],'values[student_address][OTHER][CITY]','','class=cell_medium').'</TD></tr>';
					echo '<TR><td>State</td><td>:</td><TD>'.TextInput($o_addr[1]['STATE'],'values[student_address][OTHER][STATE]','','class=cell_medium').'</TD></tr>';
					echo '<TR><td>Zip/Postal Code</td><td>:</td><TD>'.TextInput($o_addr[1]['ZIPCODE'],'values[student_address][OTHER][ZIPCODE]','','class=cell_medium').'</TD></tr>';	
					echo '<TR><TD>School Bus Pick-up</TD><td>:</td><TD>'.CheckboxInputMod($o_addr[1]['BUS_PICKUP'],'values[student_address][OTHER][BUS_PICKUP]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></tr>';
					echo '<TR><TD>School Bus Drop-off</TD><td>:</td><TD>'.CheckboxInputMod($o_addr[1]['BUS_DROPOFF'],'values[student_address][OTHER][BUS_DROPOFF]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></tr>';
					echo '<TR><TD>Bus No</td><td>:</td><TD>'.TextInput($o_addr[1]['BUS_NO'],'values[student_address][OTHER][BUS_NO]','','class=cell_small').'</TD></tr>';
					echo '</table>';

					echo '</TABLE>';

					

				}
				else
				{
                                     $extra="id="."'values[people][OTHER][RELATIONSHIP]'";
					echo '<TABLE border=0><TR><TD colspan=3><table><tr><td><input type=hidden name=values[people][OTHER][IS_EMERGENCY_HIDDEN] id=IS_EMERGENCY_HIDDEN value="Y">'.CheckboxInputMod($o_addr[1]['IS_EMERGENCY'],'values[people][OTHER][IS_EMERGENCY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD><TD>This is an Emergency Contact</TD></TR></table></TD></TR><tr><td colspan=3 class=break></td></tr>';	
                                         if(User('PROFILE')!='teacher')
                                             
                                        echo '<TR><td style="width:120px" style=white-space:nowrap><span class=red>*</span>Relationship to Student</td><td>:</td><TD><table><tr><td>'.SelectInput($o_addr[1]['RELATIONSHIP'],'values[people][OTHER][RELATIONSHIP]','',$relation_options,'N/A',$extra).'</td><td><input type="button" name="lookup" value="Lookup" onclick="javascript:window.open(\'ForWindow.php?modname='.$_REQUEST['modname'].'&modfunc=lookup&type=other&ajax='.$_REQUEST['ajax'].'&add_id=new&address_id='.$_REQUEST['address_id'].'\',\'blank\',\'resizable=yes,scrollbars=yes,width=600,height=400\');return false;"></td></tr></table></TD></TR>';
                                        
                                            echo '<TR><TD><span class=red>*</span>First Name</td><td>:</td><TD>'.str_replace('"','\"',_makePeopleInput($o_addr[1]['FIRST_NAME'],'people','FIRST_NAME','OTHER','','id=oth_fname class=cell_medium')).'</TD></tr><tr><td ><span class=red>*</span>Last Name</td><td>:</td><TD>'.str_replace('"','\"',_makePeopleInput($o_addr[1]['LAST_NAME'],'people','LAST_NAME','OTHER','','id=oth_lname class=cell_medium')).'</TD></TR>';
                                            echo '<tr><TD>Home Phone</td><td>:</td><td> '.TextInput($o_addr[1]['HOME_PHONE'],'values[people][OTHER][HOME_PHONE]','','id=oth_hphone class=cell_medium').'</TD></tr>';
                                            echo '<tr><TD>Work Phone</td><td>:</td><td>'.TextInput($o_addr[1]['WORK_PHONE'],'values[people][OTHER][WORK_PHONE]','','id=oth_wphone class=cell_medium').'</TD></tr>';
                                            echo '<tr><TD>Mobile Phone</td><td>:</td><td> '.TextInput($o_addr[1]['CELL_PHONE'],'values[people][OTHER][CELL_PHONE]','','id=oth_cphone class=cell_medium').'</TD></tr>';
                                            if($o_addr[1]['PERSON_ID']=='')
                                            echo '<tr><TD><span class=red>*</span>Email </td><td>:</td><td><table><tr><td>'.TextInput($o_addr[1]['EMAIL'],'values[people][OTHER][EMAIL]','','autocomplete=off id=oth_email class=cell_medium onkeyup=peoplecheck_email(this,2,0); ').'</td><td><span id="email_2"></span></td></tr></table></TD></tr>';
                                            else
                                            echo '<tr><TD><span class=red>*</span>Email </td><td>:</td><td><table><tr><td>'.TextInput($o_addr[1]['EMAIL'],'values[people][OTHER][EMAIL]','','autocomplete=off id=oth_email class=cell_medium onkeyup=peoplecheck_email(this,2,'.$o_addr[1]['PERSON_ID'].') ').'</td><td><span id="email_2"></span></td></tr></table></TD></tr>';    
                                            echo '<TR><TD>Custody of Student</td><td>:</td><TD>'.CheckboxInputMod($o_addr[1]['CUSTODY'],'values[people][OTHER][CUSTODY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'<small><FONT color='.Preferences('TITLES').'></FONT></small></TD></TR>';
                                            if($o_addr[1]['USER_NAME']=='')
                                            {    
                                            $portal_check='';    
                                            $style='style="display:none"';
                                            }
                                            else
                                            {
                                            $portal_check='checked="checked"';
                                            $style='';
                                            }
                                            echo '<input type="hidden" id=oth_val_pass value="Y">';
                                            echo '<input type="hidden" id=oth_val_user value="Y">';
                                            echo '<input type="hidden" id=val_email_2 name=val_email_2 value="Y">';
                                            if($portal_check=='')
                                            echo '<TR><TD>Portal User</TD><td>:</td><TD><input type="checkbox" name="other_portal" value="Y" id="portal_2" onClick="portal_toggle(2);" '.$portal_check.'/></TD></TR>';   
                                            else
                                                echo '<TR><TD>Portal User</TD><td>:</td><TD><IMG SRC=assets/check.gif width=15></TD></TR>';
                                            echo '<tr><td colspan=3><div id="portal_div_2" '.$style.'><TABLE>';
                                            if($o_addr[1]['USER_NAME']=='' && $o_addr[1]['PASSWORD']=='')
                                            {
                                                echo '<TR><TD>Username</TD><td>:</td><TD>'.TextInput($o_addr[1]['USER_NAME'],'values[people][OTHER][USER_NAME]','','id=other_username class=cell_medium onkeyup="usercheck_init_mod(this,2)" ').'<div id="ajax_output_2"></div></TD></TR>';   
                                                echo '<TR><TD>Password</TD><td>:</td><TD>'.TextInput($o_addr[1]['PASSWORD'],'values[people][OTHER][PASSWORD]','','id=other_password class=cell_medium onkeyup="passwordStrengthMod(this.value,2);validate_password_mod(this.value,2);"').'<span id="passwordStrength2"></span></TD></TR>';   
                                            }
                                            else
                                            {
                                                echo '<TR><TD>Username</TD><td>:</td><TD>'.$o_addr[1]['USER_NAME'].'</TD></TR>';
                                                echo '<TR><TD>Password</TD><td>:</td><TD>'.str_repeat('*',strlen($o_addr[1]['PASSWORD'])).'</TD></TR>';
                                            }
                                            
                                            echo '<TR><TD>User Profile</TD><td>:</td><TD>'.SelectInput($o_addr[1]['PROFILE_ID'],'values[people][OTHER][PROFILE_ID]',(!$o_addr[1]['PROFILE']?'<FONT color=red>':'').''.(!$o_addr[1]['PROFILE']?'</FONT>':''),$parent_user_prof_options,FALSE,' id=oth_prof_id').'</TD></TR>';
                                        
                                        
                                        echo '</TABLE></td></tr></div>';

                                        echo '<tr><td colspan=3><div id="portal_hidden_div_2" ><TABLE>';
                                        echo '</TABLE></td></tr></div>';
                                        echo '<TR><TD colspan=3><table><TR><TD style=white-space:nowrap><span class=red>*</span><input type="radio" id="ros" name="r7" value="Y" onClick="addn_hidediv();" checked>&nbsp;Same as Student\'s Home Address &nbsp;&nbsp; <input type="radio" id="ron" name="r7" value="N" onClick="addn_showdiv();">&nbsp;Add New Address</TD></TR></TABLE></TD></TR>';
					echo '<TR><TD colspan=3><div id="addn_hideShow" style="display:none">';
					echo '<div class=break></div>';
					echo '<table><TR><td style=width:120px>Address Line 1</td><td>:</td><TD>'.TextInput($o_addr[1]['ADDRESS'],'values[student_address][OTHER][STREET_ADDRESS_1]','','id=oth_address class=cell_medium').'</TD></td>';
					
					
					
					echo '<TR><td>Address Line 2</td><td>:</td><TD>'.TextInput($o_addr[1]['STREET'],'values[student_address][OTHER][STREET_ADDRESS_2]','','id=oth_street class=cell_medium').'</TD></tr>';
					echo '<TR><td>City</td><td>:</td><TD>'.TextInput($o_addr[1]['CITY'],'values[student_address][OTHER][CITY]','','id=oth_city class=cell_medium').'</TD></tr>';
					echo '<TR><td>State</td><td>:</td><TD>'.TextInput($o_addr[1]['STATE'],'values[student_address][OTHER][STATE]','','id=oth_state class=cell_medium').'</TD></tr>';
					echo '<TR><td>Zip/Postal Code</td><td>:</td><TD>'.TextInput($o_addr[1]['ZIPCODE'],'values[student_address][OTHER][ZIPCODE]','','id=oth_zip class=cell_medium').'</TD></tr>';
					echo '<TR><TD>School Bus Pick-up</TD><td>:</td><TD>'.CheckboxInputMod($o_addr[1]['BUS_PICKUP'],'values[student_address][OTHER][BUS_PICKUP]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>',false).'</TD></tr>';
					echo '<TR><TD>School Bus Drop-off</TD><td>:</td><TD>'.CheckboxInputMod($o_addr[1]['BUS_DROPOFF'],'values[student_address][OTHER][BUS_DROPOFF]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>',false).'</TD></tr>';
					echo '<TR><td>Bus No</TD><td>:</td><td>'.TextInput($o_addr[1]['BUS_NO'],'values[student_address][OTHER][BUS_NO]','','id=oth_busno class=cell_small').'</TD></tr>';
					echo '</table></div></td></tr></table>';
				}
				
				
			}


			
			if($_REQUEST['person_id']=='new') {
		echo '</TD></TR>';
		echo '</TABLE>'; // end of table 2
		}
		unset($_REQUEST['address_id']);
		unset($_REQUEST['person_id']);
		}
		
	echo '</TD></TR>';
	echo '</TABLE></td></tr></table>'; // end of table 1
	

function _makePeopleInput($value,$table,$column,$opt,$title='',$options='')
{	global $THIS_RET;

	if($column=='MIDDLE_NAME')
		$options = 'class=cell_medium';
	if($_REQUEST['person_id']=='new')
		$div = false;
	else
		$div = true;



	return TextInput($value,"values[$table][$opt][$column]",$title,$options,false);
}

function _makeAutoSelect($column,$table,$opt,$values='',$options=array())
{
        if($opt!='')
            $where=' WHERE EMERGENCY_TYPE=\''.$opt.'\' ';
        else
            $where='';
        
	$options_RET = DBGet(DBQuery('SELECT DISTINCT '.$column.',upper('.$column.') AS `KEY` FROM '.$table.' '.$where.' ORDER BY `KEY`'));

	// add the 'new' option, is also the separator
	$options['---'] = '---';
	// add values already in table
	if(count($options_RET))
		foreach($options_RET as $option)
			if($option[$column]!='' && !$options[$option[$column]])
				$options[$option[$column]] = array($option[$column],'<FONT color=blue>'.$option[$column].'</FONT>');
	// make sure values are in the list
	if(is_array($values))
	{
		foreach($values as $value)
			if($value[$column]!='' && !$options[$value[$column]])
				$options[$value[$column]] = array($value[$column],'<FONT color=blue>'.$value[$column].'</FONT>');
	}
	else
		if($values!='' && !$options[$values])
			$options[$values] = array($values,'<FONT color=blue>'.$values.'</FONT>');

	return $options;
}

function _makeAutoSelectInputX($value,$column,$table,$opt,$title,$select,$id='',$div=true)
{$extra='id='."values[$table][$opt]".($id?"[$id]":'')."[$column]";
 $extra="id="."values[$table][$opt]"."[$column]";	
    if($column=='CITY' || $column=='MAIL_CITY')
		$options = 'maxlength=60';
	if($column=='STATE' || $column=='MAIL_STATE')
		$options = 'size=3 maxlength=10';
	elseif($column=='ZIPCODE' || $column=='MAIL_ZIPCODE')
		$options = 'maxlength=10';
	else
		$options = 'maxlength=100';

	if($value!='---' && count($select)>1)
		return SelectInput($value,"values[$table][$opt]".($id?"[$id]":'')."[$column]",$title,$select,'N/A',$extra,$div);
	else
		return TextInput($value=='---'?array('---','<FONT color=red>---</FONT>'):$value,"values[$table][$opt]".($id?"[$id]":'')."[$column]",$title,$options,$div);
}
?>