<?php
/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2011 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/



class Bug37953Test extends Sugar_PHPUnit_Framework_TestCase
{
    var $call;

    public function setUp()
    {
        global $current_user;
        $current_user = SugarTestUserUtilities::createAnonymousUser();
        $this->call = SugarTestCallUtilities::createCall();
        $this->useOutputBuffering = false;
	}

    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        SugarTestCallUtilities::removeAllCreatedCalls();
    }

    public function testCallAppearsWithinMonthView()
    {
        $this->markTestSkipped('Skipping for now.  Communicated with Yuri about properly resolving this since eCalendar moves out the get_occurs_within_where clause to CalendarActivity.php');
        global $timedate,$sugar_config,$DO_USER_TIME_OFFSET , $current_user;

        $DO_USER_TIME_OFFSET = true;
        $timedate = TimeDate::getInstance();
        $format = $current_user->getUserDateTimePreferences();
        $name = 'Bug37953Test' . $timedate->nowDb();
        $this->call->name = $name;
        $this->call->date_start = $timedate->swap_formats("2011-09-29 11:00pm" , 'Y-m-d h:ia', $format['date'].' '.$format['time']);
        $this->call->time_start = "";
        $this->call->object_name = "Call";
        $this->call->duration_hours = 99;
        
        $ca = new CalendarActivity($this->call);
        $where = $ca->get_occurs_within_where_clause($this->call->table_name, $this->call->rel_users_table, $ca->start_time, $ca->end_time, 'date_start', 'month');

        $this->assertRegExp('/2011\-09\-23 00:00:00/', $where, 'Assert that we go back 6 days from the date_start value');
        $this->assertRegExp('/2011\-11\-01 00:00:00/', $where, 'Assert that we go to the end of next month');
    }
}