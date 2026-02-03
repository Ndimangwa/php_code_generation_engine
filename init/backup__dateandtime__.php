<?php 
class Date {
	private $year;
	private $month;
	private $day;
	private $hour;
	private $minute;
	private $second;
	private $dateValue;
	private $dateTimeString;
	private $positive;
	private $dataHolder;
	public static function getDayComplement($month, $isLeap)	{
		$leap = 0;
		if ($isLeap) $leap = 1;
		$dayComplementArr[1] = 31;
		$dayComplementArr[2] = 28 + $leap; //Will Deal with it plz wait 
		$dayComplementArr[3] = 31;
		$dayComplementArr[4] = 30;
		$dayComplementArr[5] = 31;
		$dayComplementArr[6] = 30;
		$dayComplementArr[7] = 31;
		$dayComplementArr[8] = 31;
		$dayComplementArr[9] = 30;
		$dayComplementArr[10] = 31;
		$dayComplementArr[11] = 30;
		$dayComplementArr[12] = 31;
		$cmplt = 31;
		if (isset($dayComplementArr[$month])) $cmplt = $dayComplementArr[$month];
		return $cmplt;
	}
	public function putData($key, $value)	{
		$this->dataHolder[$key] = $value;
	}
	public function getData($key)	{
		$value = null;
		if (isset($this->dataHolder[$key])) $value = $this->dataHolder[$key];
		return $value;
	}
	public function setPositive($positive)	{
		$this->positive = $positive;
	}
	public function isPositive()	{ return $this->positive; }
	public function getDayOfAWeekOffsetValue($profile1)	{
		$timezone="Africa/Dar_es_Salaam";
		if (! is_null($profile1->getPHPTimezone())) $timezone = $profile1->getPHPTimezone()->getZoneName();
		date_default_timezone_set($timezone);
		$dtString1 = $this->year;
		$dtString1 .= "-".$this->month;
		$dtString1 .= "-".$this->day;
		//Format 2011-11-28
		return date("w", strtotime($dtString1));
	}
	public function synchronize()	{
		$string = $this->year;
		$string .= ":".$this->month;
		$string .= ":".$this->day;
		$string .= ":".$this->hour;
		$string .= ":".$this->minute;
		$string .= ":".$this->second;
		$this->dateTimeString = $string;
	}
	public function __construct($datetime)	{
		/* Format yyyy:mm:dd:hh:mm:ss */
		$this->setDateAndTime($datetime);
		$this->dataHolder = array();
	}
	public function setDateAndTime($datetime)	{
		$dt = explode(":", $datetime);
		if (sizeof($dt) != 6) {
			$this->year = "-1";
			$this->month = "-1";
			$this->day = "-1";
			$this->hour = "-1";
			$this->minute = "-1";
			$this->second = "-1";
			$this->dateValue = array();
			$this->dateTimeString=$datetime;
			return;
		}
		$this->year = $dt[0];
		$this->month = $dt[1];
		$this->day = $dt[2];
		$this->hour = $dt[3];
		$this->minute = $dt[4];
		$this->second = $dt[5];
		$this->dateValue = $dt;
		$this->dateTimeString=$datetime;
	}
	public function getClassName()	{ return "Date"; }
	public function getDateAndTimeString()	{ return $this->dateTimeString; }
	public function debug()	{
		
	}
	public function getYear()	{ return $this->year; }
	public function getYearInTwoDigitsOnly()	{
		if (! is_numeric($this->year)) return 0;
		$twoDigitsOnly = "".(intval($this->year) % 100);
		for ($i = strlen($twoDigitsOnly); $i < 2; $i++) $twoDigitsOnly = "0".$twoDigitsOnly;
		return $twoDigitsOnly;
	}
	public function getMonth()	{ return $this->month; }
	public function getDay()	{ return $this->day; }
	public function getHour()	{ return $this->hour; }
	public function getMinute()	{ return $this->minute; }
	public function getSecond()	{ return $this->second; }
	public function getDateAndTimeValue()	{ return $this->dateValue; }
	public function compareDateAndTime($date1)	{
		/*
			Return -1 if this date is less than $date1
			Return 0 if this date is equal to $date1
			Return 1 if this date is greater than $date1
		*/
		$cmp = 0;
		$dt1 = $this->getDateAndTimeValue();
		$dt2 = $date1->getDateAndTimeValue();
		for ($i=0; ($i<sizeof($dt1))||($i<sizeof($dt2)); $i++)	{
			if (! (is_numeric($dt1[$i]) && is_numeric($dt2[$i]))) continue;
			if (intval($dt1[$i]) < intval($dt2[$i]))	{
				$cmp = -1; break;
			} else if (intval($dt1[$i]) > intval($dt2[$i]))	{
				$cmp = 1; break;
			}
		}
		return $cmp;
	}
	protected function isLeapYear()	{
		$year = $this->year;
		return (((($year % 4) == 0 ) && (($year % 100) != 0 )) || (($year % 400) == 0 ));
	}
	public function dateDifference($__date)	{
		/*  
			if this date is less than date1 then 
			We assume this date is ahead of the reference date
		*/
		$date1 = $this;
		$date2 = $__date;
		$positive = true;
		if ($date1->compareDateAndTime($date2) < 0)	{
			$positive = false;
			$date1 = $__date;
			$date2 = $this;
		}
		$yearComplement = 9999;
		$monthComplement = 11; //0-11 month, 0-30 days setup 31 days default
		$dayComplement = (Date::getDayComplement(intval($date2->getMonth()), $date2->isLeapYear()) - 1);
		$hourComplement = 23;
		$minuteComplement = 59;
		$secondComplement = 59;
		//Complement - date2 , we need to adjust month and date one value less 
		$yearDiff = $yearComplement - intval($date2->getYear());
		$monthDiff = $monthComplement - (intval($date2->getMonth()) - 1);
		$dayDiff = $dayComplement - (intval($date2->getDay()) - 1);
		$hourDiff = $hourComplement - intval($date2->getHour());
		$minuteDiff = $minuteComplement - intval($date2->getMinute());
		$secondDiff = $secondComplement - intval($date2->getSecond());
		//Add diff to date1 , date 1 should be adjusted too 
		//Since this was a complement addition, we need to add 1
		$yearDiff = 1 + $yearDiff + intval($date1->getYear());
		$monthDiff = 1 + $monthDiff + (intval($date1->getMonth()) - 1);
		$dayDiff = 1 + $dayDiff + (intval($date1->getDay()) - 1);
		$hourDiff = 1 + $hourDiff + intval($date1->getHour());
		$minuteDiff = 1+ $minuteDiff + intval($date1->getMinute());
		$secondDiff = 1+ $secondDiff + intval($date1->getSecond());
		//Difference correction and take over marker, working right to left 
		$markMinute = false;
		$markHour = false;
		$markDay = false;
		$markMonth = false;
		$markYear = false;
		if ($secondDiff >= ($secondComplement + 1))	{
			$secondDiff = $secondDiff - ($secondComplement + 1);
		} else {
			$markMinute = true; //We have alredy borrowed
		}
		if ($minuteDiff >= ($minuteComplement + 1))	{
			$minuteDiff = $minuteDiff - ($minuteComplement + 1);
		} else {
			$markHour = true; //We have alredy borrowed
		}
		if ($hourDiff >= ($hourComplement + 1))	{
			$hourDiff = $hourDiff - ($hourComplement + 1);
		} else {
			$markDay = true; //We have alredy borrowed
		}
		if ($dayDiff >= ($dayComplement + 1))	{
			$dayDiff = $dayDiff - ($dayComplement + 1);
		} else {
			$markMonth = true; //We have alredy borrowed
		}
		if ($monthDiff >= ($monthComplement + 1))	{
			$monthDiff = $monthDiff - ($monthComplement + 1);
		} else {
			$markYear = true; //We have alredy borrowed
		}
		if ($yearDiff >= ($yearComplement + 1))	{
			$yearDiff = $yearDiff - ($yearComplement + 1);
		}
		//We have already add marks and adjusted
		if ($markMinute)	{			
			$minuteDiff = $minuteComplement + $minuteDiff; //same as substract one 
			if ($minuteDiff >= ($minuteComplement + 1)) { $minuteDiff = $minuteDiff - ($minuteComplement + 1); }
			//Transitive borrow 
			if ($date1->getMinute() == $date2->getMinute())	{ $markHour = true; }
		}
		if ($markHour)	{			
			$hourDiff = $hourComplement + $hourDiff; //same as substract one 
			if ($hourDiff >= ($hourComplement + 1)) { $hourDiff = $hourDiff - ($hourComplement + 1); }
			//Transitive borrow 
			if ($date1->getHour() == $date2->getHour())	{ $markDay = true; }
		}
		if ($markDay)	{			
			$dayDiff = $dayComplement + $dayDiff; //same as substract one 
			if ($dayDiff >= ($dayComplement + 1)) { $dayDiff = $dayDiff - ($dayComplement + 1); }
			//Transitive borrow 
			if ($date1->getDay() == $date2->getDay()) { $markMonth = true; }
		}
		if ($markMonth)	{
			$monthDiff = $monthComplement + $monthDiff; //same as substract one 
			if ($monthDiff >= ($monthComplement + 1)) { $monthDiff = $monthDiff - ($monthComplement + 1); }
			//Transitive borrow 
			if ($date1->getMonth() == $date2->getMonth())	{ $markYear = true; }
		}
		if ($markYear)	{
			$yearDiff = $yearComplement + $yearDiff; //same as substract one 
			if ($yearDiff >= ($yearComplement + 1)) { $yearDiff = $yearDiff - ($yearComplement + 1); }
		}
		//No need to adjust anything because this is just a difference 
		$dateString = $yearDiff.":".$monthDiff.":".$dayDiff.":".$hourDiff.":".$minuteDiff.":".$secondDiff;
		$mydate = new Date($dateString);
		$mydate->setPositive($positive);
		//Save Original Data 
		$mydate->putData('date1', $date1->getDateAndTimeString());
		$mydate->putData('date2', $date2->getDateAndTimeString());
		return $mydate;
	}
	public function daysDifference($__date)	{
		$diffDate1 = $this->dateDifference($__date);
		if (is_null($diffDate1)) return 0;
		//Default this-date2 
		$referenceDate1 = $__date;
		if ($diffDate1->isPositive()) $referenceDate1 = $this;
		//For Simplicity we will not take referenceDate1 into account 
		// Year = 365 days 
		// Month = 30days 
		return ((intval($diffDate1->getYear()) * 365) + (intval($diffDate1->getMonth()) * 30) + intval($diffDate1->getDay()));
	}
	public function inDateAndTimeRange($date1, $date2)	{
		return ($this->compareDateAndTime($date1) > 0) && ($this->compareDateAndTime($date2) < 0);
	}
	public function inDateAndTimeRangeInclude($date1, $date2)	{
		return ($this->compareDateAndTime($date1) >= 0) && ($this->compareDateAndTime($date2) <= 0);
	}
	public function getTimeHeight()	{
		return (intval($this->minute) + 60 * intval($this->hour));
	}
}
class DateAndTime extends Date	{
	private $extraFilter;
	public function toString()	{ return $this->getDateAndTimeString(); }
	public function toGUIDateFormat()	{ return self::convertFromSystemDateAndTimeFormatToGUIDateFormat($this->getDateAndTimeString()); }
	public function setExtraFilter($extraFilter) { $this->extraFilter = $extraFilter; }
	public function getExtraFilter()	{ return $this->extraFilter; }
	public static function getCurrentDateAndTime($profile1)	{
		$timezone="Africa/Dar_es_Salaam";
		if (! is_null($profile1->getPHPTimezone())) $timezone = $profile1->getPHPTimezone()->getZoneName();
		date_default_timezone_set($timezone);
		return new DateAndTime(date("Y:m:d:H:i:s"));
	}
	public function synchronize()	{
		$string = $this->year;
		$string .= ":".$this->month;
		$string .= ":".$this->day;
		$string .= ":".$this->hour;
		$string .= ":".$this->minute;
		$string .= ":".$this->second;
		$this->dateTimeString = $string;
	}
	public function updateDateAndTimeFromBiometric($dateAndTime)	{
		//INPUT : mm/dd/yyyy hh:mm 
		$dateAndTime = trim($dateAndTime);
		$dtArr1 = explode(" ",$dateAndTime);
		if (sizeof($dtArr1) != 2) __object__::shootException("Invalid Date and Time format");
		$dateSection1 = trim($dtArr1[0]);
		$timeSection1 = trim($dtArr1[1]);
		//Dealing with date section 
		$dateArr1 = explode("/", $dateSection1);
		if (sizeof($dateArr1) != 3) __object__::shootException("Invalid Date Format");
		$month = $dateArr1[0];
		$day = $dateArr1[1];
		$year = $dateArr1[2];
		//Dealing with Time 
		$timeArr1 = explode(":", $timeSection1);
		if (sizeof($timeArr1) < 2) __object__::shootException("Invalid Time Format");
		$hour = $timeArr1[0];
		$minute = $timeArr1[1];
		$second = 0;
		if (isset($timeArr1[2])) $second = $timeArr1[2];
		$dateAndTimeString = $year.":".$month.":".$day.":".$hour.":".$minute.":".$second;
		return (new DateAndTime($dateAndTimeString));
	}
	public function getDateOnly()	{
		return $this->getYear().":".$this->getMonth().":".$this->getDay();
	}
	public final static function isTimeRangeOverlap($time1, $time2, $timea, $timeb)	{
		//Range 1 : time1 .... time2 
		//Range 2 : timea .... timeb 
		return ($timea->inDateAndTimeRangeInclude($time1, $time2) || $timeb->inDateAndTimeRangeInclude($time1, $time2) || $time1->inDateAndTimeRangeInclude($timea, $timeb) || $time2->inDateAndTimeRangeInclude($timea, $timeb));
	}
	public final static function convertFromSystemDateAndTimeFormatToTimeOnlyFormat($__time)	{
		$tArr = explode(":", $__time);
		if (sizeof($tArr) == 6)	{
			$__time = $tArr[3].":".$tArr[4].":".$tArr[5];
		}
		return $__time;
	}
	public final static function convertFromGUIDateAndTimeFormatToSystemDateAndTimeFormat($__date, $__time)	{
		$dtArr = explode("/", $__date);
		$dtString = intval($dtArr[2]).":".intval($dtArr[1]).":".intval($dtArr[0]);
		$__time = explode(":", $__time);
		$tmString = ":00:00:00";
		if (sizeof($__time) == 2)	{
			$tmString = ":".trim($__time[0]);
			$tmString .= ":".trim($__time[1]);
			$tmString .= ":00";
		}
		return $dtString.$tmString;
	}
	public final static function convertFromGUIDateFormatToSystemDateAndTimeFormat($__date)	{
		$dtArr = explode("/", $__date);
		if (is_null($dtArr)) __object__::shootException("DateAndTime [static] Could not process the date value");
		if (sizeof($dtArr) < 3) __object__::shootException("Date Format Error");
		$dtString = intval($dtArr[2]).":".intval($dtArr[1]).":".intval($dtArr[0]).":00:00:00";
		return $dtString;
	}
	public final static function convertFromSystemDateAndTimeFormatToGUIDateAndTimeFormat($__date)	{
		$list = array();
		$list['date'] = self::convertFromSystemDateAndTimeFormatToGUIDateFormat($__date);
		$list['time'] = self::convertFromSystemDateAndTimeFormatToGUITimeFormat($__date);
		return $list;
	}
	public final static function convertFromSystemDateAndTimeFormatToGUIDateFormat($__date)	{
		$dtArr = explode(":", $__date);
		//System::convertIntegerToStringOfAGivenLength($__data, $__len)
		$dtString = System::convertIntegerToStringOfAGivenLength($dtArr[2], 2)."/".System::convertIntegerToStringOfAGivenLength($dtArr[1], 2)."/".System::convertIntegerToStringOfAGivenLength($dtArr[0], 4);
		return $dtString;
	}
	public final static function convertFromSystemDateAndTimeFormatToGUITimeFormat($__date)	{
		$dtArr = explode(":", $__date);
		$dtString = System::convertIntegerToStringOfAGivenLength($dtArr[3], 2)." : ".System::convertIntegerToStringOfAGivenLength($dtArr[4], 2);
		return $dtString;
	}
	public final static function convertFromDateTimeObjectToGUIDateFormat($dateObject1)	{
		return self::convertFromSystemDateAndTimeFormatToGUIDateFormat($dateObject1->getDateAndTimeString());
	}
	public function __construct($date)	{
		parent::__construct($date);
	}
	//Overriding function 
	public function getClassName()	{ return "DateAndTime"; }
}
?>