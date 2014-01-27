<?php
include_once('inc/PluginAuthsslTest.php');
/**
 * @group plugin_authssl
 * @group plugins
 */

class AuthPluginAuthsslUserDataTest extends PluginAuthsslTest {
    function setUp() {
        parent::setUp();
        $this->activateAuthssl();
        $this->resetAuth();
    }

    function tearDown() {
        $this->restoreConf();
    }

    function testGetUserNoSSL() {
        $this->assertEquals(array('name' => 'Arthur Dent',
                                  'mail' => 'arthur@example.com',
                                  'grps' => array()),
                            $this->getAuth()->getUserData('testuser'));
    }

    function testGetUserInvalid() {
        $this->assertFalse($this->getAuth()->getUserData('apü90um ü039'));
    }

    function testGetUserSSL() {
        $this->setServerSSL();
        $this->assertEquals(array('name' => 'SSL User',
                                  'mail' => 'admin@te.st',
                                  'grps' => array()),
                            $this->getAuth()->getUserData('testuser'));

        // After SSL Data - user is updated
        $this->unsetServerSSL();
        $this->assertEquals(array('name' => 'SSL User',
                                  'mail' => 'admin@te.st',
                                  'grps' => array()),
                            $this->getAuth()->getUserData('testuser'));
    }
}
