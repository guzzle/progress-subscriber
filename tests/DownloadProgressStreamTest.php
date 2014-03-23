<?php

namespace GuzzleHttp\Tests\Subscriber\Progress;

use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\Progress\DownloadProgressStream;

class DownloadProgressStreamTest extends \PHPUnit_Framework_TestCase
{
    public function testEmitsAsDataIsWritten()
    {
        $stream = Stream::factory();

        $calls = [];
        $fn = function ($e, $t) use (&$calls) {
            $calls[] = [$e, $t];
        };

        $progress = new DownloadProgressStream($stream, $fn, 7);
        $progress->write('foo');
        $progress->write('_');
        $progress->write('bar');
        $progress->write('');

        $this->assertEquals([
            [7, 3],
            [7, 4],
            [7, 7]
        ], $calls);
    }
}
