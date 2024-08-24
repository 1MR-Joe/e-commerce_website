<?php
declare(strict_types=1);

namespace App\Middlewares;

use App\Exceptions\ValidationException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AjaxValidationExceptionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory
    ){
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);

        } catch (ValidationException $e) {

            $response = $this->responseFactory->createResponse(400)->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($e->errors));
            return $response;
        }
    }
}