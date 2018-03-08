# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- Automatic registering of kernel.event_subscriber tagged services as event subscribers.

## [2.1.1] - 2018-03-08
### Added
- New plugin system to easily install custom para plugins.
- Several unit tests for commands.
### Removed
- Removed the sync command and extracted it into a new para plugin called lrackwitz/para-sync.
### Fixed
- Re-added lost command aliases `log`, `config` and `shell`

## [2.0.0] - 2018-01-22
### Added
- Projects can now have a fixed color (foreground and background) configurable in the para.yml. (See [#7](https://github.com/rackberg/para/issues/7))
- Added a shell script to see all available colors. The script is located under tools/colortable.sh.
- Added a [wiki page with upgrade instructions from version 1.6.0 to 2.0.0](https://github.com/rackberg/para/wiki/Upgrade-para-from-1.6.0-to-2.0.0-).
### Changed
- Improved the file change detection for the sync command.
 
## [1.6.0] - 2018-01-19
### Added
- More output of informations about the patch to sync to the target project.
### Changed
- Split services.yml into several files for better maintainability.
- Updated composer.json file.
### Fixed
- The regular expression to split hunks in a patch file.
 
## [1.5.0] - 2017-12-23
### Added
- The command `sync` to sync single files. See [PR #17](https://github.com/rackberg/para/pull/17)
- Added [CONTRIBUTING.md](CONTRIBUTING.md)
### Changed
- Improved the install script to install the latest release instead of the last commit.
- Updated [README.md](README.md)

## [1.4.0] - 2017-12-22
### Changed
- Improved asynchronous command execution algorithm.
- Improved project name coloring (supports up to 256 colors now).
- Changed to additively project logging.   
### Fixed
- The last command entered by the user will be now restored after pressing the down key when the history end has been reached. See issue [#16](https://github.com/rackberg/para/issues/16).

## [1.3.0] - 2017-11-19
### Added
- `para self-update --unstable` to update to the latest commit instead of the latest stable release.

### Fixed
- `para --version` does not return the installed version.
- Removed hard dependencies to the [ZSH](https://wiki.ubuntuusers.de/Zsh/)

## [1.2.0] - 2017-11-19
### Changed
- Improved the update script to abort when no update is needed.

### Fixed
- Para shell accidentally creates new line after printing the first Character (see [#10](https://github.com/rackberg/para/issues/10))

## [1.1.1] - 2017-11-18
### Fixed
- Detection and display of the current installed version / release.

## [1.1.0] - 2017-11-17
### Added
- Integrate the keyboard shortcut `ctrl+a` to jump to the first column of the current line in the shell.
- Integrate the keyboard shortcut `ctrl+e` to jump to the last column of the current line in the shell.
- Integrate the keyboard shortcut `ctrl+k` to delete all characters right from the cursor position in the shell.

## [1.0.0] - 2017-11-17
### Added
- This CHANGELOG file.

### Changed
- The command `self-update` updates from now on to the latest stable release.
