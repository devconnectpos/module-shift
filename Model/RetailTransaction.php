<?php
namespace SM\Shift\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class RetailTransaction extends AbstractModel implements \SM\Shift\Api\Data\RetailTransactionInterface, IdentityInterface
{
	protected $_idFieldName = 'id';

    const CACHE_TAG = 'sm_retail_transaction';

    protected function _construct()
    {
        $this->_init('SM\Shift\Model\ResourceModel\RetailTransaction');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
	
	/**
	 * @inheritDoc
	 */
	public function getPaymentId()
	{
		return $this->getData(self::ID);
	}
	
	/**
	 * @inheritDoc
	 */
	public function setPaymentId($paymentId)
	{
		return $this->setData(self::PAYMENT_ID, $paymentId);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getShiftId()
	{
		return $this->getData(self::SHIFT_ID);
	}
	
	/**
	 * @inheritDoc
	 */
	public function setShiftId($shiftId)
	{
		return $this->setData(self::SHIFT_ID, $shiftId);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getOutletId()
	{
		return $this->getData(self::OUTLET_ID);
	}
	
	/**
	 * @inheritDoc
	 */
	public function setOutletId($outletId)
	{
		return $this->setData(self::OUTLET_ID, $outletId);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getRegisterId()
	{
		return $this->getData(self::REGISTER_ID);
	}
	
	/**
	 * @inheritDoc
	 */
	public function setRegisterId($registerId)
	{
		return $this->setData(self::REGISTER_ID, $registerId);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getPaymentTitle()
	{
		return $this->getData(self::PAYMENT_TITLE);
	}
	
	/**
	 * @inheritDoc
	 */
	public function setPaymentTitle($paymentTitle)
	{
		return $this->setData(self::PAYMENT_TITLE, $paymentTitle);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getPaymentType()
	{
		return $this->getData(self::PAYMENT_TYPE);
	}
	
	/**
	 * @inheritDoc
	 */
	public function setPaymentType($paymentType)
	{
		return $this->setData(self::PAYMENT_TYPE, $paymentType);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getAmount()
	{
		return $this->getData(self::AMOUNT);
	}
	
	/**
	 * @inheritDoc
	 */
	public function setAmount($amount)
	{
		return $this->setData(self::AMOUNT, $amount);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getBaseAmount()
	{
		return $this->getData(self::BASE_AMOUNT);
	}
	
	/**
	 * @inheritDoc
	 */
	public function setBaseAmount($baseAmount)
	{
		return $this->setData(self::BASE_AMOUNT, $baseAmount);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getIsPurchase()
	{
		return $this->getData(self::IS_PURCHASE);
	}
	
	/**
	 * @inheritDoc
	 */
	public function setIsPurchase($isPurchase)
	{
		return $this->setData(self::IS_PURCHASE, $isPurchase);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getCreatedAt()
	{
		return $this->getData(self::CREATED_AT);
	}
	
	/**
	 * @inheritDoc
	 */
	public function setCreatedAt($createdAt)
	{
		return $this->setData(self::CREATED_AT, $createdAt);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getUpdatedAt()
	{
		return $this->getData(self::UPDATED_AT);
	}
	
	/**
	 * @inheritDoc
	 */
	public function setUpdatedAt($updatedAt)
	{
		return $this->setData(self::UPDATED_AT, $updatedAt);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getOrderId()
	{
		return $this->getData(self::ORDER_ID);
	}
	
	/**
	 * @inheritDoc
	 */
	public function setOrderId($orderId)
	{
		return $this->setData(self::ORDER_ID, $orderId);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getUsername()
	{
		return $this->getData(self::USERNAME);
	}
	
	/**
	 * @inheritDoc
	 */
	public function setUsername($username)
	{
		return $this->setData(self::USERNAME, $username);
	}
}
