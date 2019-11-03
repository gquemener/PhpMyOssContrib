<?php
declare(strict_types=1);

namespace App\Github\Infrastructure\Repository;

use App\Github\Domain\Repository\PullRequestRepository;
use App\Github\Domain\Model\PullRequest;
use Traversable;
use Iterator;
use GuzzleHttp\Client;
use function GuzzleHttp\Psr7\parse_header;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use Kevinrob\GuzzleCache\Storage\DoctrineCacheStorage;
use Doctrine\Common\Cache\FilesystemCache;

final class GuzzleRepository implements PullRequestRepository
{
    private static $client;

    public function all(): Traversable
    {
        return $this->getPaginatedIterator('is:public is:pr author:gquemener', [PullRequest::class, 'found']);
    }

    public function merged(): Traversable
    {
        return $this->getPaginatedIterator(
            'is:public is:pr is:merged author:gquemener',
            [PullRequest::class, 'foundMerged']
        );
    }

    private function getPaginatedIterator(string $query, callable $factory): Iterator
    {
        if (null === self::$client) {
            self::$client = new Client([
                'auth' => [
                    'gquemener',
                    '8928b3d6964c10da3652cfb06a5ffb645cbe4cf5'
                ]
            ]);
        }

        return new class(self::$client, $query, $factory) implements Iterator {
            private $client;
            private $query;
            private $factory;
            private $page = 1;
            private $results = [];
            private $index = 0;

            public function __construct(Client $client, string $query, callable $factory)
            {
                $this->client = $client;
                $this->query = $query;
                $this->factory = $factory;
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
                $this->results = array_map($this->factory, $result['items']);

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
