# Para

[![Build Status](https://travis-ci.org/rackberg/para.svg?branch=master)](https://travis-ci.org/rackberg/para)
[![Dependency Status](https://dependencyci.com/github/rackberg/para/badge)](https://dependencyci.com/github/rackberg/para)
[![Current Version](https://img.shields.io/badge/release-2.1.0-0e5487.svg)](https://github.com/rackberg/para/releases)

A command-line tool for parallel execution of shell commands in multiple directories.

## How to use it?
These instructions will show you how to install `para` on your local machine and how to work with it.

### Prerequisites

In order to install `para` the following software is needed:
```
git
composer
```

### Installing

To install `para` you can choose one of the following methods.

#### Standalone (Recommended)
This is the easiest way to install `para` on your local machine.<br>
Just copy the following command-line and paste it into your terminal to run the automatic install script.
```
curl -L https://raw.githubusercontent.com/lrackwitz/para/master/tools/install.sh | sh
```

#### As composer package
This will install `para` into your global composer vendor folder.
```
composer global require lrackwitz/para
```

#### Manually
Follow these instructions if you want to install `para` manually:
```
# Clone the repository
git clone https://github.com/rackberg/para --branch <release-tag> <para-install-directory>

# Change to the install directory of para
cd <para-install-directory>

# Install the composer managed dependencies
composer install

# Create a symlink
ln -s <para-install-directory>/bin/para /usr/local/bin/para
```

If `para` has been installed correctly you should get the installed version by entering the following command:
```
para --version
```
 
### Configuring

Before you can use `para` you need at least to add the directories you want `para` to manage.
This can be done by executing the following command:
```
para add:project <project_name> <project_path> [<group_name>] [--foreground_color=<color>] [--background_color=<color>]
```
Arguments:
* The value of the argument `project_name` should be a unique single word to identify the directory you want to add.
* The value of the argument `project_path` should be the absolute path of the directory you want to manage.
* The value of the optional argument `group_name` should be a unique single word to identify a group of projects. The default value is `default`.  

#### Example
```
para add:project project1 /opt/my_first_project my_group --foreground_color=222
para add:project project2 /Users/user/second_project my_group --foreground_color=104
para add:project project3 /Users/user/third_project my_group --foreground_color=129
para add:project project4 /tmp/my_fourth_project --foreground_color=93 
```

This will result in two groups called `my_group` and `default`.<br>
The group `my_group` will contain `project1`, `project2` and `project3`.<br>
The group `default` will contain only `project4`.

color: To see which color code you can choose execute the script tools/colortable.sh

To see the current configuration enter this command:
```
para config
```

### Executing a command in multiple directories (para projects) at the same time
The following example shows what you need to do to let para execute a shell command in every project configured for
a specific group.

Open the para shell for a configured project group.
```
para open:shell [options] [--] <group>
``` 

#### Example
```
para open:shell my_group
```

And execute any shell command you like.
```
echo "foo"
```

After pressing enter the shell will output something like this:
```
project1:   foo
project2:   foo
project3:   foo
```

You can continue entering more shell commands or type `exit` to leave the `para shell`.

#### Example
Imagine `project1`, `project2` and `project3` have a `composer.json` file.
We changed something in the `composer.json` of `project2` and want to sync the changes to `project1` and `project3`.
```
para sync project2 composer.json project3, project1
```

### Showing a log by `para` project

Every command you execute in the interactive `para shell` will be logged.
Enter the following command to see which commands you've executed and what the output was.
```
para show:log <project>
```

#### Example
Show the log of `para` project `project2`.
```
para show:log project2
```

### Extending the functionality with your own code
To extend the functionality of `para` with your own code, you simply need to create a new composer
package. The `type` in the `composer.json` needs to be `para-project`.

See the example composer.json file:
```
{
    "name": "vendor/para-plugin-name",
    "description": "An example para plugin",
    "type": "para-plugin",
    "require": {
        "lrackwitz/para": "^2.0"
    }
}
```
 
### Installing a custom `para` plugin
Execute the following commands:
```
# Change into the directory where you installed para.
cd <para-install-path>

# Use composer to install the custom para plugin
composer require vendor/para-plugin-name
```

If everything worked, you can now use the extended functionality of your custom `para` plugin
when executing `para` as usual.

### Synchronization of files within `para` projects
In order to be able to synchronize files within `para` projects you need to install the `para-sync` plugin.
```
composer require lrackwitz/para-sync
```

The code of the para plugin is here: https://github.com/rackberg/para-sync 

## Contributing
Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on the code of conduct, and the process for creating issues or submitting pull requests.

## Versioning
This project uses [SemVer](https://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/rackberg/para/tags).

Have a look at the [CHANGELOG.md](CHANGELOG.md).

## Authors
* **Lars Rosenberg** - *Initial work* - [Para](https://github.com/rackberg/para)

## Credits
I want to say thank you to [comm-press GmbH](https://comm-press.de/) for supporting me developing `para`. 

## License
This project is licensed under the GPL-3.0-or-later License - see the [LICENSE.md](LICENSE.md) file for details.
