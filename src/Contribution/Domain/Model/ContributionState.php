<?php
declare(strict_types=1);

namespace App\Contribution\Domain\Model;

final class ContributionState
{
    public const OPTIONS = [
        'opened' => 0,
        'merged' => 1,
        'closed' => 2,
    ];

    public const opened = 0;
    public const merged = 1;
    public const closed = 2;

    private $name;
    private $value;

    private function __construct(string $name)
    {
        $this->name = $name;
        $this->value = self::OPTIONS[$name];
    }

    public static function opened(): self
    {
        return new self('opened');
    }

    public static function merged(): self
    {
        return new self('merged');
    }

    public static function closed(): self
    {
        return new self('closed');
    }

    public static function fromName(string $value): self
    {
        if (! isset(self::OPTIONS[$value])) {
            throw new \InvalidArgumentException('Unknown enum name given');
        }

        return self::{$value}();
    }

    public static function fromValue($value): self
    {
        foreach (self::OPTIONS as $name => $v) {
            if ($v === $value) {
                return self::{$name}();
            }
        }

        throw new \InvalidArgumentException('Unknown enum value given');
    }

    public function equals(ContributionState $other): bool
    {
        return \get_class($this) === \get_class($other) && $this->name === $other->name;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function value()
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function toString(): string
    {
        return $this->name;
    }
}
