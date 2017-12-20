<?php
/**
 * @file
 * Contains lrackwitz\Para\Service\Output\OutputBuffer.php.
 */

namespace lrackwitz\Para\Service\Output;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class OutputBuffer.
 *
 * @package lrackwitz\Para\Service\Output
 */
class OutputBuffer extends ConsoleOutput implements BufferedOutputInterface
{


    public function __construct(OutputInterface $output)
    {
        parent::__construct($output->getVerbosity(), $output->isDecorated());
    }

    /**
     * {@inheritdoc}
     */
    public function write(
        $messages,
        $newline = false,
        $options = self::OUTPUT_NORMAL
    ) {

        // Add the message to write into the buffer array.
        $this->buffer[] = [
            'messages' => $messages,
            'newline' => $newline,
            'options' => $options,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        foreach ($this->buffer as $data) {
            parent::write($data['messages'], $data['newline'], $data['options']);
        }

        // Clear the buffer array.
        $this->buffer = [];
    }
}
