<?php
/**
 * Add this file to Gitlab commit hook with http://<host>/gitlabToHall.php?roomurl=https://hall.com/api/1/services/generic/<roomid>
 *
 * The room Url is accessible by clicking Integration in Hall room, then choosing Incoming Webhooks
 */
require_once('bootstrap.php');

namespace CaptainJas;

$hook = new Hook\Gitlab();
$sender = new Sender\Hall($_GET['roomurl'], 'GitLab', 'https://about.gitlab.com/images/gitlab_logo.png');

$message = $hook->processRequest();
$sender->send($message);