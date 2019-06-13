<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;

class PublishController
{
    public function __invoke(Publisher $publisher): Response
    {
        // Notification mercure
        $update = new Update(
            'http://127.0.0.1:8001/dons/{id}/select', json_encode(['status' => 'Test'])
        );

        $publisher($update);


        return new Response('published!');
    }
}
