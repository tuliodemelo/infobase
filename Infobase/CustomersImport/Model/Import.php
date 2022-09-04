<?php

declare(strict_types=1);

namespace Infobase\CustomersImport\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Import
{
    protected CustomerInterfaceFactory $customerFactory;
    protected CustomerRepositoryInterface $customerRepository;
    protected StoreManagerInterface $storeManager;
    protected LoggerInterface $logger;

    /**
     * @param CustomerInterfaceFactory $customerFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        CustomerInterfaceFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    )
    {
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    protected function saveCustomer($data): bool
    {
        try {
            $customer = $this->customerFactory->create();
            $customer->setStoreId($this->storeManager->getStore()->getId());
            $customer->setWebsiteId($this->storeManager->getStore()->getWebsiteId());
            $customer->setFirstname($data['fname']);
            $customer->setLastname($data['lname']);
            $customer->setEmail($data['emailaddress']);

            $this->customerRepository->save($customer);
            return true;
        } catch (\Exception $e) {
            $this->logger->error("Error while import customer $data[fname]: " . $e->getMessage());
            return false;
        }
    }
}
