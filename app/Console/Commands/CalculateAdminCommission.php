<?php

namespace App\Console\Commands;

use App\Models\AccountingRecord;
use App\Models\Branch;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CalculateAdminCommission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commission:calculate-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and assign monthly administrative commission based on total sales per branch.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting calculation of administrative commissions...');

        $previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $branches = Branch::with(['admins.staffProfile'])->get();

        foreach ($branches as $branch) {
            $totalSales = AccountingRecord::where('branch_id', $branch->id)
                ->where('type', 'income')
                ->whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])
                ->sum('amount');

            if ($totalSales <= 0) {
                continue;
            }

            $commission = $totalSales / 30;

            foreach ($branch->admins as $admin) {
                if ($admin->staffProfile) {
                    $admin->staffProfile->increment('commission_balance', $commission);

                    // Audit: register as an expense assigned to the branch
                    AccountingRecord::create([
                        'branch_id' => $branch->id,
                        'user_id' => $admin->id,
                        'type' => 'expense',
                        'amount' => $commission,
                        'description' => 'Monthly Administrative Commission',
                    ]);

                    $this->info("Assigned commission of {$commission} to admin {$admin->name} for branch {$branch->name}.");
                }
            }
        }

        $this->info('Commission calculation completed successfully.');
    }
}
