<?php declare(strict_types=1);

namespace App\Service;

class UuidService
{
    
    public function __construct()
    {
        
    }

    function generateV4UUID(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // set version to 0100
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    function generateV1UUID(): string
    {
        $time = microtime(true) * 10000000 + 0x01B21DD213814000;
        $timeHex = str_pad(dechex($time), 16, '0', STR_PAD_LEFT);

        $clockSeq = random_int(0, 0x3FFF);
        $node = bin2hex(random_bytes(6));

        return sprintf(
            '%08s-%04s-%04x-%04x-%012s',
            substr($timeHex, 0, 8),
            substr($timeHex, 8, 4),
            (hexdec(substr($timeHex, 12, 4)) & 0x0FFF) | 0x1000, // set version to 0001
            ($clockSeq & 0x3FFF) | 0x8000, // set bits 6-7 to 10
            $node
        );
    }

}