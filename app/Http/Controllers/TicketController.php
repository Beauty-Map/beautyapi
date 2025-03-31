<?php

namespace App\Http\Controllers;

use App\Events\UploadTicketFileEvent;
use App\Http\Requests\TicketCreateRequest;
use App\Http\Resources\TicketAdminResource;
use App\Http\Resources\TicketResource;
use App\Interfaces\TicketInterface;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     * Display a listing of the resource.
     */
    public function indexAnswers(int $id)
    {
        /** @var Ticket $ticket */
        $ticket = $this->ticketRepository->findOneOrFail($id);
        if ($this->hasPage()) {
            $limit = $this->getLimit();
            $tickets = $ticket->children()->orderByDesc('created_at')->paginate($limit);
        } else {
            $tickets = $ticket->children()->orderByDesc('created_at')->get();
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
     * Store a newly created resource in storage.
     */
    public function answer(int $id, TicketCreateRequest $request)
    {
        $auth = $this->getAuth();
        $request['user_id'] = $auth->id;
        $request['parent_id'] = $id;
        DB::beginTransaction();
        try {
            $ticket = $this->ticketRepository->create($request->only([
                'title',
                'subject_id',
                'user_id',
                'parent_id',
                'description'
            ]));
            DB::commit();
            return new TicketResource($ticket);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->createError('error', $e->getMessage(), 500);
        }
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
