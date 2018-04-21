<?php
// Class to encrypt/decrypt
// Copyright © 2018 Pathfinder Associates, Inc.
// Author Christopher Barlow
// version 1.1
// updated 04/19/2018 created


abstract class PAI_Crypt_Abstract {

	const version = "1.1";
	abstract function encrypt($data);
	abstract function decrypt($data);
}
	
class PAI_Crypt extends PAI_Crypt_Abstract {

	private $mykey;
	private $nonce;
	
	public function __construct($mykey,$nonce) {
		$this->mykey = $mykey;
		$this->nonce = $nonce;
	}
	
    public function __destruct()
    {
    }

	function encrypt ($data) {
		if (mb_strlen($this->mykey, '8bit') !== 32) {
            throw new Exception("Needs a 256-bit key!");
        }
        $ivsize = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($ivsize);
        
        $ciphertext = openssl_encrypt(
            $data,
            'aes-256-cbc',
            $this->mykey,
            OPENSSL_RAW_DATA,
            $iv
        );
        
        return $iv . $ciphertext;

	// end of encrypt
	}

	function decrypt ($data) {
		if (mb_strlen($this->mykey, '8bit') !== 32) {
            throw new Exception("Needs a 256-bit key!");
        }
        $ivsize = openssl_cipher_iv_length('aes-256-cbc');
        $iv = mb_substr($data, 0, $ivsize, '8bit');
        $ciphertext = mb_substr($data, $ivsize, null, '8bit');
        
        return openssl_decrypt(
            $ciphertext,
            'aes-256-cbc',
            $this->mykey,
            OPENSSL_RAW_DATA,
            $iv
        );
 
	// end of decrypt
	}
}	
// end of class	
?>