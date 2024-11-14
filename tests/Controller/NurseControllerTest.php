<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NurseControllerTest extends WebTestCase
{
    // * CREATE
    /*public function testCreateNurseSuccess()
    {
        $randomNumber = rand(1, 200000);

        $client = static::createClient();
        $client->request('POST', '/nurse/create', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => "john$randomNumber@example.com",
            'password' => 'password12#',
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    */public function testCreateNurseMissingFields()
    {
        $client = static::createClient();
        $client->request('POST', '/nurse/create', [
            'first_name' => 'John',
            'email' => 'john@example.com',
        ]);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    /*
    public function testCreateNurseDuplicateEmail()
    {
        $randomNumber = rand(1, 200000);

        $client = static::createClient();
        $client->request('POST', '/nurse/create', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => "john$randomNumber@example.com",
            'password' => 'password12#',
        ]);

        $client->request('POST', '/nurse/create', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => "john$randomNumber@example.com",
            'password' => 'password12#',
        ]);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    // * READ
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
    }*/

    public function testNurseLoginNotFound()
    {
        $client = static::createClient();
        $client->request('POST', '/login', [
            'first_name' => 'Nonexistent',
            'password' => '123456',
        ]);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testNurseLoginWrongPassword()
    {
        $client = static::createClient();
        $client->request('POST', '/login', [
            'first_name' => 'John',
            'password' => 'wrongpassword',
        ]);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    /*
    public function testFindNurseByNameSuccess()
    {
        $client = static::createClient();

        $client->request('GET', '/nurse/findName/John');

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('first_name', $responseData[0]);
        $this->assertEquals('John', $responseData[0]['first_name']);
    }

    public function testFindNurseByNameNotFound()
    {
        $client = static::createClient();

        $client->request('GET', '/nurse/findName/Nonexistent');

        $response = $client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('This nurse name does not exist.', $responseData['message']);
    }

    // * UPDATE
    /*public function testUpdateNurseByIdSuccess()
    {
        $client = static::createClient();
        $client->request('PUT', '/nurse/updateById', [
            'id' => 9,
            'first_name' => 'UpdatedName',
            'last_name' => 'UpdatedLastName',
            'email' => 'john18942@example.com',
            'password' => 'password#123'
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }*/
    
    // * DELETE
    /*
    public function testDeleteNurseByIdSuccess()
    {
        $client = static::createClient();
        $client->request('DELETE', '/nurse/deleteById', ['id_nurse' => 1]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }*/

    public function testDeleteNurseByIdNotFound()
    {
        $client = static::createClient();
        $client->request('DELETE', '/deleteById', ['id_nurse' => 999]);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
