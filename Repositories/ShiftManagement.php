<?php
/**
 * Created by mr.vjcspy@gmail.com - khoild@smartosc.com.
 * Date: 13/01/2017
 * Time: 17:03
 */

namespace SM\Shift\Repositories;

use Exception;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use SM\Core\Api\SearchResult;
use SM\Payment\Helper\PaymentHelper;
use SM\Shift\Model\ResourceModel\RetailTransaction\CollectionFactory as ShiftRetailCollectionFactory;
use SM\Shift\Model\ResourceModel\Shift\CollectionFactory;
use SM\Shift\Model\ResourceModel\ShiftInOut\CollectionFactory as ShiftInOutCollectionFactory;
use SM\Shift\Model\ShiftFactory;
use SM\Shift\Model\ShiftInOutFactory;
use SM\XRetail\Helper\Data;
use SM\XRetail\Helper\DataConfig;
use SM\XRetail\Repositories\Contract\ServiceAbstract;

/**
 * Class ShiftManagement
 *
 * @package SM\Shift\Repositories
 */
class ShiftManagement extends ServiceAbstract
{
    const CHANGE = 'Change';
    const CASH = 'Cash';
    const PAYMENT_CASH = 'cash';
    /**
     * @var \SM\Shift\Model\ResourceModel\Shift\CollectionFactory
     */
    protected $shiftCollectionFactory;
    /**
     * @var \SM\Shift\Model\ShiftInOutFactory
     */
    protected $shiftInOut;

    /**
     * @var \SM\Shift\Model\ResourceModel\ShiftInOut\CollectionFactory
     */
    protected $shiftInOutCollection;

    /**
     * @var \SM\Shift\Model\ShiftFactory
     */
    protected $shiftFactory;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;
    /**
     * @var \SM\Shift\Model\ResourceModel\RetailTransaction\CollectionFactory
     */
    protected $transactionCollectionFactory;

    /**
     * @var \SM\XRetail\Helper\Data
     */
    private $retailHelper;

    /**
     * @var \SM\Payment\Helper\PaymentHelper
     */
    private $paymentHelper;

    /**
     * ShiftManagement constructor.
     *
     * @param \Magento\Framework\App\RequestInterface                           $requestInterface
     * @param \SM\XRetail\Helper\DataConfig                                     $dataConfig
     * @param \SM\XRetail\Helper\Data                                           $retailHelper
     * @param \Magento\Store\Model\StoreManagerInterface                        $storeManager
     * @param \SM\Shift\Model\ResourceModel\Shift\CollectionFactory             $shiftCollectionFactory
     * @param \SM\Shift\Model\ShiftInOutFactory                                 $shiftInOutFactory
     * @param \SM\Shift\Model\ResourceModel\ShiftInOut\CollectionFactory        $shiftInOutCollectionFactory
     * @param \SM\Shift\Model\ShiftFactory                                      $shiftFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                       $dateTime
     * @param \SM\Payment\Helper\PaymentHelper                                  $paymentHelper
     * @param \SM\Shift\Model\ResourceModel\RetailTransaction\CollectionFactory $transactionCollectionFactory
     */
    public function __construct(
        RequestInterface $requestInterface,
        DataConfig $dataConfig,
        Data $retailHelper,
        StoreManagerInterface $storeManager,
        CollectionFactory $shiftCollectionFactory,
        ShiftInOutFactory $shiftInOutFactory,
        ShiftInOutCollectionFactory $shiftInOutCollectionFactory,
        ShiftFactory $shiftFactory,
        DateTime $dateTime,
        PaymentHelper $paymentHelper,
        ShiftRetailCollectionFactory $transactionCollectionFactory
    ) {
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->dateTime = $dateTime;
        $this->shiftFactory = $shiftFactory;
        $this->shiftInOut = $shiftInOutFactory;
        $this->shiftInOutCollection = $shiftInOutCollectionFactory;
        $this->shiftCollectionFactory = $shiftCollectionFactory;
        $this->retailHelper = $retailHelper;
        $this->paymentHelper = $paymentHelper;
        parent::__construct($requestInterface, $dataConfig, $storeManager);
    }

    /**
     * @return array
     */
    public function getShiftData()
    {
        return $this->load($this->getSearchCriteria())->getOutput();
    }

    /**
     * @param \Magento\Framework\DataObject $searchCriteria
     *
     * @return SearchResult
     * @throws \Exception
     */
    public function load(DataObject $searchCriteria)
    {
        if (is_null($searchCriteria) || !$searchCriteria) {
            $searchCriteria = $this->getSearchCriteria();
        }

        $this->getSearchResult()->setSearchCriteria($searchCriteria);
        $collection = $this->getShiftCollection($searchCriteria);
        //$shiftInout = $this->shiftInOut->create();
        $items = [];
        if ($collection->getLastPageNumber() < $searchCriteria->getData('currentPage')) {
        } else {
            $outletId = $searchCriteria->getData('outlet_id');
            $storeId = $this->retailHelper->getStoreByOutletId($outletId);
            foreach ($collection as $shift) {
                $shiftData = $shift->getData();
                $shiftData['open_at'] = $this->retailHelper->convertTimeDBUsingTimeZone(
                    $shift->getData('open_at'),
                    $storeId
                );
                $shiftData['close_at'] = $this->retailHelper->convertTimeDBUsingTimeZone(
                    $shift->getData('close_at'),
                    $storeId
                );
                $shiftData['in_out'] = $this->getInOutData($shift->getId(), $storeId);
                $shiftData['transactions'] = $this->getPaymentTransaction($shift->getId());
                $shiftData['data'] = json_decode((string)$shiftData['data']);
                if (isset($shiftData['detail_tax']) && $shiftData['detail_tax'] != null) {
                    $shiftData['detail_tax'] = json_decode((string)$shiftData['detail_tax'], true);
                } else {
                    $shiftData['detail_tax'] = "";
                }
                $items[] = $shiftData;
            }
        }

        return $this->getSearchResult()
            ->setItems($items)
            ->setTotalCount($collection->getSize())
            ->setLastPageNumber($collection->getLastPageNumber());
    }

    /**
     * @param $shiftId
     *
     * @return array
     * @throws \Exception
     */
    public function getInOutData($shiftId, $storeId)
    {
        // làm riêng ra function này vì trên resource model shift in out không construct được retail helper
        if (!$shiftId) {
            throw new Exception("Please define shift id");
        }

        $collection = $this->shiftInOutCollection->create();
        $collection->addFieldToFilter('shift_id', $shiftId);

        $items = [];
        foreach ($collection as $inOut) {
            $item = $inOut->getData();
            $item['created_at'] = $this->retailHelper->convertTimeDBUsingTimeZone(
                $inOut->getData("created_at"),
                $storeId
            );
            $items[] = $item;
        }

        return $items;
    }

    /**
     * @param $shiftId
     * @param bool $onlyCash
     * @return array
     */
    protected function getPaymentTransaction($shiftId, $onlyCash = false): array
    {
        /** @var \SM\Shift\Model\ResourceModel\RetailTransaction\Collection $collection */
        $collection = $this->transactionCollectionFactory->create();

        $collection->addFieldToFilter('shift_id', $shiftId);
        if ($onlyCash) {
            $collection->addFieldToFilter('payment_type', 'cash');
        }
        $payments = [];

        $validPaymentMethods = [];
        foreach ($collection as $payment) {
            if ($payment->getData('payment_id') < 100000) {
                $key = strtolower($payment->getData('payment_title')) . '_' . strtolower($payment->getData('payment_type'));
                $validPaymentMethods[$key] = $payment->getData('payment_id');
            }
        }

        foreach ($collection as $payment) {
            $key = strtolower($payment->getData('payment_title')) . '_' . strtolower($payment->getData('payment_type'));
            if (isset($validPaymentMethods[$key])) {
                $payment->setData('payment_id', $validPaymentMethods[$key]);
            }
            $payments[] = $payment->getData();
        }
        return $payments;
    }

    /**
     * @param $searchCriteria
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     * @throws \Exception
     */
    public function getShiftCollection(DataObject $searchCriteria)
    {
        if (is_null($searchCriteria->getData('outlet_id'))) {
            $outletId = $searchCriteria->getData('outletId');
        } else {
            $outletId = $searchCriteria->getData('outlet_id');
        }
        if (is_null($searchCriteria->getData('register_id'))) {
            $registerId = $searchCriteria->getData('registerId');
        } else {
            $registerId = $searchCriteria->getData('register_id');
        }
        if (is_null($searchCriteria->getData('shift_id'))) {
            $shiftId = $searchCriteria->getData('shiftId');
        } else {
            $shiftId = $searchCriteria->getData('shift_id');
        }
        if (is_null($outletId) || is_null($registerId)) {
            throw new Exception(__('Must have param outlet and register'));
        }

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->shiftCollectionFactory->create();
        $collection->addFieldToFilter('outlet_id', $outletId);
        $collection->addFieldToFilter('register_id', $registerId);

        if ($shiftId) {
            $collection->addFieldToFilter('id', $shiftId);
        }

        if (is_nan((float)$searchCriteria->getData('currentPage'))) {
            $collection->setCurPage(1);
        } else {
            $collection->setCurPage($searchCriteria->getData('currentPage'));
        }
        if (is_nan((float)$searchCriteria->getData('pageSize'))) {
            $collection->setPageSize(
                DataConfig::PAGE_SIZE_LOAD_DATA
            );
        } else {
            $collection->setPageSize(
                $searchCriteria->getData('pageSize')
            );
        }
        $collection->addOrder('id');

        return $collection;
    }

    /**
     * @throws \Exception
     */
    public function openShift()
    {
        $outletId = $this->getRequest()->getParam('outlet_id');
        $registerId = $this->getRequest()->getParam('register_id');
        $userId = $this->getRequest()->getParam('user_id');
        $userName = $this->getRequest()->getParam('user_name');
        $amount = $this->getRequest()->getParam('amount');
        $bankNotes = $this->getRequest()->getParam('bank_notes', []);

        if (is_null($outletId) || is_null($registerId) || is_null($userId) || is_null($userName) || is_null($amount)) {
            throw new Exception("Must define required data");
        }

        $shift = $this->shiftFactory->create();
        //check no shift opening
        /** @var \SM\Shift\Model\ResourceModel\Shift\Collection $collection */
        $collection = $this->shiftCollectionFactory->create();
        $collection->addFieldToFilter('outlet_id', $outletId)
            ->addFieldToFilter('register_id', $registerId)
            ->addFieldToFilter('is_open', 1);
        $openShift = $collection->getFirstItem();
        if ($openShift->getId()) {
            throw new Exception("Shift has already been opened");
        }

        $shift->setData('is_open', 1)
            ->setData('register_id', $registerId)
            ->setData('outlet_id', $outletId)
            ->setData('user_open_id', $userId)
            ->setData('user_open_name', $userName)
            ->setData('start_amount', $amount)
            ->setData('open_note', $this->getRequest()->getParam('note'))
            ->setData('detail_tax', '{}')
            ->setData('bank_notes', json_encode($bankNotes))
            ->save();

        return $this->load(
            new DataObject(
                [
                    'shift_id'    => $shift->getId(),
                    'outlet_id'   => $outletId,
                    'register_id' => $registerId,
                ]
            )
        )->getOutput();
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function isOpenShift()
    {
        $outletId = $this->getRequest()->getParam('outlet_id');
        $registerId = $this->getRequest()->getParam('register_id');
        if (is_null($outletId) || is_null($registerId)) {
            throw new Exception("Must define required data");
        }

        /** @var \SM\Shift\Model\ResourceModel\Shift\Collection $collection */
        $collection = $this->shiftCollectionFactory->create();
        $collection->addFieldToFilter('outlet_id', $outletId)
            ->addFieldToFilter('register_id', $registerId)
            ->addFieldToFilter('is_open', 1);
        $openShift = $collection->getFirstItem();

        if (!$openShift->getId()) {
            return false;
        }

        return true;
    }

    public function isOpenShiftJs()
    {
        return $this->isOpenShift() ? ["is_open" => true] : ["is_open" => false];
    }

    /**
     * @throws \Exception
     */
    public function closeShift()
    {
        $outletId = $this->getRequest()->getParam('outlet_id');
        $shiftId = $this->getRequest()->getParam('shift_id');
        $registerId = $this->getRequest()->getParam('register_id');
        $userId = $this->getRequest()->getParam('user_id');
        $userName = $this->getRequest()->getParam('user_name');
        $data = $this->getRequest()->getParam('data');
        if (is_null($outletId)
            || is_null($registerId)
            || is_null($userId)
            || is_null($userName)
            || is_null($shiftId)
            || is_null($data)
        ) {
            throw new Exception("Must define required data");
        }

        $shift = $this->getShiftModel();
        $shift->load($shiftId);
        if (!$shift->getId()) {
            throw new Exception("Can't find shift");
        }
        if (!$shift->getData('is_open')) {
            throw new Exception("Can't close shift because it isn't opening");
        }

        $cashPaymentMethods = $this->paymentHelper->getPaymentMethodsOfType('cash', $registerId);

        $totalCounted = 0;
        $totalExpected = 0;

        foreach ($cashPaymentMethods as $method) {
            $cashPaymentId = $method->getData('id');
            if (isset($data['counted'][$cashPaymentId]) && is_numeric($data['counted'][$cashPaymentId])) {
                $totalCounted += (float)$data['counted'][$cashPaymentId];
            }

            if (isset($data['expected'][$cashPaymentId]) && is_numeric($data['expected'][$cashPaymentId])) {
                $totalExpected += (float)$data['expected'][$cashPaymentId];
            }
        }

        if (isset($data['takeOut']) && is_numeric($data['takeOut'])) {
            $takeOutAmount = $data['takeOut'];
        } else {
            throw new Exception('Total takeout must be number');
        }

        $totalAdjustment = 0;
        $storeId = $this->retailHelper->getStoreByOutletId($outletId);
        foreach ($this->getInOutData($shiftId, $storeId) as $inOut) {
            if ($inOut['is_in'] == 1) {
                $totalAdjustment += floatval($inOut['amount']);
            } else {
                $totalAdjustment -= floatval($inOut['amount']);
            }
        }

        $totalNetAmount = 0;
        foreach ($this->getPaymentTransaction($shiftId) as $tran) {
            $totalNetAmount += $tran['amount'];
        }

        $shift->setData('is_open', 0)
            ->setData('user_close_id', $userId)
            ->setData('user_close_name', $userName)
            ->setData('data', json_encode($data))
            ->setData('close_note', $data['note'])
            ->setData('take_out_amount', $takeOutAmount)
            ->setData('total_counted_amount', $totalCounted)
            ->setData('total_expected_amount', $totalExpected)
            ->setData('total_net_amount', $totalNetAmount)
            ->setData('total_adjustment', $totalAdjustment)
            ->save();

        return $this->load(
            new DataObject(
                [
                    'shift_id'    => $shiftId,
                    'outlet_id'   => $outletId,
                    'register_id' => $registerId,
                ]
            )
        )->getOutput();
    }

    protected function getCashPaymentId($registerId)
    {
        $cashPayment = $this->paymentHelper->getPaymentIdByType('cash', $registerId);
        if ($cashPayment != null) {
            return $cashPayment;
        }

        return 1;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function adjustCash()
    {
        $outletId = $this->getRequest()->getParam('outlet_id');
        $shiftId = $this->getRequest()->getParam('shift_id');
        $registerId = $this->getRequest()->getParam('register_id');
        $userId = $this->getRequest()->getParam('user_id');
        $userName = $this->getRequest()->getParam('user_name');
        $amount = $this->getRequest()->getParam('amount');
        $isCashIn = $this->getRequest()->getParam('is_in');
        $created_at = $this->retailHelper->getCurrentTime();
        if (is_null($outletId)
            || is_null($registerId)
            || is_null($userId)
            || is_null($userName)
            || is_null($shiftId)
            || is_null($amount)
            || is_null($isCashIn)
        ) {
            throw new Exception("Must define required data");
        }
        $shiftInOut = $this->shiftInOut->create();
        $shiftInOut->setData('shift_id', $shiftId)
            ->setData('user_name', $userName)
            ->setData('user_id', $userId)
            ->setData('amount', $amount)
            ->setData('note', $this->getRequest()->getParam('note'))
            ->setData('is_in', $isCashIn == true || $isCashIn == 1 ? 1 : 0)
            ->setData('created_at', $created_at)
            ->save();

        return $this->load(
            new DataObject(
                [
                    'shift_id'    => $shiftId,
                    'outlet_id'   => $outletId,
                    'register_id' => $registerId,
                ]
            )
        )->getOutput();
    }

    /**
     * @return \SM\Shift\Model\Shift
     */
    protected function getShiftModel()
    {
        return $this->shiftFactory->create();
    }
}
