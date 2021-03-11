<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class DevEnvTest extends TestCase
{
  public function testOk(): void
  {
    self::assertTrue(true, 'testing environment should work');
    self::assertEquals('test', $_ENV['ENV']);
  }
}
