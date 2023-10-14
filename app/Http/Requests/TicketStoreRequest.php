<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="Ticket Store Request",
 *     description="Ticket store request body data",
 *     type="object",
 *     required={"title", "description"}
 * )
 */
class TicketStoreRequest extends FormRequest {
    /**
     * @OA\Property(
     *      title="title",
     *      description="Title of the ticket",
     *      format="string",
     *      example="A sample ticket"
     * )
     *
     * @var string
     */
    public $title;

    /**
     * @OA\Property(
     *      title="description",
     *      description="Description of the ticket",
     *      format="string",
     *      example="This is a sample description for the ticket."
     * )
     *
     * @var string
     */
    public $description;

    /**
     * @OA\Property(
     *      title="is_resolved",
     *      description="Status of the ticket",
     *      format="boolean",
     *      example=false
     * )
     *
     * @var bool
     */
    public $is_resolved;
}
