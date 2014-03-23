<?php

namespace GuzzleHttp\Tests\Subscriber\Progress;

use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\Progress\UploadProgressStream;

class UploadProgressStreamTest extends \PHPUnit_Framework_TestCase
{
    public function testEmitsAsDataIsRead()
    {
        $stream = Stream::factory('foo_baz');

        $calls = [];
        $fn = function ($e, $t) use (&$calls) {
            $calls[] = [$e, $t];
        };

        $progress = new UploadProgressStream($stream, $fn);
        $progress->read(3);
        $progress->read(1);
        $progress->read(3);
        $progress->read(100);

        $this->assertEquals([
            [7, 3],
            [7, 4],
            [7, 7]
        ], $calls);
    }
}
