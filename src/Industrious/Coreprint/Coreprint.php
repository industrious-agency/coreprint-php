<?php
namespace Industrious\Coreprint;

use GuzzleHttp\Client;

final class CorePrint
{
    /**
     *
     */
    const ENDPOINT_LIVE = 'https://www.coreprint.net/ws/jsonfactory/';

    /**
     *
     */
    const ENDPOINT_TEST = 'https://www.coreprint.net/test-ws/jsonfactory/';

    /**
     * @var GuzzleHttp\Client
     */
    private $client;

    /**
     * @var array
     */
    private $auth;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->client = new Client([
            'allow_redirects' => true,
            'timeout' => 10,
            'verify' => false,
        ]);

        $this->auth = [
            'username' => env('COREPRINT_USERNAME'),
            'password' => env('COREPRINT_PASSWORD'),
        ];
    }

    /**
     * @param  string $method
     * @param  array  $arguments
     * @return mixed
     */
    public function __call(string $method, $arguments)
    {
        if (method_exists($this, $method)) {
            return $this->$method($arguments);
        }

        $service = strtolower($method);
        $http_method = $arguments[0];

        unset($arguments[0]);

        return $this->execute($service, $http_method, ...$arguments);
    }

    /**
     * Execute
     *
     * @return
     */
    private function execute($service = null, string $method, $data = [])
    {
        $error = false;

        $url = $this->getEndpoint($method, $service);
        $options = $this->getOptions($method, $service, $data);

        try {
            $response = $this->client->request($method, $url, $options);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        //  Log the error here.
        if ($error) {
            die($error);
        }

        $body = $response->getBody();

        return json_decode($body);
    }

    /**
     * Get Options
     *
     * @param  string $method
     * @param  string $service
     * @return array
     */
    private function getOptions(string $method, string $service, array $data): array
    {
        $key = $this->getKey();

        $options = [
            'auth' => array_values($this->auth),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ];

        if ($method === 'GET') {
            $options['query'] = compact('service', 'key') + $data;
        } else {
            $options['body'] = json_encode((object) $data);
        }

        return $options;
    }

    /**
     * Get Endpoint
     *
     * @return string
     */
    private function getEndpoint(string $method = 'GET', string $service): string
    {
        $url = env('WP_ENV') === 'production'
            ? self::ENDPOINT_LIVE
            : self::ENDPOINT_TEST;

        if ($method !== 'GET') {
            $url .= '?' . http_build_query([
                'service' => $service,
                'key' => $this->getKey(),
            ]);
        }

        return $url;
    }

    /**
     * @return string
     */
    private function getKey(): string
    {
        return env('COREPRINT_KEY');
    }
}
