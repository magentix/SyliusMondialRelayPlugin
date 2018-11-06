<?php
/**
 * @author    Matthieu Vion
 * @copyright 2018 Magentix
 * @license   https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 * @link      https://github.com/magentix/mondial-relay-plugin
 */
declare(strict_types=1);

namespace Magentix\SyliusMondialRelayPlugin\Shipping\Calculator;

use Magentix\SyliusPickupPlugin\Shipping\Calculator\CalculatorInterface;
use Magentix\SyliusMondialRelayPlugin\Repository\PickupRepository;
use BitBag\SyliusShippingExportPlugin\Repository\ShippingGatewayRepository;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Webmozart\Assert\Assert;
use stdClass;

final class MondialRelayCalculator implements CalculatorInterface
{

    public const MONDIAL_RELAY_CODE_24R = '24R';

    public const MONDIAL_RELAY_CODE_24L = '24L';

    public const MONDIAL_RELAY_CODE_DRI = 'DRI';

    /**
     * @var PickupRepository $pickupRepository
     */
    private $pickupRepository;

    /**
     * @var ShippingGatewayRepository $shippingGatewayRepository
     */
    private $shippingGatewayRepository;

    /**
     * @param PickupRepository $pickupRepository
     * @param ShippingGatewayRepository $shippingGatewayRepository
     */
    public function __construct(
        PickupRepository $pickupRepository,
        ShippingGatewayRepository $shippingGatewayRepository
    ) {
        $this->pickupRepository = $pickupRepository;
        $this->shippingGatewayRepository = $shippingGatewayRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function calculate(ShipmentInterface $subject, array $configuration): int
    {
        Assert::isInstanceOf($subject, ShipmentInterface::class);

        $weight = $subject->getShippingWeight();

        foreach ($configuration['ranges'] as $range) {
            if ($range['fromValue'] <= $weight && $weight < $range['toValue']) {
                return (int) $range['amount'];
            }
        }

        return 0;
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
     * @param ShippingMethodInterface $shippingMethod
     * @return array
     */
    public function getPickupList(
        AddressInterface $address,
        OrderInterface $cart,
        ShippingMethodInterface $shippingMethod
    ): array {
        $gateway = $this->shippingGatewayRepository->findOneByShippingMethod($shippingMethod);

        if (!$gateway) {
            $result['error'] = 'mondial_relay.pickup.list.error.gateway';
            return  $result;
        }

        $configuration = $gateway->getConfig();
        $this->pickupRepository->setConfig($configuration);

        $shippingWeight = $cart->getShipments()->current()->getShippingWeight();

        if (!isset($configuration['product_weight'])) {
            $configuration['product_weight'] = 1;
        }

        if ($shippingWeight > (150 * $configuration['product_weight'])) {
            $result['error'] = 'mondial_relay.pickup.list.error.max_size';
            return  $result;
        }

        $shippingCode = $this->getShippingCode($shippingWeight, $configuration['product_weight']);

        $result = $this->pickupRepository->findAll(
            $address->getPostcode(),
            $address->getCountryCode(),
            $shippingCode,
            (int) $configuration['pickup_number']
        );

        if ($result['error']) {
            $result['error'] = 'mondial_relay.pickup.list.error.' . $result['error'];
            return $result;
        }

        if (!isset($result['response']->PointsRelais->PointRelais_Details)) {
            $result['error'] = 'mondial_relay.pickup.list.error.empty';
            return  $result;
        }

        $pickup = $result['response']->PointsRelais->PointRelais_Details;
        if (!is_array($pickup)) {
            $pickup = [$pickup];
        }

        foreach ($pickup as $data) {
            $result['pickup'][] = $this->convert($data, $shippingCode);
        }

        unset($result['response']);

        return $result;
    }

    /**
     * Retrieve pickup address
     *
     * @param string $pickupId
     * @param ShippingMethodInterface $shippingMethod
     * @return array
     */
    public function getPickupAddress(string $pickupId, ShippingMethodInterface $shippingMethod): array
    {
        list($id, $shippingCode, $country) = explode('-', $pickupId);

        $gateway = $this->shippingGatewayRepository->findOneByShippingMethod($shippingMethod);

        $this->pickupRepository->setConfig($gateway->getConfig());

        $result = $this->pickupRepository->find($id, $country);

        if (!$result['error']) {
            $pickup = $result['response']->PointsRelais->PointRelais_Details;
            if (!is_array($pickup)) {
                $pickup = [$pickup];
            }

            foreach ($pickup as $data) {
                $result['pickup'] = $this->convert($data, $shippingCode);
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
        return '@MagentixSyliusMondialRelayPlugin/checkout/SelectShipping/pickup/list.html.twig';
    }

    /**
     * Convert pickup list for print
     *
     * @param stdClass $pickup
     * @param string $shippingCode
     * @return array
     */
    public function convert(stdClass $pickup, string $shippingCode): array
    {
        $pickupId = [$pickup->Num, $shippingCode, $pickup->Pays];

        return [
            'id'         => join('-', $pickupId),
            'company'    => strtoupper($pickup->LgAdr1),
            'street_1'   => strtolower($pickup->LgAdr3),
            'street_2'   => strtolower($pickup->LgAdr4),
            'city'       => strtoupper($pickup->Ville),
            'country'    => $pickup->Pays,
            'postcode'   => $pickup->CP,
            'latitude'   => preg_replace('/,/', '.', $pickup->Latitude),
            'longitude'  => preg_replace('/,/', '.', $pickup->Longitude),
            'opening'    => [
                'monday'    => $this->formatOpening($pickup->Horaires_Lundi),
                'tuesday'   => $this->formatOpening($pickup->Horaires_Mardi),
                'wednesday' => $this->formatOpening($pickup->Horaires_Mercredi),
                'thursday'  => $this->formatOpening($pickup->Horaires_Jeudi),
                'friday'    => $this->formatOpening($pickup->Horaires_Vendredi),
                'saturday'  => $this->formatOpening($pickup->Horaires_Samedi),
                'sunday'    => $this->formatOpening($pickup->Horaires_Dimanche)
            ]
        ];
    }

    /**
     * Retrieve Shipping Code
     *
     * @param float $weight
     * @param float $weightRate
     * @return string
     */
    public function getShippingCode(float $weight, float $weightRate = 1): string
    {
        $shippingCode = self::MONDIAL_RELAY_CODE_24R;
        if ($weight > (30 * $weightRate)) {
            $shippingCode = self::MONDIAL_RELAY_CODE_24L;
        }
        if ($weight > (50 * $weightRate)) {
            $shippingCode = self::MONDIAL_RELAY_CODE_DRI;
        }

        return $shippingCode;
    }

    /**
     * Format opening day
     *
     * @param stdClass $opening
     * @return string
     */
    public function formatOpening(stdClass $opening): string
    {
        $date = '';

        if ($opening) {
            foreach ($opening as $hour) {
                if (intval($hour[0]) && intval($hour[1])) {
                    $date .= $this->formatHour($hour[0]) . ' - ' . $this->formatHour($hour[1]);
                }

                if (intval($hour[2]) && intval($hour[3])) {
                    $date .= ($date ? ' / ' : '') . $this->formatHour($hour[2]) . ' - ' . $this->formatHour($hour[3]);
                }
            }
        }

        return $date;
    }

    /**
     * Format hour
     *
     * @param string $hour
     * @return string
     */
    public function formatHour(string $hour): string
    {
        return substr($hour, 0, 2) . 'h' . substr($hour, 2, 2);
    }
}
