<?php

namespace App\Http\Controllers\Api;

use App\Enums\TicketStatusEnum;
use App\Enums\TicketTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\ChangeTicketStatusRequest;
use App\Http\Requests\Ticket\CreateTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\TicketType;
use App\Service\UserService;
use Illuminate\Http\Request;
use Mockery\Undefined;

class TicketController extends Controller
{
    public function __construct(private UserService $userService ){}

    function create(CreateTicketRequest $request)  {
        $task = Task::find(id: $request->task_id);
        abort_if(!$this->userService->isAdministratorOrCollaboratorOfTheProject($task->taskGroup->project->id, auth()->id()), 403, 'Action unauthorized');
        abort_if(!Task::find($request->task_id), 404, 'task  not found');
        $ticket = Ticket::create([
            'title' => $request->title,
            'description' => $request->description,
            'task_id' => $task->id,
            'ticket_type_id' => $request->ticket_type_id,
            'ticket_status_id' => TicketStatus::whereName(TicketStatusEnum::IN_PROGRESS)->first()->id,
        ]);
        return response()->json([
            'status' => true,
            'ticket' => TicketResource::make($ticket),
        ], 200);
    }


    function delete(int $ticketId)  {
        $ticket = Ticket::findOrFail($ticketId);
        abort_if(!$this->userService->isAdministratorOrCollaboratorOfTheProject($ticket->task->taskGroup->project->id, auth()->id()), 403, 'Action unauthorized');
        $ticket->delete();
        return response()->json([
            'status' => true,
            'ticket' => TicketResource::make($ticket),
        ], 200);
    }


    function fetch(Request $request, $taskId)  {
        $task = Task::find(id: $taskId);
        abort_if(!$this->userService->isAdministratorOrCollaboratorOfTheProject($task->taskGroup->project->id, auth()->id()), 403, 'Action unauthorized');
        $query = Ticket::where('task_id', $taskId)
        ->where('ticket_status_id', TicketStatus::findByName($request->status)->id);
        if (TicketTypeEnum::tryFrom($request->type) !== null) {
            $query = $query->where('ticket_type_id', TicketType::findByName($request->type)->id);
        }
        $tickets = $query->get()
        ->sortByDesc('created_at');
        return response()->json([
            'status' => true,
            'tickets' => TicketResource::collection($tickets),
        ], 200);
    }

    function update(UpdateTicketRequest $request, $ticketId)  {
        $ticket = Ticket::findOrFail($ticketId);
        abort_if(!$this->userService->isAdministratorOrCollaboratorOfTheProject($ticket->task->taskGroup->project->id, auth()->id()), 403, 'Action unauthorized');
        $ticket->update($request->all())->save();
        $ticket->refresh();
        return response()->json([
            'status' => true,
            'ticket' => TicketResource::make($ticket),
        ], 200);
    }


    function changeStatus(ChangeTicketStatusRequest $request, $ticketId)  {
        $ticket = Ticket::findOrFail($ticketId);
        abort_if(!$this->userService->isAdministratorOrCollaboratorOfTheProject($ticket->task->taskGroup->project->id, auth()->id()), 403, 'Action unauthorized');
        $ticket->update([
            'ticket_status_id' => TicketStatus::findByName($request->status)->id
        ]);
        $ticket->refresh();
        return response()->json([
            'status' => true,
            'ticket' => TicketResource::make($ticket),
        ], 200);
    }
}
