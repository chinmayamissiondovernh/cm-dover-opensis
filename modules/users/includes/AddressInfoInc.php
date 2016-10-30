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
echo '<TABLE width=100% border=0 cellpadding=6>';
echo '<TR>';
$_SESSION['staff_selected']=$staff['STAFF_ID'];
$addr=DBGet(DBQuery('SELECT STREET_ADDRESS_1 as ADDRESS,STREET_ADDRESS_2 as STREET,CITY,STATE,ZIPCODE FROM student_address WHERE PEOPLE_ID='.$_SESSION['staff_selected']));
$addr=$addr[1];
echo '<TD colspan=2>';

echo '<TABLE width=100%  cellpadding=5 >';
echo '<TR><td valign="top">';
echo '<TABLE border=0>';
echo '<tr><td style=width:100px><span class=red>*</span>Address</td><td>:</td><td>';
echo TextInput($addr['ADDRESS'],'student_addres[STREET_ADDRESS_1]','','size=25 maxlength=100 id=email class=cell_floating');
echo'</td></tr>';
echo '<tr><td>Street</td><td>:</td><td>';
echo TextInput($addr['STREET'],'student_addres[STREET_ADDRESS_2]','','size=25 maxlength=100 id=email class=cell_floating');

echo '</TD></tr>';
echo '<tr><td><span class=red>*</span>City</td><td>:</td><td><table cellpadding=0 cellspacing=0><tr><td>';
echo TextInput($addr['CITY'],'student_addres[CITY]','','size=25 maxlength=100 id=email class=cell_floating');
echo '</td></tr></table></TD>';
echo '<TR><TD>';
echo '<span class=red>*</span>State</TD><TD>:</TD><TD>';
echo TextInput($addr['STATE'],'student_addres[STATE]','','size=25 maxlength=100 id=email class=cell_floating');
echo '</TD></TR>';
echo '<TR><TD>';
echo '<span class=red>*</span>Zip Code</TD><TD>:</TD><TD>';
echo TextInput($addr['ZIPCODE'],'student_addres[ZIPCODE]','','size=25 maxlength=100 id=email class=cell_floating');
echo '</TD></TR>';
echo '</TR>';

echo'</table></td>';


echo '</TR>';

echo '</TABLE>';
echo '</TD></TR></TABLE>';
echo '<TABLE border=0 cellpadding=6 width=100%>';

echo '</TD></TR>';
echo '</TABLE>';

$_REQUEST['category_id'] = 3;






?>
