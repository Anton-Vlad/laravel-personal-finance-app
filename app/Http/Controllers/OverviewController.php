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
        $period_type = ($request->has('period_type')) ? $request->input('period_type') : 'year';
        $period_value = ($request->has('period_value')) ? $request->input('period_value') : '2024';

        $main_query = Transaction::forAuthUser()->forPeriod($period_type, $period_value)->getQuery();


        $total_transactions = (clone $main_query)->count();
        $total_balance = (clone $main_query)->sum('amount');


        $total_incomes = floatval((clone $main_query)->where('amount', '>=', 0)->sum('amount'));
        $total_expenses = floatval((clone $main_query)->where('amount', '<', 0)->sum('amount'));

        $income_expense_ratio = ($total_expenses != 0 ? $total_incomes / $total_expenses : 0);

        // chek if prev comparation is doable
//        $main_query = Transaction::forAuthUser()->forPrevPeriod($period_type, $period_value)->getQuery();

        return Inertia::render('Overview', [
            'incomes' => $total_incomes,
            'expenses' => $total_expenses,
            'totalTransactions' => $total_transactions,
            'totalBalance' => $this->displayAmountValue($total_balance) . ' RON',
            'expenseIncomeRatio' => number_format(abs($income_expense_ratio), 2, '.', ','),
            'filters' => $request->only(['period_type', 'period_value']),
            'frontend-filters' => config('frontend-filters.period-filter')
        ]);
    }

    private function displayAmountValue(float $val): string {
        $formattedValue = number_format(abs($val), 2, '.', ',');
        return ($val >= 0 ? '+' : '-') . $formattedValue;
    }
}
