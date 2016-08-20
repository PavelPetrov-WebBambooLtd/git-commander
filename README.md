GIT-Commander
=========

As a developer, I use git to store my code repositories and regularly have to push the updates to multiple folders on a lot of servers. The more repositories you have, however, the easier it is to forget to push the changes somewhere and the more time this manual task takes.

*GIT-Commander is a command line tool intended for developers using Linux*

# What GIT-commander does is:
Allows you to add all servers you have git repositories to. You enter the server name, port, user, password and secret, the secret is then used to encode the password with AES-256-CBC. Remember your secret! You will be asked for it anytime you push changes to the server.

Add local git repositories, and configure the remote folders you want them pushed to(you can also specify branch)

Check if all/any remote folders using a repository need updating and run a git pull on them.

# How to use
GIT-Commander uses sshpass(required) and knock(optional - if you need to update servers with port knocking)
So you should install sshpass by (Valid for Debian, Ubuntu, Linux Mint):
```bash
sudo apt-get install sshpass
```

After cloning the repository you have to run: 
```bash
composer update
```

After composer update install successfully, you have to run:
```bash
sudo php bin/console commander:generate-sh
```
To generate the Bash script and copy it to /usr/bin so that you can use the command locally.

After that the GIT-Commander is ready to use. You can type "commander" from anywhere in your system and should be able to use it.

# Command arguments

* `commander list-servers` - Lists all servers you've added
* `commander list-servers <ID>` - eg. "commander list-servers 0" - Lists details about server by ID
* `commander add-server` - A wizard to add a server
* `commander delete-server <ID>` - eg. "commander delete-server 0" - Deletes server from the list
* `commander list-repositories` - Lists all repositories you've added
* `commander list-repositories <ID>` - eg. "commander list-repositories 0" - Lists details about repository and its added remote folders
* `commander add-repository` - A wizard to add a repository and remote folders to keep synced
* `commander delete-repository <ID>` - eg. "Delete repository from the list by id"
* `commander check-repositories <ID>` - eg. "commander check-repositories 0" - Logs in to all servers you've specified and checks if the remote folders are in sync with the current repository. It takes into account the branch you've selected.
* `commander check-repositories <ID> --commonSecret=true` - Same as above but if you've used the same secret to all of your servers you can specify this option to only type the secret once, instead of on demand for each server.

*If at some point any lost soul decides to use this tool, run phpunit, because there is moderate to high probability I've broken something at some point*

*The tool is completely written in [Symfony3](http://symfony.com/)*
