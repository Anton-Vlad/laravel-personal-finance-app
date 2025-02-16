<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Transaction extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\TransacionFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category',
        'date',
        'amount',
        'recurring',
        'user_id',
        'statement_id',
        'details',
        'currency',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'datetime',
            'recurring' => 'boolean',
            'details' => 'array',
        ];
    }

    /**
     * Scope a query to transactions within today.
     */
    public function scopeForAuthUser(Builder $query): Builder
    {
        $current_user_id = Auth::id();
        return $query->where('user_id', $current_user_id);
    }

    public function scopeForToday(Builder $query): Builder
    {
        return $query->whereDate('date', now());
    }

    public function scopeForPastDays(Builder $query, int $past_days): Builder
    {
        $days_start = Carbon::now()->subDays($past_days);
        return $query->where('date', '>=', $days_start);
    }

    /**
     * Scope a query to transactions within the current week.
     */
    public function scopeForThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope a query to transactions within the current month.
     */
    public function scopeForThisMonth(Builder $query): Builder
    {
        return $query->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
    }

    /**
     * Scope a query to transactions within the previous month.
     */
    public function scopeForLastMonth(Builder $query): Builder
    {
        $start = Carbon::now()->startOfMonth()->subMonth();
        $end = Carbon::now()->startOfMonth()->subMonth()->endOfMonth();

        return $query->whereBetween('date', [$start, $end]);
    }

    /**
     * Scope a query to transactions within the past X full months.
     */
    public function scopeForPastMonths(Builder $query, int $past_months): Builder
    {
        $start = Carbon::now()->startOfMonth()->subMonths($past_months);
        $end = Carbon::now()->startOfMonth()->subMonth()->endOfMonth();

        return $query->whereBetween('date', [$start, $end]);
    }

    /**
     * Scope a query to transactions within the requested month.
     */
    public function scopeForOneMonth(Builder $query, string $month): Builder
    {
        // Ensure the format is correct before proceeding
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            throw new \InvalidArgumentException("Invalid month format. Expected 'YYYY-MM'.");
        }

        [$year, $month] = explode('-', $month); // Extract year and month

        $start_of_month = Carbon::createFromDate($year, $month, 1, 'UTC')->startOfMonth();
        $end_of_month = Carbon::createFromDate($year, $month, 1, 'UTC')->endOfMonth();

        return $query->whereBetween('date', [$start_of_month, $end_of_month]);
    }

    /**
     * Scope a query to transactions within the requested period.
     */
    public function scopeForPeriod(Builder $query, string $period_type, string $period_value = null): Builder
    {
        logger()->info('REQ FOR', ['tpye' => $period_type, 'value' => $period_value]);
        switch ($period_type) {
            case 'today':
                return $query->forToday();
            case '15days':
                return $query->forPastDays(15);
            case 'thisMonth':
                return $query->forThisMonth();
            case 'lastMonth':
                return $query->forLastMonth();
            case 'last3Months':
                return $query->forPastMonths(3);
            case 'thisYear':
                return $query->forThisYear();
            case 'week':
                return $query->forThisWeek();
            case 'month':
                if (!$period_value) {
                    return $query->forThisMonth();
                }
                return $query->forOneMonth($period_value);
            case 'year':
                if (!$period_value) {
                    return $query->forThisYear();
                }
                return $query->forOneYear($period_value);
            default:
                return $query;
        }
    }

    /**
     * Scope a query to transactions within the current year.
     */
    public function scopeForThisYear(Builder $query): Builder
    {
        return $query->whereBetween('date', [now()->startOfYear(), now()->endOfYear()]);
    }

    /**
     * Scope a query to transactions within the requested year.
     */
    public function scopeForOneYear(Builder $query, int $year): Builder
    {
        $startOfYear = Carbon::createFromDate($year, 1, 1, 'UTC')->startOfYear();
        $endOfYear = Carbon::createFromDate($year, 12, 31, 'UTC')->endOfYear();

        return $query->whereBetween('date', [$startOfYear, $endOfYear]);
    }
}
