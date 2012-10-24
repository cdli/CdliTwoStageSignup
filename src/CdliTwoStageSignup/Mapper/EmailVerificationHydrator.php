<?php

namespace CdliTwoStageSignup\Mapper;

use Zend\Stdlib\Hydrator\ClassMethods;
use CdliTwoStageSignup\Entity\EmailVerification as Entity;

class EmailVerificationHydrator extends ClassMethods
{
    /**
     * Extract values from an object
     *
     * @param  object $object
     * @return array
     * @throws Exception\InvalidArgumentException
     */
    public function extract($object)
    {
        if (!$object instanceof Entity) {
            throw new \InvalidArgumentException('$object must be an instance of EmailVerification entity');
        }
        /* @var $object Entity*/
        $data = parent::extract($object);
        unset($data['is_expired']);
        if ( isset($data['request_time']) && $data['request_time'] instanceof \DateTime ) {
            $data['request_time'] = $data['request_time']->format('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  object $object
     * @return UserInterface
     * @throws Exception\InvalidArgumentException
     */
    public function hydrate(array $data, $object)
    {
        if (!$object instanceof Entity) {
            throw new \InvalidArgumentException('$object must be an instance of EmailVerification entity');
        }
        if ( isset($data['request_time']) && ! $data['request_time'] instanceof \DateTime ) {
            $data['request_time'] = new \DateTime($data['request_time']);
        }
        return parent::hydrate($data, $object);
    }
}
