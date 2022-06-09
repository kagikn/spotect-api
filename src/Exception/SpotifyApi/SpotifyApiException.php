<?php

namespace App\Exception\SpotifyApi;

class SpotifyApiException extends \Exception
{
    /**
     * @var string
     */
    protected string $endpoint;
    protected array $query;
    protected string $apiMessage;

    public function __construct(
        string $endpoint,
        int $httpStatusCode,
        string $apiMessage,
        array|string $query = null,
        \Throwable $previous = null,
    ) {
        $msg = "The endpoint ${endpoint} returned a response with the status code ${httpStatusCode}.";
        parent::__construct($msg, $httpStatusCode, $previous);
        $this->endpoint = $endpoint;
        $this->apiMessage = $apiMessage;

        if (is_null($query)) {
            $this->query = [];
            return;
        } elseif (is_string($query)) {
            $this->query = self::buildApiArray($query);
            return;
        }

        $this->query = $query;
    }

    private static function buildApiArray(string $query): array
    {
        $newQueryArray = [];
        parse_str($query, $newQueryArray);
        return $newQueryArray;
    }

    public function getEndpointUri(): string
    {
        return $this->endpoint;
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    public function getJsonStringOfErrorObject(): string
    {
        $arrayForErrorObj = [
            'error' => [
                'status' => $this->getCode(),
                'message' => $this->getApiMessage(),
            ]
        ];
        return json_encode($arrayForErrorObj);
    }

    public function getApiMessage(): string
    {
        return $this->apiMessage;
    }
}
