<?php

declare(strict_types=1);

namespace BestIt\CommercetoolsODM\ActionBuilder\ProductType;

use BestIt\CommercetoolsODM\Mapping\ClassMetadataInterface;
use Commercetools\Core\Model\ProductType\ProductType;
use Commercetools\Core\Request\AbstractAction;
use Commercetools\Core\Request\ProductTypes\Command\ProductTypeAddLocalizedEnumValueAction;
use Commercetools\Core\Request\ProductTypes\Command\ProductTypeChangeLocalizedEnumLabelAction;
use Commercetools\Core\Request\ProductTypes\Command\ProductTypeRemoveEnumValuesAction;

/**
 * ActionBuilder to add, remove and modify localized enum values
 *
 * @author Michel Chowanski <michel.chowanski@bestit-online.de>
 * @package BestIt\CommercetoolsODM\ActionBuilder\ProductType
 */
class ChangeLocalizedEnumValues extends ProductTypeActionBuilder
{
    /**
     * @var string The field name.
     */
    protected $complexFieldFilter = '^attributes\/(\w+)\/type\/values$';

    /**
     * Creates the update actions for the given class and data.
     *
     * @param mixed $changedValue
     * @param ClassMetadataInterface $metadata
     * @param array $changedData
     * @param array $oldData
     * @param ProductType $sourceObject
     *
     * @return AbstractAction[]
     */
    public function createUpdateActions(
        $changedValue,
        ClassMetadataInterface $metadata,
        array $changedData,
        array $oldData,
        $sourceObject
    ): array {
        $actions = [];

        list(, $attrIndex) = $this->getLastFoundMatch();
        $attribute = $sourceObject->getAttributes()->toArray()[$attrIndex];

        // Only apply on _localized_ enums
        if ($attribute['type']['name'] !== 'lenum') {
            return [];
        }

        // Do no apply on new attributes
        if (!array_key_exists($attrIndex, $oldData['attributes'])) {
            return [];
        }

        $knownValues = [];
        foreach ($oldData['attributes']  as $item) {
            if ($item['name'] === $attribute['name']) {
                foreach ($item['type']['values'] as $value) {
                    $knownValues[$value['key']] = $value;
                }
            }
        }

        // Detect new enum or modified
        foreach ($changedValue as $item) {
            // If key is unknown, then create new enum
            if (!array_key_exists($item['key'], $knownValues)) {
                $actions[] = ProductTypeAddLocalizedEnumValueAction::fromArray([
                    'attributeName' => $attribute['name'],
                    'value' => $item
                ]);

                $knownValues[$item['key']] = $item;

                continue;
            }

            // If key is known, check if label are changed
            $knownValue = $knownValues[$item['key']];
            if ($knownValue !== $item) {
                $actions[] = ProductTypeChangeLocalizedEnumLabelAction::fromArray([
                    'attributeName' => $attribute['name'],
                    'newValue' => $item
                ]);
            }
        }

        // Detect removed enum values
        $allCurrentKeys = array_column($attribute['type']['values'], 'key');
        $removedKeys = array_values(array_diff(array_keys($knownValues), $allCurrentKeys));

        if (count($removedKeys) > 0) {
            $actions[] = ProductTypeRemoveEnumValuesAction::fromArray([
                'attributeName' => $attribute['name'],
                'keys' => $removedKeys
            ]);
        }

        return $actions;
    }
}
