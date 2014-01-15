<?php

abstract class PluginAuthsslTest extends DokuWikiTest {
    function setUp() {
        $this->pluginsEnabled[] = 'authssl';
        parent::setUp();
        $this->oldAuth = $this->get_auth();
    }

    function tearDown() {
        $this->set_auth($this->oldAuth);
    }

    static function restoreConf() {
        TestUtils::rcopy(TMP_DIR, DOKU_UNITTEST.'conf');
    }

    function reset_auth() {
        global $DOKU_PLUGINS;
        $DOKU_PLUGINS = NULL;

        $this->set_auth(NULL);
        auth_setup();
    }

    function activate_authssl() {
        global $conf;
        $conf['authtype'] = 'authssl';
    }

    function get_auth() {
        global $auth;
        return $auth;
    }

    function set_auth($value) {
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
