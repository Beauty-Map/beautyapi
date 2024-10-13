<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Events\UploadTicketFileEvent;
use App\Http\Requests\AdminUserUpdateRequest;
use App\Http\Requests\PaymentRequestUpdateStatusRequest;
use App\Http\Requests\PortfolioUpdateRequest;
use App\Http\Requests\PortfolioUpdateStatusRequest;
use App\Http\Requests\TicketCreateRequest;
use App\Http\Resources\PaymentRequestResource;
use App\Http\Resources\PortfolioResource;
use App\Http\Resources\TicketAdminResource;
use App\Http\Resources\TicketResource;
use App\Http\Resources\UserSimpleResource;
use App\Interfaces\IntroInterface;
use App\Interfaces\MetaInterface;
use App\Interfaces\PaymentRequestInterface;
use App\Interfaces\PortfolioInterface;
use App\Interfaces\TicketInterface;
use App\Interfaces\UserInterface;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public UserInterface $userRepository;
    public IntroInterface $introRepository;
    public MetaInterface $metaRepository;
    public PortfolioInterface $portfolioRepository;
    public PaymentRequestInterface $paymentRequestRepository;
    public TicketInterface $ticketRepository;

    public function __construct(
        UserInterface $userRepository,
        IntroInterface $introRepository,
        MetaInterface $metaRepository,
        PortfolioInterface $portfolioRepository,
        PaymentRequestInterface $paymentRequestRepository,
        TicketInterface $ticketRepository,
    )
    {
        $this->userRepository = $userRepository;
        $this->introRepository = $introRepository;
        $this->metaRepository = $metaRepository;
        $this->portfolioRepository = $portfolioRepository;
        $this->paymentRequestRepository = $paymentRequestRepository;
        $this->ticketRepository = $ticketRepository;
    }

    public function indexUsers()
    {
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $users = $this->userRepository->findByPaginate([], $page, $limit);
        } else {
            $users = $this->userRepository->findBy([]);
        }
        return UserSimpleResource::collection($users);
    }

    public function showUser(int $id)
    {
        $user = $this->userRepository->findOneOrFail($id);
        return $user;
    }

    public function updateUser(AdminUserUpdateRequest $request, int $id)
    {
        $request = $request->all();
        /** @var User $auth */
        $auth = $this->userRepository->findOneOrFail($id);
        DB::beginTransaction();
        if (array_key_exists('full_name', $request) && $request['full_name']) {
            $auth->update(['full_name' => $request['full_name']]);
            unset($request['full_name']);
        }
        if (array_key_exists('city_id', $request) && $request['city_id']) {
            $auth->update(['city_id' => $request['city_id']]);
            unset($request['city_id']);
        }
        if (array_key_exists('birth_date', $request) && $request['birth_date']) {
            $auth->update(['birth_date' => $request['birth_date']]);
            unset($request['birth_date']);
        }
        if (array_key_exists('phone_number', $request) && $request['phone_number']) {
            $auth->update(['phone_number' => $request['phone_number']]);
            unset($request['phone_number']);
        }
        if (array_key_exists('roles', $request) && $request['roles']) {
            $auth->syncRoles($request['roles']);
            unset($request['roles']);
        }
        $res = $this->metaRepository->insertOrAdd($request, $auth->id, 'user');
        if ($res) {
            DB::commit();
            return $this->createCustomResponse(1);
        }
        DB::rollBack();
        return $this->createError('error', Constants::UNDEFINED_ERROR, 422);
    }

    public function destroyUser(int $id)
    {
        return $this->userRepository->delete($id);
    }

    public function indexRoles()
    {
        return Role::all();
    }

    public function indexUserPortfolios(int $id)
    {
        $filter = [
            'user_id' => $id,
        ];
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $data = $this->portfolioRepository->findByPaginate($filter, $page, $limit, 'status', 'desc');
        } else {
            $data = $this->portfolioRepository->findBy($filter, 'status', 'desc');
        }
        return PortfolioResource::collection($data);
    }

    public function showUserPortfolio(int $id, int $portfolio)
    {
        return new PortfolioResource($this->portfolioRepository->findOneOrFail($portfolio));
    }

    public function updateUserPortfolio(PortfolioUpdateRequest $request, int $id, int $portfolio)
    {
        return $this->portfolioRepository->update($request->only([
            'status',
        ]), $portfolio);
    }

    public function updateUserPortfolioStatus(PortfolioUpdateStatusRequest $request, int $id, int $portfolio)
    {
        return $this->portfolioRepository->update($request->only([
            'status',
        ]), $portfolio);
    }

    public function destroyUserPortfolio(int $id, int $portfolio)
    {
        return $this->portfolioRepository->delete($portfolio);
    }

    public function indexPortfolios()
    {
        $filter = [
        ];
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $data = $this->portfolioRepository->findByPaginate($filter, $page, $limit, 'status', 'desc');
        } else {
            $data = $this->portfolioRepository->findBy($filter, 'status', 'desc');
        }
        return PortfolioResource::collection($data);
    }

    public function showPortfolio(int $portfolio)
    {
        return new PortfolioResource($this->portfolioRepository->findOneOrFail($portfolio));
    }

    public function updatePortfolio(PortfolioUpdateRequest $request, int $portfolio)
    {
        return $this->portfolioRepository->update($request->only([
            'status',
        ]), $portfolio);
    }

    public function updatePortfolioStatus(PortfolioUpdateStatusRequest $request, int $portfolio)
    {
        return $this->portfolioRepository->update($request->only([
            'status',
        ]), $portfolio);
    }

    public function destroyPortfolio(int $portfolio)
    {
        return $this->portfolioRepository->delete($portfolio);
    }

    public function indexPaymentRequests()
    {
        $filter = [
        ];
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $data = $this->paymentRequestRepository->findByPaginate($filter, $page, $limit, 'created_at', 'desc');
        } else {
            $data = $this->paymentRequestRepository->findBy($filter, 'created_at', 'desc');
        }
        return PaymentRequestResource::collection($data);
    }

    public function showPaymentRequest(int $id)
    {
        return new PaymentRequestResource($this->paymentRequestRepository->findOneOrFail($id));
    }

    public function updatePaymentRequest(PaymentRequestUpdateStatusRequest $request, int $id)
    {
        return $this->paymentRequestRepository->update($request->only([
            'status',
        ]), $id);
    }

    public function updatePaymentRequestStatus(PaymentRequestUpdateStatusRequest $request, int $id)
    {
        return $this->paymentRequestRepository->update($request->only([
            'status',
        ]), $id);
    }

    /**
     * Display a listing of the resource.
     */
    public function indexTickets()
    {
        $data = [
            'parent_id' => null,
        ];
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $tickets = $this->ticketRepository->findByPaginate($data, $page, $limit, 'created_at', 'desc');
        } else {
            $tickets = $this->ticketRepository->findBy($data,'created_at', 'desc');
        }
        return TicketAdminResource::collection($tickets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeTicket(TicketCreateRequest $request)
    {
        $auth = $this->getAuth();
        $request['user_id'] = $auth->id;
        $ticket = $this->ticketRepository->create($request->only([
            'title',
            'subject_id',
            'user_id',
            'parent_id',
            'description'
        ]));
        if ($request['new_file']) {
            event(new UploadTicketFileEvent($ticket, $request['new_file']));
        }
        return new TicketAdminResource($ticket);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeTicketAnswer(TicketCreateRequest $request, int $id)
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
            $this->ticketRepository->update([
                'status' => Ticket::ANSWERED_STATUS,
            ], $id);
            if ($request['new_file']) {
                event(new UploadTicketFileEvent($ticket, $request['new_file']));
            }
            DB::commit();
            return new TicketAdminResource($ticket);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->createError('error', $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function showTicket(int $id)
    {
        return new TicketAdminResource($this->ticketRepository->findOneOrFail($id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function closeTicket(int $id)
    {
        return $this->ticketRepository->update([
            'status' => 'closed'
        ], $id);
    }
}