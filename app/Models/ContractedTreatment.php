<?php
namespace App\Models;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ContractedTreatment extends Model
{
    use HasFactory, \App\Traits\BelongsToBranch;

    protected $fillable = [
        'user_id',
        'branch_id',
        'treatment_id',
        'contracted_packages',
        'contracted_additionals',
        'selected_zones',
        'total_price',
        'payment_type',
        'status',
        'sessions',
        'days_between_sessions',
        'terms_acepted',
        'is_pregnant',
        'legacy_paid_amount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // Automatically encode/decode these JSON columns to/from arrays
        'contracted_packages' => 'array',
        'contracted_additionals' => 'array',
        'selected_zones' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    // Relación con las cuotas contratadas
    public function installments(): HasMany
    {
        return $this->hasMany(ContractedTreatmentInstallment::class);
    }

    // Relación con las órdenes de pago
    public function orders(): HasMany
    {
        return $this->hasMany(TreatmentOrder::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ContractedTreatmentNote::class)->orderBy('created_at', 'desc');
    }

    public function packageUpgrade(): HasOne
    {
        return $this->hasOne(PackageUpgrade::class);
    }

    public function upgradeSale(): HasOne
    {
        return $this->hasOne(Sale::class)->where('type', 'upgrade');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function repurchaseSale(): HasOne
    {
        return $this->hasOne(Sale::class)->where('type', 'repurchase');
    }

    /**
     * Obtener la venta por referido.
     */
    public function referralSale(): HasOne
    {
        return $this->hasOne(Sale::class)->where('type', 'referral');
    }

    // Helper para saber si tiene cuotas
    public function hasInstallments(): bool
    {
        return $this->installments()->exists();
    }

    public function canBeUpgraded(): bool
    {
        if ($this->packageUpgrade()->exists()) {
            return false;
        }

        // Also check for upgrade sales (upgrades done before PackageUpgrade table existed)
        if ($this->upgradeSale()->exists()) {
            return false;
        }

        $firstAppointment = $this->appointments()->where('session_number', 1)->first();
        if (!$firstAppointment || !in_array($firstAppointment->status, ['Atendida', 'Completada']) || is_null($firstAppointment->staff_user_id)) {
            return false;
        }

        // Cannot upgrade if session 2 or higher is already attended/completed
        $hasSubsequentSessions = $this->appointments()
            ->where('session_number', '>=', 2)
            ->whereIn('status', ['Atendida', 'Completada'])
            ->exists();

        if ($hasSubsequentSessions) {
            return false;
        }

        return true;
    }

    // Helper para calcular el total pagado en órdenes aprobadas
    public function totalPaid(): float
    {
        $ordersTotal = (float) $this->orders()
            ->whereIn('status', ['Pagado', 'Paid', 'Pago completado', 'Aprobado'])
            ->sum('total');
            
        return $ordersTotal + (float) ($this->legacy_paid_amount ?? 0);
    }

    // Helper para obtener el saldo restante
    public function remainingBalance(): float
    {
        return max(0.0, (float) $this->total_price - $this->totalPaid());
    }

    // Helper para determinar si el tratamiento está totalmente pagado
    public function isFullyPaid(): bool
    {
        if ($this->payment_type === 'abono') {
            return $this->remainingBalance() <= 0;
        }
        if ($this->hasInstallments()) {
            return $this->installments()->where('status', 'PENDING')->count() === 0;
        }
        // For standard full payments: check if the total has been covered by approved orders
        return $this->remainingBalance() <= 0;
    }

    // Helper to determine if scheduling is allowed based on payment status
    public function isPaymentUpToDate(): bool
    {
        if ($this->isFullyPaid()) {
            return true;
        }

        if ($this->payment_type === 'abono') {
            return false;
        }

        if ($this->hasInstallments()) {
            $lastAttended = $this->appointments()->where('attended', true)->max('session_number') ?? 0;
            $nextSessionNumber = $lastAttended + 1;

            $installments = $this->installments()->orderBy('installment_number')->get();
            $totalInstallments = $installments->count();

            $targetInstallmentNumber = ($nextSessionNumber > $totalInstallments) ? $totalInstallments : $nextSessionNumber;

            $pendingInstallment = $installments->where('status', 'PENDING')
                                               ->where('installment_number', '<=', $targetInstallmentNumber)
                                               ->where('price', '>', 0)
                                               ->first();

            return !$pendingInstallment;
        }

        return false;
    }
}
