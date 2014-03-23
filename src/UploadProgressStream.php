<?php

namespace GuzzleHttp\Subscriber\Progress;

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

    /**
     * @param StreamInterface $stream Stream to wrap
     * @param callable        $notify Function to invoke as data is read
     */
    public function __construct(StreamInterface $stream, callable $notify)
    {
        $this->stream = $stream;
        $this->notify = $notify;
    }

    public function read($length)
    {
        $result = $this->stream->read($length);

        if (!$result) {
            $this->reachedEnd = true;
        } elseif (!$this->reachedEnd) {
            call_user_func($this->notify, $this->getSize(), $this->tell());
        }

        return $result;
    }
}
