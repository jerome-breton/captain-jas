Captain Jas
===========

A really light php hooking and watcher system to inter-operate systems.

This app is composed of connectors of three diferent types :
- *Hooks* : Simple class intended to receive messages from a service
- *Watchers* : Connectors to be called frenquently to pull data from a service
- *Senders* : Connectors to send data to a service

Quickstart
----------

1. Clone this repository in a PHP server. You might want to expose the \pub folder with an HTTP(S) server.
2. Copy config.json.dist to config.json and complete it with your credentials
3. Add a commit hook to Gitlab : http://yourserver/hook.php?hook=gitlabToHall
4. Add a crontab job calling : php -f pub/watcher.php

In depth
--------

###config.json

This file is the main configuration file of Captain Jas. It as three sections :

####connectors

You can define here new namespaces where Captain Jas can find connectors. This namespaces must respect PSR-4 autoloading
and have the following structure :

    RootNamespace
        - Hook
            - MyHook.php    (should extend \CaptainJas\Connectors\Hook\HookAbstract.php)
        - Sender
            - MySender.php  (should extend \CaptainJas\Connectors\Sender\SenderAbstract.php)
        - Watcher
            - MyWatcher.php (should extend \CaptainJas\Connectors\Watcher\WatcherAbstract.php)
            
####hooks

This section will define the hooks that will be available via hook.php.

Each hook must be defined with the following structure :

    "gitlabToHall": {
      "hook": {
        "connector": "jas|gitlab_message",  /* jas will be mapped to namespace definition and gitlab_message will be resolved to Gitlab\Message */
        "params": []                        /* as we are defining a hook this will lead to new \CaptainJas\Connector\Hook\Gitlab\Message()      */
      },                                     
      "sender": {                            
        "connector": "jas|hall",
        "params": [
          "https://hall.com/api/1/services/generic/<roomId>",
          "GitLab",
          "https://about.gitlab.com/images/gitlab_logo.png"
        ]
      }
    }

###hook.php and watcher.php

```hook.php``` and ```watcher.php``` are both callable from command-line and http. Any parameter passed via query 
string can be passed in command line :

```http://yourserver/hook.php?hook=gitlabToHall``` is equivalend to ```php -f pub/hook.php -- --hook=gitlabToHall```




