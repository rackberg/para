<?php
/**
 * @file
 * Contains lrackwitz\Para\Service\ConfigurationManagerInterface.php.
 */

namespace lrackwitz\Para\Service;

use Symfony\Component\HttpFoundation\File\File;

interface ConfigurationManagerInterface
{
    /**
     * Adds a new group to the configuration.
     *
     * @param string $groupName The name of the group.
     *
     * @return bool True if the group has been added successfully, otherwise false.
     */
    public function addGroup($groupName);

    /**
     * Deletes an existing group and all its projects from the configuration.
     *
     * @param string $groupName The name of the group.
     *
     * @return bool True if the group has been deleted successfully, otherwise false.
     */
    public function deleteGroup($groupName);

    /**
     * Changes the name of an existing group.
     *
     * @param string $savedGroupName The name of the group to change.
     * @param string $newGroupName The new name of the group.
     */
    public function editGroupName($savedGroupName, $newGroupName);

    /**
     * Checks if a group exists in the configuration.
     *
     * @param string $groupName The name of the group
     *
     * @return bool Returns true if existing, otherwise false.
     */
    public function hasGroup($groupName);

    /**
     * Adds a new project to the configuration.
     *
     * If the group name is specified and the group does not exist,
     * the group will be created before. Finally the project will be added
     * as a child of this group.
     *
     * @param string $projectName The name of the project.
     * @param string $path The path where to find the project.
     * @param string $groupName (Optional) The name of the group. Defaults to 'default'.
     * @param string $foregroundColor (Optional) The foreground color.
     * @param string $backgroundColor (Optional) The background color.
     *
     * @return bool True if the project has been added successfully, otherwise false.
     */
    public function addProject(
        $projectName,
        $path,
        $groupName = 'default',
        $foregroundColor = '',
        $backgroundColor = ''
    );

    /**
     * Deletes an existing project.
     *
     * @param string $projectName The name of the project.
     */
    public function deleteProject($projectName);

    /**
     * Changes the name of an existing project.
     *
     * @param string $projectName The name of the project to change.
     * @param string $newProjectName The new name of the project.
     */
    public function editProjectName($projectName, $newProjectName);

    /**
     * Changes the path of the project.
     *
     * @param string $projectName The name of the project.
     * @param string $path The new path of the project.
     */
    public function editProjectPath($projectName, $path);

    /**
     * Reads all groups from the configuration.
     *
     * @return array An array with groups.
     */
    public function readGroups();

    /**
     * Reads the information of an existing group from the configuration.
     *
     * @param string $groupName The name of the group.
     *
     * @return string[] An array with information of the group.
     *
     * @throws \lrackwitz\Para\Exception\GroupNotFoundException If the group is not existing.
     */
    public function readGroup($groupName);

    /**
     * Reads the information of an existing project from the configuration.
     *
     * @param string $projectName The name of the project.
     *
     * @return string[] An array with information of the project.
     */
    public function readProject($projectName);

    /**
     * Checks if a project exists in the configuration.
     *
     * @param string $projectName The name of the project.
     *
     * @return bool Returns true if existing, otherwise false.
     */
    public function hasProject($projectName);

    /**
     * Returns the project the file is located at.
     *
     * @param \Symfony\Component\HttpFoundation\File\File $file
     *   The file that is in a project.
     *
     * @return \lrackwitz\Para\Entity\ProjectInterface|null
     *   The found project or null.
     */
    public function findProjectByFile(File $file);
}
