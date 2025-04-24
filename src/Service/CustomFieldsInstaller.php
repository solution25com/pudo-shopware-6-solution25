<?php declare(strict_types=1);

namespace Pudo\Service;

use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\CustomField\CustomFieldTypes;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class CustomFieldsInstaller
{
    private const CUSTOM_FIELDSET_NAME = 'pudo_dealer';

    private const CUSTOM_FIELDSET = [
        'name' => self::CUSTOM_FIELDSET_NAME,
        'config' => [
            'label' => [
                'en-GB' => 'Pudo Dealer',
                'de-DE' => 'Händlerfelder',
                Defaults::LANGUAGE_SYSTEM => 'Pudo Dealer'
            ]
        ],
        'customFields' => [
            [
                'name' => 'dealerAddress1',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Dealer Address Line 1',
                        'de-DE' => 'Händler Adresse Zeile 1',
                        Defaults::LANGUAGE_SYSTEM => 'Dealer Address Line 1'
                    ],
                    'customFieldPosition' => 1
                ]
            ],
            [
                'name' => 'dealerAddress2',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Dealer Address Line 2',
                        'de-DE' => 'Händler Adresse Zeile 2',
                        Defaults::LANGUAGE_SYSTEM => 'Dealer Address Line 2'
                    ],
                    'customFieldPosition' => 2
                ]
            ],
            [
                'name' => 'dealerAnnualFee',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Dealer Annual Fee',
                        'de-DE' => 'Händler Jahresgebühr',
                        Defaults::LANGUAGE_SYSTEM => 'Dealer Annual Fee'
                    ],
                    'customFieldPosition' => 3
                ]
            ],
            [
                'name' => 'dealerCity',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Dealer City',
                        'de-DE' => 'Händler Stadt',
                        Defaults::LANGUAGE_SYSTEM => 'Dealer City'
                    ],
                    'customFieldPosition' => 4
                ]
            ],
            [
                'name' => 'dealerCountry',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Dealer Country',
                        'de-DE' => 'Händler Land',
                        Defaults::LANGUAGE_SYSTEM => 'Dealer Country'
                    ],
                    'customFieldPosition' => 5
                ]
            ],
            [
                'name' => 'dealerDistance',
                'type' => CustomFieldTypes::FLOAT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Dealer Distance',
                        'de-DE' => 'Händler Entfernung',
                        Defaults::LANGUAGE_SYSTEM => 'Dealer Distance'
                    ],
                    'customFieldPosition' => 6
                ]
            ],
            [
                'name' => 'dealerHours',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Dealer Hours',
                        'de-DE' => 'Händler Öffnungszeiten',
                        Defaults::LANGUAGE_SYSTEM => 'Dealer Hours'
                    ],
                    'customFieldPosition' => 7
                ]
            ],
            [
                'name' => 'dealerID',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Dealer ID',
                        'de-DE' => 'Händler ID',
                        Defaults::LANGUAGE_SYSTEM => 'Dealer ID'
                    ],
                    'customFieldPosition' => 8
                ]
            ],
            [
                'name' => 'dealerLanguages',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Dealer Languages',
                        'de-DE' => 'Händler Sprachen',
                        Defaults::LANGUAGE_SYSTEM => 'Dealer Languages'
                    ],
                    'customFieldPosition' => 9
                ]
            ],
            [
                'name' => 'dealerLatitude',
                'type' => CustomFieldTypes::FLOAT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Dealer Latitude',
                        'de-DE' => 'Händler Breitengrad',
                        Defaults::LANGUAGE_SYSTEM => 'Dealer Latitude'
                    ],
                    'customFieldPosition' => 10
                ]
            ],
            [
                'name' => 'dealerLongitude',
                'type' => CustomFieldTypes::FLOAT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Dealer Longitude',
                        'de-DE' => 'Händler Längengrad',
                        Defaults::LANGUAGE_SYSTEM => 'Dealer Longitude'
                    ],
                    'customFieldPosition' => 11
                ]
            ],
            [
                'name' => 'dealerName',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Dealer Name',
                        'de-DE' => 'Händler Name',
                        Defaults::LANGUAGE_SYSTEM => 'Dealer Name'
                    ],
                    'customFieldPosition' => 12
                ]
            ],
            [
                'name' => 'dealerNo',
                'type' => CustomFieldTypes::INT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Dealer Number',
                        'de-DE' => 'Händler Nummer',
                        Defaults::LANGUAGE_SYSTEM => 'Dealer Number'
                    ],
                    'customFieldPosition' => 13
                ]
            ],
            [
                'name' => 'dealerOpen24S',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Dealer Open 24/7',
                        'de-DE' => 'Händler 24/7 geöffnet',
                        Defaults::LANGUAGE_SYSTEM => 'Dealer Open 24/7'
                    ],
                    'customFieldPosition' => 14
                ]
            ],
            [
                'name' => 'dealerPhone',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Dealer Phone',
                        'de-DE' => 'Händler Telefon',
                        Defaults::LANGUAGE_SYSTEM => 'Dealer Phone'
                    ],
                    'customFieldPosition' => 15
                ]
            ],
            [
                'name' => 'dealerPostal',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Dealer Postal Code',
                        'de-DE' => 'Händler Postleitzahl',
                        Defaults::LANGUAGE_SYSTEM => 'Dealer Postal Code'
                    ],
                    'customFieldPosition' => 16
                ]
            ],
            [
                'name' => 'dealerProvince',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Dealer Province',
                        'de-DE' => 'Händler Provinz',
                        Defaults::LANGUAGE_SYSTEM => 'Dealer Province'
                    ],
                    'customFieldPosition' => 17
                ]
            ],
            [
                'name' => 'dealerSupports',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Dealer Supports',
                        'de-DE' => 'Händler Unterstützt',
                        Defaults::LANGUAGE_SYSTEM => 'Dealer Supports'
                    ],
                    'customFieldPosition' => 18
                ]
            ],
            [
                'name' => 'company',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Company',
                        'de-DE' => 'Unternehmen',
                        Defaults::LANGUAGE_SYSTEM => 'Company'
                    ],
                    'customFieldPosition' => 18
                ]
            ]
        ]
    ];

    public function __construct(
        private readonly EntityRepository $customFieldSetRepository,
        private readonly EntityRepository $customFieldSetRelationRepository,
        private readonly EntityRepository $customFieldRepository
    ) {
    }

    public function install(Context $context): void
    {
        $this->customFieldSetRepository->upsert([
            self::CUSTOM_FIELDSET
        ], $context);
        $this->addRelations($context);
    }

    public function uninstall(Context $context): void
    {
        // provide code to delete custom fields and custom field set
        // if you want to remove the custom fields and custom field set upon uninstall
        $customFieldSetId = $this->getCustomFieldSetId($context);
        $this->deleteCustomFields($context);
        $this->customFieldSetRepository->delete([
            ['id' => $customFieldSetId]
        ], $context);
    }

    public function addRelations(Context $context): void
    {
        $this->customFieldSetRelationRepository->upsert(array_map(function (string $customFieldSetId) {
            return [
                'customFieldSetId' => $customFieldSetId,
                'entityName' => 'order',
            ];
        }, $this->getCustomFieldSetIds($context)), $context);
    }

    /**
     * @return string[]
     */
    private function getCustomFieldSetIds(Context $context): array
    {
        $criteria = new Criteria();

        $criteria->addFilter(new EqualsFilter('name', self::CUSTOM_FIELDSET_NAME));

        return $this->customFieldSetRepository->searchIds($criteria, $context)->getIds();
    }

    /**
     * Get the custom field set ID.
     */
    private function getCustomFieldSetId(Context $context): ?string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', self::CUSTOM_FIELDSET_NAME));

        $ids = $this->customFieldSetRepository->searchIds($criteria, $context)->getIds();
        return !empty($ids) ? $ids[0] : null;
    }

    /**
     * Delete custom fields associated with the fieldset.
     */
    private function deleteCustomFields(Context $context): void
    {
        // provide code to delete custom fields
        // if you want to remove the custom fields upon uninstall
//        $

        $customFieldId = $this->getCustomFieldId($context);

        if ($customFieldId) {
            $this->customFieldRepository->delete([
                ['id' => $customFieldId]
            ], $context);
        }
    }

    /**
     * Get the custom field ID.
     */
    private function getCustomFieldId(Context $context): ?string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', 'custom_age_confirmed'));

        $ids = $this->customFieldRepository->searchIds($criteria, $context)->getIds();
        return !empty($ids) ? $ids[0] : null;
    }
}
