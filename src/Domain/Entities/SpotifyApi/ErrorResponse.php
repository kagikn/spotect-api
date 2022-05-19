<?php

namespace App\Domain\Entities\SpotifyApi;

use Psr\Http\Message\ResponseInterface as Response;

class ErrorResponse
{
    /**
     * @param int $httpStatus
     * @param string $message
     */
    public function __construct(
        public readonly int    $httpStatus,
        public readonly string $message
    )
    {
    }

    public function writeErrorResponse(Response $response): Response
    {
        $error = [];
        $error['error'] = ['status' => $this->httpStatus, 'message' => $this->message];

        $response = $response->withHeader('Content-Type', 'application/json');
        $response = $response->withStatus($this->httpStatus);
        $response->getBody()->write(json_encode($error));

        return $response;
    }
}
