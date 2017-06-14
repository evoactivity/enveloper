<?php

namespace Outstack\Enveloper\Tests\Functional;

use Http\Client\Exception\HttpException;
use Outstack\Components\SymfonySwiftMailerAssertionLibrary\SwiftMailerAssertionTrait;
use Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector;
use Zend\Diactoros\Request;
use Zend\Diactoros\Stream;
use Zend\Diactoros\Uri;

class ErrorHandlingFunctionalTest extends AbstractApiTestCase
{
    public function test_page_not_found_is_nicely_formatted()
    {
        $request = new Request(
            '/not-found',
            'GET'
        );

        try {
            $this->client->sendRequest($request);
        } catch (HttpException $e) {

            $response   = $e->getResponse();
            $body       = (string) $response->getBody();

            $this->assertEquals(404, $response->getStatusCode());
            $this->assertJson($body);
            $this->assertEquals([
                'title' => 'Not Found',
                'detail' => 'No matching action was found to handle the request',
                'status' => 404
            ], json_decode($body, true));
            $this->assertEquals('application/problem+json', $response->getHeaderLine('Content-type'));

            return;
        }

        throw new \LogicException("Expected HttpException, none caught");
    }

    public function test_server_error_is_nicely_formatted()
    {
        $request = new Request(
            '/errors/500',
            'GET'
        );

        try {
            $this->client->sendRequest($request);
        } catch (HttpException $e) {

            $response   = $e->getResponse();
            $body       = (string) $response->getBody();

            $this->assertEquals(500, $response->getStatusCode());
            $this->assertJson($body);
            $this->assertEquals([
                'title' => 'Server Error',
                'detail' => 'An unexpected error occurred',
                'status' => 500
            ], json_decode($body, true));
            $this->assertEquals('application/problem+json', $response->getHeaderLine('Content-type'));

            return;
        }

        throw new \LogicException("Expected HttpException, none caught");
    }
}