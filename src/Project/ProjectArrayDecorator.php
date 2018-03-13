<?php

namespace Para\Project;

use Para\Decorator\EntityArrayDecoratorInterface;
use Para\Entity\ProjectInterface;

/**
 * Class ProjectArrayDecorator.
 *
 * @package Para\Project
 */
class ProjectArrayDecorator implements EntityArrayDecoratorInterface
{
    /**
     * The project to decorate.
     *
     * @var ProjectInterface
     */
    private $project;

    /**
     * ProjectArrayDecorator constructor.
     *
     * @param ProjectInterface $project The project to decorate.
     */
    public function __construct(ProjectInterface $project)
    {
        $this->project = $project;
    }

    /**
     * Returns an array of the entity data.
     *
     * @return array The entity data.
     */
    public function asArray(): array
    {
        $data = [
            'name' => $this->project->getName(),
            'path' => $this->project->getPath(),
        ];
        if (($foregroundColor = $this->project->getForegroundColor())) {
            $data['foreground_color'] = $foregroundColor;
        }
        if (($backgroundColor = $this->project->getBackgroundColor())) {
            $data['background_color'] = $backgroundColor;
        }

        return $data;
    }
}
