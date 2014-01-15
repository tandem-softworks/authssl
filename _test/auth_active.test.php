<?php
include_once('inc/PluginAuthsslTest.php');
/**
 * @group plugin_authssl
 * @group plugins
 */

class auth_plugin_authssl_active_test extends PluginAuthsslTest {
    function setUp() {
        parent::setUp();
        $this->activate_authssl();
        $this->reset_auth();
    }

    static function tearDownAfterClass() {
        self::restoreConf();
    }

    function testIsActive() {
        $this->assertAuthPluginAuthssl();
        $this->assertTrue(auth_plugin_authssl::is_active());
    }

    function testNotAuthenticated() {
        $request = new TestRequest();
        $response = $request->execute();

        $this->assertAuthPluginAuthssl();

        $query = $response->queryHTML('#permission_denied');
        $this->assertEquals(1,$query->size());
    }

    function testAuthenticated() {
        $this->setServerSSL();

        $this->reset_auth();
        $this->assertAuthPluginAuthssl();
        $this->assertTrue($this->get_auth()->success);

        $request = new TestRequest();
        $request->setSession(DOKU_COOKIE,$_SESSION[DOKU_COOKIE]);
        $request->setServer('REMOTE_USER',$_SERVER['REMOTE_USER']);

        global $INFO; /* otherwise $INFO does not survive */
        $response = $request->execute();

        $query = $response->queryHTML('#permission_denied');
        $this->assertEquals(0,$query->size());
    }

    function assertAuthPluginAuthssl() {
        $this->assertInstanceOf('auth_plugin_authssl',$this->get_auth());
    }
}
