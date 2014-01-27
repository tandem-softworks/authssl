<?php
include_once('inc/PluginAuthsslTest.php');

/**
 * @group plugin_authssl
 * @group plugins
 */

class auth_plugin_authssl_inactive_test extends PluginAuthsslTest {
    function setUp() {
        parent::setUp();
        $this->resetAuth();
    }

    function testIsNotActive() {
        $this->assertFalse(auth_plugin_authssl::is_active());
        $this->assertNotInstanceOf('auth_plugin_authssl',$this->getAuth());
    }

    function testAuthNotInitialized() {
        $this->setAuth(NULL);
        $this->assertFalse(auth_plugin_authssl::is_active());
        $this->assertNotInstanceOf('auth_plugin_authssl',$this->getAuth());
    }
}
