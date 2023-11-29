<?php
namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskServiceTest extends WebTestCase
{
    public function testCreateTask(): void
    {
        $client = static::createClient();

        $client->request('POST', '/create', ['title' => 'Test Task']);

        $this->assertSame(200, $client->getResponse()->getStatusCode());

    }

}
