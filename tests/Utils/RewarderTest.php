<?php

namespace App\Tests\Utils;

use App\Utils\Rewarder;
use PHPUnit\Framework\TestCase;

class RewarderTest extends TestCase
{
    public function testRewarder()
    {
        // je prépare les cas possibles avec le Rewarder
        $rewarder1 = new Rewarder();
        $result1 = $rewarder1->rewarder(542);

        $rewarder2 = new Rewarder();
        $result2 = $rewarder2->rewarder(0);

        $rewarder3 = new Rewarder();
        $result3 = $rewarder3->rewarder(1639);


        $this->assertEquals(3, $result1);
        $this->assertEquals(1, $result2);
        $this->assertEquals(1, $result3);
    }
}
