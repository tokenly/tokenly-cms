<?php
namespace API;
//hex input must be in uppercase, with no leading 0x
//This Script has been Modified by CrazyRabbit with help from Pooler to Validate LTC address
//Original Version by theymos
//Works without being connected to Litecoin Server
//To ask questions follow in the litecoin Forums Thread: //http://forum.litecoin.net/index.php/topic,521.0.html

define("LTC_ADDRESSVERSION","30"); //this is a hex byte (changed from 00 to 30 to validate LTC)

class LTCValidate
{

	function decodeHex($hex)
	{
		$hex=strtoupper($hex);
		$chars="0123456789ABCDEF";
		$return="0";
		for($i=0;$i<strlen($hex);$i++)
		{
			$current=(string)strpos($chars,$hex[$i]);
			$return=(string)bcmul($return,"16",0);
			$return=(string)bcadd($return,$current,0);
		}
		return $return;
	}

	function encodeHex($dec)
	{
		$chars="0123456789ABCDEF";
		$return="";
		while (bccomp($dec,0)==1)
		{
			$dv=(string)bcdiv($dec,"16",0);
			$rem=(integer)bcmod($dec,"16");
			$dec=$dv;
			$return=$return.$chars[$rem];
		}
		return strrev($return);
	}

	function decodeBase58($base58)
	{
		$origbase58=$base58;
		
		$chars="123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz";
		$return="0";
		for($i=0;$i<strlen($base58);$i++)
		{
			$current=(string)strpos($chars,$base58[$i]);
			$return=(string)bcmul($return,"58",0);
			$return=(string)bcadd($return,$current,0);
		}
		
		$return= $this->encodeHex($return);
		
		//leading zeros
		for($i=0;$i<strlen($origbase58)&&$origbase58[$i]=="1";$i++)
		{
			$return="00".$return;
		}
		
		if(strlen($return)%2!=0)
		{
			$return="0".$return;
		}
		
		return $return;
	}

	function encodeBase58($hex)
	{
		if(strlen($hex)%2!=0)
		{
			die("encodeBase58: uneven number of hex characters");
		}
		$orighex=$hex;
		
		$chars="123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz";
		$hex= $this->decodeHex($hex);
		$return="";
		while (bccomp($hex,0)==1)
		{
			$dv=(string)bcdiv($hex,"58",0);
			$rem=(integer)bcmod($hex,"58");
			$hex=$dv;
			$return=$return.$chars[$rem];
		}
		$return=strrev($return);
		
		//leading zeros
		for($i=0;$i<strlen($orighex)&&substr($orighex,$i,2)=="00";$i+=2)
		{
			$return="1".$return;
		}
		
		return $return;
	}

	function hash160ToAddress($hash160,$addressversion=LTC_ADDRESSVERSION)
	{
		$hash160=$addressversion.$hash160;
		$check=pack("H*" , $hash160);
		$check=hash("sha256",hash("sha256",$check,true));
		$check=substr($check,0,8);
		$hash160=strtoupper($hash160.$check);
		return $this->encodeBase58($hash160);
	}

	function addressToHash160($addr)
	{
		$addr=$this->decodeBase58($addr);
		$addr=substr($addr,2,strlen($addr)-10);
		return $addr;
	}

	function checkAddress($addr,$addressversion=LTC_ADDRESSVERSION)
	{
		$addr=$this->decodeBase58($addr);
		if(strlen($addr)!=50)
		{
			return false;
		}
		$version=substr($addr,0,2);
		if(hexdec($version)!=hexdec($addressversion)) //Changed from ">" to "!=" for LTC
		{
			return false;
		}
		$check=substr($addr,0,strlen($addr)-8);
		$check=pack("H*" , $check);
		$check=strtoupper(hash("sha256",hash("sha256",$check,true)));
		$check=substr($check,0,8);
		return $check==substr($addr,strlen($addr)-8);
	}

	function hash160($data)
	{
		$data=pack("H*" , $data);
		return strtoupper(hash("ripemd160",hash("sha256",$data,true)));
	}

	function pubKeyToAddress($pubkey)
	{
		return $this->hash160ToAddress(hash160($pubkey));
	}

	function remove0x($string)
	{
		if(substr($string,0,2)=="0x"||substr($string,0,2)=="0X")
		{
			$string=substr($string,2);
		}
		return $string;
	}

}
?>
