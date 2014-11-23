<?php
/**
 * Api doc https://github.com/basecamp/bcx-api
 */
namespace CaptainJas\Connectors\Watcher;

use CaptainJas\Connectors\Watcher;

/**
 * Class Basecamp implements basic Basecamp access
 * @package CaptainJas\Watcher
 */
abstract class Basecamp extends WatcherAbstract
{
    protected $_accountId;
    protected $_user;
    protected $_pass;
    protected $_userAgent;
    protected $_url;

    /**
     * @param int $accountId Basecamp account id (as in https://basecamp.com/<accountId>
     * @param int $projectId Project id (as in https://basecamp.com/<accountId>/projects/<projectId>
     * @param string $username Basecamp username
     * @param string $password Basecamp password
     * @param string $ua UserAgent of this app (defaults to CaptainJas)
     * @param string $url Basecamp Url (defaults to https://basecamp.com/)
     */
    public function __construct($accountId, $projectId, $username, $password,
                                $ua = 'CaptainJas (jerome.breton@gmail.com https://github.com/jerome-breton/captain-jas/)',
                                $url = 'https://basecamp.com/'
    )
    {
        $this->_accountId = $accountId;
        $this->_projectId = $projectId;
        $this->_user = $username;
        $this->_pass = $password;
        $this->_userAgent = $ua;
        $this->_url = $url;
        parent::__construct();
    }

    /**
     * Gets a unique basecamp watcher identifier
     *
     * @return string
     */
    protected function _getDataIdentifier()
    {
        return md5(
            join(
                '|', array(
                    $this->_accountId,
                    $this->_projectId,
                    $this->_user,
                    $this->_pass,
                    $this->_userAgent,
                    $this->_url,
                    $this->_getClass()
                )
            )
        );
    }

    /**
     * Performs a Basecamp request
     *
     * @param string $resource Resource to request (projects, events, ...)
     * @param array $params HTTP params to add
     * @param string $method HTTP method (defaults to GET)
     * @param array $data body data to be json-encoded
     * @param string $api api version to compose url (defaults to /api/v1)
     * @return string
     */
    protected function _request($resource, $params = array(), $method = 'GET', $data = array(), $api = '/api/v1')
    {
        $options = array(
            'http' => array(
                'header' => "Content-type: text/json\r\n" . $this->_getAuthorizationHeader() . $this->_getUserAgentHeader(),
                'method' => $method,
                'content' => json_encode($data),
            ),
        );

        $context = stream_context_create($options);
        $response = file_get_contents($this->_getUrl($resource, $params, $api), false, $context);

        return json_decode($response);
    }

    /**
     * Return HTTP basic auth header
     * @return string
     */
    protected function _getAuthorizationHeader()
    {
        return 'Authorization: Basic ' . base64_encode($this->_user . ":" . $this->_pass) . "\r\n";
    }

    /**
     * Return HTTP user agent header
     * @return string
     */
    protected function _getUserAgentHeader()
    {
        return 'User-Agent: ' . $this->_userAgent . "\r\n";
    }

    /**
     * Build a basecamp request url
     *
     * @param string $resource Resource to request (projects, events, ...)
     * @param array $params HTTP params to add
     * @param string $api api version to compose url (defaults to /api/v1)
     * @param string $resourceFormat response format (defaults to json)
     * @return string
     */
    protected function _getUrl($resource, $params = array(), $api = '/api/v1', $resourceFormat = 'json')
    {
        return join(
            '', array(
                $this->_url, $this->_accountId, $api, '/projects/', $this->_projectId, '/', $resource, '.',
                $resourceFormat, '?', http_build_query($params)
            )
        );
    }
}
