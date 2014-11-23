<?php
/**
 * Connector intended to work with https://wiki.jenkins-ci.org/display/JENKINS/Notification+Plugin
 *
 * Please configure Jenkins plugin to use :
 * "Format" : JSON
 * "Protocol": HTTP
 */
namespace CaptainJas\Connectors\Hook;

use CaptainJas\CaptainJas;

/**
 * Class Jenkins
 * @package CaptainJas\Connectors\Hook
 */
abstract class Jenkins extends HookAbstract
{

    /**
     * Processes the event
     */
    function process()
    {
        $request = $this->_getRequest();
        $responses = array();

        $response = $this->_processRequest($request);
        if ($response) {
            $responses[] = $response;
        }
        return $responses;
    }

    /**
     * Gets Jenkins hook data from the request body
     *
     * @return array
     */
    protected function _getRequest()
    {
        if ($debug = CaptainJas::p('debug')) {
            $body = urldecode($debug);
        } else {
            $body = file_get_contents('php://input');
        }
        $request = json_decode($body);

        return $request;
    }

    /**
     * Process push body messages
     *  {
     *      "name": "asgard",
     *      "url": "job/asgard/",
     *      "build": {
     *          "full_url": "http://localhost:8080/job/asgard/18/",
     *          "number": 18,
     *          "phase": "COMPLETED",
     *          "status": "SUCCESS",
     *          "url": "job/asgard/18/",
     *          "scm": {
     *              "url": "https://github.com/evgeny-goldin/asgard.git",
     *              "branch": "origin/master",
     *              "commit": "c6d86dc654b12425e706bcf951adfe5a8627a517"
     *          },
     *          "artifacts": {
     *              "asgard.war": {
     *                  "archive": "http://localhost:8080/job/asgard/18/artifact/asgard.war"
     *              },
     *              "asgard-standalone.jar": {
     *                  "archive": "http://localhost:8080/job/asgard/18/artifact/asgard-standalone.jar",
     *                  "s3": "https://s3-eu-west-1.amazonaws.com/evgenyg-bakery/asgard/asgard-standalone.jar"
     *              }
     *          }
     *      }
     *  }
     *
     * @param  stdClass $data GitLab request object attributes
     * @return array    $data processed
     */
    abstract protected function _processRequest($data);
}
