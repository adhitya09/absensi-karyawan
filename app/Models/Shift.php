<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Shift extends Model
{
    use HasFactory, HasTimestamps;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
    ];

    /**
     * Determine if this shift crosses midnight (overnight shift).
     */
    public function isOvernight(): bool
    {
        return $this->end_time < $this->start_time;
    }

    /**
     * Get the scheduled start datetime for a given calendar date.
     */
    public function getScheduledStart(Carbon|string $date): Carbon
    {
        $d = is_string($date) ? Carbon::parse($date) : $date->copy();
        return $d->setTimeFromTimeString($this->start_time);
    }

    /**
     * Get the scheduled end datetime for a given calendar date.
     * If the shift is overnight, the end datetime is on the next calendar day.
     */
    public function getScheduledEnd(Carbon|string $date): Carbon
    {
        $d = is_string($date) ? Carbon::parse($date) : $date->copy();
        if ($this->isOvernight()) {
            return $d->addDay()->setTimeFromTimeString($this->end_time);
        }
        return $d->setTimeFromTimeString($this->end_time);
    }
}
