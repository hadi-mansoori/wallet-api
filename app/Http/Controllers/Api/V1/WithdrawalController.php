<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\WithdrawalRequest;
use App\Http\Resources\Api\V1\WithdrawalResource;
use App\Services\Api\V1\Wallet\WalletService;
use App\Models\Withdrawal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class WithdrawalController extends Controller
{
    public function __construct(WithdrawalRequest $request)
    {
        $this->wallet = WalletService::getInstance($request);
    }
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(WithdrawalRequest $request): JsonResponse
    {
        $teams = QueryBuilder::for(Withdrawal::class)
            ->allowedSorts('id')
            ->allowedFields(['id',
                'user_id',
                'amount',
                'created_at',
                'updated_at',
            ])
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('amount'),
                AllowedFilter::exact('created_at'),
                AllowedFilter::exact('updated_at'),
            ])
            ->paginate($request->input('count'));

        return Response::paginate($teams, $teams->items(), __('withdrawal.index'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param WithdrawalRequest $request
     * @return JsonResponse
     */
    public function store(WithdrawalRequest $request)
    {
        if (!$this->wallet->hasDeposit()) {
            return Response::badRequest('bad Request');
        }

        if ($request->amount > $this->wallet->getBalance()) {
            return Response::badRequest(__('withdrawal.withdrawal_grater_than_deposits_error'));
        }

        $deposit = Withdrawal::query()->create($request->validated());
        return Response::created(new WithdrawalResource($deposit));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $deposit = Withdrawal::query()->findOrFail($id);
        return Response::success(new WithdrawalResource($deposit), __('withdrawal.showed'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param WithdrawalRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(WithdrawalRequest $request, $id): JsonResponse
    {
        $deposit = Withdrawal::query()->findOrFail($id);
        $deposit->update($request->validated());
        return Response::success(new WithdrawalResource($deposit->fresh()), __('withdrawal.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        Withdrawal::query()->findOrFail($id)->delete();
        return Response::withoutData(__('withdrawal.destroyed'));
    }

    /**
     *
     * Restore by id
     *
     * @param $id
     * @return JsonResponse
     */
    public function restore($id): JsonResponse
    {
        Withdrawal::withTrashed()->findOrFail($id)->restore();
        return Response::withoutData(__('withdrawal.restored'));
    }

    /**
     *
     * List of deleted (trash)
     *
     * @return JsonResponse
     */
    public function trash(): JsonResponse
    {
        $trashed = Withdrawal::onlyTrashed()->get();
        return Response::success($trashed, __('withdrawal.trashed'));
    }
}
