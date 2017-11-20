# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Fixed
- `para --version` does not return the installed version

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