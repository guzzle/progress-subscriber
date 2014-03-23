<?php

namespace GuzzleHttp\Subscriber\Progress;

use GuzzleHttp\Stream\StreamDecoratorTrait;
use GuzzleHttp\Stream\StreamInterface;

/**
 * Adds download progress events to a stream.
 *
 * The supplied callable is invoked each time data is written to the stream.
 * The callable is provided the expected number of bytes to download followed
 * by the total number of downloaded bytes.
 */
class DownloadProgressStream implements StreamInterface
{
    use StreamDecoratorTrait;

    private $expectedSize;
    private $reachedEnd;

    /**
     * @param StreamInterface $stream       Stream to wrap
     * @param callable        $notify       Invoked as data is written
     * @param int             $expectedSize Expected number of bytes to write
     */
    public function __construct(
        StreamInterface $stream,
        callable $notify,
        $expectedSize
    ) {
        $this->stream = $stream;
        $this->notify = $notify;
        $this->expectedSize = $expectedSize;
    }

    public function write($string)
    {
        $result = $this->stream->write($string);

        if (!$this->reachedEnd) {
            $this->reachedEnd = $this->tell() >= $this->expectedSize;
            if ($result) {
                call_user_func($this->notify, $this->expectedSize, $this->tell());
            }
        }

        return $result;
    }
}
