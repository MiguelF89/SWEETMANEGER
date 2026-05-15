<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEncomendaRequest;
use App\Http\Requests\UpdateEncomendaRequest;
use App\Models\Encomenda;
use Illuminate\Http\Request;

class EncomendaController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Encomenda::class, 'encomenda');
    }

    public function index(Request $request)
    {
        $query = $request->user()->encomendas();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($sub) use ($search) {
                $sub->where('cliente', 'like', "%{$search}%")
                    ->orWhere('descricao', 'like', "%{$search}%")
                    ->orWhere('observacoes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'pago') {
                $query->where('pago', true);
            } elseif ($request->status === 'pendente') {
                $query->where('pago', false);
            }
        }

        if ($request->filled('repassado')) {
            if ($request->repassado === 'sim') {
                $query->where('repassado_cliente', true);
            } elseif ($request->repassado === 'nao') {
                $query->where('repassado_cliente', false);
            }
        }

        $sortDirection = $request->get('sort') === 'desc' ? 'desc' : 'asc';
        $encomendas = $query->orderBy('data_entrega', $sortDirection)
            ->paginate(10)
            ->withQueryString();

        return view('encomendas.index', [
            'encomendas' => $encomendas,
            'search' => $request->search,
            'status' => $request->status,
            'repassado' => $request->repassado,
            'sort' => $sortDirection,
        ]);
    }

    public function create()
    {
        return view('encomendas.create');
    }

    public function store(StoreEncomendaRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        Encomenda::create($validated);

        return redirect()->route('encomendas.index')
            ->with('success', 'Encomenda criada com sucesso.');
    }

    public function show(Encomenda $encomenda)
    {
        return view('encomendas.show', [
            'encomenda' => $encomenda,
        ]);
    }

    public function edit(Encomenda $encomenda)
    {
        return view('encomendas.edit', [
            'encomenda' => $encomenda,
        ]);
    }

    public function update(UpdateEncomendaRequest $request, Encomenda $encomenda)
    {
        $encomenda->update($request->validated());

        return redirect()->route('encomendas.index')
            ->with('success', 'Encomenda atualizada com sucesso.');
    }

    public function destroy(Encomenda $encomenda)
    {
        $encomenda->delete();

        return redirect()->route('encomendas.index')
            ->with('success', 'Encomenda removida com sucesso.');
    }
}
