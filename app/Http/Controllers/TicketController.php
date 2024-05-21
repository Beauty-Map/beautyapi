<?php

namespace App\Http\Controllers;

use App\Events\UploadTicketFileEvent;
use App\Http\Requests\TicketCreateRequest;
use App\Http\Resources\TicketResource;
use App\Interfaces\TicketInterface;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    protected TicketInterface $ticketRepository;

    public function __construct(
        TicketInterface $ticketRepository,
    )
    {
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $auth = $this->getAuth();
        $data = [
            'user_id' => $auth->id,
            'parent_id' => null,
        ];
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $tickets = $this->ticketRepository->findByPaginate($data, $page, $limit, 'created_at', 'desc');
        } else {
            $tickets = $this->ticketRepository->findBy($data,'created_at', 'desc');
        }
        return TicketResource::collection($tickets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TicketCreateRequest $request)
    {
        $auth = $this->getAuth();
        $request['user_id'] = $auth->id;
        $ticket = $this->ticketRepository->create($request->only([
            'title',
            'subject_id',
            'user_id',
            'description'
        ]));
        if ($request['new_file']) {
            event(new UploadTicketFileEvent($ticket, $request['new_file']));
        }
        return new TicketResource($ticket);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return new TicketResource($this->ticketRepository->findOneOrFail($id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function close(int $id)
    {
        return $this->ticketRepository->update([
          'status' => 'closed'
        ], $id);
    }
}
