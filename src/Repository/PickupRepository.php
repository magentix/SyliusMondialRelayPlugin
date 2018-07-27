<?php

declare(strict_types=1);

namespace MagentixMondialRelayPlugin\Repository;

use MagentixMondialRelayPlugin\Model\Soap;

class PickupRepository extends Soap
{

    /**
     * @var array $config
     */
    private $config;

    /**
     * @param array $config
     * @return void
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * @param string $pickupId
     * @param string $countryCode
     * @return array
     */
    public function find(string $pickupId, string $countryCode = 'FR'): array
    {
        $data = [
            'NumPointRelais' => $pickupId,
            'Pays'           => $countryCode,
        ];

        return $this->execute('WSI4_PointRelais_Recherche', $data, $this->config);
    }

    /**
     * @param string $postcode
     * @param string $countryCode
     * @param string $shippingCode
     * @return array
     */
    public function findAll(string $postcode, string $countryCode = 'FR', string $shippingCode = '24R'): array
    {
        $data = [
            'Pays'            => $countryCode,
            'CP'              => $postcode,
            'Action'          => $shippingCode,
            'NombreResultats' => 10
        ];

        return $this->execute('WSI4_PointRelais_Recherche', $data, $this->config);
    }
}
