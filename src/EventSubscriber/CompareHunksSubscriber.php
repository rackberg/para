<?php
/**
 * @file
 * Contains lrackwitz\Para\EventSubscriber\CompareHunksSubscriber.php.
 */

namespace lrackwitz\Para\EventSubscriber;

use lrackwitz\Para\Event\CompareHunksEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class CompareHunksSubscriber.
 *
 * @package lrackwitz\Para\EventSubscriber
 */
class CompareHunksSubscriber implements EventSubscriberInterface
{
    const COMPARISON_MARK_BEGIN = '[PARA-COMPARE-BEGIN]';
    const COMPARISON_MARK_END = '[PARA-COMPARE-END]';

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            CompareHunksEvent::NAME => [
                ['onCompareHunks']
            ]
        ];
    }

    public function onCompareHunks(CompareHunksEvent $event)
    {
        // Check if the hunks match.
        if ($event->getHunk() === $event->getCompareHunk()) {
            $event->setMatching(true);

            // Add the hunk header to the hunk.
            $event->setHunk($event->getHunkHeader() . $event->getHunk());
            return;
        }
        // Check if the hunk is within the hunk used for comparison.
        if (false !== ($pos = strpos($event->getCompareHunk(), $event->getHunk()))) {
            // Add a special chars at the position where the hunk begins.
            $prepared = $this->addComparisonMarks($event->getCompareHunk(), $event->getHunk(), $pos);

            // Split the prepared string into an array by newline.
            $lines = array_values(array_filter(explode("\n", $prepared)));

            // Parse the hunk identifier.
            $identifier = $this->parseHunkIdentifier($event->getHunkIdentifier());

            // Get the line number where the hunk to patch begins.
            $lineNumber = array_search(self::COMPARISON_MARK_BEGIN, $lines);
            if (!$lineNumber) {
                // An error occured.
                // TODO: Write a log message.
                return;
            }

            // Adjust the line number.
            $identifier['-line'] = abs($identifier['-line']) + $lineNumber;
            $identifier['+line'] = abs($identifier['+line']) + $lineNumber;

            // Adjust the context.
            $identifier['-context'] = $this->countRows($lines, '-');
            $identifier['+context'] = $this->countRows($lines, '+');

            // Save the new hunk identifier.
            $event->setHunkIdentifier(sprintf(
                '@@ -%d,%d +%d,%d @@',
                $identifier['-line'],
                $identifier['-context'],
                $identifier['+line'],
                $identifier['+context']
            ));

            // Set the flag to indicate that this hunk can be used for the patch.
            $event->setMatching(true);
        }
    }

    private function addComparisonMarks(string $string, string $hunk, int $startPos)
    {
        $hunkLen = strlen($hunk);

        return sprintf(
            '%s%s%s%s%s',
            substr($string, 0, $startPos - 1),
            self::COMPARISON_MARK_BEGIN,
            substr($string, $startPos, strlen($hunk)),
            self::COMPARISON_MARK_END,
            substr($string, $startPos + $hunkLen)
        );
    }

    /**
     * @param string $hunkIdentifier
     *   The hunk identifier to parse.
     *
     * @return array
     *   The extracted data.
     */
    private function parseHunkIdentifier($hunkIdentifier)
    {
        // Get the hunk header data.
        preg_match('~^@@ (-\d+)[, ]?(?:(\d+) )?\+(\d+)[, ]?(?:(\d+) )?~m', $hunkIdentifier, $matches);

        return [
            '-line' => $matches[1],
            '-context' => $matches[2],
            '+line' => $matches[3],
            '+context' => $matches[4],
        ];
    }

    private function countRows(array $lines, string $sign)
    {
        $startLine = array_search(self::COMPARISON_MARK_BEGIN, $lines);
        $endLine = array_search(self::COMPARISON_MARK_END, $lines);

        // Remove the comparison marks from the lines.
        $lines[$startLine] = str_replace(self::COMPARISON_MARK_BEGIN, '', $lines[$startLine]);
        $lines[$endLine] = str_replace(self::COMPARISON_MARK_END, '', $lines[$endLine]);

        $rows = 0;

        for ($i = $startLine; $i <= $endLine; $i++) {
            // Check if the first char of the line is empty or the sign.
            if (!empty($lines[$i]{0}) && ($lines[$i]{0} === ' ' || $lines[$i]{0} === $sign)) {
                $rows++;
            }
        }

        return $rows;
    }
}
