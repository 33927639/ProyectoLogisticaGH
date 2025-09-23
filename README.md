Toma en cuenta que eres un desarrollador con mucha experiencia trabajando con Laravel y Filament. Necesito implementar un sistema de gestión logística completo con relaciones hasMany() y historial de ventas por cliente. 

# 1) Validé que existía mi modelo TblCustomer (Reliese lo había generado)
app/Models/TblCustomer.php

# 2) Le agregué la relación hasMany hacia TblIncome
public function incomes()
{
    return $this->hasMany(\App\Models\TblIncome::class, 'id_customer', 'id_customer');
}

# 3) Revisé mi modelo TblIncome y añadí la relación inversa belongsTo
public function customer()
{
    return $this->belongsTo(\App\Models\TblCustomer::class, 'id_customer', 'id_customer');
}

# 4) En la base de datos, confirmé que tbl_incomes tenía el campo id_customer
#    (si no existía, se añadió con ALTER TABLE)
ALTER TABLE tbl_incomes 
  ADD COLUMN id_customer INT NOT NULL AFTER id_income,
  ADD CONSTRAINT FK_incomes_customer FOREIGN KEY (id_customer) 
  REFERENCES tbl_customers(id_customer);

# 5) En mi TblCustomerResource de Filament, añadí la relación en getRelations()
return [
    RelationManagers\SalesRelationManager::class,
];

# 6) Creé el RelationManager SalesRelationManager.php
app/Filament/Resources/TblCustomerResource/RelationManagers/SalesRelationManager.php

Contenido de SalesRelationManager.php

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
            ->headerActions([]) // Solo lectura
            ->actions([])
            ->bulkActions([]);
    }
}


########################################################################################################################################

### 📋 1. ANÁLISIS INICIAL DEL SISTEMA
```powershell
# Explorar la estructura existente del proyecto
PS C:\wamp64\www\Proyecto\ProyectoLogisticaGH> Get-ChildItem app\Models\
PS C:\wamp64\www\Proyecto\ProyectoLogisticaGH> Get-ChildItem app\Filament\Resources\

# Verificar migraciones existentes
PS C:\wamp64\www\Proyecto\ProyectoLogisticaGH> php artisan migrate:status
```

### 🔗 2. IMPLEMENTACIÓN DE RELACIONES ELOQUENT hasMany()

**Modelos actualizados con relaciones:**
- `TblCustomer.php` - Agregadas relaciones con income() y deliveries()
- `TblIncome.php` - Relación con tbl_customer() y tbl_delivery()
- `TblDelivery.php` - Relación con tbl_customer() y tbl_route()
- `TblMunicipality.php` - Relaciones con customers() y routes()

### 🗃️ 3. CORRECCIÓN DE ESTRUCTURA DE BASE DE DATOS

```powershell
# Migración para cambiar id_user a id_customer en tbl_incomes
PS C:\wamp64\www\Proyecto\ProyectoLogisticaGH> php artisan make:migration change_id_user_to_id_customer_in_tbl_incomes_table

# Migración para agregar id_customer a tbl_deliveries
PS C:\wamp64\www\Proyecto\ProyectoLogisticaGH> php artisan make:migration add_id_customer_to_tbl_deliveries_table

# Migración para eliminar campos redundantes de delivery_status
PS C:\wamp64\www\Proyecto\ProyectoLogisticaGH> php artisan make:migration remove_delivery_status_fields_from_tbl_deliveries_table

# Migración final para corregir estructura completa
PS C:\wamp64\www\Proyecto\ProyectoLogisticaGH> php artisan make:migration fix_tbl_incomes_final

# Ejecutar todas las migraciones
PS C:\wamp64\www\Proyecto\ProyectoLogisticaGH> php artisan migrate
```


### 🔄 4. RELACIÓN MANAGERS PARA HISTORIAL DE VENTAS

**SalesRelationManager.php:**
```powershell
# Creado para mostrar ingresos desde vista de cliente
PS C:\wamp64\www\Proyecto\ProyectoLogisticaGH> php artisan make:filament-relation-manager TblCustomerResource sales id_customer
```

**DeliveriesRelationManager.php:**
```powershell
# Creado para mostrar entregas desde vista de cliente
PS C:\wamp64\www\Proyecto\ProyectoLogisticaGH> php artisan make:filament-relation-manager TblCustomerResource deliveries id_customer
```

### 🏢 5. RECURSOS COMPLETOS DE UBICACIÓN

**TblDepartmentResource.php - Mejorado:**
- Tabla con información completa
- RelationManager para municipios
- Formulario simplificado

**TblMunicipalityResource.php - Creado desde cero:**
```powershell
PS C:\wamp64\www\Proyecto\ProyectoLogisticaGH> php artisan make:filament-resource TblMunicipality
```
- RelationManagers para customers y routes
- Selección dependiente de departamento
- Vista completa con navegación

### ⚠️ 6. RESOLUCIÓN DE ERRORES CRÍTICOS

**Error SQLSTATE[HY000]: Field 'id_user' doesn't have a default value:**
```powershell
# Identificación del problema
PS C:\wamp64\www\Proyecto\ProyectoLogisticaGH> php artisan tinker --execute="DB::select('SHOW COLUMNS FROM tbl_incomes')"

# Solución con migración SQL directa
PS C:\wamp64\www\Proyecto\ProyectoLogisticaGH> php artisan make:migration fix_tbl_incomes_final

# Eliminación de foreign key problemática FK__tbl_incom__id_us__1DB06A4F
PS C:\wamp64\www\Proyecto\ProyectoLogisticaGH> php artisan migrate
```



