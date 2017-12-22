# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

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
