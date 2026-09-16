<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number', 'project_id', 'client_id', 'cost_estimation_id',
        'status', 'issue_date', 'due_date', 'payment_terms',
        'client_ref', 'our_ref', 'currency',
        'subtotal', 'tax_rate', 'tax_amount', 'discount_amount', 'grand_total',
        'amount_in_words',
        'payment_bank_name', 'payment_account_name',
        'payment_account_number', 'payment_swift_code',
        'notes',
        'prepared_by_name', 'prepared_by_title',
        'approved_by_name', 'approved_by_title',
        'user_id',
    ];

    protected $casts = [
        'issue_date'      => 'date',
        'due_date'        => 'date',
        'subtotal'        => 'float',
        'tax_rate'        => 'float',
        'tax_amount'      => 'float',
        'discount_amount' => 'float',
        'grand_total'     => 'float',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function costEstimation()
    {
        return $this->belongsTo(CostEstimation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    /**
     * Get the status badge color classes.
     */
    public function getStatusColorAttribute(): array
    {
        return match ($this->status) {
            'Draft'     => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'border' => 'border-slate-300'],
            'Issued'    => ['bg' => 'bg-blue-50',   'text' => 'text-blue-600',  'border' => 'border-blue-200'],
            'Paid'      => ['bg' => 'bg-emerald-50','text' => 'text-emerald-600','border' => 'border-emerald-200'],
            'Overdue'   => ['bg' => 'bg-red-50',    'text' => 'text-red-600',   'border' => 'border-red-200'],
            'Cancelled' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-400', 'border' => 'border-slate-200'],
            default     => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'border' => 'border-slate-300'],
        };
    }
}
