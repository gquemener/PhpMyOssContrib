<?php
declare(strict_types=1);

namespace App\Github\Domain\Model;

final class PullRequestId
{
    private $id;

    private function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function fromInt(int $id): self
    {
        return new self($id);
    }

    public function toInt(): int
    {
        return $this->id;
    }
}
