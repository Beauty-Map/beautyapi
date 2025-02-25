<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentOptionCreateRequest;
use App\Http\Requests\PaymentOptionUpdateRequest;
use App\Http\Resources\PaymentOptionResource;
use App\Interfaces\PaymentOptionInterface;
use App\Models\PaymentOption;
use Illuminate\Http\Request;

class PaymentOptionController extends Controller
{
    protected PaymentOptionInterface $paymentOptionRepository;

    public function __construct(
        PaymentOptionInterface $paymentOptionRepository,
    )
    {
        $this->paymentOptionRepository = $paymentOptionRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $options = $this->paymentOptionRepository->allByPagination('*', 'coins', 'asc', $page, $limit);
        } else {
            $options = $this->paymentOptionRepository->all('*', 'coins', 'asc');
        }
        return PaymentOptionResource::collection($options);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentOptionCreateRequest $request)
    {
        $option = $this->paymentOptionRepository->create($request->only([
            'coins',
            'gift',
            'price',
            'discount_price',
        ]));
        return new PaymentOptionResource($option);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return new PaymentOptionResource($this->paymentOptionRepository->findOneOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PaymentOptionUpdateRequest $request, int $id)
    {
        return $this->paymentOptionRepository->update($request->only([
            'coins',
            'gift',
            'price',
            'discount_price',
        ]), $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        return $this->paymentOptionRepository->delete($id);
    }
}
