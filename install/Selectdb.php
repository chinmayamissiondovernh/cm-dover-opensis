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
		$conn_string = $_SESSION['conn'];
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link rel="stylesheet" href="../styles/Installer.css" type="text/css" />
</head>
<body>
<div class="heading">Thanks for providing MySQL Connection Information

<div style="background-image:url(images/step2.gif); background-repeat:no-repeat; background-position:50% 20px; height:270px;">
  <form name='selectdb' id='selectdb' method='post' action="UpgradeProcessingMsg.php">
    <table border="0" cellspacing="6" cellpadding="3" align="center">
      <tr>
        <td  align="center" style="padding-top:36px; padding-bottom:16px">Step 2 of 4</td>
      </tr>
      <tr>
        <td align="center" valign="top"><strong>Please select the Database from 
		the list that you want to upgrade from.</strong></td>
      </tr>
      <tr>
        <td align="center" valign="top">
        
		<?php
		$connection =new mysqli($_SESSION['host_mod'],$_SESSION['username'],$_SESSION['password']) or die() ;
             
                if($connection->connect_errno>0)
    die('Not connected');
                $sql="show databases;" ;
		$res = $connection->query($sql);
		echo "<select name='sdb' id='sdb'>";
		while ($row = $res->fetch_row()) 
		{
            if ($row[0] != 'information_schema' && $row[0] != 'mysql' && $row[0] != 'performance_schema' && $row[0] != 'phpmyadmin')
                echo "<option>".$row[0]."</option>";
 		}
		echo "</select>";
        ?>        </td>
      </tr>
      <tr>
        <td align="center" valign="bottom" height="100px"><input type="submit" value="Save & Next" class=btn_wide name="Add_DB"  /></td>
      </tr>
    </table>
  </form>
  
</div>
</div>
</body>
</html>
