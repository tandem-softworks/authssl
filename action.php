<?php
  /**
   * DokuWiki Plugin authssl (Action Component)
   *
   * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
   * @author  Jörg Schray <joerg.schray@tandem-softworks.de>
   */

  // must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class action_plugin_authssl extends DokuWiki_Action_Plugin {
    public function __construct() {
        if ($this->is_active()) {
            $this->disable_login_action();
        }
    }

    /**
     * Registers a callback function for a given event
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object
     * @return void
     */
    public function register(Doku_Event_Handler $controller) {

        $controller->register_hook('ACTION_ACT_PREPROCESS', 'FIXME', $this, 'handle_action_act_preprocess');
        $controller->register_hook('HTML_REGISTERFORM_OUTPUT', 'FIXME', $this, 'handle_html_registerform_output');
   
    }

    /**
     * [Custom event handler which performs action]
     *
     * @param Doku_Event $event  event object by reference
     * @param mixed      $param  [the parameters passed as fifth argument to register_hook() when this
     *                           handler was registered]
     * @return void
     */

    public function is_active() {
        return auth_plugin_authssl::is_active();
    }

    public function handle_action_act_preprocess(Doku_Event &$event, $param) {
    }

    public function handle_html_registerform_output(Doku_Event &$event, $param) {
    }

    public static function disable_login_action() {
        global $conf;
        if ($conf['disableactions'] == '') {
            $conf['disableactions'] = 'login';
        }
        elseif (preg_match('/(?:\A|,)login(?:\Z|,)/',$conf['disableactions']) !== 1) {
            $conf['Matched'] = false;
            $conf['disableactions'] = $conf['disableactions'] . ',login';
        }
        else {
            $conf['Matched'] = true;
        }        
    }
}

// vim:ts=4:sw=4:et:
