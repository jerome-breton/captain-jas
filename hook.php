<?php
/**
 * Add this file to Gitlab commit hook with http://<host>/gitlabToHall.php?roomurl=https://hall.com/api/1/services/generic/<roomid>
 *
 * The room Url is accessible by clicking Integration in Hall room, then choosing Incoming Webhooks
 */
namespace CaptainJas;
require_once('bootstrap.php');

if (empty($_GET) || empty($_GET['roomurl'])) {
    dieInHelp();
}

$hook = new Hook\Gitlab\Message();
$sender = new Sender\Hall($_GET['roomurl'], 'GitLab', 'https://about.gitlab.com/images/gitlab_logo.png');

$message = $hook->process();
if ($message) {
    $sender->send($message);
}

function dieInHelp()
{
    $demoRoomUrl = 'https://hall.com/api/1/services/generic/3fea40b404dc43f105f2593f45357dea';
    $debugUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . '?roomurl=' . $demoRoomUrl . '&debug=';
    $pushJson
        = '{"before":"95790bf891e76fee5e1747ab589903a6a1f80f22","after":"da1560886d4f094c3e6c9ef40349f7d38b5d27d7","ref":"refs/heads/master","user_id":4,"user_name":"John Smith","project_id":15,"repository":{"name":"Diaspora","url":"git@example.com:diaspora.git","description":"","homepage":"http://example.com/diaspora"},"commits":[{"id":"b6568db1bc1dcd7f8b4d5a946b0b91f9dacd7327","message":"Update Catalan translation to e38cb41.","timestamp":"2011-12-12T14:27:31+02:00","url":"http://example.com/diaspora/commits/b6568db1bc1dcd7f8b4d5a946b0b91f9dacd7327","author":{"name":"Jordi Mallach","email":"jordi@softcatala.org"}},{"id":"da1560886d4f094c3e6c9ef40349f7d38b5d27d7","message":"fixed readme","timestamp":"2012-01-03T23:36:29+02:00","url":"http://example.com/diaspora/commits/da1560886d4f094c3e6c9ef40349f7d38b5d27d7","author":{"name":"GitLab dev user","email":"gitlabdev@dv6700.(none)"}}],"total_commits_count":4}';
    $mergeRequestJson
        = '{"object_kind":"merge_request","object_attributes":{"id":99,"target_branch":"master","source_branch":"ms-viewport","source_project_id":14,"author_id":51,"assignee_id":6,"title":"MS-Viewport","created_at":"2013-12-03T17:23:34Z","updated_at":"2013-12-03T17:23:34Z","st_commits":null,"st_diffs":null,"milestone_id":null,"state":"opened","merge_status":"unchecked","target_project_id":14,"iid":1,"description":"","source":{"name":"awesome_project","ssh_url":"ssh://git@example.com/awesome_space/awesome_project.git","http_url":"http://example.com/awesome_space/awesome_project.git","visibility_level":20,"namespace":"awesome_space"},"target":{"name":"awesome_project","ssh_url":"ssh://git@example.com/awesome_space/awesome_project.git","http_url":"http://example.com/awesome_space/awesome_project.git","visibility_level":20,"namespace":"awesome_space"},"last_commit":{"id":"da1560886d4f094c3e6c9ef40349f7d38b5d27d7","message":"fixed readme","timestamp":"2012-01-03T23:36:29+02:00","url":"http://example.com/awesome_space/awesome_project/commits/da1560886d4f094c3e6c9ef40349f7d38b5d27d7","author":{"name":"GitLab dev user","email":"gitlabdev@dv6700.(none)"}}}}';
    ?>

    <h1>GitLab to Hall hook</h1>
    <h2>A really light php hook for GitLab to display pushes and merge requests to Hall.
    <h2>
    <h3>Usage :</h3>

    <p>Add this file to Gitlab commit hook with
        <code>http://<?php echo $_SERVER['HTTP_HOST'], $_SERVER['SCRIPT_NAME'] ?>
            ?roomurl=https://hall.com/api/1/services/generic/&lt;roomid&gt;</code>
        with the correct room url. Room url is accessible by clicking Integration in Hall room, then choosing Incoming
        Webhooks</p>

    <h3>Debug :</h3>

    <p>You can also add a <code>debug</code> param to the url to simulate Gitlab message.

    <p>This script will use this demo room for following urls : <code><?php echo $demoRoomUrl; ?></code></p>
    <ul>
        <li><a href="<?php echo $debugUrl . urlencode($pushJson); ?>">Push demo</a></li>
        <li><a href="<?php echo $debugUrl . urlencode($mergeRequestJson); ?>">Merge Request demo</a></li>
    </ul>
    <?php
    exit(-1);
}