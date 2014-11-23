Captain Jas
===========

**A really light php hooking and watcher system to inter-operate systems.**

This app is composed of connectors of three different types :
- **Hooks** : Simple class intended to receive messages from a service
- **Watchers** : Connectors to be called frequently to pull data from a service
- **Senders** : Connectors to send data to a service

Quickstart
----------

1. Clone this repository in a PHP server. You might want to expose the `/pub` folder with an HTTP(S) server.
2. Copy `config.json.dist` to `config.json` and complete it with your credentials
3. Add a commit hook to Gitlab : `http://yourserver/hook.php?hook=gitlabToHall`
4. Add a crontab job calling : `php -f pub/watcher.php`

In depth
--------

###config.json

This file is the main configuration file of Captain Jas. 

In this file connectors will be selected via a json config :

```json
"watcher": {
  "connector": "jas|subversion_commit_message",
  "params": [ "http://mysvnrepo.example.com", "username", "password" ]
}
```

In the example above, you will see that connector is a string like `jas|subversion_commit_message`. 

The first term (left of the pipe `|`), will be mapped to connectors section of the configuration to find the 
prefix of the namespace. 

The second term will be resolved as a path to the class including the end of the namespace. Underscores `_` will 
be replaced by namespace separator `\` and terms will get a upper capital letter. 

Based on the first key name, Hook, Sender or Watcher will be use to join this two parts.

The params array will be passed in the same order to the `__construct()` of the connector.

Example :

The example above will be resolved to `\CaptainJas\Connector\Hook\Gitlab\Message`, so something like this will be
run :

```php
new \CaptainJas\Connector\Hook\Gitlab\Message('http://mysvnrepo.example.com','username','password');
```

The config.json is composed of three sections :

####connectors section

You can define here new namespaces where Captain Jas can find connectors. This namespaces must respect PSR-4 autoloading
and have the following structure (each section is optional) :

    - RootNamespace
      - Hook
        - MyHook.php    
      - Sender
        - MySender.php
      - Watcher
        - MyWatcher.php
        
Your extensions connectors should extends CaptainJas abstract classes (found in `\CaptainJas\Connectors\*\*Abstract.php`)
or at least respond to the same public methods. More on this in the framework section of this document.
            
####hooks section

This section will define the hooks that will be available via `hook.php`.

Each hook must be defined with the following structure :

```json
"gitlabToHall": {
  "hook": {
    "connector": "jas|gitlab_message",  
    "params": []                        
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
```
    
The connector defined in `hook` processing result will be sent using connector defined in `sender`.

####watchers section

This section will define the watchers that will be available via `watcher.php`.

Each hook must be defined with the following structure :

```json
"basecampToHall": {
  "watcher": {
    "connector": "jas|basecamp_events_message",
    "params": [ "<account id>", "<project id>", "<user email>", "<user password>" ]
  },
  "sender": {
    "connector": "jas|hall",
    "params": [
      "https://hall.com/api/1/services/generic/<roomId>",
      "Basecamp",
      "https://avatars1.githubusercontent.com/u/13131?v=3&s=200"
    ]
  }
}
```
   
The connector defined in `watcher` processing result will be sent using connector defined in `sender`.

###hook.php and watcher.php

`hook.php` and `watcher.php` are both callable from command-line and http. Any parameter passed via query 
string can be passed in command line :

`http://yourserver/hook.php?hook=gitlabToHall` is equivalent to `php -f pub/hook.php -- --hook=gitlabToHall`

**hook.php** as only one required parameter `hook` that must correspond to a hook defined in config.json.

**watcher.php** as only one parameter `watcher` that must correspond to a watcher defined in config.json. 
If `watcher` is ommited, then every watchers are called in sequence.


Connectors
----------

###Hooks

**jas|gitlab_message** 

Will catch GitLab post commit hooks to send messages in case of push and merge request
creation or update. This connector does not need params.

###Senders

**jas|hall** 

Will send messages to a Hall Chat Room. You need to give this parameters :
- The complete api room url (ie: https://hall.com/api/1/services/generic/<roomId>). This url can be found
in the target chat room in choosing to add *Incomming Webhooks Integration*.
- The bot name. This will appear in chat as the name of the user
- The bot icon url. Hall recommand not to give a picture bigger than 100x100.

###Watchers

**jas|basecamp_events_message** 

Will check basecamp events feed and give you new events on change. This connector uses this parameters :
- The account id (this can be found in basecamp url : https://basecamp.com/{account id}/projects/{project id}
- The project id (this can be found in basecamp url : https://basecamp.com/{account id}/projects/{project id}
- Your username
- Your password
- (optional) The user agent string pushed to Basecamp that identifies this application. Defaults to : 
CaptainJas (jerome.breton@gmail.com https://github.com/jerome-breton/captain-jas/)
- (optional) Base url (defaults to https://basecamp.com)

**jas|subversion_commit_message**

Will check a subversion repository and alert you of new commits. This connector uses this parameters :
- The repository root url without /trunk/
- (optional) svn username
- (optional) svn password

**jas|subversion_locks_message**

Will check a subversion repository and alert you of new locks or locks releases. This connector uses this 
parameters :
- The repository root url without /trunk/
- (optional) svn username
- (optional) svn password

Framework
---------

###HookAbstract

Nothing to say for now. You must implement `process()` that will be called when hook is triggered and will
return data for senders.

###WatcherAbstract

Extends HookAbstract. This class also brings you methods to save the state of a system so you can know when
it changes, or make a diff.

You must implement `_getDataIdentifier()` that will return the unique key that will be used for this 
instance state file.

You will then be able to use `_getData($key)` and `_saveData($key, $val)` to read and write data. 
`_getData($key)` will return `null` if the key is not set. You can set a value to null to delete it via 
`_saveData($key, null)`

You must implement `process()` that will be called when watcher is triggered.

###SenderAbstract

Nothing to say for now. You must implement `send($data)` that will be called when hook is triggered.

**Sender\Message** is an abstract class implementing a message sender.



