<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\DepositRequest;
use App\Http\Resources\Api\V1\DepositResource;
use App\Models\Deposit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $teams = QueryBuilder::for(Deposit::class)
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

        return Response::paginate($teams, $teams->items(), 'success');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param DepositRequest $request
     * @return JsonResponse
     */
    public function store(DepositRequest $request): JsonResponse
    {
        $deposit = Deposit::query()->create($request->validated());
        return Response::created(new DepositResource($deposit));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $deposit = Deposit::query()->findOrFail($id);
        return Response::success(new DepositResource($deposit), __('deposit.showed'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param DepositRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(DepositRequest $request, $id): JsonResponse
    {
        $deposit = Deposit::query()->findOrFail($id);
        $deposit->update($request->validated());
        return Response::success(new DepositResource($deposit->fresh()), __('deposit.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        Deposit::query()->findOrFail($id)->delete();
        return Response::withoutData(__('deposit.destroyed'));
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

        Deposit::withTrashed()->findOrFail($id)->restore();
        return Response::withoutData(__('deposit.restored'));
    }

    /**
     *
     * List of deleted (trash)
     *
     * @return JsonResponse
     */
    public function trash(): JsonResponse
    {
        $trashed = Deposit::onlyTrashed()->get();
        return Response::success($trashed, __('deposit.trashed'));
    }
}
