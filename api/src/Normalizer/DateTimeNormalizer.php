<?php

namespace App\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class DateTimeNormalizer implements NormalizerInterface, DenormalizerInterface
{
    use SerializerAwareTrait;
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return $object->format('d-m-Y');
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        return new $class($data);
    }

    /**
     * Checks if the given class is a DateTime.
     *
     * @param mixed  $data   Data to normalize.
     * @param string $format The format being (de-)serialized from or into.
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof \DateTime;
    }

    /**
     * Checks if the given class is a DateTime.
     *
     * @param mixed $data Data to denormalize from.
     * @param string $type The class to which the data should be denormalized.
     * @param string $format The format being deserialized from.
     *
     * @return bool
     *
     * @throws \ReflectionException
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        $class = new \ReflectionClass($type);

        return $class->isSubclassOf('\DateTime');
    }
}
