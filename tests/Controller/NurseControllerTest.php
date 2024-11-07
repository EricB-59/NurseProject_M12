<?php

use App\Controller\NurseController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NurseControllerTest extends WebTestCase
{
    public function testCreateNurseSuccess()
    {
        $client = static::createClient();
        $client->request('POST', '/nurse/create', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => '123456',
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

}
