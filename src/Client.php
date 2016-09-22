<?php
/**
 * Created by PhpStorm.
 * User: joakim
 * Date: 22/08/16
 * Time: 22:01
 */
declare(strict_types = 1);

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
     * @var string
     */
    private $accessToken;

    /**
     * @var array
     */
    private $queries;

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
                'token' => $accessToken,
            ],
        ];
    }

    /**
     * @param String $accessToken
     * @return Client
     */
    public static function make(string $accessToken)
    {
        $client = new \GuzzleHttp\Client();
        return new self($client, $accessToken);
    }

    /**
     * @param ResponseInterface $response
     * @param bool $assoc whether do decode to an associative array
     * @return string[] decoded response
     */
    public static function decodeResponse(ResponseInterface $response, bool $assoc = true)
    {
        return json_decode((string)$response->getBody(), $assoc);
    }

    /**
     * @param array $options
     * @return ResponseInterface
     */
    public function getClients(array $options = []) : ResponseInterface
    {
        return $this->sendRequest('GET', 'accounts', $options);
    }

    public function getClientByOrgNo(string $orgNo) : ResponseInterface
    {
        $orgNoFilter = implode(',', self::getOrgNoVariations($orgNo));
        $options = [
            'query' => [
                'custom' => "eq:1:$orgNoFilter",
            ],
        ];
        return $this->getClients($options);
    }

    public function getClientById(string $id) : ResponseInterface
    {
        return $this->sendRequest('GET', "accounts/$id");
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $options
     * @return ResponseInterface
     */
    protected function sendRequest(string $method, string $endpoint, array $options = []) : ResponseInterface
    {
        $options = array_merge_recursive($options, $this->queries);
        return $this->client->request($method, self::API_URL . $endpoint, $options);
    }

    /**
     * Takes an organisational number and returns both with and without dash or empty
     * if correct Orgno is not supplied
     * @param string $orgNo
     * @return array
     */
    public static function getOrgNoVariations(string $orgNo): array
    {
        if (preg_match('/(\d{6})-?(\d{4})/', $orgNo, $matches) === 1) {
            $parts = [$matches[1], $matches[2]];
            return [
                implode('-', $parts),
                implode('', $parts),
            ];
        }
        return [];
    }

}
