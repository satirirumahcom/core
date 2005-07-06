<?php

/**
* 
* Authenticate against a file generated by htpasswd.
* 
* @category Solar
* 
* @package Solar
* 
* @subpackage Solar_User
* 
* @author Paul M. Jones <pmjones@solarphp.com>
* 
* @license LGPL
* 
* @version $Id$
* 
*/

/**
* 
* Authenticate against a file generated by htpasswd.
* 
* Format for each line is "username:hashedpassword\n";
* 
* Automatically checks against DES, SHA, and apr1-MD5.
* 
* SECURITY NOTE: Default DES encryption will only check up to the first
* 8 characters of a password; chars after 8 are ignored.  This means
* that if the real password is "atechars", the word "atecharsnine" would
* be valid.  This is bad.  As a workaround, if the password provided by
* the user is longer than 8 characters, and DES encryption is being
* used, this class will *not* validate it.
* 
* @category Solar
* 
* @package Solar
* 
* @subpackage Solar_User
* 
*/

class Solar_User_Auth_Htpasswd extends Solar_Base {
	
	
	/**
	* 
	* User-provided configuration values.
	* 
	* Keys:
	* 
	* file => (string) Path to password file.
	* 
	* @access protected
	* 
	* @var array
	* 
	*/
	
	protected $config = array(
		'file'  => null,
	);


	/**
	* 
	* Validate a username and password.
	*
	* @param string $user Username to authenticate.
	* 
	* @param string $pass The plain-text password to use.
	* 
	* @return boolean|Solar_Error True on success, false on failure,
	* or a Solar_Error object if there was a file error.
	* 
	*/
	
	public function valid($user, $pass)
	{
		// force the full, real path to the file
		$file = realpath($this->config['file']);
		
		// does the file exist?
		if (! file_exists($file) || ! is_readable($file)) {
			return $this->error(
				'ERR_FILE_FIND',
				array('file' => $file),
				E_USER_ERROR
			);
		}
		
		// open the file
		$fp = @fopen($file, 'r');
		if (! $fp) {
			return $this->error(
				'ERR_FILE_OPEN',
				array('file' => $file),
				E_USER_ERROR
			);
		}
		
		// find the user's line in the file
		$len = strlen($user) + 1;
		$ok = false;
		while ($line = fgets($fp)) {
			if (substr($line, 0, $len) == "$user:") {
				// found the line, leave the loop
				$ok = true;
				break;
			}
		}
		
		// close the file
		fclose($fp);
		
		// did we find the username?
		if (! $ok) {
			// username not in the file
			return false;
		}
		
		// break up the pieces: 0 = username, 1 = encrypted (hashed)
		// password. may be more than that but we don't care.
		$tmp = explode(':', trim($line));
		$stored_hash = $tmp[1];
		
		// what kind of encryption hash are we using?  look at the first
		// few characters of the hash to find out.
		if (substr($stored_hash, 0, 6) == '$apr1$') {
		
			// use the apache-specific MD5 encryption
			$computed_hash = self::apr1($pass, $stored_hash);
			
		} elseif (substr($stored_hash, 0, 5) == '{SHA}') {
		
			// use SHA1 encryption.  pack SHA binary into hexadecimal,
			// then encode into characters using base64. this is per
			// Tomas V. V. Cox.
			$hex = pack('H40', sha1($pass));
			$computed_hash = '{SHA}' . base64_encode($hex);
			
		} else {
		
			// use DES encryption (the default).
			// 
			// Note that crypt() will only check up to the first 8
			// characters of a password; chars after 8 are ignored. This
			// means that if the real password is "atecharsnine", the
			// word "atechars" would be valid.  This is bad.  As a
			// workaround, if the password provided by the user is
			// longer than 8 characters, this method will *not* validate
			// it.
			//
			// is the password longer than 8 characters?
			if (strlen($pass) > 8) {
				// automatically reject
				return false;
			} else {
				$computed_hash = crypt($pass, $stored_hash);
			}
		}
		
		// did the hashes match?
		return $stored_hash == $computed_hash;
	}
	
	/**
	* 
	* APR compatible MD5 encryption.
	*
	* @author Mike Wallner <mike@php.net>
	* 
	* @author Paul M. Jones (minor modfications) <pmjones@solarphp.com>
	* 
	* @access public
	* 
	* @param string $plain plaintext to crypt
	* 
	* @param string $salt the salt to use for encryption
	* 
	* @return mixed
	* 
	*/
	
	public static function apr1($plain, $salt)
	{
		if (preg_match('/^\$apr1\$/', $salt)) {
			$salt = preg_replace('/^\$apr1\$([^$]+)\$.*/', '\\1', $salt);
		} else {
			$salt = substr($salt, 0,8);
		}
		
		$length  = strlen($plain);
		$context = $plain . '$apr1$' . $salt;
		
		$binary = md5($plain . $salt . $plain, true);
		
		for ($i = $length; $i > 0; $i -= 16) {
			$context .= substr($binary, 0, min(16 , $i));
		}
		for ( $i = $length; $i > 0; $i >>= 1) {
			$context .= ($i & 1) ? chr(0) : $plain[0];
		}
		
		$binary = md5($context, true);
		
		for($i = 0; $i < 1000; $i++) {
			$new = ($i & 1) ? $plain : $binary;
			if ($i % 3) {
				$new .= $salt;
			}
			if ($i % 7) {
				$new .= $plain;
			}
			$new .= ($i & 1) ? $binary : $plain;
			$binary = md5($new, true);
		}
		
		$p = array();
		for ($i = 0; $i < 5; $i++) {
			$k = $i + 6;
			$j = $i + 12;
			if ($j == 16) {
				$j = 5;
			}
			$p[] = self::_64(
				(ord($binary[$i]) << 16) |
				(ord($binary[$k]) << 8) |
				(ord($binary[$j])),
				5
			);
		}
		
		return '$apr1$' . $salt . '$' . implode($p) . 
			self::_64(ord($binary[11]), 3);
	}
	
	
	/**
	* 
	* Convert to allowed 64 characters for encryption.
	*
	* @author Mike Wallner <mike@php.net>
	* 
	* @author Paul M. Jones (minor modfications) <pmjones@solarphp.com>
	* 
	* @access protected
	* 
	* @param string $value
	* 
	* @param int $count
	* 
	* @return string
	* 
	*/
	
	protected static function _64($value, $count)
	{
		$charset = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		$result = '';
		while(--$count) {
			$result .= $charset[$value & 0x3f];
			$value >>= 6;
		}
		return $result;
	}
}
?>