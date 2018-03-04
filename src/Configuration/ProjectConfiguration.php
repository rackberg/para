<?php

namespace Para\Configuration;

use Para\Entity\ProjectInterface;
use Para\Factory\ProjectFactoryInterface;
use Para\Service\ConfigurationManagerInterface;

/**
 * Class ProjectConfiguration
 *
 * @package Para\Configuration
 */
class ProjectConfiguration implements ProjectConfigurationInterface
{
    /**
     * The configuration manager.
     *
     * @var \Para\Service\ConfigurationManagerInterface
     */
    private $configurationManager;

    /**
     * The project factory.
     *
     * @var \Para\Factory\ProjectFactoryInterface
     */
    private $projectFactory;

    /**
     * ProjectConfiguration constructor.
     *
     * @param \Para\Service\ConfigurationManagerInterface $configurationManager The configuration manager.
     * @param \Para\Factory\ProjectFactoryInterface $projectFactory The project factory.
     */
    public function __construct(
        ConfigurationManagerInterface $configurationManager,
        ProjectFactoryInterface $projectFactory
    ) {
        $this->configurationManager = $configurationManager;
        $this->projectFactory = $projectFactory;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getProject(string $projectName): ?ProjectInterface
    {
        $data = $this->configurationManager->getData();
        foreach ($data as $groupName => $projects) {
            if (isset($projects[$projectName])) {
                return $this->projectFactory->getProject(
                    $projectName,
                    $projects[$projectName]['path']
                );
            }
        }

        return null;
    }

}
