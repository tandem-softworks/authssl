<?php
include_once('inc/PluginAuthsslTest.php');
/**
 * @group plugin_authssl
 * @group plugins
 */

class action_plugin_authssl_active_test extends PluginAuthsslTest {

    function setUp() {
        parent::setUp();
        $this->activate_authssl();
        $this->reset_auth();
    }

    function getDisableactions() {
        global $conf;
        return $conf['disableactions'];
    }

    function setDisableactions($value) {
        global $conf;
        $conf['disableactions'] = $value;
    }

    function testLoginDisabled() {
        global $plugin_controller;
        $plugin_controller->load('action','authssl');
        $this->assertEquals('login',$this->getDisableactions());
    }

    function testDisableLoginAction() {
        $this->setDisableactions('index');
        action_plugin_authssl::disable_login_action();
        $this->assertEquals('index,login',$this->getDisableactions());

        action_plugin_authssl::disable_login_action();
        $this->assertEquals('index,login',$this->getDisableactions());

        $this->setDisableactions('login,index');
        action_plugin_authssl::disable_login_action();
        $this->assertEquals('login,index',$this->getDisableactions());

        $this->setDisableactions('backlink,login,index');
        action_plugin_authssl::disable_login_action();
        $this->assertEquals('backlink,login,index',$this->getDisableactions());
    }

}

