<?php declare(strict_types=1);

namespace Pudo;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Context;
use Pudo\Service\CustomFieldsInstaller;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\Checkout\Shipping\ShippingMethodEntity;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;

class Pudo extends Plugin
{
    private ShippingMethodEntity $shippingMethodEntity;

    public function install(InstallContext $installContext): void
    {
        // Do stuff such as creating a new payment method
        $context = $installContext->getContext();

        $this->createShippingMethod($context);
        $this->getCustomFieldsInstaller()->install($context);
    }

    private function createShippingMethod(Context $context): void
    {
        $deliveryTimeRepository = $this->container->get('delivery_time.repository');

        $deliveryTimeCriteria = new Criteria();
        $deliveryTimeCriteria->addFilter(new EqualsFilter('unit', 'day'));
        $deliveryTimeCriteria->setLimit(1);

        $deliveryTimeId = $deliveryTimeRepository->searchIds($deliveryTimeCriteria, $context)->getIds();

        $shippingMethodRepository = $this->container->get('shipping_method.repository');
        $shippingMethodData = [
            'name' => 'Shipping Pudo',
            'active' => true,
            'description' => 'Description of the shipping method.',
            'tracking_url' => '',
            'deliveryTimeId' => $deliveryTimeId[0],
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            'technicalName' => 'shipping_pudo',
        ];
        $shippingMethodRepository->create(
            [$shippingMethodData],
            $context
        );

        $salesChannelRepository = $this->container->get('sales_channel.repository');
        $salesChannelCriteria = new Criteria();
        $salesChannelCriteria->addFilter(
            new NotFilter(
                NotFilter::CONNECTION_AND,
                [new EqualsFilter('homeCmsPageId', null)]
            )
        );

        $salesChannelCriteria->setLimit(1);
        $salesChannelId = $salesChannelRepository->searchIds($salesChannelCriteria, $context)->getIds();

        $shippingMethodCriteria = new Criteria();
        $shippingMethodCriteria->addSorting(new FieldSorting('createdAt', FieldSorting::DESCENDING));
        $shippingMethodCriteria->setLimit(1);
        $lastShippingMethodId = $shippingMethodRepository->searchIds($shippingMethodCriteria, $context)->getIds();
        $salesChannelShippingMethodData = [
            'salesChannelId' => $salesChannelId[0],
            'shippingMethodId' => $lastShippingMethodId[0]
        ];

        $salesChannelShippingMethodRepository = $this->container->get('sales_channel_shipping_method.repository');
        $salesChannelShippingMethodRepository->create(
            [$salesChannelShippingMethodData],
            $context
        );

        $shippingMethodPriceRepository = $this->container->get('shipping_method_price.repository');
        $shippingMethodPriceData = [
            'shippingMethodId' => $lastShippingMethodId[0],
            'calculation' => 1,
            'currencyPrice' => [
                [
                    'currencyId' => $context->getCurrencyId(),
                    'gross' => 0,
                    'net' => 0,
                    'linked' => false
                ]
            ],
            'quantityStart' => 0,
            'createdAt' => (new \DateTime())->format('Y-m-d H:i:s')
        ];

        $shippingMethodPriceRepository->create(
            [$shippingMethodPriceData],
            $context
        );
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }

        // Remove or deactivate the data created by the plugin
    }

    public function activate(ActivateContext $activateContext): void
    {
        // Activate entities, such as a new payment method
        // Or create new entities here, because now your plugin is installed and active for sure
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        // Deactivate entities, such as a new payment method
        // Or remove previously created entities
    }

    public function update(UpdateContext $updateContext): void
    {
        // Update necessary stuff, mostly non-database related
    }

    public function postInstall(InstallContext $installContext): void
    {
    }

    public function postUpdate(UpdateContext $updateContext): void
    {
    }

    private function getCustomFieldsInstaller(): CustomFieldsInstaller
    {
        if ($this->container->has(CustomFieldsInstaller::class)) {
            return $this->container->get(CustomFieldsInstaller::class);
        }

        return new CustomFieldsInstaller(
            $this->container->get('custom_field_set.repository'),
            $this->container->get('custom_field_set_relation.repository'),
            $this->container->get('custom_field.repository')
        );
    }
}
