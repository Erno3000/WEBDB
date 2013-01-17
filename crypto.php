<?php

/* Blowfish implementation.
 * Usage:
 *  - hashPassword($password) to create the hash
 *  - checkPassword($hash, $password) to check the password
 *
 */

    const BLOWFISH_PRE = '$2a$10$';
    const BLOWFISH_END = '$';
    const SALT_LEN = 21;

    function hashPassword($password, $salt = '') {
        if($salt == '') {
            $salt = generateSalt();
        }

        $hash = crypt($password, BLOWFISH_PRE . $salt . BLOWFISH_END);

        return substr($hash, 7);
    }

    function checkPassword($hash, $password) {
        return $hash == hashPassword($password, substr($hash, 0, SALT_LEN));
    }

    function generateSalt() {
        return substr(sha1(mt_rand()), 0, SALT_LEN);
    }

?>