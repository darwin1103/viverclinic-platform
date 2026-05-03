<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SetupSalesRole extends Command
{
    protected $signature = 'setup:sales-role';
    protected $description = 'Creates the SALES role and assigns permissions';

    public function handle()
    {
        $role = Role::firstOrCreate(['name' => 'SALES']);
        
        // Ensure permissions exist
        $permissions = ['admin_dashboard', 'ver_pacientes', 'crear_citas'];
        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }
        
        $role->syncPermissions($permissions);
        $this->info('SALES role created and permissions assigned successfully.');
    }
}
