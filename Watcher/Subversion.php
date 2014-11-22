<?php
/**
 * Created by PhpStorm.
 * User: jerome
 * Date: 22/11/14
 * Time: 19:56
 */

namespace CaptainJas\Watcher;


abstract class Subversion extends WatcherAbstract{

    protected $_svnurl;
    protected $_svnuser;
    protected $_svnpass;
    protected $_svnclient;

    public function __construct($url, $user = false, $pass = false){
        $this->_svnurl = $url;
        $this->_svnuser = $user;
        $this->_svnpass = $pass;
    }

    private function _getClient(){
        if(!$this->_svnclient) {
            require_once(JAS_ROOT . DS . 'phpsvnclient' . DS . 'phpsvnclient.php');

            $this->_svnclient = new phpsvnclient($this->_svnurl, $this->_svnuser, $this->_svnpass);
        }

        return $this->_svnclient;
    }
} 