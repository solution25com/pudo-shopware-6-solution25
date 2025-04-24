<?php declare(strict_types=1);

namespace Pudo\Subscriber;

use Pudo\Service\PudoPointClient;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\HttpFoundation\RequestStack;
use Shopware\Core\Checkout\Cart\Event\CartSavedEvent;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Storefront\Page\Checkout\Confirm\CheckoutConfirmPageLoadedEvent;

class PudoCheckoutSubscriber implements EventSubscriberInterface
{
    private SystemConfigService $systemConfigService;
    private EntityRepository $orderRepository;
    private EntityRepository $customerRepository;
    private EntityRepository $orderAddressRepository;
    private RequestStack $requestStack;
    private string $pudoPointField;
    private PudoPointClient $pudoPointClient;

    public function __construct(SystemConfigService $systemConfigService, EntityRepository $orderRepository, EntityRepository $customerRepository, EntityRepository $orderAddressRepository, RequestStack $requestStack, PudoPointClient $pudoPointClient)
    {
        $this->systemConfigService = $systemConfigService;
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->requestStack = $requestStack;
        $this->pudoPointClient = $pudoPointClient;
        $this->pudoPointField = 'pudo-point';
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckoutOrderPlacedEvent::class => 'onCheckoutOrderPlaced',
            CartSavedEvent::class => 'onCartSaved',
        ];
    }

    public function onCartSaved(CartSavedEvent $event): void
    {
        $requestStack = $this->requestStack->getCurrentRequest();
        $pudoPointId = $requestStack->get($this->pudoPointField);

        $source = $event->getContext()->getSource()->type;
        $scope = $event->getContext()->getScope();

        if ($pudoPointId) {
            $requestStack->getSession()->set($this->pudoPointField, $pudoPointId);
        }
    }

    public function onCheckoutOrderPlaced(CheckoutOrderPlacedEvent $event): void
    {
        $salesChannelId = $event->getSalesChannelId();
        $context = $event->getContext();
        $b2bDisableCustomerGroups = $this->systemConfigService->get('Pudo.config.customerGroups', $salesChannelId);

        $session = $this->requestStack->getSession();
        $context = $event->getContext();

        $customerId = $event->getCustomerId();
        $criteria = new Criteria([$customerId]);
        $criteria->addAssociation('defaultBillingAddress');
        $customer = $this->customerRepository->search($criteria, $context)->first();
        $customerGroupId = $customer->getGroupId();
        if (is_array($b2bDisableCustomerGroups) && in_array($customerGroupId, $b2bDisableCustomerGroups)) {
            return;
        }
        $order = $event->getOrder();

        if($session->has($this->pudoPointField) &&
            $session->get($this->pudoPointField) != '-1' &&
            $order->getDeliveries()->first()->getShippingMethod()->getTechnicalName() === 'shipping_pudo'
        ) {
            $dealersResponse = $this->pudoPointClient->request($customer->getDefaultBillingAddress()->getZipcode());

            $dealerId = $session->get($this->pudoPointField);

            if ($dealersResponse->Result === 'SUCCESS' && isset($dealersResponse->dealers)) {
                $dealer = array_filter($dealersResponse->dealers, function($dealer) use ($dealerId) {
                    return $dealer->dealerID === $dealerId;
                });

                if (count($dealer) === 0) {
                    $dealer = null;
                } else {
                    $dealer = array_shift($dealer);

                    $order = $event->getOrder();
                    $customFields = $order->getCustomFields();
                    $customFields['dealerID'] = $dealer->dealerID;
                    $customFields['dealerAddress1'] = $dealer->dealerAddress1;
                    $customFields['dealerAddress2'] = $dealer->dealerAddress2;
                    $customFields['dealerAnnualFee'] = $dealer->dealerAnnualFee;
                    $customFields['dealerCity'] = $dealer->dealerCity;
                    $customFields['dealerCountry'] = $dealer->dealerCountry;
                    $customFields['dealerDistance'] = $dealer->dealerDistance;
                    $customFields['dealerHours'] = $dealer->dealerHours;
                    $customFields['dealerLanguages'] = $dealer->dealerLanguages;
                    $customFields['dealerLatitude'] = $dealer->dealerLatitude;
                    $customFields['dealerLongitude'] = $dealer->dealerLongitude;
                    $customFields['dealerName'] = $dealer->dealerName;
                    $customFields['dealerNo'] = $dealer->dealerNo;
                    $customFields['dealerOpen24S'] = $dealer->dealerOpen24S;
                    $customFields['dealerPhone'] = $dealer->dealerPhone;
                    $customFields['dealerPostal'] = $dealer->dealerPostal;
                    $customFields['dealerProvince'] = $dealer->dealerProvince;
                    $customFields['dealerSupports'] = $dealer->dealerSupports;
                    $customFields['company'] = "PUDO-$dealer->dealerNo";

                    $order->setCustomFields($customFields);

                    $orderBillingAddressId = $order->getBillingAddressId();
                    $order->setBillingAddressId($customer->getDefaultBillingAddressId());
                    $shippingDeliveryId = $order->getDeliveries()->first()->getId();

                    $newShippingAddressId = Uuid::randomHex();
                    $this->orderAddressRepository->upsert(
                        [
                            [
                                'id' => $newShippingAddressId,
                                'orderId' => $order->getId(),
                                'firstName' => $dealer->dealerName,
                                'lastName' => $dealer->dealerName,
                                'street' => $dealer->dealerAddress1,
                                'city' => $dealer->dealerCity,
                                'countryId' => $order->getAddresses()->first()->getCountryId()
                            ]
                        ],
                        $context
                    );

                    $this->orderRepository->update(
                        [
                            [
                                'id' => $order->getId(),
                                'customFields' => $customFields,
                                'deliveries' => [
                                    [
                                        'id' => $shippingDeliveryId,
                                        'shippingOrderAddressId' => $newShippingAddressId,
                                    ]
                                ],
                            ]
                        ],
                        $context
                    );
                }
            }
            $session->remove($this->pudoPointField);
        }
        $request = $this->requestStack->getCurrentRequest();

        $order = $event->getOrder();
        $customFieldValue = $order->getCustomFields()['customFieldi'] ?? null;
    }

    public function checkoutConfirmPageLoaded(CheckoutConfirmPageLoadedEvent $event): void
    {
    }
}