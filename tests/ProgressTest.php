<?php
namespace GuzzleHttp\Tests\Subscriber\Progress;

use GuzzleHttp\Adapter\MockAdapter;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Subscriber\Progress\Progress;
use GuzzleHttp\Stream\Stream;

class ProgressTest extends \PHPUnit_Framework_TestCase
{
    public function testEmitsAsDataIsRead()
    {
        $str = str_repeat('.', 2000);
        $body = Stream::factory($str);
        $adapter = new MockAdapter(
            new Response(200, ['Content-Length' => 2000], $body)
        );

        $up = $down = [];
        $upload = function ($e, $t) use (&$up) { $up[] = [$e, $t]; };
        $download = function ($e, $t) use (&$down) { $down[] = [$e, $t]; };
        $progress = new Progress($upload, $download);

        $client = new Client(['adapter' => $adapter]);
        $response = $client->put('http://foo.com', [
            'body'        => $str,
            'subscribers' => [$progress]
        ]);

        $this->assertEquals([[2000, 2000]], $up);

        // The MockAdapter doesn't actually write to the mocked request, so this
        // is the best assertion we can make here.
        $this->assertInstanceOf(
            'GuzzleHttp\Subscriber\Progress\DownloadProgressStream',
            $response->getBody()
        );
    }
}
