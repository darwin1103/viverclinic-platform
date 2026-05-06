<?php

namespace App\Console\Commands;

use App\Models\AccountingRecord;
use App\Models\Order;
use App\Models\TreatmentOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReconcileAccounting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reconcile-accounting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register past approved payments into the accounting records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting accounting reconciliation...');

        DB::transaction(function () {
            // 1. Reconcile Treatment Orders
            $treatmentOrders = TreatmentOrder::where(function($query) {
                    $query->where('status', 'Pagado')
                          ->orWhere('payment_status', 'APPROVED');
                })
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                          ->from('accounting_records')
                          ->whereColumn('accounting_records.reference_id', 'treatment_orders.id')
                          ->where('accounting_records.reference_type', TreatmentOrder::class);
                })
                ->get();

            $this->info("Found {$treatmentOrders->count()} treatment orders to reconcile.");

            foreach ($treatmentOrders as $order) {
                AccountingRecord::create([
                    'branch_id'      => $order->branch_id ?? 1,
                    'user_id'        => $order->user_id,
                    'type'           => 'income',
                    'amount'         => $order->total,
                    'description'    => 'Reconciliación: Pago de tratamiento - Paciente: ' . ($order->user->name ?? 'N/A'),
                    'category'       => 'Tratamientos',
                    'reference_id'   => $order->id,
                    'reference_type' => TreatmentOrder::class,
                    'created_at'     => $order->created_at, // Preserve original date
                ]);
            }

            // 2. Reconcile Product Orders
            $productOrders = Order::where('status', 'Pago completado')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                          ->from('accounting_records')
                          ->whereColumn('accounting_records.reference_id', 'orders.id')
                          ->where('accounting_records.reference_type', Order::class);
                })
                ->get();

            $this->info("Found {$productOrders->count()} product orders to reconcile.");

            foreach ($productOrders as $order) {
                AccountingRecord::create([
                    'branch_id'      => $order->branch_id,
                    'user_id'        => $order->user_id,
                    'type'           => 'income',
                    'amount'         => $order->total,
                    'description'    => 'Reconciliación: Pago de productos - Paciente: ' . ($order->user->name ?? 'N/A'),
                    'category'       => 'Productos',
                    'reference_id'   => $order->id,
                    'reference_type' => Order::class,
                    'created_at'     => $order->created_at, // Preserve original date
                ]);
            }
        });

        $this->info('Reconciliation completed successfully.');
    }
}
