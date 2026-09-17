<?php

namespace App\Domain\Shared\ValueObjects;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use JsonSerializable;
use Stringable;

/**
 * BusinessDay represents a café operational operating date.
 *
 * Cafés often operate past midnight (e.g. until 02:00 or 04:00 AM).
 * Financial settlement, shift reconciliation, and daily reporting group
 * transactions into the Business Day of the shift that opened the previous morning.
 */
class BusinessDay implements JsonSerializable, Stringable
{
    public const DEFAULT_TURNOVER_TIME = '04:00:00';
    public const DEFAULT_TIMEZONE = 'Africa/Cairo';

    protected CarbonImmutable $date;
    protected string $timezone;
    protected string $turnoverTime;

    public function __construct(
        string|DateTimeInterface $date,
        string $timezone = self::DEFAULT_TIMEZONE,
        string $turnoverTime = self::DEFAULT_TURNOVER_TIME
    ) {
        $this->timezone = $timezone;
        $this->turnoverTime = $turnoverTime;

        if ($date instanceof DateTimeInterface) {
            $this->date = CarbonImmutable::instance($date)->setTimezone($timezone)->startOfDay();
        } else {
            $this->date = CarbonImmutable::parse($date, $timezone)->startOfDay();
        }
    }

    /**
     * Resolve the operating Business Day for an arbitrary timestamp.
     */
    public static function forDateTime(
        DateTimeInterface|string $dateTime,
        string $timezone = self::DEFAULT_TIMEZONE,
        string $turnoverTime = self::DEFAULT_TURNOVER_TIME
    ): self {
        $carbon = $dateTime instanceof DateTimeInterface
            ? CarbonImmutable::instance($dateTime)->setTimezone($timezone)
            : CarbonImmutable::parse($dateTime, $timezone);

        [$cutoffHour, $cutoffMin, $cutoffSec] = array_map('intval', explode(':', $turnoverTime));
        $cutoffToday = $carbon->startOfDay()->addHours($cutoffHour)->addMinutes($cutoffMin)->addSeconds($cutoffSec);

        // If time is before the cutoff, it belongs to the previous calendar day's business shift
        if ($carbon->lessThan($cutoffToday)) {
            $businessDate = $carbon->subDay()->startOfDay();
        } else {
            $businessDate = $carbon->startOfDay();
        }

        return new self($businessDate, $timezone, $turnoverTime);
    }

    /**
     * Current business day for the venue.
     */
    public static function today(
        string $timezone = self::DEFAULT_TIMEZONE,
        string $turnoverTime = self::DEFAULT_TURNOVER_TIME
    ): self {
        return self::forDateTime(CarbonImmutable::now($timezone), $timezone, $turnoverTime);
    }

    public function toDateString(): string
    {
        return $this->date->format('Y-m-d');
    }

    public function timezone(): string
    {
        return $this->timezone;
    }

    public function turnoverTime(): string
    {
        return $this->turnoverTime;
    }

    /**
     * Exact start timestamp of this operational business day.
     */
    public function startsAt(): CarbonImmutable
    {
        [$hour, $min, $sec] = array_map('intval', explode(':', $this->turnoverTime));
        return $this->date->addHours($hour)->addMinutes($min)->addSeconds($sec);
    }

    /**
     * Exact end timestamp of this operational business day (inclusive).
     */
    public function endsAt(): CarbonImmutable
    {
        return $this->startsAt()->addDay()->subSecond();
    }

    /**
     * Check if a timestamp falls within this business day.
     */
    public function contains(DateTimeInterface|string $dateTime): bool
    {
        $carbon = $dateTime instanceof DateTimeInterface
            ? CarbonImmutable::instance($dateTime)->setTimezone($this->timezone)
            : CarbonImmutable::parse($dateTime, $this->timezone);

        return $carbon->greaterThanOrEqualTo($this->startsAt()) && $carbon->lessThanOrEqualTo($this->endsAt());
    }

    public function next(): self
    {
        return new self($this->date->addDay(), $this->timezone, $this->turnoverTime);
    }

    public function previous(): self
    {
        return new self($this->date->subDay(), $this->timezone, $this->turnoverTime);
    }

    public function equals(BusinessDay $other): bool
    {
        return $this->toDateString() === $other->toDateString() && $this->timezone === $other->timezone();
    }

    public function jsonSerialize(): array
    {
        return [
            'date' => $this->toDateString(),
            'timezone' => $this->timezone,
            'starts_at' => $this->startsAt()->toIso8601String(),
            'ends_at' => $this->endsAt()->toIso8601String(),
            'turnover_time' => $this->turnoverTime,
        ];
    }

    public function __toString(): string
    {
        return $this->toDateString();
    }
}
