<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::all();
        return response()->json($tickets, 200);
    }

    public function store(Request $ticket)
    {
        $data = $ticket->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string',
        ]);

        $ticket = Ticket::create($data);
        return response()->json($ticket, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $ticketId)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
