<?php
include_once('inc/PluginAuthsslTest.php');
/**
 * @group plugin_authssl
 * @group plugins
 */

class AuthPluginAuthsslGetUserDataTest extends PluginAuthsslTest {
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

    function testGetUserDataForKnownUserWithSSL() {
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

    function testGetUserDataForUnknownUserWithSSL() {
        $user_count = $this->getAuth()->getUserCount();
        $this->setServerSSL('new_user');
        $this->assertEquals(array('name' => 'SSL User',
                                  'mail' => 'admin@te.st',
                                  'grps' => array('user')),
                            $this->getAuth()->getUserData('new_user'));
        // User was created in authplain
        $this->assertEquals($user_count + 1,$this->getAuth()->getUserCount());
    }
}
