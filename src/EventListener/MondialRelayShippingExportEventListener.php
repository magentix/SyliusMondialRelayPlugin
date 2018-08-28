<?php
/**
 * @author    Matthieu Vion
 * @copyright 2018 Magentix
 * @license   https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 * @link      https://github.com/magentix/mondial-relay-plugin
 */
declare(strict_types=1);

namespace Magentix\SyliusMondialRelayPlugin\EventListener;

use Magentix\SyliusMondialRelayPlugin\Repository\PickupRepository;
use BitBag\SyliusShippingExportPlugin\Event\ExportShipmentEvent;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Magentix\SyliusPickupPlugin\Entity\Shipment;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

final class MondialRelayShippingExportEventListener
{
    /**
     * @var PickupRepository $pickupRepository
     */
    private $pickupRepository;

    /**
     * @var EntityManagerInterface|EntityManager $shipmentManager
     */
    private $shipmentManager;

    /**
     * @param PickupRepository $pickupRepository
     * @param EntityManagerInterface|EntityManager $shipmentManager
     */
    public function __construct(
        PickupRepository $pickupRepository,
        EntityManagerInterface $shipmentManager
    ) {
        $this->pickupRepository = $pickupRepository;
        $this->shipmentManager = $shipmentManager;
    }

    /**
     * @param ExportShipmentEvent $event
     * @return void
     */
    public function exportShipment(ExportShipmentEvent $event): void
    {
        /** @var ShippingExportInterface $shippingExport */
        $shippingExport = $event->getShippingExport();

        /** @var ShippingGatewayInterface $shippingGateway */
        $shippingGateway = $shippingExport->getShippingGateway();

        $configuration = $shippingGateway->getConfig();

        if (!array_key_exists('mondial_relay', $configuration)) {
            return;
        }

        $shipment = $shippingExport->getShipment();
        $order    = $shipment->getOrder();

        if ($configuration['label_generate'] === 1) {
            /* Create Shipment in Mondial Relay database */
            $result = $this->createShipping($order, $shipment, $configuration);

            if ($result['error']) {
                $event->addErrorFlash('mondial_relay.pickup.list.error.' . $result['error']);
                return;
            }

            $shipment->setTracking($result['response']->ExpeditionNum);

            /* Retrieve Shipping Label from shipment */
            $result = $this->getShippingLabel(
                $result['response']->ExpeditionNum,
                $order->getShippingAddress()->getCountryCode(),
                $configuration
            );

            if ($result['error']) {
                $event->addErrorFlash('mondial_relay.pickup.list.error.' . $result['error']);
                return;
            }

            $labelSize = $configuration['label_size'];
            $label = file_get_contents('https://www.mondialrelay.fr' . $result['response']->$labelSize);

            $event->saveShippingLabel($label, 'pdf');
        }

        $shipment->setState(ShipmentInterface::STATE_SHIPPED);
        $order->setShippingState(ShipmentInterface::STATE_SHIPPED);

        $this->shipmentManager->flush($shipment);
        $this->shipmentManager->flush($order);

        $event->addSuccessFlash();
        $event->exportShipment();
    }

    /**
     * Retrieve Shipping Label content
     *
     * @param OrderInterface $order
     * @param Shipment|ShipmentInterface $shipment
     * @param array $configuration
     * @return array
     */
    protected function createShipping(OrderInterface $order, ShipmentInterface $shipment, array $configuration): array
    {
        $shippingAddress = $order->getShippingAddress();

        list($id, $code, $country) = explode('-', $shipment->getPickupId());

        if (!isset($configuration['product_weight'])) {
            $configuration['product_weight'] = 1;
        }

        $weight = ($shipment->getShippingWeight() * 1000) / $configuration['product_weight'];

        $data = [
            'ModeCol'      => 'CCC',
            'ModeLiv'      => $code,
            'NDossier'     => $order->getNumber(),
            'NClient'      => $order->getCustomer()->getId(),
            'Expe_Langage' => $this->getLanguage($configuration['label_shipper_country_code']),
            'Expe_Ad1'     => $configuration['label_shipper_company'],
            'Expe_Ad2'     => '',
            'Expe_Ad3'     => $configuration['label_shipper_street'],
            'Expe_Ad4'     => '',
            'Expe_Ville'   => $configuration['label_shipper_city'],
            'Expe_CP'      => $configuration['label_shipper_postcode'],
            'Expe_Pays'    => $configuration['label_shipper_country_code'],
            'Expe_Tel1'    => $configuration['label_shipper_phone_number'],
            'Expe_Tel2'    => '',
            'Expe_Mail'    => $configuration['label_shipper_email'],
            'Dest_Langage' => $this->getLanguage($shippingAddress->getCountryCode()),
            'Dest_Ad1'     => $shippingAddress->getFullName(),
            'Dest_Ad2'     => $shippingAddress->getCompany(),
            'Dest_Ad3'     => $shippingAddress->getStreet(),
            'Dest_Ad4'     => '',
            'Dest_Ville'   => $shippingAddress->getCity(),
            'Dest_CP'      => $shippingAddress->getPostcode(),
            'Dest_Pays'    => $shippingAddress->getCountryCode(),
            'Dest_Tel1'    => $shippingAddress->getPhoneNumber(),
            'Dest_Tel2'    => '',
            'Dest_Mail'    => $order->getCustomer()->getEmail(),
            'Poids'        => $weight,
            'NbColis'      => 1,
            'CRT_Valeur'   => 0,
            'CRT_Devise'   => '',
            'Exp_Valeur'   => '',
            'Exp_Devise'   => '',
            'COL_Rel_Pays' => $configuration['label_shipper_country_code'],
            'COL_Rel'      => 0,
            'LIV_Rel_Pays' => $country,
            'LIV_Rel'      => $id,
            'TAvisage'     => '',
            'TReprise'     => '',
            'Montage'      => '',
            'TRDV'         => '',
            'Assurance'    => 0,
            'Instructions' => '',
        ];

        $this->pickupRepository->setConfig($configuration);

        return $this->pickupRepository->createShipping($data);
    }

    /**
     * Retrieve country language
     *
     * @param string $country
     * @return string
     */
    protected function getLanguage(string $country): string
    {
        $languages = [
            'FR' => 'FR',
            'BE' => 'NL',
            'ES' => 'ES',
        ];

        $language = 'FR';
        if (isset($languages[$country])) {
            $language = $languages[$country];
        }

        return $language;
    }

    /**
     * Retrieve Shipping Label content
     *
     * @param string $tracking
     * @param string $countryCode
     * @param array $configuration
     * @return array
     */
    protected function getShippingLabel(string $tracking, string $countryCode, array $configuration): array
    {
        $data = [
            'Expeditions' => $tracking,
            'Langue'      => $countryCode,
        ];

        $this->pickupRepository->setConfig($configuration);

        return $this->pickupRepository->getLabel($data);
    }
}
