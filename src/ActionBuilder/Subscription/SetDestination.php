<?php

declare(strict_types=1);

namespace BestIt\CommercetoolsODM\ActionBuilder\Subscription;

use BestIt\CommercetoolsODM\Mapping\ClassMetadataInterface;
use Commercetools\Core\Model\Extension\Destination;
use Commercetools\Core\Request\Subscriptions\Command\SubscriptionChangeDestinationAction;
use Commercetools\Core\Request\Subscriptions\Command\SubscriptionSetKeyAction;

/**
 * Reacts on the change of the destination field and creates the update action.
 *
 * @author Michel Chowanski <michel.chowanski@bestit-online.de>
 * @package BestIt\CommercetoolsODM\ActionBuilder\Subscription
 */
class SetDestination extends SubscriptionActionBuilder
{
    /**
     * @var string Working on the destination field.
     */
    protected $fieldName = 'destination';

    /**
     * Creates the update actions for the given class and data.
     *
     * @param mixed $changedValue
     * @param ClassMetadataInterface $metadata
     * @param array $changedData
     * @param array $oldData
     * @param mixed $sourceObject
     *
     * @return SubscriptionSetKeyAction[]
     */
    public function createUpdateActions(
        $changedValue,
        ClassMetadataInterface $metadata,
        array $changedData,
        array $oldData,
        $sourceObject
    ): array {
        if (!is_array($changedValue) || count($changedValue) === 0) {
            return [];
        }
        
        $action = new SubscriptionChangeDestinationAction();
        $action->setDestination(new Destination(
            array_replace($oldData['destination'] ?? [], $changedValue)
        ));

        return [$action];
    }
}
