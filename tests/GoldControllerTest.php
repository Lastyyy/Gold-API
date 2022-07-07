<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;


class GoldControllerTest extends WebTestCase
{

    public function testGoldJanuary2021Single()
    {
        $client = static::createClient();
        $client->xmlHttpRequest('POST', '/api/gold', [
            'from' => '2021-01-04T00:00:00+00:00',
            'to' => '2021-01-04T00:00:00+00:00'
        ]);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertIsArray($response);
        $this->assertArrayHasKey('from', $response);
        $this->assertEquals('2021-01-04T00:00:00+00:00', $response['from']);
        $this->assertArrayHasKey('to', $response);
        $this->assertEquals('2021-01-04T00:00:00+00:00', $response['to']);
        $this->assertArrayHasKey('avg', $response);
        $this->assertEquals(228.1, $response['avg']);
    }

    public function testGoldJanuary2021Range()
    {
        $client = static::createClient();
        $client->xmlHttpRequest('POST', '/api/gold', [
            'from' => '2021-01-01T00:00:00+00:00',
            'to' => '2021-01-31T00:00:00+00:00'
        ]);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertIsArray($response);
        $this->assertArrayHasKey('from', $response);
        $this->assertEquals('2021-01-04T00:00:00+00:00', $response['from']);
        $this->assertArrayHasKey('to', $response);
        $this->assertEquals('2021-01-29T00:00:00+00:00', $response['to']);
        $this->assertArrayHasKey('avg', $response);
        $this->assertEquals(223.52, $response['avg']);
    }

    public function testMissingTimezone()
    {
        $client = static::createClient();
        $client->xmlHttpRequest('POST', '/api/gold', [
            'from' => '2001-01-04 00:00:00',
            'to' => '2001-01-04 00:00:00'
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}