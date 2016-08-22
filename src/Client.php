<?php
/**
 * Created by PhpStorm.
 * User: joakim
 * Date: 22/08/16
 * Time: 22:01
 */

namespace Vinnia\Upsales;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class Client
{
    const API_URL = 'https://integration.upsales.com/api/v2/';
    /**
     * @var ClientInterface
     */
    private $client;
    /**
     * @var String
     */
    private $accessToken;

    /**
     * Client constructor.
     * @param ClientInterface $client
     * @param $accessToken
     */
    public function __construct(ClientInterface $client, String $accessToken)
    {
        $this->client = $client;
        $this->accessToken = $accessToken;
        $this->queries = [
            'query' => [
                'token' => $accessToken
            ]
        ];
    }

    /**
     * @param String $accessToken
     * @return Client
     */
    public static function make(String $accessToken)
    {
        $client = new \GuzzleHttp\Client();
        return new self($client, $accessToken);
    }

    /**
     * @param ResponseInterface $response
     * @param bool $assoc whether do decode to an associative array
     * @return string[] decoded response
     */
    public static function decodeResponse(ResponseInterface $response, $assoc = true)
    {
        return json_decode((string)$response->getBody(), $assoc);
    }

    /**
     * @return ResponseInterface
     */
    public function getClients($options = [])
    {
        return $this->sendRequest('GET', 'accounts', $options);
    }

    public function getClientByOrgNo(String $orgNo)
    {
        //TODO: Verify that fieldId 1 is always orgno at Upsales
        //TODO: Verify orgno supplied
        $options = [
            'query' => [
                'custom.fieldId' => 1,
                'custom.value' => $orgNo
            ]
        ];
        return $this->getClients($options);
    }

    /**
     * @param String $method
     * @param String $endpoint
     * @param array $options
     * @return ResponseInterface
     */
    protected function sendRequest(String $method, String $endpoint, $options = []) : ResponseInterface
    {
        $options = array_merge_recursive($options, $this->queries);
        return $this->client->request($method, self::API_URL . $endpoint, $options);
    }

}

/**
 * Working url for searching:
 * https://integration.upsales.com/api/v2/accounts/?token=XXX&custom.fieldId=1&custom.value=556933-9251
 */