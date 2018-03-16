<?php
include_once('inc/PluginAuthsslTest.php');
/**
 * @group plugin_authssl
 * @group plugins
 */

class AuthPluginAuthsslGetUserDataNoSSLTest extends PluginAuthsslTest {
    function setUp() {
        parent::setUp();
        $this->activateAuthssl();
        $this->resetAuth();
    }

    function tearDown() {
        $this->restoreConf();
    }

    function testGetUserDataForKnownUserWithoutSSL() {
        $this->assertEquals(array('name' => 'Arthur Dent',
                                  'mail' => 'arthur@example.com',
                                  'grps' => array()),
                            $this->getAuth()->getUserData('testuser'));
    }

    function testGetUserDataForUnknownUserWithoutSSL() {
        $this->assertFalse($this->getAuth()->getUserData('apü90um ü039'));
    }
}
