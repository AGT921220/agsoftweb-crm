<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Currency;
use App\Enums\QuotationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use HasCommercialRecords;
    use SoftDeletes;

    public const SORTABLE = [
        'folio',
        'client_name',
        'issued_on',
        'expires_on',
        'total',
        'status',
    ];

    protected $fillable = [
        'folio',
        'sequence_number',
        'version_number',
        'parent_id',
        'root_id',
        'client_id',
        'client_name',
        'legal_name',
        'contact_name',
        'email',
        'phone',
        'issued_on',
        'expires_on',
        'user_id',
        'currency',
        'commercial_terms',
        'payment_terms',
        'delivery_time',
        'internal_notes',
        'client_notes',
        'status',
        'subtotal',
        'discount_total',
        'tax_total',
        'total',
        'next_follow_up_at',
    ];

    protected function casts(): array
    {
        return [
            'issued_on' => 'date',
            'expires_on' => 'date',
            'currency' => Currency::class,
            'status' => QuotationStatus::class,
            'subtotal' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'total' => 'decimal:2',
            'next_follow_up_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function root(): BelongsTo
    {
        return $this->belongsTo(self::class, 'root_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class)->orderBy('position');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(QuotationVersion::class)->orderByDesc('version_number');
    }

    public function purchaseOrder(): HasOne
    {
        return $this->hasOne(PurchaseOrder::class);
    }

    public function chain(): Builder
    {
        $rootId = $this->root_id ?? $this->id;

        return static::query()->where('id', $rootId)->orWhere('root_id', $rootId);
    }

    public function scopeFiltered(Builder $query, array $filters): void
    {
        $query->when($filters['q'] ?? null, function (Builder $query, string $term) {
            $like = '%'.$term.'%';
            $query->where(function (Builder $query) use ($like) {
                $query->where('folio', 'like', $like)
                    ->orWhere('client_name', 'like', $like)
                    ->orWhere('legal_name', 'like', $like)
                    ->orWhere('email', 'like', $like);
            });
        });

        $query->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status));
        $query->when($filters['currency'] ?? null, fn (Builder $query, string $currency) => $query->where('currency', $currency));
        $query->when($filters['client_id'] ?? null, fn (Builder $query, string $clientId) => $query->where('client_id', $clientId));
        $query->when($filters['user_id'] ?? null, fn (Builder $query, string $userId) => $query->where('user_id', $userId));
        $query->when($filters['date_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('issued_on', '>=', $date));
        $query->when($filters['date_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('issued_on', '<=', $date));

        if (($filters['follow_up'] ?? null) === 'overdue') {
            $query->whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<', now());
        }

        if (($filters['follow_up'] ?? null) === 'pending') {
            $query->whereNotNull('next_follow_up_at')->where('next_follow_up_at', '>=', now());
        }

        $sort = in_array($filters['sort'] ?? '', self::SORTABLE, true) ? $filters['sort'] : 'issued_on';
        $direction = ($filters['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sort, $direction);
    }
}
