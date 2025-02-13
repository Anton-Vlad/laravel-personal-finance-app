<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class OverviewController extends Controller
{
    public function index(Request $request): Response
    {
        $current_user_id = Auth::id();

        $total_transactions = Transaction::query()->where('user_id', $current_user_id)->count();
        $total_balance = Transaction::query()->where('user_id', $current_user_id)->sum('amount');


        $total_incomes = floatval(Transaction::query()->where('user_id', $current_user_id)->where('amount', '>=', 0)->sum('amount'));
        $total_expenses = floatval(Transaction::query()->where('user_id', $current_user_id)->where('amount', '<', 0)->sum('amount'));

        $expense_income_ratio = ($total_incomes != 0 ? $total_expenses / $total_incomes : 0);

        return Inertia::render('Overview', [

            'incomes' => $total_incomes,
            'expenses' => $total_expenses,
            'totalTransactions' => $total_transactions,
            'totalBalance' => $this->displayAmountValue($total_balance) . ' RON',
            'expenseIncomeRatio' => number_format(abs($expense_income_ratio), 2, '.', ',')
        ]);
    }

    private function displayAmountValue(float $val): string {
        $formattedValue = number_format(abs($val), 0, '.', ',');
        return ($val >= 0 ? '+' : '-') . $formattedValue;
    }
}
