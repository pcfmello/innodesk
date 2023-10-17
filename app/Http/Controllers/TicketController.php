<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketStoreRequest;
use App\Models\Ticket;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
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
     *     summary="Get a list of tickets",
     *     description="Get a list of tickets with optional filtering by isResolved.",
     *     @OA\Parameter(
     *         name="isResolved",
     *         in="query",
     *         description="Filter tickets by isResolved (0 for false, 1 for true)",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             enum={0, 1}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of tickets",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Ticket")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="The given data was invalid."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={
     *                     "isResolved": {
     *                         "The is resolved field must be one of the following: 0, 1."
     *                     }
     *                 }
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $isResolved = $request->input('isResolved');
        $query = Ticket::query();

        if ($isResolved !== null) {
            $query->where('is_resolved', $isResolved);
        }

        $tickets = $query->get();
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
     *                  example=false
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

    /**
     * @OA\Put(
     *     path="/tickets/{id}",
     *     tags={"Tickets"},
     *     summary="Update a Ticket",
     *     description="Updates an existing ticket with the provided data.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the ticket to be updated.",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data for updating the ticket.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="title",
     *                 type="string",
     *                 description="Title of the ticket (maximum 100 characters).",
     *                 example="Existing ticket"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 description="Description of the ticket.",
     *                 example="Editing the ticket description."
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket updated successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors in the provided data.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 description="Validation errors."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error while updating the ticket or querying the database.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="string",
     *                 description="Error message."
     *             )
     *         )
     *     )
     * )
     */
    public function update(TicketStoreRequest $ticket, string $id)
    {
        try {

            $ticketToUpdate = Ticket::findOrFail($id);
            $data = $ticket->validate([
                'title' => 'required|string|max:100',
                'description' => 'required|string',
            ]);
            $ticketToUpdate->update($data);
            return response()->json(['data' => $ticketToUpdate], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        } catch (QueryException $e) {
            return response()->json(['errors' => 'Error while querying the database.'], 500);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Error to update the ticket. Please, try again later or contact the technical support.'], 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/tickets/{id}/toggle-resolve",
     *     tags={"Tickets"},
     *     summary="Toggle Ticket Resolution Status",
     *     description="Toggle the resolution status of a ticket by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the ticket to toggle resolution status.",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Ticket resolution status toggled successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Updated ticket data."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors in the provided data.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 description="Validation errors."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error while toggling ticket resolution status or querying the database.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="string",
     *                 description="Error message."
     *             )
     *         )
     *     )
     * )
     */
    public function toggleResolve(string $id)
    {
        try {

            $ticketToToggle = Ticket::findOrFail($id);
            $ticketToToggle->is_resolved = !$ticketToToggle->is_resolved;
            $ticketToToggle->update();
            return response()->json(['data' => $ticketToToggle], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        } catch (QueryException $e) {
            return response()->json(['errors' => 'Error while querying the database.'], 500);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Error to update the ticket. Please, try again later or contact the technical support.'], 500);
        }
    }
}
