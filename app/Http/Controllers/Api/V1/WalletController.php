<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\WalletRequest;
use App\Services\Api\V1\Wallet\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class WalletController extends Controller
{
    private $wallet;

    public function __construct(WalletRequest $request)
    {
        $this->wallet = WalletService::getInstance($request);
    }

    /**
     * @param WalletRequest $request
     * @return JsonResponse
     */
    public function balance(WalletRequest $request): JsonResponse
    {

        if (!$this->wallet->hasDeposit()) {
            return Response::withoutData(__('wallet.user_not_found'));
        }

        return Response::success([
            'user_id' => $request->user_id,
            'balance' => $this->wallet->getBalance()
        ], __('wallet.get_balance_success'));
    }

    /**
     *
     * Get all user deposits
     *
     * @param WalletRequest $request
     * @return JsonResponse
     */
    public function deposits(WalletRequest $request): JsonResponse
    {
        return Response::success($this->wallet->deposits(), __('wallet.get_deposits'));
    }

    /**
     *
     * Get all user withrawals
     *
     * @param WalletRequest $request
     * @return JsonResponse
     */
    public function withdrawals(WalletRequest $request): JsonResponse
    {
        return Response::success($this->wallet->withdrawals(), __('wallet.get_withdrawals'));
    }
}
