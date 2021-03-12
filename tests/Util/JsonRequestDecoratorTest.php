<?php

namespace Tests\Util;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Xiag\Poll\Util\JsonRequestDecorator;
use Xiag\Poll\Util\Request;
use Xiag\Poll\Util\RequestInterface;
use function json_encode;
use function uniqid;

class JsonRequestDecoratorTest extends TestCase
{
  /** @var RequestInterface */
  private $subject;
  /** @var MockObject|Request */
  private $request;

  protected function setUp(): void
  {
    $this->request = $this->createMock(Request::class);
    $this->subject = new JsonRequestDecorator($this->request);
  }

  public function testGetTransparent(): void
  {
    $param1 = uniqid('x_', false);
    $param2 = uniqid('d_', false);

    $this->request->expects(self::once())
        ->method('get')
        ->with($param1, $param2);

    $this->subject->get($param1, $param2);
  }
  public function testGetHeadersTransparent(): void
  {
    $this->request->expects(self::once())
        ->method('getHeaders')
        ->with();

    $this->subject->getHeaders();
  }
  public function testGetPathTransparent(): void
  {
    $this->request->expects(self::once())
        ->method('getPath')
        ->with();

    $this->subject->getPath();
  }
  public function testGetMethodTransparent(): void
  {
    $this->request->expects(self::once())
        ->method('getMethod')
        ->with();

    $this->subject->getMethod();
  }

  public function testGet(): void
  {
    /** @var JsonRequestDecorator|MockObject $mock */
    $mock = $this->getMockBuilder(JsonRequestDecorator::class)
        ->onlyMethods(['getInputStream'])
        ->setConstructorArgs([new Request()])
        ->getMock();

    $mock->method('getInputStream')
        ->willReturn(json_encode(['x' => 42]));

    $_POST['x'] = 'post';
    $_GET['x']  = 'get';

    self::assertEquals(42, $mock->get('x', 'default'));
    unset($_POST['x'], $_GET['x']);
  }

  public function testGetWrongJson(): void
  {
    /** @var JsonRequestDecorator|MockObject $mock */
    $mock = $this->getMockBuilder(JsonRequestDecorator::class)
        ->onlyMethods(['getInputStream'])
        ->setConstructorArgs([new Request()])
        ->getMock();

    $mock->method('getInputStream')
        ->willReturn('=' . json_encode(['x' => 42]));

    self::assertEquals('default', $mock->get('x', 'default'));
    self::assertEquals('Syntax error', $mock->get('__json_parse_error'));
  }
}
