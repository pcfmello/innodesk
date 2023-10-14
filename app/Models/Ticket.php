<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Ticket",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="The unique ID of the ticket"
 *     ),
 *     @OA\Property(
 *         property="ticket_code",
 *         type="string",
 *         description="The unique code of the ticket"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="The title of the ticket"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The description of the ticket"
 *     ),
 *     @OA\Property(
 *         property="is_resolved",
 *         type="boolean",
 *         description="Flag indicating if the ticket is resolved"
 *     ),
 * )
 */
class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_code',
        'title',
        'description',
        'is_resolved',
    ];
}
