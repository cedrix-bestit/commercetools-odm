<?php

namespace BestIt\CommercetoolsODM\ActionBuilder\Product;

use BestIt\CommercetoolsODM\Mapping\ClassMetadataInterface;
use Commercetools\Core\Model\TaxCategory\TaxCategoryReference;
use Commercetools\Core\Request\AbstractAction;
use Commercetools\Core\Request\Products\Command\ProductSetTaxCategoryAction;

/**
 * Sets the tax category.
 *
 * @author blange <lange@bestit-online.de>
 * @package BestIt\CommercetoolsODM\ActionBuilder\Product
 * @subpackage ActionBuilder\Product
 */
class SetTaxCategory extends ProductActionBuilder
{
    /**
     * A PCRE to match the hierarchical field path without delimiter.
     *
     * @var string
     */
    protected $complexFieldFilter = '^taxCategory$';

    /**
     * Creates the update actions for the given class and data.
     *
     * @todo The documentation does not tell of the staged param in the action. what does that mean?
     *
     * @param mixed $changedValue
     * @param ClassMetadataInterface $metadata
     * @param array $changedData
     * @param array $oldData
     * @param mixed $sourceObject
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
        $action = new ProductSetTaxCategoryAction();

        if ($changedValue) {
            if (isset($changedValue['id']) && $changedValue['id'] !== null) {
                $action->setTaxCategory(TaxCategoryReference::ofId($changedValue['id']));
            }
            if (isset($changedValue['key']) && $changedValue['key'] !== null) {
                $action->setTaxCategory(TaxCategoryReference::ofKey($changedValue['key']));
            }
        }
        return [$action];
    }
}
