<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'quantity'        => 'integer',
            'quantity_before' => 'integer',
            'quantity_after'  => 'integer',
        ];
    }

    // -------------------------------------------------------
    // SCOPES
    // -------------------------------------------------------

    public function scopeIn($query)
    {
        return $query->where('type', 'in');
    }
    public function scopeOut($query)
    {
        return $query->where('type', 'out');
    }
    public function scopeTransferIn($query)
    {
        return $query->where('type', 'transfer_in');
    }
    public function scopeTransferOut($query)
    {
        return $query->where('type', 'transfer_out');
    }
    public function scopeAdjustment($query)
    {
        return $query->where('type', 'adjustment');
    }

    public function scopeByReference($query, string $type, int $id)
    {
        return $query->where('reference_type', $type)
            ->where('reference_id', $id);
    }

    // -------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------

    /** Produk yang bergerak */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /** Gudang tempat pergerakan terjadi */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /** User yang mencatat pergerakan ini */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Sumber pergerakan stok secara dinamis berdasarkan reference_type.
     *
     * reference_type  →  Model
     * ─────────────────────────────────────────────────
     * 'request'        →  \App\Models\Request
     * 'purchase_order' →  PurchaseOrder
     * 'stock_transfer' →  StockTransfer
     * 'stock_opname'   →  StockOpname
     * 'manual'         →  null (koreksi manual admin)
     *
     * Contoh pemakaian:
     *   $movement->reference   // instance model yang sesuai
     */
    public function reference()
    {
        $map = [
            'request'        => \App\Models\Request::class,
            'purchase_order' => PurchaseOrder::class,
            'stock_transfer' => StockTransfer::class,
            'stock_opname'   => StockOpname::class,
        ];

        $class = $map[$this->reference_type] ?? null;

        if (! $class) {
            return null;
        }

        return $this->belongsTo($class, 'reference_id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
