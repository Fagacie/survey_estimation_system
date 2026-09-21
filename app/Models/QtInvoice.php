<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QtInvoice extends Model
{
    // Table name specification
    protected $table = 'qt_invoice';
    protected $primaryKey = 'quotation_Id'; // Set custom primary key

    protected $fillable = [
        'project_Id', 
        'quotation_no', 
        'payment_terms',
        'additional_notes',
        'grand_total', 
        'survey_distance_nm',
        'survey_hours',
        'survey_duration_days',
        'created_by', 
        'updated_by'];

    protected $casts = [
        'grand_total' => 'decimal:2',
        'survey_distance_nm' => 'decimal:4',
        'survey_hours' => 'decimal:2',
        'survey_duration_days' => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_Id', 'project_Id');
    }

    public function items()
    {
        return $this->hasMany(QtInvoiceItem::class, 'quotation_id', 'quotation_Id');
    }

    public function paymentTerms()
    {
        return $this->hasMany(PaymentTerm::class, 'quotation_Id', 'quotation_Id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'quotation_Id', 'quotation_Id');
    }
}