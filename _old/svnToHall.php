<?php
namespace CaptainJas;
require_once('bootstrap.php');

$watch = new Watcher\Subversion\Commit\Message($_GET['svnurl'], $_GET['svnuser'], $_GET['svnpass']);
$sender = new Sender\Hall($_GET['roomurl'], 'Subversion', 'https://subversion.apache.org/images/svn-square.jpg');

$message = $watch->process();
if ($message) {
    $sender->send($message);
}