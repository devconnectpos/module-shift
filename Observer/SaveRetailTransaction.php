<?php


namespace SM\Shift\Observer;


use Magento\Framework\Event\ObserverInterface;
use SM\Payment\Model\RetailMultiple;

class SaveRetailTransaction implements ObserverInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Magento\Directory\Model\Currency
     */
    private $currencyModel;
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;
    /**
     * @var \SM\XRetail\Helper\Data
     */
    private $retailHelper;
    /**
     * @var \SM\Shift\Model\RetailTransactionFactory
     */
    private $retailTransactionFactory;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\Currency $currencyModel,
        \Magento\Framework\Registry $registry,
        \SM\XRetail\Helper\Data $retailHelper,
        \SM\Shift\Model\RetailTransactionFactory $retailTransactionFactory
    ) {
        $this->storeManager = $storeManager;
        $this->currencyModel = $currencyModel;
        $this->registry = $registry;
        $this->retailHelper = $retailHelper;
        $this->retailTransactionFactory = $retailTransactionFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderData = $observer->getData('orderData');
        $data = $observer->getData('requestData');

        $baseCurrencyCode = $this->storeManager->getStore()->getBaseCurrencyCode();
        $currentCurrencyCode = $this->storeManager->getStore($orderData->getData('store_id'))->getCurrentCurrencyCode();
        $allowedCurrencies = $this->currencyModel->getConfigAllowCurrencies();
        $rates = $this->currencyModel->getCurrencyRates($baseCurrencyCode, array_values($allowedCurrencies));
        $order = $data['order'];

        if (isset($order['payment_method'])
            && $order['payment_method'] === RetailMultiple::PAYMENT_METHOD_RETAILMULTIPLE_CODE
        ) {
            $openingShift = $this->registry->registry('opening_shift');
            if (isset($order['payment_data'])
                && is_array($order['payment_data'])
                && count($order['payment_data']) > 0
            ) {
                foreach ($order['payment_data'] as $payment_datum) {
                    if (!is_array($payment_datum)) {
                        continue;
                    }
                    if (!isset($payment_datum['id']) || !$payment_datum['id']) {
                        throw new \Exception("Payment data not valid");
                    }
                    $amount = floatval($payment_datum['amount']);
                    if ($amount == 0) {
                        continue;
                    }
                    $created_at = $this->retailHelper->getCurrentTime();
                    $p = $this->retailTransactionFactory->create();
                    $p->addData(
                        [
                            'outlet_id'     => $data['outlet_id'],
                            'register_id'   => $data['register_id'],
                            'shift_id'      => $openingShift->getData('id'),
                            'payment_id'    => $payment_datum['id'],
                            'payment_title' => $payment_datum['title'],
                            'payment_type'  => $payment_datum['type'],
                            'amount'        => $amount,
                            'is_purchase'   => 1,
                            "created_at"    => $created_at,
                            'order_id'      => $orderData->getData('entity_id'),
                            "user_name"     => $data['user_name'] ?? '',
                            'base_amount'   => isset($rates[$currentCurrencyCode]) && $rates[$currentCurrencyCode] != 0 ? $payment_datum['amount'] / $rates[$currentCurrencyCode] : $payment_datum['amount'],
                        ]
                    )->save();
                }
            }
        }
    }
}
