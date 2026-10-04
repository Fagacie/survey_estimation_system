<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class Package extends Model
{
    use HasFactory;
 
    protected $table = 'packages';
 
    protected $fillable = [
        'name',
    ];
 
    public function modules()
    {
        return $this->belongsToMany(
            CatalogModule::class,
            'module_package', // pivot table
            'package_id',     // this model's key on the pivot
            'module_id'       // related model's key on the pivot
        );
    }
}
 