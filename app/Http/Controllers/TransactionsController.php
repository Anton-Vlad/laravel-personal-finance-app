<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class TransactionsController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function index(Request $request): Response
    {
        $data = Transaction::query();

        $data = $data->where('user_id', Auth::id());
        $data = $data->where('amount', '>', 0);
        $data = $data->orderBy('date', 'asc');

        $data = $data->paginate(100);

        return Inertia::render('Transactions', [
            'data' => $data,
            'filters' => [],
        ]);
    }
}
