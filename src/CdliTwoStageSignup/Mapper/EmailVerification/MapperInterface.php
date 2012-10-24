<?php
namespace CdliTwoStageSignup\Mapper\EmailVerification;

use CdliTwoStageSignup\Entity\EmailVerification as Model;
use Zend\Stdlib\Hydrator\HydratorInterface;

interface MapperInterface
{
    public function findByEmail($email);

    public function findByRequestKey($key);

    public function cleanExpiredVerificationRequests($expiryTime=86400);

    public function insert($entity, $tableName = null, HydratorInterface $hydrator = null);

    public function remove(Model $evrModel);
}
