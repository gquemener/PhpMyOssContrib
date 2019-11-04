<?php
declare(strict_types=1);

namespace App\Contribution\Domain\Model;

use JsonSerializable;

final class Contribution implements JsonSerializable
{
    private $id;
    private $title;
    private $url;
    private $projectName;
    private $createdAt;
    private $updatedAt;
    private $closedAt;
    private $state;

    private function __construct(
        ContributionId $id,
        string $title,
        string $url,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->url = $url;
        if (1 !== preg_match('#^https://github.com/(.*)/p#', $this->url, $matches)) {
            $this->projectName = '???/???';
        }
        $this->projectName = $matches[1];
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->state = ContributionState::opened();
    }

    public static function open(
        ContributionId $id,
        string $title,
        string $url,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ): self {
        return new self($id, $title, $url, $createdAt, $updatedAt);
    }

    public function merge(): void
    {
        $this->state = ContributionState::merged();
    }

    public function close(\DateTimeImmutable $closedAt): void
    {
        $this->state = ContributionState::closed();
        $this->closedAt = $closedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->toInt(),
            'title' => $this->title,
            'url' => $this->url,
            'state' => $this->state->toString(),
            'created_at' => $this->createdAt->format(\DateTimeInterface::ISO8601),
            'updated_at' => $this->updatedAt->format(\DateTimeInterface::ISO8601),
            'closed_at' => $this->closedAt ? $this->closedAt->format(\DateTimeInterface::ISO8601) : null,
        ];
    }

    public static function fromArray(array $data): self
    {
        $self = new self(
            ContributionId::fromInt($data['id']),
            $data['title'],
            $data['url'],
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['created_at']),
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['updated_at'])
        );

        $self->state = ContributionState::fromName($data['state']);
        $self->closedAt = $data['closed_at'] ?
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['closed_at']) :
            null;

        return $self;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function state(): ContributionState
    {
        return $this->state;
    }

    public function projectName(): string
    {
        return $this->projectName;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function closedAt(): ?\DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toInt(),
            'projectName' => $this->projectName,
            'title' => $this->title,
            'url' => $this->url,
            'state' => $this->state->toString(),
            'createdAt' => $this->createdAt->format(\DateTimeInterface::ISO8601),
            'updatedAt' => $this->updatedAt->format(\DateTimeInterface::ISO8601),
            'closedAt' => $this->closedAt ? $this->closedAt->format(\DateTimeInterface::ISO8601) : null,
        ];
    }
}
