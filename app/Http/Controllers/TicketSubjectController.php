<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketSubjectCreateRequest;
use App\Http\Requests\TicketSubjectUpdateRequest;
use App\Http\Resources\TicketSubjectResource;
use App\Interfaces\TicketSubjectInterface;
use Illuminate\Http\Request;

class TicketSubjectController extends Controller
{
    protected TicketSubjectInterface $ticketSubjectRepository;

    public function __construct(
        TicketSubjectInterface $ticketSubjectRepository,
    )
    {
        $this->ticketSubjectRepository = $ticketSubjectRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $subjects = $this->ticketSubjectRepository->allByPagination('*', 'created_at', 'asc', $page, $limit);
        } else {
            $subjects = $this->ticketSubjectRepository->all('*', 'created_at', 'asc');
        }
        return TicketSubjectResource::collection($subjects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TicketSubjectCreateRequest $request)
    {
        return $this->ticketSubjectRepository->create($request->only([
            'title'
        ]));
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return new TicketSubjectResource($this->ticketSubjectRepository->findOneOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TicketSubjectUpdateRequest $request, int $id)
    {
        return $this->ticketSubjectRepository->update($request->only([
            'title'
        ]), $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        return $this->ticketSubjectRepository->delete($id);
    }
}
