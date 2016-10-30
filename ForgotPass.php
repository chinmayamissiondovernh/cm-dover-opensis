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
include("functions/ParamLibFnc.php");
include("Data.php");
include("functions/DbGetFnc.php");
include("functions/MonthNwSwitchFnc.php");
include("functions/ProperDateFnc.php");
function DateInputAY($value,$name,$counter=1)
{

                $show="";
                $date_sep="";
                $monthVal="";
                $yearVal="";
                $dayVal="";   
                $display="";
                
                
                
                if($value!='')
                    return  '<table><tr><td><div id="date_div_'.$counter.'" style="display: inline" >'.ProperDateAY($value).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td><td><input type=text id="date_'.$counter.'" '.$show.'  style="display:none" readonly></td><td><a onClick="init('.$counter.',2);"><img src="assets/calendar.gif"  /></a></td><td><input type=hidden '.$monthVal.' id="monthSelect'.$counter.'" name="month_'.$name.'" ><input type=hidden '.$dayVal.'  id="daySelect'.$counter.'"   name="day_'.$name.'"><input type=hidden '.$yearVal.'  id="yearSelect'.$counter.'" name="year_'.$name.'" ></td></tr></table>';
                else
                {
                    if($counter==2)
                        return  '<input type=text id="date_'.$counter.'"  class="login_txt" disabled=disabled readonly>&nbsp;<a onClick="init('.$counter.',1);"><img src="assets/calendar.gif"  /></a><input type=hidden '.$monthVal.' id="monthSelect'.$counter.'" name="month_'.$name.'" disabled=disabled><input type=hidden '.$dayVal.'  id="daySelect'.$counter.'"   name="day_'.$name.'" disabled=disabled><input type=hidden '.$yearVal.'  id="yearSelect'.$counter.'" name="year_'.$name.'" disabled=disabled>'; 
                    else
                        return  '<input type=text id="date_'.$counter.'"  class="login_txt" readonly>&nbsp;<a onClick="init('.$counter.',1);"><img src="assets/calendar.gif"  /></a><input type=hidden '.$monthVal.' id="monthSelect'.$counter.'" name="month_'.$name.'" ><input type=hidden '.$dayVal.'  id="daySelect'.$counter.'"   name="day_'.$name.'"><input type=hidden '.$yearVal.'  id="yearSelect'.$counter.'" name="year_'.$name.'" >'; 
                }
	}


function db_start()
{	global $DatabaseServer,$DatabaseUsername,$DatabasePassword,$DatabaseName,$DatabasePort,$DatabaseType;

	switch($DatabaseType)
	{
		case 'mysqli':
			$connection = new mysqli($DatabaseServer,$DatabaseUsername,$DatabasePassword,$DatabaseName);
                break;
	}

	// Error code for both.
	if($connection === false)
	{
		switch($DatabaseType)
		{
			case 'mysqli':
				$errormessage = mysqli_error($connection);
			break;
		}
		db_show_error("","Could not Connect to Database: $DatabaseServer",$errstring);
	}
	return $connection;
}

// This function connects, and does the passed query, then returns a connection identifier.
// Not receiving the return == unusable search.
//		ie, $processable_results = DBQuery("select * from students");
function DBQuery($sql)
{	global $DatabaseType,$_openSIS;

	$connection = db_start();

	switch($DatabaseType)
	{
		case 'mysqli':
			$sql = str_replace('&amp;', "", $sql);
			$sql = str_replace('&quot', "", $sql);
			$sql = str_replace('&#039;', "", $sql);
			$sql = str_replace('&lt;', "", $sql);
			$sql = str_replace('&gt;', "", $sql);
		  	$sql = ereg_replace("([,\(=])[\r\n\t ]*''",'\\1NULL',$sql);
			if(preg_match_all("/'(\d\d-[A-Za-z]{3}-\d{2,4})'/",$sql,$matches))
				{
					foreach($matches[1] as $match)
					{
						$dt = date('Y-m-d',strtotime($match));
						$sql = preg_replace("/'$match'/","'$dt'",$sql);
					}
				}
			if(substr($sql,0,6)=="BEGIN;")
			{
				$array = explode( ";", $sql );
				foreach( $array as $value )
				{
					if($value!="")
					{
                                            $result = $connection->query($value);
						if(!$result)
						{
                                                    $connection->query("ROLLBACK");
							die(db_show_error($sql,"DB Execute Failed.",mysql_error()));
						}
					}
				}
			}
			else
			{
				$result = $connection->query($sql) or die(db_show_error($sql,"DB Execute Failed.",mysql_error()));
			}
		break;
	}
	return $result;
}

// return next row.
function db_fetch_row($result)
{	global $DatabaseType;

	switch($DatabaseType)
	{
		case 'mysqli':
			$return = $result->fetch_assoc();
			if(is_array($return))
			{
				foreach($return as $key => $value)
				{
					if(is_int($key))
						unset($return[$key]);
				}
			}
		break;
	}
	return @array_change_key_case($return,CASE_UPPER);
}

// returns code to go into SQL statement for accessing the next value of a sequence function db_seq_nextval($seqname)
function db_seq_nextval($seqname)
{	global $DatabaseType;

	if($DatabaseType=='mysqli')
		$seq="fn_".strtolower($seqname)."()";
		
	return $seq;
}

function db_case($array)
{	global $DatabaseType;

	$counter=0;
	if($DatabaseType=='mysqli')
	{
		$array_count=count($array);
		$string = " CASE WHEN $array[0] =";
		$counter++;
		$arr_count = count($array);
		for($i=1;$i<$arr_count;$i++)
		{
			$value = $array[$i];

			if($value=="''" && substr($string,-1)=='=')
			{
				$value = ' IS NULL';
				$string = substr($string,0,-1);
			}

			$string.="$value";
			if($counter==($array_count-2) && $array_count%2==0)
				$string.=" ELSE ";
			elseif($counter==($array_count-1))
				$string.=" END ";
			elseif($counter%2==0)
				$string.=" WHEN $array[0]=";
			elseif($counter%2==1)
				$string.=" THEN ";

			$counter++;
		}
	}
	return $string;
}

function db_properties($table)
{	global $DatabaseType,$DatabaseUsername;

	switch($DatabaseType)
	{
		case 'mysqli':
			$result = DBQuery("SHOW COLUMNS FROM $table");
			while($row = db_fetch_row($result))
			{
				$properties[strtoupper($row['FIELD'])]['TYPE'] = strtoupper($row['TYPE'],strpos($row['TYPE'],'('));
				if(!$pos = strpos($row['TYPE'],','))
					$pos = strpos($row['TYPE'],')');
				else
					$properties[strtoupper($row['FIELD'])]['SCALE'] = substr($row['TYPE'],$pos+1);

				$properties[strtoupper($row['FIELD'])]['SIZE'] = substr($row['TYPE'],strpos($row['TYPE'],'(')+1,$pos);

				if($row['NULL']!='')
					$properties[strtoupper($row['FIELD'])]['NULL'] = "Y";
				else
					$properties[strtoupper($row['FIELD'])]['NULL'] = "N";
			}
		break;
	}
	return $properties;
}

function db_show_error($sql,$failnote,$additional='')
{	global $openSISTitle,$openSISVersion,$openSISNotifyAddress,$openSISMode;

	$tb = debug_backtrace();
	$error = $tb[1]['file'] . " at " . $tb[1]['line'];
        
            echo "
                    <TABLE CELLSPACING=10 BORDER=0>
                            <TD align=right><b>Date:</TD>
                            <TD><pre>".date("m/d/Y h:i:s")."</pre></TD>
                    </TR><TR>
                            <TD align=right><b>Failure Notice:</b></TD>
                            <TD><pre> $failnote </pre></TD>
                    </TR><TR>
                            <TD align=right><b>SQL:</b></TD>
                            <TD>$sql</TD>
                    </TR>
                    </TR><TR>
                            <TD align=right><b>Traceback:</b></TD>
                            <TD>$error</TD>
                    </TR>
                    </TR><TR>
                            <TD align=right><b>Additional Information:</b></TD>
                            <TD>$additional</TD>
                    </TR>
                    </TABLE>";
       
		echo "
		<TABLE CELLSPACING=10 BORDER=0>
			<TR><TD align=right><b>Date:</TD>
			<TD><pre>".date("m/d/Y h:i:s")."</pre></TD>
		</TR><TR>
			<TD align=right></TD>
			<TD>openSIS has encountered an error that could have resulted from any of the following:
			<br/>
			<ul>
			<li>Invalid data input</li>
			<li>Database SQL error</li>
			<li>Program error</li>
			</ul>
			
			Please take this screen shot and send it to your openSIS representative for debugging and resolution.
			</TD>
		</TR>
		
		</TABLE>";
        
	echo "<!-- SQL STATEMENT: \n\n $sql \n\n -->";

	if($openSISNotifyAddress)
	{
		$message = "System: $openSISTitle \n";
		$message .= "Date: ".date("m/d/Y h:i:s")."\n";
		$message .= "Page: ".$_SERVER['PHP_SELF'].' '.ProgramTitle()." \n\n";
		$message .= "Failure Notice:  $failnote \n";
		$message .= "Additional Info: $additional \n";
		$message .= "\n $sql \n";
		$message .= "Request Array: \n".ShowVar($_REQUEST,'Y', 'N');
		$message .= "\n\nSession Array: \n".ShowVar($_SESSION,'Y', 'N');
		mail($openSISNotifyAddress,'openSIS Database Error',$message);

	}

	die();
}
$log_msg=  DBGet(DBQuery("SELECT MESSAGE FROM login_message WHERE DISPLAY='Y'"));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>openSIS Student Information System</title>
<link rel="shortcut icon" href="favicon.ico">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="styles/Help.css" />
<link rel="stylesheet" type="text/css" href="styles/Calendar.css" />
<link rel="stylesheet" type="text/css" href="styles/Login.css"/>
<link rel="stylesheet" type="text/css" href="styles/CalendarMod.css"/>
<script src='js/Ajaxload.js'></script>
<script src='js/Validation.js'></script>
<script src='js/Validator.js'></script>
<script src='js/ForgotPass.js'></script>
<script src='js/CalendarModForgotPass.js'></script>
<script type='text/javascript'>
    
function init(param,param2) {
        
        calendar.set('date_'+param);    
        document.getElementById('date_'+param).click();
        
        }
		
</script>
</head>
<body>
<form name="f1" id="f1" method="post" action="ResetUserInfo.php">
<table width='100%' height='550px' border='0' cellspacing='0' cellpadding='0'>
  <tr>
    <td valign='middle' height='100%'>
    <table class='wrapper' border='0' cellspacing='0' cellpadding='0' align='center'>
        <tr>
          <td class='header'>
          <table width='100%' border='0' cellspacing='0' cellpadding='0' class='logo_padding'>
              
                <tr>
                <td><img src='assets/osis_logo.png' border='0' /></td>
                <td align='right'><a href='http://www.os4ed.com' target=_blank ><img src='assets/os4ed_logo.png' height='62' width='66' border='0'/></a></td>
              
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td class='content'>
          <table width='100%' border='0' cellspacing='0' cellpadding='0'>
              <tr>
                <td>
                <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                    <tr>
                      <td class='header_padding'>
                      <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                          <tr>
                            <td class='header_txt' id="header_text">Forgot Password</td>
                          </tr>
                        </table>
                        </td>
                    </tr><tr>
                      <td class='padding'>
                      <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                          <tr>
                            <tr>
                            <td>
                                <a href="index.php" style="text-decoration:none;color:black;font-weight:bold">&lsaquo;&lsaquo; Back To Login Screen</a>
                            </td>
                            </tr>
                              <tr><td align="center"><input type="hidden" id="valid_func" value="N"/><div id="divErr"><?php if($_SESSION['err_msg']!='') echo $_SESSION['err_msg']; unset($_SESSION['err_msg']);?></div></td></tr>
                            <td>
                                <table class="retrieval_tab" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td><a id="password_retrieval_link" class="tab1 active" onclick="tab_select('password_retrieval')" href="javascript:void(0)">Reset Password</a></td>
                                        <td><a id="username_retrieval_link" class="tab2" onclick="tab_select('username_retrieval')" href="javascript:void(0)">Username Retrieval</a></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                        <table border='0' width='100%' cellspacing='2' cellpadding='2' id="password_retrieval" align=center>
                                    <tr>
                                        <td colspan=2 align="center">
                                        </td>
                                    </tr>
                                     <tr>
                                         <td align="right">I am a :</td><td><input type="hidden" name="pass_type_form" id="pass_type_form" value="password"/><input type="radio" name="pass_user_type" id="pass_student" value="pass_student" checked="checked" onclick="show_fields('student'); forgotpassusername_init(this.value);" /><label>Student</label> &nbsp; <input type="radio" name="pass_user_type" id="pass_staff" value="pass_staff" onclick="show_fields('staff'); forgotpassusername_init(this.value);forgotpassemail_init('pass_email');" /><label>Staff</label> &nbsp; <input type="radio" name="pass_user_type" id="pass_parent" value="pass_parent" onclick="show_fields('parent'); forgotpassusername_init(this.value);forgotpassemail_init('pass_email');" /><label>Parent</label></td>
                                     </tr>
                                     <tr id="pass_stu_id">
                                       <td width="30%" align="right">Student ID :</td>
                                       <td width="70%"><input type="text" name="password_stn_id" id="password_stn_id" class="login_txt" onkeydown="return numberOnly(event);" onblur="return check_input_val(this.value,'password_stn_id');"/></td>
                                     </tr>
                                     <tr>
                                       <td width="30%" align="right">Username :</td>
                                       <td width="70%"><input type="text" name="uname" id="uname" class="login_txt" onkeydown=" return withoutspace_forgotpass(event);" onblur="return withoutspace_forgotpass(event);"/><span style="display: none" id="calculating"><img src="assets/ajax_loader.gif"/></span><span id="err_msg"></span></td>
                                     </tr>
                                     <tr id="pass_stu_dob">
                                       <td width="30%" align="right">Date of Birth :</td>
                                       <td width="70%"><?php echo DateInputAY('', 'password_dob',1)?></td>
                                     </tr> 
                                    <tr id="pass_stf_email" style="display: none">
                                       <td width="30%" align="right">Email Address :<input type="hidden" name="pass_email" id="pass_email" value=""/></td>
                                       <td width="70%"><input type="text" name="password_stf_email" id="password_stf_email"  class="login_txt" onblur="forgotpassemail_init('pass_email');" /><span style="display: none" id="pass_calculating_email"><img src="assets/ajax_loader.gif"/></span><span id="pass_err_msg_email"></span></td>
                                     </tr>
                                    <tr>
                                       <td></td>
                                       <td><input type="submit"  name="save" class="btn" value="Confirm" onClick="return forgotpass();"/>
                                       </td>
                                     </tr>
                                     <tr>
                                     <td align='center'></td>
                                     <td></td>
                                       </tr>
				  </table>
                                            <table border='0' width='100%' cellspacing='2' cellpadding='2' id="username_retrieval" style="display: none;" align=center>
                                    <tr>
                                        <td colspan=2 align="center">
                                            
                                        </td>
                                    </tr>
                                     <tr>
                                         <td align="right">I am a :</td><td><input type="hidden" name="user_type_form" id="user_type_form" value="username" disabled="disabled"/><input type="radio" name="uname_user_type" id="uname_student" value="uname_student" checked="checked" disabled="disabled" onclick="uname_show_fields('student')" /><label>Student</label> &nbsp; <input type="radio" name="uname_user_type" id="uname_staff" value="uname_staff" disabled="disabled" onclick="uname_show_fields('staff');forgotpassemail_init('uname_email');" /><label>Staff</label> &nbsp; <input type="radio" name="uname_user_type" id="uname_parent" disabled="disabled" value="uname_parent" onclick="uname_show_fields('parent');forgotpassemail_init('uname_email');" /><label>Parent</label></td>
                                     </tr>
                                     <tr id="uname_stu_id">
                                       <td width="30%" align="right">Student ID :</td>
                                       <td width="70%"><input type="text" name="username_stn_id" id="username_stn_id" disabled="disabled" class="login_txt" onblur="return check_input_val(this.value,'username_stn_id');" onkeydown="return numberOnly(event);"/></td>
                                     </tr>
                                     <tr>
                                       <td width="30%" align="right">Password :</td>
                                       <td width="70%"><input type="password" name="pass" id="pass" class="login_txt" disabled="disabled"/></td>
                                     </tr>
                                     <tr id="uname_stu_dob">
                                       <td width="30%" align="right">Date of Birth :</td>
                                       <td width="70%"><?php echo DateInputAY('', 'username_dob',2)?></td>
                                     </tr>
                                    <tr id="uname_stf_email" style="display: none">
                                       <td width="30%" align="right">Email Address :<input type="hidden" name="un_email" id="un_email" value=""/></td>
                                       <td width="70%"><input type="text" name="username_stf_email" id="username_stf_email" disabled="disabled" class="login_txt" onblur="forgotpassemail_init('uname_email');" /><span style="display: none" id="uname_calculating_email"><img src="assets/ajax_loader.gif"/></span><span id="uname_err_msg_email"></span></td>
                                     </tr>
                                    <tr>
                                       <td></td>
                                       <td><input type="submit" name="save" class="btn" value="Confirm" onClick="return forgotusername();"/>
                                       </td>
                                     </tr>
                                     <tr>
                                     <td align='center'></td>
                                     <td></td>
                                       </tr>
				  </table>    
                                        </td>
                                    </tr>
                                </table>
				  </td>
                          </tr>
                          <tr>
                            <td align='center'><p style='padding:6px;'><?php echo $log_msg[1]['MESSAGE'];?></p></td>
                          </tr>
                        </table></td>
                    </tr>
                  </table>
              </tr>
            </table>
            </td>
        <tr>
          <td class='footer' valign='top'>
          <table width='100%' border='0' cellspacing='0' cellpadding='0'>
              <tr>
                <td class='margin'></td>
              </tr>
              <tr>
                <td align='center' class='copyright'>
                openSIS is a product of Open Solutions for Education, Inc. (<a href='http://www.os4ed.com' target='_blank'>OS4Ed</a>).
                and is licensed under the <a href='http://www.gnu.org/licenses/gpl.html' target='_blank'>GPL License</a>.
                </td>
              </tr>
            </table></td>
        </tr>
      </table>
      </td>
  		</tr>
	</table>
</form>
</body>
</html>

