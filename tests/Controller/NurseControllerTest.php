<?php

use App\Controller\NurseController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NurseControllerTest extends WebTestCase
{
    public function testCreateNurseSuccess()
    {
        $randomNumber = rand(1, 200000);
        $client = static::createClient();
        $client->request('POST', '/nurse/create', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => "john$randomNumber@example.com",
            'password' => '123456',
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function testGetAllNurses()
    {
        $client = static::createClient();
        $client->request('GET', '/nurse/getAll');
        $response = $client->getResponse();
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertIsArray(json_decode($response->getContent(), true));
    }

    public function testNurseLoginSuccess()
    {
        $client = static::createClient();
        $client->request('POST', '/nurse/login', [
            'first_name' => 'John',
            'password' => '123456',
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testFindNurseByNameSuccess()
    {
        $client = static::createClient();
        $client->request('GET', '/nurse/findName', ['first_name' => 'John']);
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testDeleteNurseByIdSuccess()
    {
        $client = static::createClient();
        $client->request('DELETE', '/nurse/deleteById', ['id_nurse' => 1]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
