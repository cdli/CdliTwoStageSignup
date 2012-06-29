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
        $statement = $sql->prepareStatementForSqlObject($sql->delete(array(
            $this->keyField => $evrModel->getRequestKey()
        )));
        var_dump($statement->execute());
    }

    public function findByEmail($email)
    {
        $rowset = $this->getTableGateway()->select(array($this->emailField => $email));
        $row = $rowset->current();
        $evr = $this->fromRow($row);
        $this->events()->trigger(__FUNCTION__ . '.post', $this, array('user' => $evr, 'row' => $row));
        return $evr;
    }

    public function findByRequestKey($key)
    {
        $rowset = $this->getTableGateway()->select(array($this->keyField => $key));
        $row = $rowset->current();
        $evr = $this->fromRow($row);
        $this->events()->trigger(__FUNCTION__ . '.post', $this, array('user' => $evr, 'row' => $row));
        return $evr;
    }

    public function cleanExpiredVerificationRequests($expiryTime=86400)
    {
        $now = new \DateTime((int)$expiryTime . ' seconds ago');
        $where = new \Zend\Db\Sql\Where();
        $where->lessThanOrEqualTo($this->reqtimeField, $now->format('Y-m-d H:i:s'));
        return $this->getTableGateway()->delete($where);
    }

    protected function fromRow($row)
    {
        if (!$row) return false;
        $evr = Model::fromArray($row->getArrayCopy());
        return $evr;
    }

    public function toScalarValueArray($evrModel)
    {
        return new ArrayObject(array(
            $this->keyField      => $evrModel->getRequestKey(),
            $this->emailField    => $evrModel->getEmailAddress(),
            $this->reqtimeField  => $evrModel->getRequestTime()->format('Y-m-d H:i:s'),
        ));
    }

    public function persist($evrModel)
    {
        $data = $this->toScalarValueArray($evrModel);
        $this->events()->trigger(__FUNCTION__ . '.pre', $this, array('data' => $data, 'record' => $evrModel));
        $this->getTableGateway()->insert((array) $data);
        return $evrModel;
    }

    public function getTableName() { return $this->tableName; }
    public function getPrimaryKey() { $this->keyField; }
    public function getPaginatorAdapter(array $params) { }
    public function getClassName() { return 'CdliTwoStageSignup\Model\EmailVerification'; }
}
