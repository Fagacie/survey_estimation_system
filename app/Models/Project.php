<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'projects';
    protected $primaryKey = 'project_Id';

    public const TYPES = [
    'CP' => 'Coastal Project',
    'RP' => 'River Project',
    'MP' => 'Maritime Project',
    'GP' => 'Geotech Project',
    'JP' => 'Jetty Project',
    ];

    const LOCATIONS = [
    'Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 'Pahang',
    'Perak', 'Perlis', 'Pulau Pinang', 'Sabah', 'Sarawak', 'Selangor',
    'Terengganu', 'Kuala Lumpur', 'Labuan', 'Putrajaya',
    ];

    protected $fillable = [
        'number',
        'project_type',
        'name',
        'location',
        'status',
        'description',
        'start_date',
        'end_date',
        'period',
        'client_Id',
        'pic_name',
        'pic_no',
        'weather_days',
        'mod_demod_days',
        'patch_test_days',
        'project_category',
        'survey_type',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'weather_days' => 'float',
        'mod_demod_days' => 'float',
        'patch_test_days' => 'float',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_Id', 'client_Id');
    }

    public function lineItems()
    {
        return $this->hasMany(QtInvoice::class, 'project_Id', 'project_Id');
    }

    public function surveyLocations()
    {
        return $this->hasMany(SurveyLocation::class, 'project_id', 'project_Id');
    }

    public function surveyLines()
    {
        return $this->hasMany(SurveyLine::class, 'project_id', 'project_Id');
    }

    public function boundaries()
    {
        return $this->hasMany(ProjectBoundary::class, 'project_id', 'project_Id');
    }

    public function modellingItems()
    {
        return $this->hasMany(ProjectModellingItem::class, 'project_Id', 'project_Id');
    }

    public function modellingSummary()
    {
        return $this->hasOne(ProjectModellingSummary::class, 'project_Id', 'project_Id');
    }

    /**
     * Company code = first letter of each word, e.g. "Kuala Umbra" -> "KU".
     * Returns "XX" when there is no company name.
     */
    public static function clientCode(?string $clientName): string
    {
        return collect(preg_split('/\s+/', trim($clientName ?? '')))
            ->filter()
            ->map(fn ($w) => strtoupper(mb_substr($w, 0, 1)))
            ->implode('') ?: 'XX';
    }

    /**
     * If the number still has the XX placeholder (EHS/CP/XX/005) and we now know
     * the company, return the corrected number (EHS/CP/KU/005).
     * Otherwise return the number unchanged.
     */
    public static function fillClientInNumber(?string $number, ?string $clientName): ?string
    {
        $code = self::clientCode($clientName);

        if (!$number || $code === 'XX' || !str_contains($number, '/XX/')) {
            return $number;
        }

        return \Illuminate\Support\Str::replaceFirst('/XX/', "/{$code}/", $number);
    }

}