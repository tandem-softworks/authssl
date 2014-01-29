<?php
/**
 * DokuWiki Plugin authssl (Auth Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Jörg Schray <joerg.schray@tandem-softworks.de>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class auth_plugin_authssl extends DokuWiki_Auth_Plugin {
    private static $instance = NULL;

    /**
     * Check if authorization is done via this plugin.
     */
    public static function is_active() {
        global $auth;
        if (!$auth) return false;
        $auth->isCaseSensitive();
        return ($auth === self::$instance);
    }

    private $group_plugin_name = 'authplain';
    private $group_plugin = NULL;
    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct(); // for compatibility
        self::$instance = $this;

        $this->cando['modLogin']    = false; // can login names be changed?
        $this->cando['modPass']     = false; // can passwords be changed?
        $this->cando['modName']     = false; // can real names be changed?
        $this->cando['modMail']     = false; // can emails be changed?
        $this->cando['external']    = false; // does the module do external auth checking?
        $this->cando['logout']      = false; // can the user logout again? (eg. not possible with HTTP auth)
        global $plugin_controller;
        $this->group_plugin = $plugin_controller->load('auth', $this->group_plugin_name);

        if ($this->group_plugin) {
            foreach (array('getUsers',
                           'getUserCount',
                           'addUser',
                           'modLogin', // otherwise 'addUser' does not work
                           'delUser',
                           'modGroups',
                           'getGroups') as $capability) {
                $this->cando[$capability] = $this->group_plugin->canDo($capability);
            }
        }
        // intialize your auth system and set success to true, if successful
        if ($_SERVER['SSL_CLIENT_S_DN_userID'] == "") {
            msg($this->getLang('nocreds'), -1);
            $this->success = false;
            return;
        }
        else {
            $_SERVER['PHP_AUTH_USER'] = $_SERVER['SSL_CLIENT_S_DN_userID'];
            $_SERVER['PHP_AUTH_PW'] = '';
        }
        $this->success = true;
    }

    /**
     * Check user+password
     *
     * May be ommited if trustExternal is used.
     *
     * @param   string $user the user name
     * @param   string $pass the clear text password
     * @return  bool
     */
    public function checkPass($user, $pass) {
        return true; // return true if okay
    }

    /**
     * Return user info
     *
     * Returns info about the given user needs to contain
     * at least these fields:
     *
     * name string  full name of the user
     * mail string  email addres of the user
     * grps array   list of groups the user is in
     *
     * @param   string $user the user name
     * @return  array containing user data or false
     */
    public function getUserData($user) {
        $group_plugin_info = $this->getGroupPluginUserData($user);
        if ($user == $_SERVER['SSL_CLIENT_S_DN_userID']) {
            $info['name'] = $_SERVER['SSL_CLIENT_S_DN_CN'];
            $info['mail'] = $_SERVER['SSL_CLIENT_S_DN_Email'];

            if ($group_plugin_info === false) {
                $this->createUser($user,NULL,$info['name'],$info['mail']);
                $group_plugin_info = $this->getGroupPluginUserData($user);
            }
            else {
                $diff = array();
                foreach ($info as $key => $value) {
                    if ($value !== $group_plugin_info[$key]) $diff[$key] = $value;
                }
                $this->modifyUser($user,$diff);
            }

            $info['grps'] = $group_plugin_info ? $group_plugin_info['grps'] : array();
            return $info;
        }
        else {
            unset($group_plugin_info['pass']);
            return $group_plugin_info;
        }
    }

    private function getGroupPluginUserData($user) {
        return $this->delegate_to_group_plugin('getUserData',array($user),false);
    }
    /**
     * Bulk retrieval of user data
     *
     */
    public function retrieveUsers() {
        return $this->delegate_to_group_plugin('retrieveUsers',func_get_args(),array());
    }

    /**
     * Return a count of the number of user which meet $filter criteria
     */
    public function getUserCount() {
        return $this->delegate_to_group_plugin('getUserCount',func_get_args(),0);
    }

    /**
     * User management via delegation to $this->group_plugin
     */
    public function createUser() {
        $args = func_get_args();
        $args[1] = auth_pwgen($args[0]); // generate password - only used after switch to authplain
        return $this->delegate_to_group_plugin('createUser',$args,NULL);
    }
    public function deleteUsers() {
        return $this->delegate_to_group_plugin('deleteUsers',func_get_args(),0);
    }
    public function modifyUser() {
        return $this->delegate_to_group_plugin('modifyUser',func_get_args(),false);
    }

    /**
     * Delegation to $this->group_plugin
     */
    private function delegate_to_group_plugin($name,$args,$default_return) {
        return $this->group_plugin ?
            call_user_func_array(array($this->group_plugin,$name),$args) :
            $default_return;
    }

    /**
     * Return case sensitivity of the backend
     *
     * When your backend is caseinsensitive (eg. you can login with USER and
     * user) then you need to overwrite this method and return false
     *
     * @return bool
     */
    public function isCaseSensitive() {
        self::$instance = $this;
        return true;
    }

    /**
     * Sanitize a given username
     *
     * This function is applied to any user name that is given to
     * the backend and should also be applied to any user name within
     * the backend before returning it somewhere.
     *
     * This should be used to enforce username restrictions.
     *
     * @param string $user username
     * @return string the cleaned username
     */
    public function cleanUser($user) {
        return $user;
    }

    /**
     * Sanitize a given groupname
     *
     * This function is applied to any groupname that is given to
     * the backend and should also be applied to any groupname within
     * the backend before returning it somewhere.
     *
     * This should be used to enforce groupname restrictions.
     *
     * Groupnames are to be passed without a leading '@' here.
     *
     * @param  string $group groupname
     * @return string the cleaned groupname
     */
    public function cleanGroup($group) {
        return $group;
    }

    /**
     * Check Session Cache validity [implement only where required/possible]
     *
     * DokuWiki caches user info in the user's session for the timespan defined
     * in $conf['auth_security_timeout'].
     *
     * This makes sure slow authentication backends do not slow down DokuWiki.
     * This also means that changes to the user database will not be reflected
     * on currently logged in users.
     *
     * To accommodate for this, the user manager plugin will touch a reference
     * file whenever a change is submitted. This function compares the filetime
     * of this reference file with the time stored in the session.
     *
     * This reference file mechanism does not reflect changes done directly in
     * the backend's database through other means than the user manager plugin.
     *
     * Fast backends might want to return always false, to force rechecks on
     * each page load. Others might want to use their own checking here. If
     * unsure, do not override.
     *
     * @param  string $user - The username
     * @return bool
     */
    //public function useSessionCache($user) {
      // FIXME implement
    //}
}

// vim:ts=4:sw=4:et: