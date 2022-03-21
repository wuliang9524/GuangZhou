<?php

declare(strict_types=1);

use Logan\Guangzhou\Client;
use Logan\Guangzhou\DES;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    protected $domain = 'http://113.108.174.224/jkgl/';
    protected $key    = "4966fcb14f7096bdf0753a241d4dd7df";
    protected $secret = '8c3991d386fb655e62b36ac0f6e39f6a';

    public function testAddAttendance()
    {
        $instance = new Client($this->domain, $this->key, $this->secret);
        $res = $instance->addAttendance([
            'accessNo'      => "4966fcb14f7096bdf0753a241d4dd7df",
            'builderIdcard' => "des:2079b45e9eb9a1072a280c8d3ce8ca09e5efc0c843ea4755",
            'atteTime'      => "20220318115011",
            'atteImage'     => null,
            'atteType'      => "2",
            'checkChannel'  => "1353271",
            'checkType'     => "2",
            'builderType'   => "0",
            'factoryNum'    => "59e58cad776340c7a2de315db4ddd2f2",
            'timestamp'     => $instance->getReqTimestamp()
        ]);
        var_dump($res);
    }

    public function testDESEncrypt()
    {
        $idCode = '5113011990010181111';
        $encrypt = (new DES('8c2da4c769828fcfa77aedb690999cf9', 'DES-ECB', DES::OUTPUT_HEX))->encrypt($idCode);
        var_dump($encrypt);
    }

    public function testDESDecrypt()
    {
        $idCodeEncrypt = '2079b45e9eb9a1072a280c8d3ce8ca09e5efc0c843ea4755';
        $dencrypt = (new DES($this->secret, 'DES-ECB', DES::OUTPUT_HEX))->decrypt($idCodeEncrypt);
        var_dump($dencrypt);
    }
}
