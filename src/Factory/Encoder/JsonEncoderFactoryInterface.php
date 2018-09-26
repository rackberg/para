<?php

namespace Para\Factory\Encoder;

use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * Interface JsonEncoderFactoryInterface.
 *
 * @package Para\Factory\Encoder
 */
interface JsonEncoderFactoryInterface
{
    /**
     * Creates and returns a new json encoder instance.
     *
     * @return \Symfony\Component\Serializer\Encoder\JsonEncoder
     */
    public function getEncoder(): JsonEncoder;
}
