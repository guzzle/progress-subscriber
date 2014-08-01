<?php
namespace GuzzleHttp\Subscriber\Progress;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Stream\StreamDecoratorTrait;
use GuzzleHttp\Stream\StreamInterface;

/**
 * Adds upload progress events to a stream.
 *
 * The supplied callable is invoked each time data is read from the stream.
 * The callable is provided the expected number of bytes to upload followed
 * by the total number of uploaded bytes.
 */
class UploadProgressStream implements StreamInterface
{
    use StreamDecoratorTrait;

    private $reachedEnd;
    private $client;
    private $request;

    /**
     * @param StreamInterface  $stream  Stream to wrap
     * @param callable         $notify  Function to invoke as data is read
     * @param ClientInterface  $client  Client sending the request
     * @param RequestInterface $request Request being sent
     */
    public function __construct(
        StreamInterface $stream,
        callable $notify,
        ClientInterface $client,
        RequestInterface $request
    ) {
        $this->stream = $stream;
        $this->notify = $notify;
        $this->client = $client;
        $this->request = $request;
    }

    public function read($length)
    {
        $result = $this->stream->read($length);

        if (!$result) {
            $this->reachedEnd = true;
        } elseif (!$this->reachedEnd) {
            call_user_func(
                $this->notify,
                $this->getSize(),
                $this->tell(),
                $this->client,
                $this->request
            );
        }

        return $result;
    }
}
