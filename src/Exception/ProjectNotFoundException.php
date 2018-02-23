<?php
/**
 * @file
 * Contains Para\Exception\ProjectNotFoundException.php.
 */

namespace Para\Exception;

/**
 * Class ProjectNotFoundException.
 *
 * @package Para\Exception
 */
class ProjectNotFoundException extends \Exception
{
    public function __construct($projectName)
    {
        parent::__construct('The project "' . $projectName . '" was not found."');
    }
}
