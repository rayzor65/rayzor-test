<?php

namespace RayzorTest\Test;

use GuzzleHttp\Client;
use PHPUnit_Framework_TestCase;
use Illuminate\Config\FileLoader;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;

/**
 * Class EndpointTest
 * @package Reporting\Service
 */
class EndpointTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var
     */
    protected $token;
    /**
     * @var
     */
    protected $config;

    /**
     *
     */
    function setUp()
    {
        require 'vendor/autoload.php';
        $configPath = __DIR__ . '/../config';

        $loader = new FileLoader(new Filesystem, $configPath);
        $config = new Repository($loader, ENV);
        $this->setConfig($config);
    }

    /**
     *
     */
    function testPageNotFound()
    {
        $client = new Client();
        $response = $client->request('GET', $this->getConfig()->get('app.base-uri')
            . $this->getConfig()->get('app.invalid-uri'), array(
            'http_errors' => false,
        ));

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     *
     */
    function testLogin()
    {
        $response = $this->login();

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     *
     */
    function testInvalidLogin()
    {
        $client = new Client();
        $response = $client->request('GET', $this->getConfig()->get('app.base-uri')
            . $this->getConfig()->get('app.login-uri'), array(
            'http_errors' => false,
            'auth' => array($this->getConfig()->get('app.valid-username'), ''),
        ));

        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     *
     */
    function testGets()
    {
        $this->login();
        $client = new Client();

        $getEndpoints = $this->getConfig()->get('gets');
        foreach($getEndpoints as $key => $val) {

            // Attempt to connect to the endpoint
            $response = $client->request('GET', $this->getConfig()->get('app.base-uri') . $val['url'], array(
                'http_errors' => false,
                'headers' => array(
                    'Authorization' => 'Bearer ' . $this->getToken(),
                )
            ));

            // Check the response status
            $this->assertEquals(200, $response->getStatusCode(), "Status Code failure at " . $val['url']);

            // Check the response body
//            $jsonDecodedResponse = json_decode(file_get_contents(__DIR__.'/../responses/categories.json'), true);
            $jsonDecodedResponse = json_decode($response->getBody(), true);

            $this->assertNotNull($jsonDecodedResponse, "JSON formatting failure at " . $val['url']
                . "\nFull debug output: " . $response->getBody());

            // Assert response values against expected types
            $this->recurseAndCheckType($jsonDecodedResponse, $val['response'], $val['url']);
        }
    }

    /**
     * @param $field
     * @param $expectedResponse
     * @param $url
     */
    function recurseAndCheckType($field, $expectedResponse, $url)
    {
        foreach ($field as $key => $type) {
            // Type might be an array of types so we need to recurse through
            if (is_array($type)) {
                if (is_string($key)) {
                    $expectedResponse = $expectedResponse[$key];
                }
                $this->recurseAndCheckType($type, $expectedResponse, $url);
            } else {
                $msg = "Assert that {$key} with value {$type} is " . $expectedResponse[$key] . " for endpoint {$url}\n";
                switch ($expectedResponse[$key]) {
                    case 'boolean':
                        $this->assertInternalType('boolean', $type, $msg);
                        break;
                    case 'int':
                        $this->assertInternalType('int', $type, $msg);
                        break;
                    case 'float':
                        $this->assertInternalType('float', $type, $msg);
                        break;
                    case 'array':
                        $this->assertInternalType('array', $type, $msg);
                        break;
                    case 'string':
                        $this->assertInternalType('string', $type, $msg);
                        break;
                }
            }
        }
    }

    /**
     *
     */
    function testPost()
    {
        $client = new Client();
        $testUrl = 'http://local.laravel.com/user';

        // Attempt to connect to the endpoint
        $response = $client->request('POST', $testUrl, array(
            'http_errors' => false,
            'form_params' => $this->getConfig()->get('posts.add-user.body')
        ));

        // Check the response status
        $this->assertEquals(201, $response->getStatusCode(), "Endpoint failure at " . $testUrl);

        // Check the response body
        $jsonDecodedResponse = json_decode($response->getBody(), true);
        $this->assertNotNull($jsonDecodedResponse);

        // Assert response values against expected types
        $this->recurseAndCheckType($jsonDecodedResponse, $this->getConfig()->get('posts.add-user.response'), $testUrl);
    }

    /**
     *
     */
    function testPosts()
    {
        $client = new Client();
        // Specifying where the API is
        $baseUrl = 'http://local.laravel.com';

        $getEndpoints = $this->getConfig()->get('posts');
        foreach($getEndpoints as $key => $val) {
            // Attempt to connect to the endpoint
            $response = $client->request('POST', $baseUrl . $val['url'], array(
                'http_errors' => false,
                'form_params' => $val['body']
            ));

            // Check the response status
            $this->assertEquals(201, $response->getStatusCode(), "Endpoint failure at " . $val['url']);

            // Check the response body
            $jsonDecodedResponse = json_decode($response->getBody(), true);
            $this->assertNotNull($jsonDecodedResponse);

            // Assert response values against expected types
            $this->recurseAndCheckType($jsonDecodedResponse, $val['response'], $val['url']);
        }
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param mixed $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    function login()
    {
        $client = new Client();

        $response = $client->request('GET', $this->getConfig()->get('app.base-uri')
            . $this->getConfig()->get('app.login-uri'), array(
            'http_errors' => false,
            'auth' => array($this->getConfig()->get('app.valid-username'),
                $this->getConfig()->get('app.valid-password')),
        ));

        $jsonData = json_decode($response->getBody());
        $this->setToken($jsonData->token);

        return $response;
    }
}