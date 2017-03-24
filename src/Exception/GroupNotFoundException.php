<?php
/**
 * @file
 * Contains lrackwitz\Para\Exception\GroupNotFoundException.php.
 */

namespace lrackwitz\Para\Exception;

/**
 * Class GroupNotFoundException.
 *
 * @package lrackwitz\Para\Exception
 */
class GroupNotFoundException extends \Exception
{
    public function __construct($groupName)
    {
        parent::__construct('The group "' . $groupName . '" was not found."');
    }
}
