<?php

namespace Tests\Util;

use PHPUnit\Framework\TestCase;
use Xiag\Poll\Util\Request;

class RequestTest extends TestCase
{
  /**
   * @var Request
   */
  private $subject;

  protected function setUp(): void
  {
    $this->subject = new Request();
  }
  public function testGet(): void
  {
    $_POST = ['a' => 42];
    $_GET  = ['a' => 24, 'b' => 36];

    self::assertEquals(42, $this->subject->get('a', null));
    self::assertEquals(36, $this->subject->get('b', null));
    self::assertEquals($this->subject, $this->subject->get('none', $this->subject));
    self::assertNull($this->subject->get('none'));
  }
  public function testGetHeaders(): void
  {
    $_SERVER['HTTP_CUSTOM_HEADER'] = 'Some value';

    $headers = $this->subject->getHeaders();

    self::assertNotEmpty($headers);
    self::assertEquals('Some value', $headers['Custom-Header']);
    self::assertNull($headers['Nonexistence-Header'] ?? null);
  }
  public function testGetPath(): void
  {
    self::assertEquals('/', $this->subject->getPath());
    $_SERVER['PATH_INFO'] = '/path/1';
    self::assertEquals('/path/1', $this->subject->getPath());
    $_SERVER['REDIRECT_PATH_INFO'] = '/path/2';
    self::assertEquals('/path/2', $this->subject->getPath());
    unset($_SERVER['PATH_INFO'], $_SERVER['REDIRECT_PATH_INFO']);
  }
  public function testGetMethod(): void
  {
    $_SERVER['REQUEST_METHOD'] = 'External_Patch';

    self::assertEquals('EXTERNAL_PATCH', $this->subject->getMethod());

    unset($_SERVER['REQUEST_METHOD']);
  }
}
