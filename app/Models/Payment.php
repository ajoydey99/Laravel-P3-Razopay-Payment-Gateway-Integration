<?php
namespace App\Models;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'amount', 'order_id', 'razorpay_payment_id', 'razorpay_signature', 'failure_reason', 'status', 'webhook_processed_at',
    ];

    protected $casts = [
        'amount'               => 'integer',
        'webhook_processed_at' => 'datetime',
    ];

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function markAsCompleted(array $data)
    {
        $this->update([
            'razorpay_payment_id' => $data['razorpay_payment_id'],
            'razorpay_signature'  => $data['razorpay_signature'],
            'status'              => 'completed',
            'failure_reason'      => null,
        ]);
    }

    public function markAsFailed(?string $reason = null)
    {
        $this->update([
            'status'         => 'failed',
            'failure_reason' => $reason ?? 'Unknown Error',
        ]);
    }
}
