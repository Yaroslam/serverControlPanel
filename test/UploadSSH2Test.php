<?php

namespace test;

use PHPUnit\Framework\TestCase;

class UploadSSH2Test extends TestCase
{
    public function testUploadOfSSH2ext()
    {

        $this->assertTrue(extension_loaded('ssh2'), 'ext ssh2 doesnt loaded');
    }
}
