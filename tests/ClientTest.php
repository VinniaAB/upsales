<?php declare(strict_types = 1);

namespace Vinnia\Upsales\Test;

use GuzzleHttp\Exception\TransferException;
use PHPUnit\Framework\TestCase;
use Vinnia\Upsales\Client;

class ClientTest extends TestCase
{

    /**
     * @var array
     */
    private $env;

    /**
     * @var Client
     */
    private $client;

    public function setUp()
    {
        parent::setUp();

        $this->markTestSkipped('Seems dangerous...');

        $this->env = require __DIR__.'/../../env.php';
        $this->client = Client::make($this->env['accessToken']);
    }

    public function testItShouldLoad1000Clients()
    {
        $this->markTestSkipped('Only takes bandwidth');

        $res = $this->client->getClients();
        $clients = Client::decodeResponse($res);
        $this->assertSame(1000, $clients['data']);
    }

    public function testItShouldFindClientByOrgNo()
    {
        $res = $this->client->getClientByOrgNo('556933-9251');
        $clients = Client::decodeResponse($res);
        $this->assertSame(1, $clients['metadata']['total']);
    }

    public function testItShouldFindClientByOrgNoWithoutDash()
    {
        $res = $this->client->getClientByOrgNo('5569339251');
        $clients = Client::decodeResponse($res);
        $this->assertSame(1, $clients['metadata']['total']);
    }

    public function testItShouldFindClientById()
    {
        $res = $this->client->getClientById('3326');
        $client = Client::decodeResponse($res);
        $this->assertSame('Snille Bemanning AB', $client['data']['name']);
    }

    public function orgNoProvider(): array
    {
        return [
            ['556933-9251', ['5569339251', '556933-9251']],
            ['5569339251', ['5569339251', '556933-9251']],
            ['5569339251_HARAMBE_COOL', ['5569339251', '556933-9251']], // extra stuff at the end works too.
            ['INVALID_NUMBER', []],
        ];
    }

    /**
     * @dataProvider orgNoProvider
     * @param string $toParse
     * @param array $expectedResult
     */
    public function testItShouldGenerateTwoVariationsOfOrgNo(string $toParse, array $expectedResult)
    {
        $variations = Client::getOrgNoVariations($toParse);
        $this->assertCount(count($expectedResult), $variations);

        foreach ($expectedResult as $num) {
            $this->assertContains($num, $variations);
        }
    }

    public function testGetOrdersByOrgNo()
    {
        $res = $this->client->getOrdersByClientId('4536'); // Vinnia AB
        $data = Client::decodeResponse($res);
        $this->assertNotEmpty($data['data']);
    }

    public function testGetOrderStages()
    {
        $res = $this->client->getOrderStages();
        $data = Client::decodeResponse($res);
    }

    public function testCreateOrder()
    {
        try {
            $res = $this->client->createOrder([
                'description' => 'Harambe',
                'date' => date('c'),
                'user' => 19, // amanda thorén
                'stage' => 11, // Muntlig överenskommelse
                'probability' => 95, // for some reason this field cannot be inferred from the stage
                'client' => 4536,
                'orderRow' => [
                    [
                        'product' => [
                            'id' => 10000001,
                        ],
                        'price' => 5000,
                        'quantity' => 1,
                    ],
                ],
            ]);
            $data = Client::decodeResponse($res);

            // delete the created order when we're done
            $res = $this->client->deleteOrder((string) $data['data']['id']);
            $data = Client::decodeResponse($res);
        }
        catch (TransferException $e) {
            $res = (string) $e->getResponse()->getBody();
        }

    }

}
