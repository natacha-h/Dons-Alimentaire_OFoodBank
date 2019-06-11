<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;

class PublishController
{
    public function __invoke(Publisher $publisher): Response
    {
        $update = new Update(
            '127.0.0.1:8001/test',
            json_encode(['message' => 'OutOfStock'])
        );

        // The Publisher service is an invokable object
        $publisher($update);

        return new Response('published!');
    }
}
