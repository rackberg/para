<?php
/**
 * @file
 * Contains lrackwitz\Para\Service\Strategy\DisplayStrategyFactory.php.
 */

namespace lrackwitz\Para\Service\Strategy;

use lrackwitz\Para\Service\ProcessFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class DisplayStrategyFactory.
 *
 * @package lrackwitz\Para\Service\Strategy
 */
class DisplayStrategyFactory
{
    /**
     * The process factory.
     *
     * @var \lrackwitz\Para\Service\ProcessFactory
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
     * @param \lrackwitz\Para\Service\ProcessFactory $processFactory
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
     * @return \lrackwitz\Para\Service\Strategy\DisplayProgressbarStrategy
     */
    public function createProgressBarDisplayStrategy()
    {
        return new DisplayProgressbarStrategy($this->processFactory, $this->dispatcher);
    }

    /**
     * Creates a new instance of the combined output display strategy.
     *
     * @return \lrackwitz\Para\Service\Strategy\DisplayCombinedOutputStrategy
     */
    public function createCombinedOutputDisplayStrategy()
    {
        return new DisplayCombinedOutputStrategy($this->processFactory, $this->dispatcher);
    }
}
