<?php
declare(strict_types=1);

namespace App\Contribution\Domain\Model;

use App\Github\Domain\Model\PullRequestId;

final class ContributionId
{
    private $id;

    private function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function fromGithubId(PullRequestId $id): self
    {
        return new self($id->toInt());
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
