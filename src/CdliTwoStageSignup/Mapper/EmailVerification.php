<?php
namespace CdliTwoStageSignup\Mapper;

use ZfcBase\Mapper\AbstractDbMapper;
use CdliTwoStageSignup\Model\EmailVerification as Model;
use Zend\Db\Sql\Sql;

class EmailVerification extends AbstractDbMapper
{
    protected $tableName         = 'user_signup_email_verification';
    protected $keyField          = 'request_key';
    protected $emailField        = 'email_address';
    protected $reqtimeField      = 'request_time';

    public function remove($evrModel)
    {
        $sql = new Sql($this->getDbAdapter(), $this->tableName);
        $delete= $sql->delete();
        $delete->where->equalTo($this->keyField, $evrModel->getRequestKey());
        $statement = $sql->prepareStatementForSqlObject($delete);
        $statement->execute();
        return true;
    }

    public function findByEmail($email)
    {
        $select = $this->select()
                       ->from($this->tableName)
                       ->where(array($this->emailField => $email));
        return $this->selectWith($select)->current();
    }

    public function findByRequestKey($key)
    {
        $select = $this->select()
                       ->from($this->tableName)
                       ->where(array($this->keyField => $key));
        return $this->selectWith($select)->current();
    }

    public function cleanExpiredVerificationRequests($expiryTime=86400)
    {
        $now = new \DateTime((int)$expiryTime . ' seconds ago');

        $sql = new Sql($this->getDbAdapter(), $this->tableName);
        $delete = $sql->delete();
        $delete ->where->lessThanOrEqualTo($this->reqtimeField, $now->format('Y-m-d H:i:s'));
        $statement = $sql->prepareStatementForSqlObject($delete);
        $statement->execute();
        return true; 
    }

    protected function fromRow($row)
    {
        if (!$row) return false;
        $evr = Model::fromArray($row->getArrayCopy());
        return $evr;
    }

    public function toScalarValueArray($evrModel)
    {
        return new \ArrayObject(array(
            $this->keyField      => $evrModel->getRequestKey(),
            $this->emailField    => $evrModel->getEmailAddress(),
            $this->reqtimeField  => $evrModel->getRequestTime()->format('Y-m-d H:i:s'),
        ));
    }

    /**
     * @todo
     */
    public function persist($evrModel)
    {
        return parent::insert($evrModel);
    }

    public function getTableName() { return $this->tableName; }
    public function getPrimaryKey() { $this->keyField; }
    public function getPaginatorAdapter(array $params) { }
    public function getClassName() { return 'CdliTwoStageSignup\Model\EmailVerification'; }
}
