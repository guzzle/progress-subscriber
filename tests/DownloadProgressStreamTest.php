<?php
namespace GuzzleHttp\Tests\Subscriber\Progress;

use GuzzleHttp\Client;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\Progress\DownloadProgressStream;

class DownloadProgressStreamTest extends \PHPUnit_Framework_TestCase
{
    public function testEmitsAsDataIsWritten()
    {
        $stream = Stream::factory();

        $calls = [];
        $fn = function ($e, $t) use (&$calls) {
            $calls[] = func_get_args();
        };

        $client = new Client();
        $request = new Request('GET', 'http://www.foo.com');
        $response = new Response(200);

        $progress = new DownloadProgressStream(
            $stream,
            $fn,
            7,
            $client,
            $request,
            $response
        );

        $progress->write('foo');
        $progress->write('_');
        $progress->write('bar');
        $progress->write('');

        $this->assertEquals([
            [7, 3, $client, $request, $response],
            [7, 4, $client, $request, $response],
            [7, 7, $client, $request, $response]
        ], $calls);
    }
}
