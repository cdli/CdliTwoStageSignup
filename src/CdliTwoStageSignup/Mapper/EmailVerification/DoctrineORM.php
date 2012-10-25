<?php
namespace CdliTwoStageSignup\Mapper\EmailVerification;

use Doctrine\ORM\EntityManager;
use CdliTwoStageSignup\Options\ModuleOptions;
use CdliTwoStageSignup\Entity\EmailVerification as Model;
use Zend\Stdlib\Hydrator\HydratorInterface;
use ZfcBase\EventManager\EventProvider;

class DoctrineORM extends EventProvider implements MapperInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \CdliTwoStageSignup\Options\ModuleOptions
     */
    protected $options;

    public function __construct(EntityManager $em, ModuleOptions $options)
    {
        $this->em      = $em;
        $this->options = $options;
    }

    protected function getEntityRepository()
    {
        return $this->em->getRepository('CdliTwoStageSignup\Entity\EmailVerification');
    }

    public function findByEmail($email)
    {
        $entity = $this->getEntityRepository()->findOneBy(array('email_address' => $email));
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));
        return $entity;
    }

    public function findByRequestKey($key)
    {
        $entity = $this->getEntityRepository()->findOneBy(array('request_key' => $key));
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));
        return $entity;
    }

    public function cleanExpiredVerificationRequests($expiryTime=86400)
    {
        $now = new \DateTime((int)$expiryTime . ' seconds ago');
        $dql = "DELETE CdliTwoStageSignup\Entity\EmailVerification ev WHERE ev.request_time <= '" . $now->format('Y-m-d H:i:s') . "'";
        return $this->em->createQuery($dql)->getResult();
    }

    public function insert($entity, $tableName = null, HydratorInterface $hydrator = null)
    {
        $this->em->persist($entity);
        $this->em->flush();
        return $entity;
    }

    public function remove(Model $evrModel)
    {
        $this->em->remove($evrModel);
        $this->em->flush();
        return true;
    }
}
