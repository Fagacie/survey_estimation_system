<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class CatalogModule extends Model
{
    use HasFactory;
 
    protected $table = 'catalog_modules';
 
    protected $fillable = [
        'name',
        'group',
    ];
 
    public function items()
    {
        return $this->hasMany(CatalogItem::class, 'module_id');
    }
 
    public function packages()
    {
        return $this->belongsToMany(
            Package::class,
            'module_package', // pivot table
            'module_id',      // this model's key on the pivot
            'package_id'      // related model's key on the pivot
        );
    }
}