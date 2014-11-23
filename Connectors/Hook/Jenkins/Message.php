<?php
namespace CaptainJas\Connectors\Hook\Jenkins;

use CaptainJas\Connectors\Hook\Jenkins;

class Message extends Jenkins
{

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
    protected function _processRequest($data)
    {
        if (empty($data)) {
            return false;
        }

        $message = 'Jenkins build <a target="jenkins" href="' . $data->build->full_url . '">#' .
            $data->build->number . ' of ' . $data->name . '</a> is <b>' . $data->build->phase . '</b>';

        if (isset($data->build->status)) {
            $message .= ' with status <b>' . $data->build->status . '</b>';
        }

        if (isset($data->build->scm) && $data->build->scm->url) {
            $message .= $this->_processScm($data->build->scm);
        }

        if (isset($data->build->artifacts)) {
            $message .= $this->_processArtifcats($data->build->artifacts);
        }

        return new \CaptainJas\Utils\Message($message);
    }

    /**
     * {
     *     "url": "https://github.com/evgeny-goldin/asgard.git",
     *     "branch": "origin/master",
     *     "commit": "c6d86dc654b12425e706bcf951adfe5a8627a517"
     * }
     * @param $artifacts
     * @return string
     */
    protected function _processScm($scm)
    {
        $message = '<br>Built <code>' . $scm->url . '</code> ' .
            'branch <i>' . $scm->branch . '</i> ' .
            'on commit <b>' . substr($scm->commit, 0, 6) . '</b>';

        return $message;
    }

    /**
     * {
     *     "asgard.war": {
     *         "archive": "http://localhost:8080/job/asgard/18/artifact/asgard.war"
     *     },
     *     "asgard-standalone.jar": {
     *         "archive": "http://localhost:8080/job/asgard/18/artifact/asgard-standalone.jar",
     *         "s3": "https://s3-eu-west-1.amazonaws.com/evgenyg-bakery/asgard/asgard-standalone.jar"
     *     }
     * }
     * @param $artifacts
     * @return string
     */
    protected function _processArtifcats($artifacts)
    {
        return '';
    }
}
