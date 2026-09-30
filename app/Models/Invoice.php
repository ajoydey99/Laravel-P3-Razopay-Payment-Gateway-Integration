<?php
namespace App\Models;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasUlids;

    protected $fillable = [
        'payment_id', 'invoice_path', 'mailed_at',
    ];

    protected $casts = ['mailed_at' => 'datetime'];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function markAsMailed()
    {
        $this->update(['mailed_at' => now()]);
    }
}
