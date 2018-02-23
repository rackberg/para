<?php
/**
 * @file
 * Contains Para\Service\Strategy\DisplayStrategyFactory.php.
 */

namespace Para\Service\Strategy;

use Para\Service\ProcessFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class DisplayStrategyFactory.
 *
 * @package Para\Service\Strategy
 */
class DisplayStrategyFactory
{
    /**
     * The process factory.
     *
     * @var \Para\Service\ProcessFactory
     */
    private $processFactory;

    /**
     * The event dispatcher.
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * DisplayStrategyFactory constructor.
     *
     * @param \Para\Service\ProcessFactory $processFactory
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function __construct(
        ProcessFactory $processFactory,
        EventDispatcherInterface $dispatcher
    ) {
        $this->processFactory = $processFactory;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Creates a new instance of the progress bar display strategy.
     *
     * @return \Para\Service\Strategy\DisplayProgressbarStrategy
     */
    public function createProgressBarDisplayStrategy()
    {
        return new DisplayProgressbarStrategy($this->processFactory, $this->dispatcher);
    }

    /**
     * Creates a new instance of the combined output display strategy.
     *
     * @return \Para\Service\Strategy\DisplayCombinedOutputStrategy
     */
    public function createCombinedOutputDisplayStrategy()
    {
        return new DisplayCombinedOutputStrategy($this->processFactory, $this->dispatcher);
    }
}
