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
echo '<script type="text/javascript">
var page=parent.location.href.replace(/.*\//,"");
if(page && page!="index.php"){
	window.location.href="index.php";
	}

</script>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Untitled Document</title>
        <link rel="stylesheet" href="../styles/Installer.css" type="text/css" />
        <script type="text/javascript" src="js/Validator.js"></script>

        <script type="text/javascript" src="js/Datetimepicker.js"></script>
        <script type="text/javascript" src="js/Prototype.js"></script>

    </head>
    <body>
        <div class="heading">Database created
            <div style="background-image:url(images/step3_new.gif); background-repeat:no-repeat; background-position:50% 15px; height:270px;">
                <form name='step3' id='step3' method="post" action="Ins3.php">
                    <table border="0" cellspacing="2" cellpadding="3" align="center">
                        <tr>
                            <td  align="center" style="padding-top:35px; padding-bottom:10px">Step 3 of 5</td>
                        </tr>
                        <tr>
                            <td align="center" valign="top" style="padding-top: 10px;">
                                <table width="400" border="0" cellpadding="0" align="center" cellspacing="0" id="table1">
                                    <tr>
                                        <td>
                                            <div>Please enter your School Name, Beginning and Ending dates of the school year.</div>
                                            <div style="height:6px;"></div>
                                            <div>
                                                <table width="100%" border="0" cellspacing="2" cellpadding="0" align="center">
                                                    <tr align="left">
                                                        <td colspan="3" align="center" valign="top"><div id="error">&nbsp;</div></td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left">School Name </td><td> : </td><td><input type="text" name="sname" id="sname" size="30" value=""  /></td>
                                                    </tr>



                                                    <tr>
                                                        <td align="left">Begining Date (mm/dd/yyyy)</td><td> : </td><td> <input name="beg_date" id="beg_date" maxlength="25" size="10" type="Text" readonly />
                                                            <a href="javascript:NewCal('beg_date','mmddyyyy')"><img src="images/cal.gif" width="16" height="16" border="0" alt="Pick a date" /></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left">Ending Date (mm/dd/yyyy)</td><td> : </td><td> <input name="end_date" id="end_date" maxlength="25" size="10" type="Text" readonly />
                                                            <a href="javascript:NewCal('end_date','mmddyyyy')"><img src="images/cal.gif" width="16" height="16" border="0" alt="Pick a date" /></a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div style="height:6px;" align="center"></div>
                                            <div>
                                                <table width="100%" border="0" cellspacing="2" cellpadding="0" align="center">
                                                    <tr><td align="center" valign="top"><input type="checkbox" name="sample_data" id="sample_data" value="insert" id="sample_data"/><strong>Install with sample school data</strong></td></tr>
                                                </table>
                                            </div>
                                            <div style="height:6px;"></div>
                                            <table width="100%" border="0" cellspacing="2" cellpadding="0" align="center">
                                                <tr>
                                                    <td  align="center"><input type="submit" value="Save & Next" class=btn_wide name="btnsyear" onclick="return check();" /></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <script language="JavaScript" type="text/javascript">
                                    function validatedate(inputText)
                                    {
                                        var dateformat = /^(0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])[\/\-]\d{4}$/;

                                        if (inputText.match(dateformat))
                                        {

                                            var opera1 = inputText.split('/');
                                            var opera2 = inputText.split('-');
                                            lopera1 = opera1.length;
                                            lopera2 = opera2.length;
                                            // Extract the string into month, date and year  
                                            if (lopera1 > 1)
                                            {
                                                var pdate = inputText.split('/');
                                            }
                                            else if (lopera2 > 1)
                                            {
                                                var pdate = inputText.split('-');
                                            }
                                            var mm = parseInt(pdate[0]);
                                            var dd = parseInt(pdate[1]);
                                            var yy = parseInt(pdate[2]);
                                            // Create list of days of a month [assume there is no leap year by default]  
                                            var ListofDays = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
                                            if (mm == 1 || mm > 2)
                                            {
                                                if (dd > ListofDays[mm - 1])
                                                {

                                                    return false;
                                                }
                                            }
                                            if (mm == 2)
                                            {
                                                var lyear = false;
                                                if ((!(yy % 4) && yy % 100) || !(yy % 400))
                                                {
                                                    lyear = true;
                                                }
                                                if ((lyear == false) && (dd >= 29))
                                                {

                                                    return false;
                                                }
                                                if ((lyear == true) && (dd > 29))
                                                {

                                                    return false;
                                                }
                                            }
                                        }
                                        else
                                        {

                                            return false;
                                        }
                                    }
                                    function check()
                                    {
                                        var sample_data = document.getElementById('sample_data');
                                        var sname = document.getElementById("sname");
                                        var beg_date = document.getElementById("beg_date");
                                        var end_date = document.getElementById("end_date");
                                        if (sname.value != '' && beg_date.value != '')
                                        {



                                            if (sname.value == '')
                                            {
                                                document.getElementById("error").innerHTML = '<font style="color:red"><b>School name cannot be blank.</b></font>';

                                                sname.focus();
                                                return false;
                                            }
                                            else
                                            {
                                                if (sname.value.length > 50)
                                                {
                                                    document.getElementById("error").innerHTML = '<font style="color:red"><b>Maximum length of School name is 50</b></font>';

                                                    sname.focus();
                                                    return false;
                                                }
                                            }
                                            if (beg_date.value == '')
                                            {
                                                document.getElementById("error").innerHTML = '<font style="color:red"><b>Begining date cannot be blank.</b></font>';

                                                beg_date.focus();
                                                return false;
                                            }
                                            else
                                            {
                                                if (false == validatedate(beg_date.value))
                                                {
                                                    document.getElementById("error").innerHTML = '<font style="color:red"><b>Begining date format is wrong.</b></font>';

                                                    beg_date.focus();
                                                    return false;
                                                }
                                            }
                                            if (end_date.value == '')
                                            {
                                                document.getElementById("error").innerHTML = '<font style="color:red"><b>Ending date cannot be blank.</b></font>';

                                                end_date.focus();
                                                return false;
                                            }
                                            else
                                            {
                                                if (false == validatedate(end_date.value))
                                                {
                                                    document.getElementById("error").innerHTML = '<font style="color:red"><b>Ending date format is wrong.</b></font>';

                                                    beg_date.focus();
                                                    return false;
                                                }
                                            }
                                        }
                                        if (sample_data.checked == false && sname.value == '')
                                        {
                                            document.getElementById("error").innerHTML = '<font style="color:red"><b>Please Enter School name with Begining and Ending date or check sample data. </b></font>';
                                            sname.focus();
                                            return false;
                                        }

                                    }


                                    function blankValidation() {
                                        var school_name = $('sname');
                                        var beg_date = $('beg_date');
                                        var end_date = $('end_date');
                                        var sample_data = $('sample_data');


                                        var bd = beg_date.value.split("/");
                                        var ed = end_date.value.split("/");





                                        if ((school_name.value != '' && beg_date.value != '' && end_date.value != '') || sample_data.checked == true) {
                                            if (school_name.value != '' || beg_date.value != '' || end_date.value != '') {
                                                if (!(school_name.value != '' && beg_date.value != '' && end_date.value != '')) {
                                                    document.getElementById("error").innerHTML = '<font style="color:red"><b>Please provide required info.</b></font>';

                                                    return false;
                                                }

                                            }
                                            bd[0] = parseInt(bd[0]);
                                            bd[1] = parseInt(bd[1]);
                                            bd[2] = parseInt(bd[2]);
                                            ed[0] = parseInt(ed[0]);
                                            ed[1] = parseInt(ed[1]);
                                            ed[2] = parseInt(ed[2]);

                                            if (bd[2] > ed[2]) {
                                                document.getElementById("error").innerHTML = '<font style="color:red"><b>End date must be greater than begin date.</b></font>';

                                                return false;
                                            } else if (bd[2] < ed[2]) {
                                                return true;

                                            }
                                            else if (bd[2] == ed[2] && bd[0] > ed[0]) {
                                                document.getElementById("error").innerHTML = '<font style="color:red"><b>End date must be greater than begin date.</b></font>';

                                                return false;

                                            } else if (bd[0] < ed[0]) {

                                                return true;
                                            }
                                            else if (bd[0] == ed[0] && bd[1] > ed[1]) {
                                                document.getElementById("error").innerHTML = '<font style="color:red"><b>End date must be greater than begin date.</b></font>';

                                                return false;

                                            } else if (bd[1] <= ed[1]) {
                                                return true;
                                            }


                                            return true;
                                        }
                                        else
                                        {
                                            document.getElementById("error").innerHTML = '<font style="color:red"><b>Please provide required info.</b></font>';

                                            return false;
                                        }


                                    }

                                    var frmvalidator = new Validator("step3");

                                    frmvalidator.setAddnlValidationFunction("blankValidation");
                                </script>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </body>
</html>
