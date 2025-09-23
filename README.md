# Toma en cuenta que eres un desarrollador con mucha experiencia trabajando con Laravel y Filament. Necesito implementar un sistema de gestión logística completo con relaciones hasMany() y historial de ventas por cliente.

---

## 1 Análisis inicial del sistema
```powershell
# Explorar estructura existente
PS C:\wamp64\www\Proyecto\ProyectoLogisticaGH> Get-ChildItem app\Models\
PS C:\wamp64\www\Proyecto\ProyectoLogisticaGH> Get-ChildItem app\Filament\Resources\

# Verificar migraciones existentes
PS C:\wamp64\www\Proyecto\ProyectoLogisticaGH> php artisan migrate:status

## 2 Modelo y relaciones Eloquent

// app/Models/TblCustomer.php
public function incomes()
{
    return $this->hasMany(\App\Models\TblIncome::class, 'id_customer', 'id_customer');
}

// app/Models/TblIncome.php
public function customer()
{
    return $this->belongsTo(\App\Models\TblCustomer::class, 'id_customer', 'id_customer');
}


-- Ajuste de base de datos si faltaba la FK en tbl_incomes
ALTER TABLE tbl_incomes 
  ADD COLUMN id_customer INT NOT NULL AFTER id_income,
  ADD CONSTRAINT FK_incomes_customer
    FOREIGN KEY (id_customer) REFERENCES tbl_customers(id_customer);

## 3 Correcion de estructura de base de datos

# Cambiar id_user a id_customer en tbl_incomes
php artisan make:migration change_id_user_to_id_customer_in_tbl_incomes_table

# Agregar id_customer a tbl_deliveries
php artisan make:migration add_id_customer_to_tbl_deliveries_table

# Eliminar campos redundantes de delivery_status
php artisan make:migration remove_delivery_status_fields_from_tbl_deliveries_table

# Migración final de corrección
php artisan make:migration fix_tbl_incomes_final

# Ejecutar todas las migraciones
php artisan migrate

## 4 Recurso filament 

// TblCustomerResource.php
public static function getRelations(): array
{
    return [
        RelationManagers\SalesRelationManager::class,
        RelationManagers\DeliveriesRelationManager::class,
    ];
}


## 5 RelationManagers

// app/Filament/Resources/TblCustomerResource/RelationManagers/SalesRelationManager.php
<?php

namespace App\Filament\Resources\TblCustomerResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SalesRelationManager extends RelationManager
{
    protected static string $relationship = 'incomes';
    protected static ?string $title = 'Historial de Ventas';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')->label('Descripción')->wrap(),
                Tables\Columns\TextColumn::make('amount')->label('Monto')->money('GTQ', true),
                Tables\Columns\TextColumn::make('income_date')->label('Fecha')->date(),
                Tables\Columns\TextColumn::make('created_at')->label('Registrado')->dateTime('d/m/Y H:i'),
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}


# Crear RelationManager para entregas
php artisan make:filament-relation-manager TblCustomerResource deliveries id_customer


##6 Recursos completos de ubicacion

# Crear resource de municipios
php artisan make:filament-resource TblMunicipality


## 7 Resolucion de errores criticos

Error: SQLSTATE[HY000]: Field 'id_user' doesn't have a default value

# Verificar columnas
php artisan tinker --execute="DB::select('SHOW COLUMNS FROM tbl_incomes')"

# Crear migración de corrección
php artisan make:migration fix_tbl_incomes_final

# Eliminar foreign key conflictiva
# FK__tbl_incom__id_us__1DB06A4F

# Ejecutar migraciones
php artisan migrate

