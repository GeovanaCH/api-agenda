<?php

namespace App\Models;

use App\Enums\AtendimentoStatus;
use Database\Factories\AtendimentoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['cliente_id', 'data_hora', 'status', 'assunto', 'observacoes'])]
class Atendimento extends Model
{
    /** @use HasFactory<AtendimentoFactory> */
    use HasFactory, SoftDeletes;

    public const DELETED_AT = 'excluido_em';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_hora' => 'datetime',
            'status' => AtendimentoStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Cliente, $this>
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * @param  Builder<Atendimento>  $query
     * @return Builder<Atendimento>
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (! empty($filters['cliente_id'])) {
            $query->where('cliente_id', $filters['cliente_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['data_inicio'])) {
            $query->where('data_hora', '>=', $filters['data_inicio']);
        }

        if (! empty($filters['data_fim'])) {
            $query->where('data_hora', '<=', $filters['data_fim']);
        }

        return $query;
    }
}
