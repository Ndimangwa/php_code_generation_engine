<?php
class DateAndTime {
	private static $__YEAR = "year";
	private static $__MONTH = "month";
	private static $__DAY = "day";
	private static $__HOUR = "hour";
	private static $__MINUTE = "minute";
	private static $__SECOND = "second";
	private static $__SEPARATOR = ":";
	private static $__GUI_DATE_SEPARATOR = "/";
	private static $__GUI_TIME_SEPARATOR = ":";
	private $extraFilter;
	private $dateString;
	private $dateArray;
	private $timestamp;
	//---Begin Date and Time Navigation
	public function getPreviousDateAndTimeByYears($numberofyears = 1, $preserveHoursMinutesAndSecounds = true, $defaultHoursMinutesAndSecondsIfNotPreserved = "00:00:00")	{
		return ( $this->getNextDateAndTimeByMonths(($numberofyears * -1), $preserveHoursMinutesAndSecounds, $defaultHoursMinutesAndSecondsIfNotPreserved) );
	}
	public function getNextDateAndTimeByYears($numberofyears = 1, $preserveHoursMinutesAndSecounds = true, $defaultHoursMinutesAndSecondsIfNotPreserved = "00:00:00")	{
		$timeArray1 = $this->getDateAndTimeNavigationTimeArray($preserveHoursMinutesAndSecounds, $defaultHoursMinutesAndSecondsIfNotPreserved);
		//Now return object
		return ( new DateAndTime(date("Y:m:d:H:i:s", mktime(intval($timeArray1[self::$__HOUR]), intval($timeArray1[self::$__MINUTE]), intval($timeArray1[self::$__SECOND]), intval($timeArray1[self::$__MONTH]), intval($timeArray1[self::$__DAY]), intval($timeArray1[self::$__YEAR]) + $numberofyears))) );
	}
	public function getPreviousDateAndTimeByMonths($numberofmonths = 1, $preserveHoursMinutesAndSecounds = true, $defaultHoursMinutesAndSecondsIfNotPreserved = "00:00:00")	{
		return ( $this->getNextDateAndTimeByMonths(($numberofmonths * -1), $preserveHoursMinutesAndSecounds, $defaultHoursMinutesAndSecondsIfNotPreserved) );
	}
	public function getNextDateAndTimeByMonths($numberofmonths = 1, $preserveHoursMinutesAndSecounds = true, $defaultHoursMinutesAndSecondsIfNotPreserved = "00:00:00")	{
		$timeArray1 = $this->getDateAndTimeNavigationTimeArray($preserveHoursMinutesAndSecounds, $defaultHoursMinutesAndSecondsIfNotPreserved);
		//Now return object
		return ( new DateAndTime(date("Y:m:d:H:i:s", mktime(intval($timeArray1[self::$__HOUR]), intval($timeArray1[self::$__MINUTE]), intval($timeArray1[self::$__SECOND]), intval($timeArray1[self::$__MONTH]) + $numberofmonths, intval($timeArray1[self::$__DAY]), intval($timeArray1[self::$__YEAR])))) );
	}
	public function getPreviousDateAndTimeByDays($numberofdays = 1, $preserveHoursMinutesAndSecounds = true, $defaultHoursMinutesAndSecondsIfNotPreserved = "00:00:00")	{
		return ( $this->getNextDateAndTimeByDays(( $numberofdays * -1 ), $preserveHoursMinutesAndSecounds, $defaultHoursMinutesAndSecondsIfNotPreserved) );
	}	
	public function getNextDateAndTimeByDays($numberofdays = 1, $preserveHoursMinutesAndSecounds = true, $defaultHoursMinutesAndSecondsIfNotPreserved = "00:00:00")	{
		$timeArray1 = $this->getDateAndTimeNavigationTimeArray($preserveHoursMinutesAndSecounds, $defaultHoursMinutesAndSecondsIfNotPreserved);
		//Now return object
		return ( new DateAndTime(date("Y:m:d:H:i:s", mktime(intval($timeArray1[self::$__HOUR]), intval($timeArray1[self::$__MINUTE]), intval($timeArray1[self::$__SECOND]), intval($timeArray1[self::$__MONTH]), intval($timeArray1[self::$__DAY]) + $numberofdays, intval($timeArray1[self::$__YEAR])))) );
	}
	private function getDateAndTimeNavigationTimeArray($preserveHoursMinutesAndSecounds = true, $defaultHoursMinutesAndSecondsIfNotPreserved = "00:00:00")	{
		$year = $this->dateArray[self::$__YEAR];
		$month = $this->dateArray[self::$__MONTH];
		$day = $this->dateArray[self::$__DAY];
		$hour = $this->dateArray[self::$__HOUR];
		$minute = $this->dateArray[self::$__MINUTE];
		$second = $this->dateArray[self::$__SECOND];
		if (! $preserveHoursMinutesAndSecounds)	{
			//load from default 
			$dt = explode(self::$__SEPARATOR, $defaultHoursMinutesAndSecondsIfNotPreserved);
			$hour = isset($dt[0]) ? $dt[0] : "0";
			$minute = isset($dt[1]) ? $dt[1] : "0";
			$second = isset($dt[2]) ? $dt[2] : "0";
		}
		return array(
			(self::$__YEAR) => $year,
			(self::$__MONTH) => $month,
			(self::$__DAY) => $day,
			(self::$__HOUR) => $hour,
			(self::$__MINUTE) => $minute,
			(self::$__SECOND) => $second
		);
	}
	public function getBeginOfADay($hour = 0, $minute = 0, $second = 0)	{
		return (self::makeDateAndTime($this->getYear(), $this->getMonth(), $this->getDay(), $hour, $minute, $second));
	}
	public function getEndOfADay($hour = 23, $minute = 59, $second = 59)	{
		return ( $this->getBeginOfADay($hour, $minute, $second) );
	}
	//--End Date and Time Navigation
	public static function makeDateAndTime($year = 0, $month = 0, $day = 0, $hour = 0, $minute = 0, $second = 0)	{
		return (new DateAndTime(mktime($hour, $minute, $second, $month, $day, $year)));
	}
	private static function fixedLength($string1, $length, $pad = 0)	{
		$string1 = "" . $string1;
		for ($i = strlen($string1); $i < $length; $i++) $string1 = $pad . $string1;
		return $string1;
	}
	public function getYear()	{ return $this->dateArray[self::$__YEAR]; }
	public function getMonth()	{ return $this->dateArray[self::$__MONTH]; }
	public function getDay()	{ return $this->dateArray[self::$__DAY]; }
	public function getHour()	{ return $this->dateArray[self::$__HOUR]; }
	public function getMinute()	{ return $this->dateArray[self::$__MINUTE]; }
	public function getSecond()	{ return $this->dateArray[self::$__SECOND]; }
	public function getDateAndTimeString()	{
		return $this->dateString;
	}
	public function getTimestamp()	{
		return $this->timestamp;
	}
	public function setDateAndTime($datetimestring = null/* can be either string or php date */, $year = null, $month = null, $day = null, $hour = null, $minute = null, $second = null)	{
		$mytimestamp = null;
		$larray1 = array(
			( self::$__YEAR ) => "0000",
			( self::$__MONTH) => "00",
			( self::$__DAY ) => "00",
			( self::$__HOUR ) => "00",
			( self::$__MINUTE ) => "00",
			( self::$__SECOND ) => "00"
		);
		$dt = is_null($datetimestring) ? array() : explode(self::$__SEPARATOR, $datetimestring);
		if (sizeof($dt) == 6)	{
			$larray1 = array(
				( self::$__YEAR ) => ( self::fixedLength($dt[0], 4, 0) ),
				( self::$__MONTH) => ( self::fixedLength($dt[1], 2, 0) ),
				( self::$__DAY ) => ( self::fixedLength($dt[2], 2, 0) ),
				( self::$__HOUR ) => ( self::fixedLength($dt[3], 2, 0) ),
				( self::$__MINUTE ) => ( self::fixedLength($dt[4], 2, 0) ),
				( self::$__SECOND ) => ( self::fixedLength($dt[5], 2, 0) )
			);
		} else if (sizeof($dt) == 3)	{
			$larray1[self::$__YEAR] = ( self::fixedLength($dt[0], 4, 0) );
			$larray1[self::$__MONTH] = ( self::fixedLength($dt[1], 2, 0) );
			$larray1[self::$__DAY] = ( self::fixedLength($dt[2], 2, 0) );
		} else if (sizeof($dt) == 1) {
			//This must be a timestamp
			$mytimestamp = $dt[0];
		} else {
			//no datetime string
			if (! is_null($year))	$larray1[self::$__YEAR] = ( self::fixedLength($year, 4, 0) );
			if (! is_null($month)) $larray1[self::$__MONTH] = ( self::fixedLength($month, 2, 0) );
			if (! is_null($day)) $larray1[self::$__DAY] = ( self::fixedLength($day, 2, 0) );
			if (! is_null($hour)) $larray1[self::$__HOUR] = ( self::fixedLength($hour, 2, 0) );
			if (! is_null($month)) $larray1[self::$__MONTH] = ( self::fixedLength($month, 2, 0) );
			if (! is_null($second)) $larray1[self::$__SECOND] = ( self::fixedLength($second, 2, 0) ); 
		}
		//Generating Timestamp
		if (is_null($mytimestamp))	{
			$this->timestamp = mktime(
				intval($larray1[self::$__HOUR]),
				intval($larray1[self::$__MINUTE]),
				intval($larray1[self::$__SECOND]),
				intval($larray1[self::$__MONTH]),
				intval($larray1[self::$__DAY]),
				intval($larray1[self::$__YEAR])
			);
		} else {
			$this->timestamp = intval($mytimestamp);
		}
		//Now perform correction on time use - advantage of timestamp
		$ltime = date("Y:m:d:H:i:s", $this->timestamp);
		//Now do assignment
		$dt = explode(":", $ltime);
		if (sizeof($dt) != 6) throw new Exception("Date Handling Error, not all components found");
		$lookupArray1 = array(
			0 => ( self::$__YEAR ),
			1 => ( self::$__MONTH ),
			2 => ( self::$__DAY ),
			3 => ( self::$__HOUR ),
			4 => ( self::$__MINUTE ),
			5 => ( self::$__SECOND )
		);
		for ($i = 0; $i < sizeof($dt); $i++)	{
			$this->dateArray[$lookupArray1[$i]] = $dt[$i];
		}
		$this->dateString = str_replace(":", (self::$__SEPARATOR), $ltime);
	}
	public function __construct($datetime)
	{
		$this->setDateAndTime($datetime);
	}
	public function getClassname()	{
		return "DateAndTime";
	}
	//---Begin Methods from old DateAndTime
	public static function calculateTimeDifferenceByYears($olderTimeObject1, $laterTimeObject1)	{
		$birthDate = ( $olderTimeObject1->getDay() ) . "-" . ( $olderTimeObject1->getMonth() ) . "-" . ( $olderTimeObject1->getYear() );
		$currentDate = ( $laterTimeObject1->getDay() ) . "-" . ( $laterTimeObject1->getMonth() ) . "-" . ( $laterTimeObject1->getYear() );
		$diff = date_diff(date_create($birthDate), date_create($currentDate));
		return $diff->format("%y");
	}
	public function toString()	{ return $this->timestamp; }
	public function setExtraFilter($extraFilter) { $this->extraFilter = $extraFilter; }
	public function getExtraFilter()	{ return $this->extraFilter; }
	public static function getCurrentDateAndTime($profile1 = null)	{
		$timezone="Africa/Dar_es_Salaam";
		if (! is_null($profile1) && ! is_null($profile1->getPHPTimezone())) $timezone = $profile1->getPHPTimezone()->getZoneName();
		date_default_timezone_set($timezone);
		return new DateAndTime(date("Y:m:d:H:i:s"));
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
		$timeArr1 = explode(( self::$__SEPARATOR ), $timeSection1);
		if (sizeof($timeArr1) < 2) __object__::shootException("Invalid Time Format");
		$hour = $timeArr1[0];
		$minute = $timeArr1[1];
		$second = 0;
		if (isset($timeArr1[2])) $second = $timeArr1[2];
		$dateAndTimeString = $year.( self::$__SEPARATOR ).$month.( self::$__SEPARATOR ).$day.( self::$__SEPARATOR ).$hour.( self::$__SEPARATOR ).$minute.( self::$__SEPARATOR ).$second;
		return (new DateAndTime($dateAndTimeString));
	}
	public function getDateOnly()	{
		return $this->getYear().( self::$__SEPARATOR ).$this->getMonth().( self::$__SEPARATOR ).$this->getDay();
	}
	public function getTimeOnlyFormat()	{
		//hh:mm:ss
		return ( ( $this->dateArray[self::$__HOUR] ) . (self::$__SEPARATOR ) . ( $this->dateArray[self::$__MINUTE] ) . ( self::$__SEPARATOR ) . ( $this->dateArray[self::$__SECOND] ) );
	}	
	public function getGUIDateOnlyFormat()	{
		//mm/dd/yyyy
		return ( ( $this->dateArray[self::$__MONTH] ) . ( self::$__GUI_DATE_SEPARATOR ) . ( $this->dateArray[self::$__DAY] ) . ( self::$__GUI_DATE_SEPARATOR ) . ( $this->dateArray[self::$__YEAR] ));
	}
	public function getGUITimeOnlyFormat()	{
		//hh:mm:ss
		return ( ( $this->dateArray[self::$__HOUR] ) . (self::$__GUI_TIME_SEPARATOR) . ( $this->dateArray[self::$__MINUTE] ) . ( self::$__GUI_TIME_SEPARATOR ) . ( $this->dateArray[self::$__SECOND] ) );
	}
	public function getGUIDateAndTimeFormat()	{
		return array(
			"__date__" => ( $this->getGUIDateOnlyFormat() ),
			"__time__" => ( $this->getGUITimeOnlyFormat() )
		);
	}
	public static function createDateAndTimeFromGUIDate($gui_date_format)	{
		$dt = explode(self::$__GUI_DATE_SEPARATOR, $gui_date_format);
		if (sizeof($dt) != 3) throw new Exception("[ $gui_date_format ] : Could not interpret GUI Date");
		return (self::makeDateAndTime($dt[2], $dt[0], $dt[1], 0, 0, 0));
	}
	public static function createDateAndTimeFromGUIDateAndTime($gui_date_format, $gui_time_format)	{
		$dtArray1 = explode(self::$__GUI_DATE_SEPARATOR, $gui_date_format);
		if (sizeof($dtArray1) != 3) throw new Exception("[ $gui_date_format ] : Could not interpret GUI Date");
		$tArray1 = explode(self::$__GUI_TIME_SEPARATOR, $gui_time_format);
		if (sizeof($tArray1) != 3) throw new Exception("[ $gui_time_format ] : Could not interpres GUI Time");
		return ( self::makeDateAndTime($dtArray1[2], $dtArray1[0], $dtArray1[1], $tArray1[0], $tArray1[1], $tArray1[2]) );
	}
	//---End Methods from old DateAndTime
} 
?>