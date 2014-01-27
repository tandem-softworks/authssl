<?php

abstract class PluginAuthsslTest extends DokuWikiTest {
    function setUp() {
        $this->pluginsEnabled[] = 'authssl';
        parent::setUp();
        $this->oldAuth = $this->getAuth();
    }

    function tearDown() {
        $this->setAuth($this->oldAuth);
    }

    static function restoreConf() {
        TestUtils::rcopy(TMP_DIR, DOKU_UNITTEST.'conf');
    }

    function resetAuth() {
        global $DOKU_PLUGINS;
        $DOKU_PLUGINS = NULL;

        $this->setAuth(NULL);
        auth_setup();
    }

    function activateAuthssl() {
        global $conf;
        $conf['authtype'] = 'authssl';
    }

    function getAuth() {
        global $auth;
        return $auth;
    }

    function setAuth($value) {
        global $auth;
        $auth = $value;
    }

    // SSL-Authentication-Data
    function setServerSSL() {
        $_SERVER['SSL_CLIENT_S_DN_userID'] = 'testuser';
        $_SERVER['SSL_CLIENT_S_DN_CN'] = 'SSL User';
        $_SERVER['SSL_CLIENT_S_DN_Email'] = 'admin@te.st';
    }

    function unsetServerSSL() {
        foreach(array_keys($_SERVER) as $key) {
            if (preg_match('/\ASSL/',$key)) unset($_SERVER[$key]);
        }
    }
  }
