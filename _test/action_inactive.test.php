<?php
include_once('inc/PluginAuthsslTest.php');
/**
 * @group plugin_authssl
 * @group plugins
 */

class action_plugin_authssl_inactive_test extends PluginAuthsslTest {
    function testLoginEnabled() {
        global $conf;
        $this->assertEquals('',$conf['disableactions']);
    }
}
