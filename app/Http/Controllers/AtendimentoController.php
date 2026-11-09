<?php

namespace App\Http\Controllers;

use App\Http\Requests\Atendimento\StoreAtendimentoRequest;
use App\Http\Requests\Atendimento\UpdateAtendimentoRequest;
use App\Http\Resources\AtendimentoResource;
use App\Models\Atendimento;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class AtendimentoController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $atendimentos = Atendimento::query()
            ->with('cliente')
            ->filter($request->only(['cliente_id', 'status', 'data_inicio', 'data_fim']))
            ->orderByDesc('data_hora')
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return AtendimentoResource::collection($atendimentos);
    }

    public function indexByCliente(Request $request, Cliente $cliente): AnonymousResourceCollection
    {
        $atendimentos = $cliente->atendimentos()
            ->with('cliente')
            ->filter($request->only(['status', 'data_inicio', 'data_fim']))
            ->orderByDesc('data_hora')
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return AtendimentoResource::collection($atendimentos);
    }

    public function store(StoreAtendimentoRequest $request): JsonResponse
    {
        $atendimento = Atendimento::query()->create($request->validated());
        $atendimento->load('cliente');

        return (new AtendimentoResource($atendimento))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Atendimento $atendimento): AtendimentoResource
    {
        $atendimento->load('cliente');

        return new AtendimentoResource($atendimento);
    }

    public function update(UpdateAtendimentoRequest $request, Atendimento $atendimento): AtendimentoResource
    {
        $atendimento->update($request->validated());
        $atendimento->refresh()->load('cliente');

        return new AtendimentoResource($atendimento);
    }

    public function destroy(Atendimento $atendimento): Response
    {
        $atendimento->delete();

        return response()->noContent();
    }
}
