<?php
/**
 * Api Doc https://svn.apache.org/repos/asf/subversion/trunk/notes/http-and-webdav/webdav-protocol
 */
namespace CaptainJas\Connectors\Watcher;

use CaptainJas\Connectors\Watcher;

/**
 * Class Subversion
 * @package CaptainJas\Connectors\Watcher
 */
abstract class Subversion extends WatcherAbstract
{

    const ACTION_ADD = 'added-path';
    const ACTION_MOD = 'modified-path';
    const ACTION_DEL = 'deleted-path';
    const NODE_FILE = 'file';
    const NODE_DIR = 'dir';
    protected $_svnurl;
    protected $_svnuser;
    protected $_svnpass;
    protected $_svnclient;

    /**
     * @param $url
     * @param bool $user
     * @param bool $pass
     */
    public function __construct($url, $user = false, $pass = false)
    {
        $this->_svnurl = $url;
        $this->_svnuser = $user;
        $this->_svnpass = $pass;
        parent::__construct();
    }

    /**
     * @param bool $vfrom
     * @param bool $vto
     * @return array
     */
    protected function _getRepositoryLogs($vfrom = false, $vto = false)
    {
        if (!$vfrom) {
            $vfrom = $this->_getData('version');
            if (!$vfrom) {
                $vfrom = 0;
            } else {
                $vfrom++;
            }
        }

        if (!$vto) {
            $vto = $this->_getVersion();
        }

        $options = array(
            'http' => array(
                'header' => "Content-type: text/xml\r\n" . $this->_getAuthorizationHeader(),
                'method' => 'REPORT',
                'content' =>
                    '<?xml version="1.0" encoding="utf-8"?>
                     <S:log-report xmlns:S="svn:">
                         <S:start-revision>' . $vfrom . '</S:start-revision>
                         <S:end-revision>' . $vto . '</S:end-revision>
                         <S:path></S:path>
                         <S:discover-changed-paths/>
                     </S:log-report>',
            ),
        );

        $context = stream_context_create($options);
        $response = file_get_contents($this->_svnurl . '!svn/bc/' . $vto, false, $context);

        $commitsXml = simplexml_load_string($response);

        $commitItems = $commitsXml->xpath('//S:log-item');
        $commits = array();
        foreach ($commitItems as $commitItem) {
            $version = $commitItem->xpath('./D:version-name');
            $comment = $commitItem->xpath('./D:comment');
            $author = $commitItem->xpath('./D:creator-displayname');
            $date = $commitItem->xpath('./S:date');
            $changes = array();
            foreach ($commitItem->xpath('./*[substring(name(), string-length(name()) - 4) = \'-path\']') as $path) {
                $nodeKind = $path->xpath('@node-kind');
                $action = str_replace('<S:', '', $path->getName());
                $changes[] = array(
                    'node-kind' => (string)reset($nodeKind),
                    'action' => $action,
                    'path' => (string)$path
                );
            }

            $commits[] = array(
                'version' => (string)reset($version),
                'comment' => (string)reset($comment),
                'author' => (string)reset($author),
                'date' => (string)reset($date),
                'changes' => $changes
            );
        }

        return $commits;
    }

    /**
     * @return int
     */
    protected function _getVersion()
    {
        $options = array(
            'http' => array(
                'header' => "Content-type: text/xml\r\n" . $this->_getAuthorizationHeader(),
                'method' => 'PROPFIND',
                'content' => '<?xml version="1.0" encoding="utf-8"?><propfind xmlns="DAV:"><prop><checked-in xmlns="DAV:"/></prop></propfind>',
            ),
        );

        $context = stream_context_create($options);
        $response = file_get_contents($this->_svnurl . '!svn/vcc/default', false, $context);
        $startToken = '!svn/bln/';
        $response = substr($response, strpos($response, $startToken) + strlen($startToken));
        $response = substr($response, 0, strpos($response, '</D:href>'));

        return (int)$response;
    }

    /**
     * @return string
     */
    protected function _getAuthorizationHeader()
    {
        return 'Authorization: Basic ' . base64_encode($this->_svnuser . ":" . $this->_svnpass) . "\r\n";
    }

    /**
     * @return array
     */
    protected function _getLocks()
    {
        $options = array(
            'http' => array(
                'header' => "Content-type: text/xml\r\n" . $this->_getAuthorizationHeader(),
                'method' => 'REPORT',
                'content' => '<?xml version="1.0" encoding="utf-8"?><S:get-locks-report xmlns:S="svn:" xmlns:D="DAV:"></S:get-locks-report>',
            ),
        );

        $context = stream_context_create($options);
        $response = file_get_contents($this->_svnurl, false, $context);

        $locksXml = simplexml_load_string($response);

        $lockItems = $locksXml->xpath('//S:lock');
        $locks = array();
        foreach ($lockItems as $lockItem) {
            $token = $lockItem->xpath('./S:token');
            $path = $lockItem->xpath('./S:path');
            $comment = $lockItem->xpath('./S:comment');
            $author = $lockItem->xpath('./S:owner');
            $date = $lockItem->xpath('./S:creationdate');

            $locks[(string)reset($token)] = array(
                'path' => (string)reset($path),
                'comment' => (string)reset($comment),
                'author' => (string)reset($author),
                'date' => (string)reset($date),
            );
        }

        return $locks;
    }

    /**
     * @return string
     */
    protected function _getDataIdentifier()
    {
        return md5(join('|', array($this->_svnurl, $this->_svnuser, $this->_svnpass, $this->_getClass())));
    }
}