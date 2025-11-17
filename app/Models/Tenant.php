<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'company_name',
        'subdomain',
        'user_id',
    ];

    /**
     * Specify which columns should be stored as actual database columns
     * instead of in the JSON 'data' column.
     *
     * @return array<int, string>
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'company_name',
            'subdomain',
            'user_id',
        ];
    }
}
