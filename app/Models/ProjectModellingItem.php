<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class ProjectModellingItem extends Model
{
    protected $table = 'project_modelling_items';
 
    protected $fillable = [
        'project_Id',
        'catalog_module_id',
        'catalog_item_id',
        'unit_qty',
        'days',
        'daily_rate',
        'mark_up',
        'line_total',
    ];
 
    protected $casts = [
        'unit_qty' => 'decimal:2',
        'days' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'mark_up' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];
 
    // project_Id is a custom-cased key, same pattern as Project's other relations
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_Id', 'project_Id');
    }
 
    public function catalogModule()
    {
        return $this->belongsTo(CatalogModule::class, 'catalog_module_id');
    }
 
    public function catalogItem()
    {
        return $this->belongsTo(CatalogItem::class, 'catalog_item_id');
    }
}