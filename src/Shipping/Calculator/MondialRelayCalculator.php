<?php

declare(strict_types=1);

namespace MagentixMondialRelayPlugin\Shipping\Calculator;

use MagentixPickupPlugin\Shipping\Calculator\CalculatorInterface;
use MagentixMondialRelayPlugin\Repository\PickupRepository;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use stdClass;

final class MondialRelayCalculator implements CalculatorInterface
{

    /**
     * @var PickupRepository $pickupRepository
     */
    private $pickupRepository;

    /**
     * @param PickupRepository $pickupRepository
     */
    public function __construct(
        PickupRepository $pickupRepository
    ) {
        $this->pickupRepository = $pickupRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function calculate(ShipmentInterface $subject, array $configuration): int
    {
        // $subject->getShippingWeight();
        return (int) $configuration['amount'];
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'mondial_relay';
    }

    /**
     * Retrieve pickup list
     *
     * @param AddressInterface $address
     * @param OrderInterface $cart
     * @param array $configuration
     * @return array
     */
    public function getPickupList(AddressInterface $address, OrderInterface $cart, array $configuration): array
    {
        $this->pickupRepository->setConfig($configuration);

        $result = $this->pickupRepository->findAll($address->getPostcode(), $address->getCountryCode());

        if (!$result['error']) {
            $pickup = $result['response']->PointsRelais->PointRelais_Details;
            if (!is_array($pickup)) {
                $pickup = [$pickup];
            }

            foreach ($pickup as $data) {
                $result['pickup'][] = $this->convert($data);
            }

            unset($result['response']);
        }

        return $result;
    }

    /**
     * Retrieve pickup address
     *
     * @param string $pickupId
     * @param array $configuration
     * @return array
     */
    public function getPickupAddress(string $pickupId, array $configuration): array
    {
        list($id, $code, $country) = explode('-', $pickupId);

        $this->pickupRepository->setConfig($configuration);

        $result = $this->pickupRepository->find($id, $country);

        if (!$result['error']) {
            $pickup = $result['response']->PointsRelais->PointRelais_Details;
            if (!is_array($pickup)) {
                $pickup = [$pickup];
            }

            foreach ($pickup as $data) {
                $result['pickup'] = $this->convert($data);
            }

            unset($result['response']);
        }

        return $result;
    }

    /**
     * Retrieve Pickup template
     *
     * @return string
     */
    public function getPickupTemplate(): string
    {
        return '@MagentixMondialRelayPlugin/checkout/SelectShipping/pickup/list.html.twig';
    }

    /**
     * Convert pickup list for print
     *
     * @param stdClass $pickup
     * @return array
     */
    public function convert(stdClass $pickup)
    {
        $pickupId = [$pickup->Num, '24R', $pickup->Pays];

        return [
            'id'         => join('-', $pickupId),
            'company'    => $pickup->LgAdr1,
            'street_1'   => $pickup->LgAdr3,
            'street_2'   => $pickup->LgAdr4,
            'city'       => $pickup->Ville,
            'country'    => $pickup->Pays,
            'postcode'   => $pickup->CP,
            'latitude'   => $pickup->Latitude,
            'longitude'  => $pickup->Longitude
        ];
    }
}
