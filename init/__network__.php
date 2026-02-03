<?php 
class Network {
	private $ipAddress;
	private $subnetMask;
	private $subnetMaskCIDR;
	private $networkAddress;
	private $broadcastAddress;
	private $numberOfHost;
	private $currentHostNumber;
	private $bitString;
	private static $CIDRPrefixCount;
	private static $IPV4_BIT_LENGTH = 32;
	public static function getPowerOfTwoLookupTable()	{
		$lookup1[0] = 1;
		//Now populate the rest 
		for ($i = 1; $i <= ( self::$IPV4_BIT_LENGTH ); $i++)	$lookup1[$i] = 2 * $lookup1[$i - 1];
		return $lookup1;
	}
	public function debug()	{
		echo "\n===============================================";
		echo "\n IP Address  		: 	".( $this->ipAddress );
		echo "\n Subnet Mask 		: 	".( $this->subnetMask );
		echo "\n Subnet Mask (CIDR)	:	".( $this->subnetMaskCIDR );
		echo "\n Network Address 	:	".( $this->networkAddress );
		echo "\n Broadcast Address	:	".( $this->broadcastAddress );
		echo "\n Number of Host		:	".( $this->numberOfHost );
		echo "\n Host Number      	:	".( $this->currentHostNumber );
		echo "\n Bit String	      	:	".( $this->bitString );
		echo "\n===============================================\n";
	}
	public final static function convertASingleBlockToBitString($blockNumber)	{
		//192 
		$blockNumber = intval("".$blockNumber);
		if (($blockNumber < 0) || ($blockNumber > 255)) return null;
		$octet = "";
		while ($blockNumber >= 1)	{
			$octet = ($blockNumber % 2).$octet."";
			$blockNumber = $blockNumber / 2;
		}
		for ($i=strlen($octet); $i < 8; $i++)	$octet = "0".$octet;
		return $octet;
	}
	public final static function convertABitStringBlockToNumber($bitString)	{
		//To speed up prepare a lookup table 
		$positionArray1 = array();
		$positionArray1[0] = 128;
		$positionArray1[1] = 64;
		$positionArray1[2] = 32;
		$positionArray1[3] = 16;
		$positionArray1[4] = 8;
		$positionArray1[5] = 4;
		$positionArray1[6] = 2;
		$positionArray1[7] = 1;
		if (strlen($bitString) != 8) return null;
		$number = 0;
		for ($i = 0; $i < 8; $i++)	{
			if (intval(substr($bitString, $i, 1)) == 1)	$number = $number + $positionArray1[$i];
		}
		return $number;
	}
	public final static function convertIpAddressToBitString($ipAddress)	{
		//Input 192.168.0.1 
		//Output 1100000001000000002000010
		$ipAddressArr = explode(".", $ipAddress);
		if (sizeof($ipAddressArr) != 4) return null;
		$bitString = "";
		foreach ($ipAddressArr as $ip)	{
			$octet = self::convertASingleBlockToBitString($ip);
			if (! is_null($octet))	{
				$bitString = $bitString.$octet;
			} else {
				$bitString = null;
				break;
			}
		}
		return $bitString;
	}
	public final static function convertBitStringToIpAddress($bitString)	{
		if (strlen($bitString) != self::$IPV4_BIT_LENGTH) return null;
		$ipAddress = "";
		$block1 = substr($bitString, 0, 8);
		$ipAddress = self::convertABitStringBlockToNumber($block1);
		$block1 = substr($bitString, 8, 8);
		$ipAddress = $ipAddress.".".self::convertABitStringBlockToNumber($block1);
		$block1 = substr($bitString, 16, 8);
		$ipAddress = $ipAddress.".".self::convertABitStringBlockToNumber($block1);
		$block1 = substr($bitString, 24, 8);
		$ipAddress = $ipAddress.".".self::convertABitStringBlockToNumber($block1);
		return $ipAddress;
	}	
	public final static function isValidIpAddress($ipAddress)	{
		$ipAddressArr = explode(".", $ipAddress);
		if (sizeof($ipAddressArr) != 4) return false;
		$validIp = true;
		foreach ($ipAddressArr as $block1)	{
			$block1 = intval($block1);
			if (! ($block1 >= 0 && $block1 <= 255)) {
				$validIp = false;
				break;
			}
		}
		return $validIp;
	}
	public final static function calculateHostCount($cidrprefixcount)	{
		if (! ($cidrprefixcount < self::$IPV4_BIT_LENGTH)) return 0;
		$noofhostbits = ( self::$IPV4_BIT_LENGTH ) - $cidrprefixcount;
		$lookupTable1 = self::getPowerOfTwoLookupTable();
		return ( $lookupTable1[$noofhostbits] - 2 );
	}
	public final static function isValidSubnetMask($netmask)	{
		if (! self::isValidIpAddress($netmask)) return false;
		$bitString = self::convertIpAddressToBitString($netmask);
		if (is_null($bitString)) return false;
		$bitLength = strlen($bitString);
		$validMask = true;
		$state = 0;
		$cidrprefixcount = 0;
		for ($i=0; $i < $bitLength; $i++)	{
			//Fill Transition states only
			$currentbit = intval(substr($bitString, $i, 1));
			if (($state == 0) && ($currentbit == 1))	{
				$cidrprefixcount++; //Just for counting purpose
			}
			//Now do business
			if (($state == 0) && ($currentbit == 0)) {
				$state = 1; 
				continue;
			}
			if (($state == 1) && ($currentbit == 1)) {
				$state = 2; //Error State 
				$validMask = false;
				break;
			}
		}
		self::$CIDRPrefixCount = $cidrprefixcount;
		return $validMask;
	}
	public final static function calculateBroadcastAddress($ipAddress, $subnetMask)	{
		//To be used by __object__ only, we assume ip and its subnet mask are valid 
		$ipBits = self::convertIpAddressToBitString($ipAddress);
		if (is_null($ipBits)) __object__::shootException("A Problem During Conversion");
		$netBits = self::convertIpAddressToBitString($subnetMask);
		if (is_null($netBits)) __object__::shootException("A Problem During Conversion");
		$bitLength = strlen($ipBits);
		$networkBits = "";
		for ($i=0; $i < $bitLength; $i++)	{
			$val1 = intval(substr($ipBits, $i, 1));
			$val2 = intval(substr($netBits, $i, 1));
			$bit = 1;
			if ($val2 > $val1) $bit = 0;
			$networkBits .= $bit;
		}
		//convert back to Ip Address 
		return self::convertBitStringToIpAddress($networkBits);
	}
	public final static function calculateNetworkAddress($ipAddress, $subnetMask)	{
		//To be used by __object__ only, we assume ip and its subnet mask are valid 
		$ipBits = self::convertIpAddressToBitString($ipAddress);
		if (is_null($ipBits)) __object__::shootException("A Problem During Conversion");
		$netBits = self::convertIpAddressToBitString($subnetMask);
		if (is_null($netBits)) __object__::shootException("A Problem During Conversion");
		$bitLength = strlen($ipBits);
		$networkBits = "";
		for ($i=0; $i < $bitLength; $i++)	{
			$networkBits .= intval(substr($ipBits, $i, 1)) * intval(substr($netBits, $i, 1));
		}
		//convert back to Ip Address 
		return self::convertBitStringToIpAddress($networkBits);
	}
	public static function calculateCurrentHostNumber($cidr, $bitString)	{
		$hostNumber = 0;
		$lookupTable1 = self::getPowerOfTwoLookupTable();
		for ($pos = $cidr; $pos < ( self::$IPV4_BIT_LENGTH ) ; $pos++)	{
			$bit = (intval(substr($bitString, $pos, 1)) == 1);
			$val = $lookupTable1[$pos];
			if ($bit)	{
				$hostNumber += $lookupTable1[( self::$IPV4_BIT_LENGTH ) - $pos - 1];
			}
		}
		return $hostNumber;
	}
	public function isThisIpAddressBelongToMyNetwork($ipAddress)	{
		if (! self::isValidIpAddress($ipAddress)) return false;
		$networkAddress = self::calculateNetworkAddress($ipAddress, $this->subnetMask);
		return (strcmp($this->networkAddress, $networkAddress) == 0);
	}
	public function __construct($ipAddress, $subnetMask)	{
		if (! self::isValidIpAddress($ipAddress)) __object__::shootException("Network, IP Address is not valid");
		if (! self::isValidSubnetMask($subnetMask)) __object__::shootException("Network, Subnet Mask not valid");
		$this->subnetMaskCIDR = self::$CIDRPrefixCount;
		$this->ipAddress = $ipAddress;
		$this->subnetMask = $subnetMask;
		$this->networkAddress = self::calculateNetworkAddress($ipAddress, $subnetMask);
		$this->broadcastAddress = self::calculateBroadcastAddress($ipAddress, $subnetMask);
		$this->numberOfHost = self::calculateHostCount($this->subnetMaskCIDR);
		$this->bitString = self::convertIpAddressToBitString($this->ipAddress);
		$this->currentHostNumber = self::calculateCurrentHostNumber($this->subnetMaskCIDR, $this->bitString);
	}
	public function advanceIpAddress()	{
		if ($this->currentHostNumber == $this->numberOfHost)	{
			throw new Exception("You have reached the last host, can not advance");
		}
		$newString1 = "";
		$carry = true; //To initiate addition
		for ($i = 0; $i < (self::$IPV4_BIT_LENGTH); $i++)	{
			$pos = ( self::$IPV4_BIT_LENGTH ) - $i - 1;
			$bit = (intval(substr($this->bitString, $pos, 1)) == 1);
			//Now calculate 
			$bit = ( $bit xor $carry ); //Save bit prior
			$carry = ( $carry && ! $bit ); //Not we are dealing with n+1 th bit , updated on the previous line
			//Now need to translate true to 1 else 0
			$bit = $bit ? "1" : "0";
			//Append Now
			$newString1 = $bit.$newString1;
		}
		$ipAddress = self::convertBitStringToIpAddress($newString1);
		if ($this->isThisIpAddressBelongToMyNetwork($ipAddress))	{
			$this->ipAddress = $ipAddress;
			$this->bitString = $newString1;
			$this->currentHostNumber = self::calculateCurrentHostNumber($this->subnetMaskCIDR, $this->bitString);
		} else {
			$networkAddress = $this->getNetworkAddress();
			$cidr = $this->subnetMaskCIDR;
			throw new Exception("Ip Address [ $ipAddress ] : Does not belong to Network [ $networkAddress / $cidr ]");
		}
	}
	public function getIpAddress()	{ return $this->ipAddress; }
	public function getSubnetMask()	{ return $this->subnetMask; }
	public function getCIDRSubnetPrefix()	{ return $this->subnetMaskCIDR; }
	public function getNetworkAddress()	{ return $this->networkAddress; }
	public function getBroadcastAddress()	{ return $this->broadcastAddress; }
	public function getNumberOfHost()	{ return $this->numberOfHost; }
	public function getCurrentHostNumber()	{ return $this->currentHostNumber; }
	public function getBitString()	{ return $this->bitString; }
	public function isAddressInThisNetwork($ipAddress)	{
		return true;
	}
}
?>