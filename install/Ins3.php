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
include '../functions/ParamLibFnc.php';

session_start();
$dbconn = mysql_connect($_SESSION['host'],$_SESSION['username'],$_SESSION['password']);
$result = mysql_select_db($_SESSION['db']);
        if(!$result)
        {
            echo "<h2>" . mysql_error() . "</h2>\n";
            exit;
        }




if(isset($_REQUEST['sname']) && $_REQUEST['sname']!='')
    $_REQUEST['sname']=  str_replace ("'","''", $_REQUEST['sname']);

if(clean_param($_REQUEST['sname'],PARAM_NOTAGS) && clean_param($_REQUEST['sample_data'],PARAM_ALPHA)){
     $_SESSION['sname']=clean_param($_REQUEST['sname'],PARAM_NOTAGS);
      $beg_date=str_replace("/","-", $_REQUEST['beg_date']);
    $end_date=str_replace("/","-", $_REQUEST['end_date']);
     $school_beg_date=explode("-",$beg_date);
    $school_end_date=explode("-", $end_date);
    $_SESSION['user_school_beg_date']=$school_beg_date[2].'-'.$school_beg_date[0].
    '-'.$school_beg_date[1];
    $_SESSION['user_school_end_date']=$school_end_date[2].'-'.$school_end_date[0].
    '-'.$school_end_date[1];
  
    $_SESSION['syear'] = $school_beg_date[2];
  include('SqlForClientSchoolAndSampleDataInc.php');
	$_SESSION['school_installed']='both';

    

}
else if(clean_param($_REQUEST['sname'],PARAM_NOTAGS)){
	$_SESSION['sname']=clean_param($_REQUEST['sname'],PARAM_NOTAGS);
    $beg_date=str_replace("/","-", $_REQUEST['beg_date']);
    $end_date=str_replace("/","-", $_REQUEST['end_date']);
    $school_beg_date=explode("-",$beg_date);
    $school_end_date=explode("-",$end_date);
    $_SESSION['user_school_beg_date']=$school_beg_date[2].'-'.$school_beg_date[0].
    '-'.$school_beg_date[1];
    $_SESSION['user_school_end_date']=$school_end_date[2].'-'.$school_end_date[0].
    '-'.$school_end_date[1];
    $_SESSION['syear']=$school_beg_date[2];
    $_SESSION['nextyear'] = $school_beg_date[2]+1;
   
    include('SqlForClientSchoolInc.php');
  $_SESSION['school_installed']='user';

}
else if(clean_param($_REQUEST['sample_data'],PARAM_ALPHA)){
     include('SqlSampleDataInc.php');
$_SESSION['school_installed']='sample';

}

$myFile = "OpensisTriggerMysqlInc.sql";
executeSQL($myFile);
mysql_close($dbconn);

echo '<script type="text/javascript">window.location = "Step4.php"</script>';


function executeSQL($myFile)
{
    $sql = file_get_contents($myFile);
    $sqllines = split("\n",$sql);
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
                    $result = mysql_query($cmd) or die(mysql_error());
                    $cmd = '';
                }
            }
        }
    }
}
// edited installation

?>
