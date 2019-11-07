<?php
declare(strict_types=1);

namespace App\Github\Infrastructure\Finder;

use App\Github\Domain\Finder\PullRequestFinder;
use Iterator;
use GuzzleHttp\Client;
use function GuzzleHttp\Psr7\parse_header;

final class GuzzlePullRequestFinder implements PullRequestFinder
{
    private static $client;

    private $username;
    private $token;

    public function __construct(string $username, string $token)
    {
        $this->username = $username;
        $this->token = $token;
    }

    public function all(): object
    {
        return $this->getPaginatedIterator(sprintf(
            'is:public is:pr author:%s',
            $this->username
        ));
    }

    public function merged(): object
    {
        return $this->getPaginatedIterator(sprintf(
            'is:public is:pr is:merged author:%s',
            $this->username
        ));
    }

    private function getPaginatedIterator(string $query): Iterator
    {
        if (null === self::$client) {
            self::$client = new Client([
                'auth' => [
                    $this->username,
                    $this->token
                ]
            ]);
        }

        return new class(self::$client, $query) implements Iterator {
            private $client;
            private $query;
            private $page = 1;
            private $results = [];
            private $index = 0;

            public function __construct(Client $client, string $query)
            {
                $this->client = $client;
                $this->query = $query;
            }

            public function next()
            {
                ++$this->index;
            }

            public function current()
            {
                if (count($this->results) > $this->index) {
                    return $this->results[$this->index];
                }

                $response = $this->client->request(
                    'GET',
                    'https://api.github.com/search/issues',
                    [
                        'query' => [
                            'page' => $this->page,
                            'q' => $this->query,
                        ],
                    ]
                );

                $nextPage = null;
                foreach (parse_header($response->getHeader('Link')) as $link) {
                    if ('next' === $link['rel']) {
                        $nextPage = $this->page + 1;
                        break;
                    }
                }
                $this->page = $nextPage;

                $result = json_decode((string) $response->getBody(), true);

                $this->rewind();
                $this->results = $result['items'];

                return $this->results[$this->index];
            }

            public function key()
            {
                return sprintf('%d.%d', $this->page, $this->index);
            }

            public function rewind(): void
            {
                $this->index = 0;
            }

            public function valid(): bool
            {
                return null !== $this->page
                    && (
                        count($this->results) !== 0
                        || count($this->results) >= $this->index
                    );
            }
        };
    }
}
