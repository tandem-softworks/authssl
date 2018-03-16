<?php

abstract class PluginAuthsslTest extends DokuWikiTest {
    protected $oldAuth = NULL;

    function setUp() {
        $this->pluginsEnabled[] = 'authssl';
        parent::setUp();
        $this->oldAuth = $this->getAuth();
        $this->unsetServerSSL();
    }

    function tearDown() {
        $this->setAuth($this->oldAuth);
        $this->unsetServerSSL();
    }

    function assertCanDo() {
        foreach(func_get_args() as $capability) {
            $this->assertTrue($this->getAuth()->canDo($capability),$capability);
        }
    }

    static function restoreConf() {
        TestUtils::rcopy(TMP_DIR, DOKU_UNITTEST.'conf');
    }

    // Reinitialization of auth-Plugin
    function resetAuth() {
        $GLOBALS['DOKU_PLUGINS'] = NULL;
        $this->setAuth(NULL);
        auth_setup();
    }

    function activateAuthssl() {
        $GLOBALS['conf']['authtype'] = 'authssl';
    }

    // Accessing global $auth
    function &getAuth() {
        return $GLOBALS['auth'];
    }

    function setAuth($value) {
        $GLOBALS['auth'] = $value;
    }

    // SSL-Authentication-Data
    function setServerSSL($user = 'testuser', $proxy = false) {
        if ($proxy) {
            $_SERVER['REQUEST_SCHEME'] = 'http';
            $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        }
        else {
            $_SERVER['REQUEST_SCHEME'] = 'https';
            unset($_SERVER['HTTP_X_FORWARDED_PROTO']);
        }
        $_SERVER['SSL_CLIENT_S_DN_userID'] = $user;
        $_SERVER['SSL_CLIENT_S_DN_CN'] = 'SSL User';
        $_SERVER['SSL_CLIENT_S_DN_Email'] = 'admin@te.st';
    }

    function unsetServerSSL() {
        foreach(array_keys($_SERVER) as $key) {
            if (preg_match('/\ASSL/',$key)) unset($_SERVER[$key]);
        }
        $_SERVER['REQUEST_SCHEME'] = 'http';
        unset($_SERVER['HTTP_X_FORWARDED_PROTO']);
    }

    // Utility
    function arrayGet($array,$key) {
        return $array[$key];
    }
  }
