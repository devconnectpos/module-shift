<?php

namespace SM\Shift\Api\Data;

interface RetailTransactionInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const PAYMENT_ID    = 'payment_id';
    const SHIFT_ID      = 'shift_id';
    const OUTLET_ID     = 'outlet_id';
    const REGISTER_ID   = 'register_id';
    const PAYMENT_TITLE = 'payment_title';
    const PAYMENT_TYPE  = 'payment_type';
    const PRODUCT_NAME  = 'product_name';
    const PRODUCT_QTY   = 'product_qty';
    const AMOUNT        = 'amount';
    const BASE_AMOUNT   = 'base_amount';
    const IS_PURCHASE   = 'is_purchase';
    const CREATED_AT    = 'created_at';
    const UPDATED_AT    = 'updated_at';
    const ORDER_ID      = 'order_id';
    const USERNAME      = 'username';
	
	/**
	 * @return mixed
	 */
	public function getPaymentId();
	
	/**
	 * @param $paymentId
	 * @return mixed
	 */
	public function setPaymentId($paymentId);
	
	/**
	 * @return mixed
	 */
	public function getShiftId();
	
	/**
	 * @param $shiftId
	 * @return mixed
	 */
	public function setShiftId($shiftId);
	
	/**
	 * @return mixed
	 */
	public function getOutletId();
	
	/**
	 * @param $outletId
	 * @return mixed
	 */
	public function setOutletId($outletId);
	
	/**
	 * @return mixed
	 */
	public function getRegisterId();
	
	/**
	 * @param $registerId
	 * @return mixed
	 */
	public function setRegisterId($registerId);
	
	/**
	 * @return mixed
	 */
	public function getPaymentTitle();
	
	/**
	 * @param $paymentTitle
	 * @return mixed
	 */
	public function setPaymentTitle($paymentTitle);
	
	/**
	 * @return mixed
	 */
	public function getPaymentType();
	
	/**
	 * @param $paymentType
	 * @return mixed
	 */
	public function setPaymentType($paymentType);
	
	/**
	 * @return mixed
	 */
	public function getAmount();
	
	/**
	 * @param $amount
	 * @return mixed
	 */
	public function setAmount($amount);
	
	/**
	 * @return mixed
	 */
	public function getBaseAmount();
	
	/**
	 * @param $baseAmount
	 * @return mixed
	 */
	public function setBaseAmount($baseAmount);
	
	/**
	 * @return mixed
	 */
	public function getIsPurchase();
	
	/**
	 * @param $isPurchase
	 * @return mixed
	 */
	public function setIsPurchase($isPurchase);
	
	/**
	 * @return mixed
	 */
	public function getCreatedAt();
	
	/**
	 * @param $createdAt
	 * @return mixed
	 */
	public function setCreatedAt($createdAt);
	
	/**
	 * @return mixed
	 */
	public function getUpdatedAt();
	
	/**
	 * @param $updatedAt
	 * @return mixed
	 */
	public function setUpdatedAt($updatedAt);
	
	/**
	 * @return mixed
	 */
	public function getOrderId();
	
	/**
	 * @param $orderId
	 * @return mixed
	 */
	public function setOrderId($orderId);
	
	/**
	 * @return mixed
	 */
	public function getUsername();
	
	/**
	 * @param $username
	 * @return mixed
	 */
	public function setUsername($username);
}
