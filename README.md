# Commandline Application to execute shell commands in multiple folders parallel at the same time.

[![CircleCI](https://circleci.com/gh/rackberg/para.svg?style=svg)](https://circleci.com/gh/rackberg/para)

This tool will make your life much easier if you have to do things again and again but in different folders.

> Please note that this software is still under heavy development and
can change anytime you do a `para self-update`.

# Installation

* Using curl: `curl -L https://raw.githubusercontent.com/lrackwitz/para/master/tools/install.sh | sh`

# How to use para as shell
To use `para` as shell just do the following:

    $ para shell group_name [-x <project1> -x <project2>]
    
Para opens itself as an interactive shell and asks for user input.
All commands you enter will be executed in every project configured in the group `group_name` except for the projects `project1` and `project2`.
The `-x <project>` parameters are optional.

# How to execute a single shell command
If you configured your projects like in the `Configuration` section below just type in..
    
    $ para execute group_name "pwd"
    
Para gets all projects you configured for the group `group_name` and executes the shell command `pwd` for each project.
The output of the command will be something like this:

    project2:       /Users/lrackwitz/projects/project2
    project3:       /Users/lrackwitz/projects/project3
    project1:       /Users/lrackwitz/projects/project1
    project4:       /Users/lrackwitz/projects/project4

Please note that `para` does a real time output for all projects that belongs to this group.

If you do not want that your shell command will be executed in every project of your
group, just `exclude` the projects you do not want to use:
    
    $ para execute group_name "ls -la" -x project1 -x project4
    
For each project `para` executed your shell command you can show a log.

    $ para show:log project3
    
The output will be something like that:
    
    /Users/lrackwitz/projects/project3
    
I hope you get the idea. You can do this with every shell command you can imagine.

eg. If you want to do a `composer install` in multiple configured projects just execute
    
    $ para execute group_name "composer install"
     
This command will get you a massive output on the console, because it executes the `composer install` command 
in every project at the same time!

Let me show you...

    project1:   Gathering patches for root package.
    project3:   Gathering patches for root package.
    project3:   Loading composer repositories with package information
    project2:   Gathering patches for root package.
    project1:   Loading composer repositories with package information
    Installing dependencies (including require-dev) from lock file
    project3:   Installing dependencies (including require-dev) from lock file
    project2:   Loading composer repositories with package information
    project2:   Installing dependencies (including require-dev) from lock file
    project3:   Package operations: 1 install, 56 updates, 1 removal
    project2:   Package operations: 1 install, 56 updates, 1 removal
    project2:   Deleting web/modules/contrib/changed_fields - deleted
    project3:   Deleting web/modules/contrib/changed_fields - deleted
    project4:   Gathering patches for root package.
    project4:   Loading composer repositories with package information
    project4:   Installing dependencies (including require-dev) from lock file
    project1:   Package operations: 1 install, 55 updates, 1 removal
    project1:   Deleting web/modules/contrib/changed_fields - deleted
    project4:   Package operations: 1 install, 54 updates, 0 removals

The project names will be color coded with a unique color for each project executed.

You can't see it here in the README, but you can see it on your console after downloading `para`.

# Configuration

In order to let `para` do what it can do best you have to add one or more `projects` to it's configuration.

So, let's begin...

## How to add a project?
Simply execute the add:project command like the following example:
    
    $ para add:project project_name /Users/lrackwitz/projects/my_project
     
 This will register the project called `project_name` as child of the group `default`in the configuration file of para.
 The path to the project has to be absolute.
 
 But wait, there is more!
 You can add the project to any group you like.
 
    $ para add:project project_name /my/project/path my_awesome_group
    
 This registers the project called `project_name` as child of the group `my_awesome_group`.

## How do I remove a project?
If you want to delete a project that you registered before just type
    
    $ para delete:project project_name
    
## Where is the configuration stored?
The configuration file is called `para.yml` and is stored in the `lrackwitz/para/config/` folder.
The file is human readable and it's okay to edit it by hand if you like.
But take care to use the correct Yaml Syntax.

## Show which groups and projects are configured
To see the configuration just type in the following command

    $ para show:config

Author
------
Lars Rackwitz-Rosenberg - <rackwitz.lars@gmail.com>

If you do have any questions or need help using `para` don't hesitate to contact me.

License
-------
`para` is licensed under the GPLv3 License - see the LICENSE file for details.
