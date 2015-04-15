<?php
namespace Phapi\Middleware\Courier;

use Phapi\Contract\Middleware\Middleware;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Middleware Courier
 *
 * The Courier middleware is responsible for taking the response
 * object and send the headers and body to the client.
 *
 * @category Phapi
 * @package  Phapi\Middleware\Courier
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/phapi/middleware-courier
 */
class Courier implements Middleware {

    /**
     * Output buffer level
     *
     * @var int
     */
    private $outputBufferLevel;

    public function __construct()
    {
        // Start the output buffer
        ob_start();

        // Save the current output buffer level
        $this->outputBufferLevel = ob_get_level();
    }

    /**
     * Handle the middleware pipeline call. This calls the next middleware
     * in the queue and after the rest of the middleware pipeline is done
     * the response will be sent to the client
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        // Call the next middleware
        $response = $next($request, $response, $next);

        // Send response
        $this->send($request, $response);

        // Return the used response
        return $response;
    }

    /**
     * Send the response to the client by first sending all
     * headers and then echoing the response body.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    private function send(RequestInterface $request, ResponseInterface $response)
    {
        // Send headers if no headers has been sent before this
        if (!headers_sent()) {
            // Send headers
            $this->sendHeaders($response);
        }

        // Flush (send) the buffer that contains output that the application has generated
        while (ob_get_level() >= $this->outputBufferLevel) {
            ob_end_flush();
        }

        // Set buffer level to null
        $this->outputBufferLevel = null;

        // Make sure it wasn't a HEAD request
        if ($request->getMethod() !== 'HEAD') {
            // Send the body to the client by echoing it
            printf($response->getBody());
        }
    }

    /**
     * Send all headers to the client
     *
     * @param ResponseInterface $response
     */
    private function sendHeaders(ResponseInterface $response)
    {
        // Set proper protocol, status code (and reason phrase) header
        if ($response->getReasonPhrase()) {
            header(sprintf(
                'HTTP/%s %d %s',
                $response->getProtocolVersion(),
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ));
        } else {
            header(sprintf(
                'HTTP/%s %d',
                $response->getProtocolVersion(),
                $response->getStatusCode()
            ));
        }

        // Loop through all headers
        foreach ($response->getHeaders() as $name => $values) {
            // Sanitize header name
            $name  = $this->sanitizeHeaderName($name);
            // First iteration, if more than one header with the same name
            // exists the $first parameter to the header method will indicate
            // if a second (etc) header should replace the previous one or
            // if multiple headers with the same name should be sent
            $first = true;
            foreach ($values as $value) {
                // Send header
                header(sprintf('%s: %s', $name, $value), $first);
                $first = false;
            }
        }
    }

    /**
     * Filter header by making all words first letter uppercase
     * and making sure all spaces are replaced with dashes "-".
     *
     * @param string $name The name of the header
     * @return string
     */
    private function sanitizeHeaderName($name)
    {
        $name = str_replace('-', ' ', $name);
        $name = ucwords($name);
        return str_replace(' ', '-', $name);
    }
}