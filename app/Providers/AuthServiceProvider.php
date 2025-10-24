<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

// Models
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Customer;
use App\Models\Route;
use App\Models\Product;
use App\Models\Order;
use App\Models\Delivery;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Maintenance;
use App\Models\Notification;

// Policies
use App\Policies\VehiclePolicy;
use App\Policies\DriverPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\RoutePolicy;
use App\Policies\ProductPolicy;
use App\Policies\OrderPolicy;
use App\Policies\DeliveryPolicy;
use App\Policies\ExpensePolicy;
use App\Policies\IncomePolicy;
use App\Policies\MaintenancePolicy;
use App\Policies\NotificationPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Fleet Management (Crítico)
        Vehicle::class => VehiclePolicy::class,
        Driver::class => DriverPolicy::class,
        
        // Operations (Operacional)
        Customer::class => CustomerPolicy::class,
        Route::class => RoutePolicy::class,
        Product::class => ProductPolicy::class,
        Order::class => OrderPolicy::class,
        Delivery::class => DeliveryPolicy::class,
        
        // Financial (Crítico)
        Expense::class => ExpensePolicy::class,
        Income::class => IncomePolicy::class,
        
        // Maintenance (Importante)
        Maintenance::class => MaintenancePolicy::class,
        
        // System (Sistema)
        Notification::class => NotificationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Additional Gates can be defined here
        // Example: Gate for accessing admin panel
        Gate::define('access-admin-panel', function ($user) {
            return $user->roles()->whereIn('name_role', [
                'Super Administrador', 
                'Administrador', 
                'Supervisor', 
                'Operador'
            ])->exists();
        });

        // Gate for financial modules
        Gate::define('access-financial-reports', function ($user) {
            return $user->roles()->whereIn('name_role', [
                'Super Administrador', 
                'Administrador', 
                'Supervisor'
            ])->exists();
        });

        // Gate for critical operations
        Gate::define('manage-fleet', function ($user) {
            return $user->roles()->whereIn('name_role', [
                'Super Administrador', 
                'Administrador', 
                'Supervisor'
            ])->exists();
        });
    }
}