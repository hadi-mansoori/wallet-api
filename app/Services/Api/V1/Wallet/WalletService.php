<?php

namespace App\Services\Api\V1\Wallet;

use App\Models\Deposit;
use App\Models\Withdrawal;
use phpDocumentor\Reflection\DocBlock\Tags\Method;

class WalletService
{
    private static $instance;
    private object $request;
    private object $deposits;
    private object $withdrawals;
    private bool $depositFind = false;
    private bool $withdrawalFind = false;
    private float $balance = 0;
    public array $fields = ['amount'];

    /**
     * @param $request
     */
    public function __construct($request)
    {
        $this->request = $request;
        $this->deposits = Deposit::query()
            ->where('user_id', $request->user_id)
            ->select($this->fields);

        $this->withdrawals = Withdrawal::query()
            ->where('user_id', $request->user_id)
            ->select($this->fields);

        if ($this->deposits->get()->isNotEmpty()) {
            $this->depositFind = true;
        }

        if ($this->withdrawals->get()->isNotEmpty()) {
            $this->withdrawalFind = true;
        }
    }

    /**
     *
     * Create instace of team service
     *
     * @param object $request
     * @return object
     */
    public static function getInstance(object $request)
    {
        if (!self::$instance) {
            self::$instance = new WalletService($request);
        }
        return self::$instance;
    }

    /**
     *
     * Return user account balance
     *
     * @return int
     */
    public function getBalance(): float
    {

        if (!$this->depositFind) {
            return $this->balance;
        }

        $totalDeposit = $this->deposits->sum('amount');
        $totalWithdrawal = $this->withdrawals->sum('amount');
        $this->balance = $totalDeposit - $totalWithdrawal;

        return $this->balance;
    }

    /**
     *
     * Check user have deposits
     *
     * @return bool
     */
    public function hasDeposit(): bool
    {
        if ($this->depositFind) {
            return true;
        }
        return false;
    }

    /**
     *
     * Check user have deposits
     *
     * @return bool
     */
    public function hasWithdrawal(): bool
    {
        if ($this->withdrawalFind) {
            return true;
        }
        return false;
    }

    /**
     *
     * wallet deposits
     *
     * @param array $fields
     * @return object
     */
    public function deposits(array $fields = ['id', 'user_id', 'amount']): object
    {
        return $this->deposits->select($fields)->get();
    }

    /**
     *
     * wallet withdrawals
     *
     * @param array $fields
     * @return object
     */
    public function withdrawals(array $fields = ['id', 'user_id', 'amount']): object
    {
        return $this->withdrawals->select($fields)->get();
    }

    /**
     *
     * Get sum withdrawals amount
     *
     * @return object
     */
    public function withdrawalAmount(): int
    {
        return $this->withdrawals->select('amount')->sum('amount');
    }

    /**
     *
     * Get sum deposits amount
     *
     * @return object
     */
    public function depositsAmount(): int
    {
        return $this->deposits->select('amount')->sum('amount');
    }
}
