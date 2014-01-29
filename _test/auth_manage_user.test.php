<?php
include_once('inc/PluginAuthsslTest.php');
/**
 * @group plugin_authssl
 * @group plugins
 */

class AuthPluginAuthsslManageUserTest extends PluginAuthsslTest {
    private $user_count;
    function setUp() {
        parent::setUp();
        $this->activateAuthssl();
        $this->resetAuth();
        $this->user_count = $this->getAuth()->getUserCount();
        $this->assertTrue(false !== $this->getAuth()->getUserData('testuser'));
    }

    function testCanDo() {
        $this->assertCanDo('addUser','modLogin','delUser','modGroups');
    }

    function testCreateUser() {
        $this->assertNotNull($this->getAuth()->createUser('new',NULL,'Neo',NULL));
        $this->assertEquals($this->user_count + 1,$this->getAuth()->getUserCount());
        $this->assertEquals(array('name' => 'Neo','mail' => NULL,'grps' => array('user')),
                            $this->getAuth()->getUserData('new'));
    }

    function testDeleteUsers() {
        $this->assertEquals(1,$this->getAuth()->deleteUsers(array('testuser')));
        $this->assertEquals($this->user_count - 1,$this->getAuth()->getUserCount());
    }

    function testModifyUser() {
        $this->assertTrue($this->getAuth()->modifyUser('testuser',array('name' => 'Neo')));
        $this->assertEquals('Neo',$this->arrayGet($this->getAuth()->getUserData('testuser'),'name'));
        $this->assertEquals($this->user_count,$this->getAuth()->getUserCount());
    }

    function tearDown() {
        self::restoreConf();
    }
}
