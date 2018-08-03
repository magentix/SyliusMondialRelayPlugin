<?php
/**
 * @author    Matthieu Vion
 * @copyright 2018 Magentix
 * @license   https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 * @link      https://github.com/magentix/mondial-relay-plugin
 */
declare(strict_types=1);

namespace Magentix\SyliusMondialRelayPlugin\Model;

use SoapClient;
use SoapFault;
use Exception;

class Soap
{

    /**
     * @var SoapClient $client
     */
    private $client = null;

    /**
     * Execute WS request
     *
     * @param string $method
     * @param array $data
     * @param array $config
     * @return array
     */
    public function execute(string $method, array $data, array $config): array
    {
        $result = [
            'error'    => false,
            'response' => false,
        ];

        try {
            $data = array_merge(['Enseigne' => $config['api_company']], $data);
            $security = strtoupper(md5(implode('', $data) . $config['api_key']));
            $data['Security'] = $security;

            $response = $method . 'Result';
            $request = $this->getClient($config)->$method($data)->$response;

            if ($request->STAT == "0") {
                $result['response'] = $request;
            } else {
                $result['error'] = $request->STAT;
            }
        } catch (SoapFault $fault) {
            $result['error'] = $fault->getMessage();
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Retrieve SOAP Client
     *
     * @param array $config
     * @return SoapClient
     */
    protected function getClient(array $config): SoapClient
    {
        if ($this->client === null) {
            $this->client = new SoapClient($config['api_wsdl'], ['exceptions' => true, 'trace' => false]);
        }

        return $this->client;
    }
}
