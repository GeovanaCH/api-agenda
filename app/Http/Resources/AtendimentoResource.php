<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Atendimento
 */
class AtendimentoResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cliente_id' => $this->cliente_id,
            'data_hora' => $this->data_hora?->toISOString(),
            'status' => $this->status?->value ?? $this->status,
            'assunto' => $this->assunto,
            'observacoes' => $this->observacoes,
            'cliente' => ClienteResource::make($this->whenLoaded('cliente')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
