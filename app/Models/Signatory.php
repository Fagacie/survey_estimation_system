<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Signatory extends Model
{
    protected $fillable = [
        'name',
        'position',
        'signature_path',
        'created_by',
    ];
}
 