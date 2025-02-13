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

class TransactionsController extends Controller
{
    /**
     * Display the user's transactions table.
     */
    public function index(Request $request): Response
    {
        $data = Transaction::query();

        $data = $data->where('user_id', Auth::id());

        $data->when($request->has('search'), function ($query) use ($request) {
            $query->where('name', 'ILIKE', "%{$request->input('search')}%")
            ->orWhere('details', 'ILIKE', "%{$request->input('search')}%");
        });


        if ($request->has('search')) {
            $data = match ($request->input('orderBy')) {
                'oldest' => $data->orderBy('date', 'asc'),
                'a2z' => $data->orderBy('name', 'asc'),
                'z2a' => $data->orderBy('name', 'desc'),
                'highest' => $data->orderBy('amount', 'desc'),
                'lowest' => $data->orderBy('amount', 'asc'),
                default => $data->orderBy('date', 'desc'),
            };
        }


        $data = $data->paginate(10)->withQueryString();

        return Inertia::render('Transactions', [
            'data' => TransactionResource::collection($data),
            'filters' => $request->only(['search', 'orderBy']), // from, to
        ]);
    }
}
