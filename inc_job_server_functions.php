<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 8/24/2006                                //
// ------------------------------------------------------------------------- //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //

// DMS Job Server Functions
// inc_job_server_functions.php

function js_next_run_time($day_flag,$time_flag,$sched_day,$sched_hour,$sched_minute)
	{
	$current_date = date("U");

	// Scheduling multipliers to adjust for current day and time
	$mult_sched_day = 0;
	$mult_sched_hour = 0;
	$mult_sched_minute = 0;
	
	$possible_next_run_time = js_calc_next_run_time($day_flag,$time_flag,$sched_day,$sched_hour,$sched_minute,
	  $mult_sched_day, $mult_sched_hour, $mult_sched_minute);
	
	if($possible_next_run_time <= $current_date)
		{
		if( ($sched_day != DAY) && ($time_flag == AT) ) $mult_sched_day = 7;
		if( ($sched_day == DAY) && ($time_flag == AT) ) $mult_sched_day ++;
		}
	
	$possible_next_run_time = js_calc_next_run_time($day_flag,$time_flag,$sched_day,$sched_hour,$sched_minute,
	  $mult_sched_day, $mult_sched_hour, $mult_sched_minute);
	
	return($possible_next_run_time);
	}

function js_calc_next_run_time($day_flag,$time_flag,$sched_day,$sched_hour,$sched_minute,
  $mult_sched_day = 0, $mult_sched_hour = 0, $mult_sched_minute = 0)
	{
	//  Most time calculations are in number of seconds since Unix Epoch.
	$number_seconds_week = 7 * 24 * 60 * 60;
	$number_seconds_day = 24 * 60 * 60;
	$number_seconds_hour = 60 * 60;
	$number_seconds_quarter_hour = 15 * 60;
	$number_seconds_minute = 60;
	
	$current_date = date("U");
	$current_hour = date("G",$current_date);
	$current_minute = date("i",$current_date);
	$current_second = date("s",$current_date);
	$current_day = $current_date - ($current_hour * $number_seconds_hour) - ($current_minute * $number_seconds_minute) - ($current_second);
	$current_week_day = date("l",$current_date);

	$next_sched_day = 0;
	$next_sched_hour = 0;
	$next_sched_minute = 0;
	
	$week_days = array(0 => "Sunday",1 => "Monday",2 => "Tuesday",3 => "Wednesday",4 => "Thursday",5 => "Friday",6 => "Saturday", 
	  7 => "Sunday", 8 => "Monday", 9 => "Tuesday", 10 => "Wednesday", 11 => "Thursday", 12 => "Friday", 13 => "Saturday");
	
	//  Determine the next scheduled week day
	if($sched_day != DAY)
		{
		//  Find a numeric value for the current week day.
		$current_week_day_num = 0;
		for($index = 0; $index <=6; $index++)
			{
			if($current_week_day == $week_days[$index])
				{
				$current_week_day_num = $index;
				break;
				}
			}
		
		//  Find a numeric value for the next scheduled week day.
		$next_sched_week_day = $week_days[$sched_day];
		$next_sched_week_day_num = 0;
		for($index = $current_week_day_num; $index <= 13; $index++)
			{
			if($week_days[$index] == $next_sched_week_day)
				{
				$next_sched_week_day_num = $index;
				break;
				}
			}
		$num_week_days_diff = $next_sched_week_day_num - $current_week_day_num; 
		$next_sched_day = $current_day + ($num_week_days_diff * $number_seconds_day);
		}
	else $next_sched_day = $current_day;
	
	if($mult_sched_day > 0) $next_sched_day += ($number_seconds_day * $mult_sched_day);
	
	//  Determine the next scheduled hour
	$next_sched_hour = ($sched_hour * $number_seconds_hour);
	
	// Determine the next scheduled minute
	$next_sched_minute = ($sched_minute * $number_seconds_minute);
	
	return($next_sched_day + $next_sched_hour + $next_sched_minute);
	}
	
?>

