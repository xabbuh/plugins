<?php

namespace Http\Client\Plugin;

use Http\Encoding\ChunkStream;
use Psr\Http\Message\RequestInterface;

/**
 * Allow to set the correct content length header on the request or to transfer it as a chunk if not possible
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
class ContentLengthPlugin implements Plugin
{
    /**
     * {@inheritDoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first)
    {
        if (!$request->hasHeader('Content-Length')) {
            $stream = $request->getBody();

            // Cannot determine the size so we use a chunk stream
            if (null === $stream->getSize()) {
                $stream = new ChunkStream($stream);
                $request = $request->withBody($stream);
            } else {
                $request = $request->withHeader('Content-Length', $stream->getSize());
            }
        }

        return $next($request);
    }
}