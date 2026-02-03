<?php
class Promise	{
	public static $COMPLETED = 1;
	public static $NOT_YET = 2;
	private $success;
	private $reason;
	private $results;
	private $extraInfo;
	private $filename;	//If there is a corresponding file
	private $status;
	private $position;
	public function __construct()	{
		$this->success = false;
	}
	public function setPromise($bln)	{ $this->success = $bln; }
	public function getPromise()	{ return $this->success; }
	public function isPromising()	{ return $this->success; }
	public function setReason($reason)	{ $this->reason = $reason; }
	public function getReason()	{ return $this->reason; }
	public function setExtraInformation($extraInfo)	{ $this->extraInfo = $extraInfo; }
	public function getExtraInformation()	{ return $this->extraInfo; }	
	public function setFileName($filename)	{ $this->filename = $filename; }
	public function getFileName()	{ return $this->filename; }
	public function setStatus($status)	{ $this->status = $status; }
	public function getStatus()	{ return $this->status; }
	public function setPosition($position)	{ $this->position = $position; }
	public function getPosition()	{ return $this->position; }
	public function setResults($results)	{ $this->results = $results; }
	public function getResults()	{ return $this->results; }
}
?>