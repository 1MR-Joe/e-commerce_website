<?php
declare(strict_types=1);

namespace App\Middlewares;

use App\Exceptions\SessionException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class StartSessionMiddleware implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if(session_status() === PHP_SESSION_ACTIVE) {
            throw new SessionException("session already started");
        }

        if(headers_sent($file, $line)) {
            throw new SessionException("Headers already sent");
        }

        session_set_cookie_params([
            'httponly' => true,
            'secure' => true,
            'samesite' => 'lax',
        ]);

        session_start();

        $response = $handler->handle($request);

        session_write_close();

        return $response;
    }
}