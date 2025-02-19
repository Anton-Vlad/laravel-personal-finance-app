<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class BudgetsController extends Controller
{
    /**
     * Display the user's budgets page.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Budgets', [
            'budgets' => config('budgets')
        ]);
    }
}
