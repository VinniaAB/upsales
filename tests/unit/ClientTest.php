<?php
/**
 * Created by PhpStorm.
 * User: joakim
 * Date: 22/08/16
 * Time: 22:51
 */

namespace Vinnia\Upsales\Test;

use Codeception\TestCase\Test;
use Vinnia\Upsales\Client;

class ClientTest extends Test
{

    private $env;

    /**
     * @var Client
     */
    private $client;

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->env = require __DIR__.'/../../env.php';
        $this->client = Client::make($this->env['accessToken']);
    }

    /**
     * deprecated test - only takes bandwidth
     */
    public function it_should_load_1000_clients()
    {
        $res = $this->client->getClients();
        $clients = Client::decodeResponse($res);

        codecept_debug($clients);
        $this->assertEquals(1000,count($clients['data']));
    }

    /**
     * @test
     */
    public function it_should_find_client_by_orgnr()
    {
        $res = $this->client->getClientByOrgNo('556933-9251');
        $clients = Client::decodeResponse($res);
        codecept_debug($clients);
        $this->assertEquals(1, count($clients['metadata']['total']));
    }

    /**
     * @test
     */
    public function it_should_find_client_by_orgnr_without_dash()
    {
        $res = $this->client->getClientByOrgNo('5569339251');
        $clients = Client::decodeResponse($res);
        codecept_debug($clients);
        $this->assertEquals(1, count($clients['metadata']['total']));
    }

    /**
     * @test
     */
    public function it_should_find_client_by_id()
    {
        $res = $this->client->getClientById('3326');
        $client = Client::decodeResponse($res);
        codecept_debug($client);
        $this->assertEquals('Snille Bemanning AB', $client['data']['name']);
    }

    /**
     * @test
     */
    public function it_should_generate_two_variations_of_org_no()
    {
        $orgNo = '556933-9251';
        $orgNos = Client::getOrgNoVariations($orgNo);
        $this->assertEquals(['5569339251', '556933-9251'], $orgNos);
    }
}