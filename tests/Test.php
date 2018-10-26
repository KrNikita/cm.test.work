<?php

use PHPUnit\Framework\TestCase;

require_once "../src/Database.php";

class Test extends TestCase
{
    public function test1(){
        $db = new Database();
        $this->expectException($db->Query("Q"));
    }
}
