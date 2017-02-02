<?php

class CryptoModel extends Model
{
	function backend($params)
	{
		switch($params['a'])
		{
			case 'e':
				$text=$params['text'];
				$pw=$params['pw'];
				$hash=$this->getFreeHash();
				$enc = $this->encrypt($text,$pw,$hash);
                $this->createBin($enc,$hash);
				return array('status'=>'OK','hash'=>$hash,'result'=>$enc);
			break;

			case 'd':
				//decrypt($encrypted,$key,$hash)
			break;
		}
	}

	function encrypt($string,$key,$hash)
	{
		$salt = hash('sha512', $hash.SALT);
		$check = hash('sha512', substr($string, 0, 4).SALT);
		$p2key = $this->mySha512($key,$salt,512);
		$offset = $this->getOffset($p2key);
		$shortkey = substr($p2key, $offset,($offset+23));
		$iv = $this->iv();

		while(strlen($string)<4096)
		{
			$string = ' '.$string.' ';
		}

        $encrypted = base64_encode($iv.openssl_encrypt($check.$string, 'AES-256-CBC', $shortkey, 0, $iv));
		return $encrypted;
	}

	function iv()
	{
	    $secureIV = false;
        $iv_size = openssl_cipher_iv_length('AES-256-CBC');
        $iv = null;

	    while (!$secureIV) {
            $iv = openssl_random_pseudo_bytes($iv_size, $secureIV);
        }

		return $iv;
	}

	function decrypt($str,$key,$hash)
	{
		$salt = hash('sha512', $hash.SALT);
		$p2key = $this->mySha512($key,$salt,512);
		$offset = $this->getOffset($p2key);
		$shortkey = substr($p2key, $offset,($offset+23));
		

        $str = base64_decode($str);
		if ($str === false || strlen($str) < 32)
			return false;

		$iv = substr($str, 0, 16);
		$encrypted = substr($str, 16);

        $decrypted = trim(rtrim(openssl_decrypt($encrypted, 'AES-256-CBC', $shortkey, 0, $iv)));

		if ($decrypted === false || is_null($decrypted) || strlen($decrypted) < 4)
			return false;
		$dataHash = substr($decrypted, 0, 128);


		$data = trim(substr($decrypted, 128));
		if (hash('sha512', substr($data, 0, 4).SALT) !== $dataHash)
			return false;
			
		return $data;
	}

	function getOffset($hash)
	{
		for($i=0;$i<strlen($hash);$i++)
		{
			if(is_numeric($hash[$i]))
				return $hash[$i];
		}

		return 0;
	}

	function mySha512($str, $salt, $iterations,$length=false)
	{
	    for ($x=0; $x<$iterations; $x++)
	    {
	        $str = hash('sha512', $str . $salt);
	    }
	    if($length)
	    	$str = substr($str, -$length);
	    return $str;
	}

	function getFreeHash()
	{
		$basedir = ROOT.DS.'data'.DS.'cryptobins'.DS;
		$hash = hash('sha256', time().microtime().rand(100,100000).SALT);
		$filename = $basedir.$hash.'.txt';
		while(file_exists($filename))
		{
			$hash = hash('sha256', time().microtime().rand(100,100000).SALT);
			$filename = $basedir.$hash.'.txt';
		}

		return $hash;
	}
}