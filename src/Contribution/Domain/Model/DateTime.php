<?php
declare(strict_types=1);

namespace App\Contribution\Domain\Model;

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;

final class DateTime
{
    private const FORMAT = DateTimeInterface::ATOM;

    private DateTimeImmutable $datetime;

    public function __construct(DateTimeImmutable $datetime)
    {
        $this->datetime = $datetime;
    }

    public static function fromString(string $data): self
    {
        $datetime = DateTimeImmutable::createFromFormat(self::FORMAT, $data);

        if (false === $datetime) {
            throw new InvalidArgumentException(sprintf(
                '"%s" is not a valid "%s" datetime',
                $data,
                self::FORMAT
            ));
        }

        return new self($datetime);
    }

    public function year(): string
    {
        return $this->datetime->format('Y');
    }

    public function toString(): string
    {
        return $this->datetime->format(self::FORMAT);
    }
}
