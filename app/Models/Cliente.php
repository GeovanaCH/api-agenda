<?php

namespace App\Models;

use Database\Factories\ClienteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['nome', 'email', 'cpf', 'telefone'])]
class Cliente extends Model
{
    /** @use HasFactory<ClienteFactory> */
    use HasFactory, SoftDeletes;

    public const DELETED_AT = 'excluido_em';

    /**
     * Search by nome, email, or cpf (partial match).
     *
     * @param  Builder<Cliente>  $query
     * @return Builder<Cliente>
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $like = '%'.$term.'%';

        $cpfTerm = preg_replace('/\D/', '', $term) ?: $term;

        return $query->where(function (Builder $builder) use ($like, $cpfTerm) {
            $builder->where('nome', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('cpf', 'like', '%'.$cpfTerm.'%');
        });
    }
}
