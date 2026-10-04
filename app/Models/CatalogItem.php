<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class CatalogItem extends Model
{
    use HasFactory;
 
    protected $table = 'catalog_items';
 
    protected $fillable = [
        'module_id',
        'work_package',
        'name',
        'qty',
        'rate',
        'days',
        'markup',
    ];
 
    protected $casts = [
        'qty' => 'decimal:2',
        'rate' => 'decimal:2',
        'days' => 'decimal:2',
        'markup' => 'decimal:2',
    ];
 
    // These two get included automatically whenever a CatalogItem is
    // converted to an array/JSON (e.g. @json($items) in a Blade view).
    protected $appends = [
        'internal_cost',
        'client_cost',
    ];
 
    public function module()
    {
        return $this->belongsTo(CatalogModule::class, 'module_id');
    }
 
    /**
     * qty x rate x days — same formula used in catalog-items.js / catalog-builder.js
     */
    public function getInternalCostAttribute(): float
    {
        return (float) $this->qty * (float) $this->rate * (float) $this->days;
    }
 
    /**
     * Internal cost grossed up for mark-up, e.g. 30% markup -> divide by 0.70
     */
    public function getClientCostAttribute(): float
    {
        $markupFraction = (float) $this->markup / 100;
        $divisor = 1 - $markupFraction;

        if ($divisor <= 0) {
            return 0.0; // Avoid division by zero if markup >= 100%
        }

        return $this->internal_cost / $divisor;
    }
}
 
