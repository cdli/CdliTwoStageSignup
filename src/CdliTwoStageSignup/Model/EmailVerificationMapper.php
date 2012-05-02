<?php

namespace CdliTwoStageSignup\Model;

use ZfcBase\Mapper\DbMapperAbstract,
    CdliTwoStageSignup\Module as modCTSS,
    ArrayObject,
    DateTime;

class EmailVerificationMapper extends DbMapperAbstract
{
    protected $tableName         = 'user_signup_email_verification';
    protected $keyField          = 'request_key';
    protected $emailField        = 'email_address';
    protected $reqtimeField        = 'request_time';

    public function add(EmailVerification $evrModel)
    {
        return $this->persist($evrModel);
    }

    public function update(EmailVerification $evrModel)
    {
        return $this->persist($evrModel, 'update');
    }

	public function delete(EmailVerification $evrModel)
	{
		return $this->getTableGateway()->delete(array($this->keyField => $evrModel->getRequestKey()));
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
        $evr = EmailVerification::fromArray($row->getArrayCopy());
        return $evr;
    }

    public function persist(EmailVerification $evrModel, $mode = 'insert')
    {
        $data = new ArrayObject(array(
            $this->keyField      => $evrModel->getRequestKey(),
            $this->emailField    => $evrModel->getEmailAddress(),
            $this->reqtimeField  => $evrModel->getRequestTime()->format('Y-m-d H:i:s'),
        ));
        $this->events()->trigger(__FUNCTION__ . '.pre', $this, array('data' => $data, 'record' => $evrModel));
        if ('update' === $mode) {
            $this->getTableGateway()->update((array) $data, array($this->keyField => $evrModel->getRequestKey()));
        } elseif ('insert' === $mode) {
            $this->getTableGateway()->insert((array) $data);
        }
        return $evrModel;
    }
}
