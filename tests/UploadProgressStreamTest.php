<?php

namespace GuzzleHttp\Tests\Subscriber\Progress;

use GuzzleHttp\Client;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\Progress\UploadProgressStream;

class UploadProgressStreamTest extends \PHPUnit_Framework_TestCase
{
    public function testEmitsAsDataIsRead()
    {
        $stream = Stream::factory('foo_baz');

        $calls = [];
        $fn = function ($e, $t) use (&$calls) {
            $calls[] = func_get_args();
        };

        $client = new Client();
        $request = new Request('GET', 'http://www.foo.com');
        $progress = new UploadProgressStream(
            $stream,
            $fn,
            $client,
            $request
        );

        $progress->read(3);
        $progress->read(1);
        $progress->read(3);
        $progress->read(100);

        $this->assertEquals([
            [7, 3, $client, $request],
            [7, 4, $client, $request],
            [7, 7, $client, $request]
        ], $calls);
    }
}
