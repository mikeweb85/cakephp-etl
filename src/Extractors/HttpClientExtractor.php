<?php

namespace App\Etl\Extractors;

use Cake\Core\Exception\CakeException;
use Cake\Utility\Hash;
use Cake\Utility\Xml;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Marquine\Etl\Extractors\Extractor;
use Marquine\Etl\Row;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use Generator;

class HttpClientExtractor extends Extractor {

    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';
    const FORMAT_YAML = 'yaml';
    const FORMAT_YML = 'yml';

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_GOPTIONS = 'OPTIONS';
    const METHOD_HEAD = 'HEAD';

    const AUTH_BASIC = 'basic';
    const AUTH_DIGEST = 'digest';
    const AUTH_NTLM = 'ntlm';

    /** @var string */
    protected string $format = self::FORMAT_JSON;

    protected string $method = self::METHOD_GET;

    protected string $url = '';

    protected array $auth = [];

    protected array $headers = [];

    protected string $path = '{n}';

    protected array $columns = [];

    /** @var Client */
    protected $input;

    /** @var string[]  */
    protected $availableOptions = [
        'format', 'method', 'url', 'auth', 'headers', 'path', 'columns',
    ];

    /**
     * @return void
     */
    public function initialize(): void {
        if (!isset($this->input)) {
            $this->input = new Client([
                'allow_redirects'   => true,
            ]);
        }
    }

    /**
     * @return Generator
     * @todo: explicit path spec for extraction
     */
    public function extract(): Generator {
        try {
            if (!trim($this->url)) {
                throw new CakeException(
                    __('A value URL spec is required to make a request, [%s] provided', [
                        $this->url
                    ])
                );
            }

            $headers = $this->headers + [
                    'Accept'        => match($this->format) {
                        static::FORMAT_XML      => 'text/xml',
                        static::FORMAT_YAML     => 'text/yaml',
                        static::FORMAT_YML      => 'application/yml',
                        default                 => 'application/json',
                    },
                ];

            $response = $this
                ->input
                ->request($this->method, $this->url, [
                    'auth'              => $this->auth,
                    'headers'           => $headers,
                    'allow_redirects'   => false,
                ]);

            if (!in_array($response->getStatusCode(), [200, 201, 304])) {
                throw new CakeException(
                    __('Invalid HTTP response code [%d].', [
                        $response->getStatusCode()
                    ])
                );
            }

            $contents = $response
                ->getBody()
                ->getContents();

            $contents = match($this->format) {
                static::FORMAT_YML,
                static::FORMAT_YAML => yaml_parse($contents),
                static::FORMAT_XML  => Xml::toArray(
                    Xml::build($contents)
                ),
                default             => json_decode($contents, true),
            };

            $contents = Hash::extract($contents, $this->path);

            foreach ($contents as $row) {
                if (!empty($this->columns)) {
                    $row = array_intersect_key($row, array_flip($this->columns));
                }

                yield new Row($row);
            }


        } catch (RequestException|GuzzleException|CakeException $e) {
            ## TODO: log or trigger error event?
        }
    }
}
