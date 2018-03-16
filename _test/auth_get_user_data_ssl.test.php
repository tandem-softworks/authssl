<?php
include_once('inc/PluginAuthsslTest.php');
/**
 * @group plugin_authssl
 * @group plugins
 */

class AuthPluginAuthsslGetUserDataSSLTest extends PluginAuthsslTest {
    function setUp() {
        parent::setUp();
        $this->activateAuthssl();
    }

    function tearDown() {
        $this->unsetServerSSL();
        $this->restoreConf();
    }

    function testGetUserDataForKnownUserWithSSL() {
        $this->setServerSSL();
        $this->resetAuth();
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
        $this->setServerSSL('new_user', true);
        $this->resetAuth();
        $this->assertEquals(array('name' => 'SSL User',
                                  'mail' => 'admin@te.st',
                                  'grps' => array('user')),
                            $this->getAuth()->getUserData('new_user'));
        // User was created in authplain
        $this->assertEquals($user_count + 1,$this->getAuth()->getUserCount());
    }
}
