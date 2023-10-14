<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketStoreRequest;
use App\Models\Ticket;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="InnoDesk API Documentation",
 *      description="Documentation for the InnoDesk API",
 *      @OA\Contact(
 *          email="pcfmello@gmail.com"
 *      )
 * )
 */
class TicketController extends Controller
{
    /**
     * @OA\Get(
     *     path="/tickets",
     *     tags={"Tickets"},
     *     summary="Retrieve list of tickets",
     *     description="Get all tickets from the database",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Ticket")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function index()
    {
        $tickets = Ticket::all();
        return response()->json($tickets, 200);
    }

    /**
 * @OA\Post(
 *     path="/tickets",
 *     tags={"Tickets"},
 *     summary="Create a new ticket",
 *     description="Store a new ticket in the database",
 *     operationId="storeTicket",
 *     @OA\RequestBody(
 *         description="Ticket to be created",
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title", "description"},
 *             @OA\Property(
 *                  property="title",
 *                  type="string",
 *                  description="The title of the ticket",
 *                  example="New ticket"
 *             ),
 *             @OA\Property(
 *                  property="description",
 *                  type="string",
 *                  description="A description of the ticket's issue",
 *                  example="New ticket description"
 *             ),
 *             @OA\Property(
 *                  property="is_resolved",
 *                  type="boolean",
 *                  description="The status of the ticket",
  *                 example=false
 *              ),
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Successful operation",
 *         @OA\JsonContent(ref="#/components/schemas/Ticket")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                  property="errors",
 *                  type="object",
 *                  description="The validation errors."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                  property="errors",
 *                  type="string",
 *                  example="Error to create the ticket. Please, try again later or contact the technical support."
 *             )
 *         )
 *     ),
 * )
 */
    public function store(TicketStoreRequest $ticket)
    {
        try {
            $data = $ticket->validate([
                'title' => 'required|string|max:100',
                'description' => 'required|string',
            ]);
            $data['ticket_code'] = $this->generate_unique_ticket_code();
            $ticket = Ticket::create($data);
            return response()->json(['data' => $ticket], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Error to create the ticket. Please, try again later or contact the technical support.'], 500);

        }
    }

    function generate_unique_ticket_code() {
        $ticket_code = 'INDK' . str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
        $ticket = Ticket::where('ticket_code', $ticket_code)->first();
        if ($ticket) {
            return $this->generate_unique_ticket_code();
        }
        return $ticket_code;
    }
}
