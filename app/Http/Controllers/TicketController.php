<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::all();
        return response()->json($tickets, 200);
    }

    public function store(Request $ticket)
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
            return response()->json(['errors' => 'Erro ao criar o ticket. Por favor, tente novamente mais tarde ou contate o suporte técnico.'], 500);

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
