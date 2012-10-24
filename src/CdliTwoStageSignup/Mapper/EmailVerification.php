<?php
namespace CdliTwoStageSignup\Mapper;

use ZfcBase\Mapper\AbstractDbMapper;
use CdliTwoStageSignup\Entity\EmailVerification as Model;
use Zend\Db\Sql\Sql;
use Zend\Stdlib\Hydrator\HydratorInterface;

class EmailVerification extends AbstractDbMapper
{
    protected $tableName = 'user_signup_email_verification';

    public function findByEmail($email)
    {
        $select = $this->getSelect($this->tableName)->where(array('email_address' => $email));
        $entity = $this->select($select)->current();
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));
        return $entity;
    }

    public function findByRequestKey($key)
    {
        $select = $this->getSelect($this->tableName)->where(array('request_key' => $key));
        return $this->select($select)->current();
    }

    public function cleanExpiredVerificationRequests($expiryTime=86400)
    {
        $now = new \DateTime((int)$expiryTime . ' seconds ago');

        $delete = $this->getSql()->delete($this->tableName);
        $delete->where->lessThanOrEqualTo('request_time', $now->format('Y-m-d H:i:s'));
        $statement = $this->getSql()->prepareStatementForSqlObject($delete);
        $statement->execute();
        return true; 
    }

    public function insert($entity, $tableName = null, HydratorInterface $hydrator = null)
    {
        $result = parent::insert($entity, $tableName, $hydrator);
        return $result;
    }

    public function remove(Model $evrModel)
    {
        $this->delete(array('request_key' => $evrModel->getRequestKey()));
        return true;
    }
}
