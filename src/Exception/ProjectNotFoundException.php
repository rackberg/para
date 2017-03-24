<?php
/**
 * @file
 * Contains lrackwitz\Para\Exception\ProjectNotFoundException.php.
 */

namespace lrackwitz\Para\Exception;

/**
 * Class ProjectNotFoundException.
 *
 * @package lrackwitz\Para\Exception
 */
class ProjectNotFoundException extends \Exception
{
    public function __construct($projectName)
    {
        parent::__construct('The project "' . $projectName . '" was not found."');
    }
}
