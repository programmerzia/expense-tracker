<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\User;
use App\Models\ExpenseCategory;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'amount',
        'start_date',
        'end_date',
        'period_type',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'amount' => 'decimal:2'
    ];

    protected $appends = ['spent_amount', 'remaining_amount', 'progress_percentage', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function getSpentAmountAttribute()
    {
        return $this->user->expenses()
            ->whereBetween('expense_date', [$this->start_date, $this->end_date])
            ->when($this->category_id, function($query) {
                return $query->where('category_id', $this->category_id);
            })
            ->sum('amount');
    }

    public function getRemainingAmountAttribute()
    {
        return max(0, $this->amount - $this->spent_amount);
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->amount <= 0) {
            return 0;
        }
        return min(100, round(($this->spent_amount / $this->amount) * 100));
    }

    public function getStatusAttribute()
    {
        $percentage = $this->progress_percentage;
        
        if ($percentage >= 100) {
            return 'exceeded';
        } elseif ($percentage >= 80) {
            return 'warning';
        } else {
            return 'normal';
        }
    }
}
