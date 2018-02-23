<?php
/**
 * @file
 * Contains Para\Exception\GroupNotFoundException.php.
 */

namespace Para\Exception;

/**
 * Class GroupNotFoundException.
 *
 * @package Para\Exception
 */
class GroupNotFoundException extends \Exception
{
    public function __construct($groupName)
    {
        parent::__construct('The group "' . $groupName . '" was not found."');
    }
}
