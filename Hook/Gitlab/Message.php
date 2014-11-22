<?php
namespace CaptainJas\Hook;

class Message extends \CaptainJas\Hook\Gitlab
{
    /**
     * Process push body messages
     * {
     *   "before": "95790bf891e76fee5e1747ab589903a6a1f80f22",
     *   "after": "da1560886d4f094c3e6c9ef40349f7d38b5d27d7",
     *   "ref": "refs/heads/master",
     *   "user_id": 4,
     *   "user_name": "John Smith",
     *   "project_id": 15,
     *   "repository": {
     *     "name": "Diaspora",
     *     "url": "git@example.com:diaspora.git",
     *     "description": "",
     *     "homepage": "http://example.com/diaspora"
     *   },
     *   "commits": [
     *     {
     *       "id": "b6568db1bc1dcd7f8b4d5a946b0b91f9dacd7327",
     *       "message": "Update Catalan translation to e38cb41.",
     *       "timestamp": "2011-12-12T14:27:31+02:00",
     *       "url": "http://example.com/diaspora/commits/b6568db1bc1dcd7f8b4d5a946b0b91f9dacd7327",
     *       "author": {
     *         "name": "Jordi Mallach",
     *         "email": "jordi@softcatala.org"
     *       }
     *     },
     *     {
     *       "id": "da1560886d4f094c3e6c9ef40349f7d38b5d27d7",
     *       "message": "fixed readme",
     *       "timestamp": "2012-01-03T23:36:29+02:00",
     *       "url": "http://example.com/diaspora/commits/da1560886d4f094c3e6c9ef40349f7d38b5d27d7",
     *       "author": {
     *         "name": "GitLab dev user",
     *         "email": "gitlabdev@dv6700.(none)"
     *       }
     *     }
     *   ],
     *   "total_commits_count": 4
     * }
     *
     * @param  array $data GitLab request object attributes
     * @return array   $message Message to send
     */
    protected function _processPush($data)
    {
        if (empty($data)) {
            return new \CaptainJas\Utils\Message();
        }

        $message = 'New push from <i>' . $data['user_name'] . '</i> in <b>' . str_replace('refs/heads/', '', $data['ref']) . '</b><code>';
        foreach ($data['commits'] as $commit) {
            $message .= $this->_displayCommit($commit);
        }
        $message .= '</code>';

        return new \CaptainJas\Utils\Message($message);
    }

    /**
     * Return the HTML of a commit
     *
     * @param array $commit
     * @param int $namePad pad and truncate names to this length
     * @return string
     */
    protected function _displayCommit($commit, $namePad = 20)
    {
        return '<br>' . join(' ', array(
            '<a href="' . $commit['url'] . '" target="gitlabhall">' . substr($commit['id'], 0, 6) . '</a>',
            '<i>' . substr(str_pad($commit['author']['name'], $namePad), 0, $namePad) . '</i>',
            $commit['message']
        ));
    }

    /**
     * Process merge body messages
     * {
     *   "object_kind": "merge_request",
     *   "object_attributes": {
     *     "id": 99,
     *     "target_branch": "master",
     *     "source_branch": "ms-viewport",
     *     "source_project_id": 14,
     *     "author_id": 51,
     *     "assignee_id": 6,
     *     "title": "MS-Viewport",
     *     "created_at": "2013-12-03T17:23:34Z",
     *     "updated_at": "2013-12-03T17:23:34Z",
     *     "st_commits": null,
     *     "st_diffs": null,
     *     "milestone_id": null,
     *     "state": "opened",
     *     "merge_status": "unchecked",
     *     "target_project_id": 14,
     *     "iid": 1,
     *     "description": "",
     *     "source": {
     *       "name": "awesome_project",
     *       "ssh_url": "ssh://git@example.com/awesome_space/awesome_project.git",
     *       "http_url": "http://example.com/awesome_space/awesome_project.git",
     *       "visibility_level": 20,
     *       "namespace": "awesome_space"
     *     },
     *     "target": {
     *       "name": "awesome_project",
     *       "ssh_url": "ssh://git@example.com/awesome_space/awesome_project.git",
     *       "http_url": "http://example.com/awesome_space/awesome_project.git",
     *       "visibility_level": 20,
     *       "namespace": "awesome_space"
     *     },
     *     "last_commit": {
     *       "id": "da1560886d4f094c3e6c9ef40349f7d38b5d27d7",
     *       "message": "fixed readme",
     *       "timestamp": "2012-01-03T23:36:29+02:00",
     *       "url": "http://example.com/awesome_space/awesome_project/commits/da1560886d4f094c3e6c9ef40349f7d38b5d27d7",
     *       "author": {
     *         "name": "GitLab dev user",
     *         "email": "gitlabdev@dv6700.(none)"
     *       }
     *     }
     *   }
     * }
     *
     * @param  array $data GitLab request object attributes
     * @return array $message Message to send
     */
    protected function _processMergeRequest($data)
    {
        if (empty($data)) {
            return new \CaptainJas\Utils\Message();
        }

        //Create merge url from last commit url
        $mergeUrl = $this->_getProjectUrlFromCommitUrl($data['last_commit']['url']) . '/merge_requests/' . $data['iid'];

        $message = 'Merge request </i><a href="' . $mergeUrl . '">#' . $data['iid'] . '</a></i> <b>' . $data['source_branch'] . ' &#10142; ' . $data['target_branch'] . '</b> ';
        if ($data['merge_status'] == 'unchecked') {
            $message .= 'as been <b>created</b>.';
        } else {
            $message .= 'is now <b>' . $data['state'] . '</b>.';
        }

        //$message .= '<code>' . $this->_displayCommit($data['last_commit']) . '</code>';

        return new \CaptainJas\Utils\Message($message);
    }
}