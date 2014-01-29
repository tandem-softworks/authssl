<?php
include_once('inc/PluginAuthsslTest.php');
/**
 * @group plugin_authssl
 * @group plugins
 */

class AuthPluginAuthsslGetUsersTest extends PluginAuthsslTest {
    private static $user_count;
    static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        self::restoreConf();
        self::$user_count = 1;
    }

    function setUp() {
        parent::setUp();
        $this->activateAuthssl();
        $this->resetAuth();
    }

    function testCanDo() {
        $this->assertCanDo('getUsers','getUserCount');
    }

    function testGetUsers() {
        $this->assertEquals(array('testuser'),array_keys($this->getAuth()->retrieveUsers()));
    }

    function testGetUserCount() {
        $this->assertEquals(self::$user_count,$this->getAuth()->getUserCount());
    }
}
