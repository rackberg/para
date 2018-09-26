<?php

namespace Para\Factory\Encoder;

use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * Class JsonEncoderFactory.
 *
 * @package Factory\Encoder
 */
class JsonEncoderFactory implements JsonEncoderFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEncoder(): JsonEncoder
    {
        return new JsonEncoder();
    }
}
