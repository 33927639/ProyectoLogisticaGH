PROMPT
Toma en cuenta que eres un desarrollador con mucha experiencia en Laravel y PhpStorm. Necesito **agregar timestamps personalizados** a los registros de productos (exponer `created_at`, `updated_at` como **`fecha_creacion`** y **`fecha_actualizacion`**) y verificarlo en Tinker.

QUE OCUPÉ?
--- Ejecutar Tinker y verificar si existe la tabla / modelo
PS C:\Users\Andre\PhpstormProjects\ProyectoLogisticaGH> php artisan tinker
Psy Shell vX.Y by Justin Hileman
>>> \Schema::hasTable('products');
=> true  // (si da false, crear la migración)

--- COMO NO EXISTÍA (o faltaban columnas), SE CREÓ/EDITÓ LA MIGRACIÓN
PS C:\Users\Andre\PhpstormProjects\ProyectoLogisticaGH> php artisan make:migration create_products_table

  INFO  Migration [database\migrations\YYYY_MM_DD_HHMMSS_create_products_table.php] created successfully.

PS C:\Users\Andre\PhpstormProjects\ProyectoLogisticaGH> notepad database\migrations\YYYY_MM_DD_HHMMSS_create_products_table.php

// Contenido mínimo recomendado de la migración:
public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('sku')->unique();
        $table->decimal('price', 8, 2);
        $table->integer('stock')->default(0);
        $table->text('description')->nullable();
        $table->date('expires_at')->nullable();
        $table->timestamps(); // <-- created_at / updated_at
    });
}

PS C:\Users\Andre\PhpstormProjects\ProyectoLogisticaGH> php artisan migrate

--- SE CREÓ EL MODELO
PS C:\Users\Andre\PhpstormProjects\ProyectoLogisticaGH> php artisan make:model Product

  INFO  Model [app\Models\Product.php] created successfully.

---- VALIDÉ LA CREACIÓN
PS C:\Users\Andre\PhpstormProjects\ProyectoLogisticaGH> Get-ChildItem app\Models\Product.php

--- MODIFIQUÉ EL MODELO PARA HABILITAR CAMPOS Y ALIAS DE TIMESTAMPS
PS C:\Users\Andre\PhpstormProjects\ProyectoLogisticaGH> notepad app\Models\Product.php

// Contenido recomendado del modelo:
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // Campos asignables
    protected $fillable = [
        'name',
        'sku',
        'price',
        'stock',
        'description',
        'expires_at',
    ];

    // Casts
    protected $casts = [
        'price'      => 'decimal:2',
        'stock'      => 'integer',
        'expires_at' => 'date',
    ];

    // Aliases de timestamps (para acceder como fecha_creacion / fecha_actualizacion)
    protected $appends = ['fecha_creacion', 'fecha_actualizacion'];

    public function getFechaCreacionAttribute()
    {
        return optional($this->created_at)->format('Y-m-d H:i:s');
    }

    public function getFechaActualizacionAttribute()
    {
        return optional($this->updated_at)->format('Y-m-d H:i:s');
    }
}

-- PROBÉ LA CREACIÓN CON TIMESTAMPS PERSONALIZADOS EN TINKER
PS C:\Users\Andre\PhpstormProjects\ProyectoLogisticaGH> php artisan tinker --execute="
$product = \App\Models\Product::create([
    'name' => 'Producto Test',
    'price' => 99.99,
    'sku' => 'TEST001',
    'stock' => 10,
    'description' => 'Producto de prueba',
    'expires_at' => now()->addMonth()->toDateString(),
]);

// Verificación de alias de timestamps
echo \$product->fecha_creacion . PHP_EOL;       // ✅ se auto-completa (alias de created_at)
echo \$product->fecha_actualizacion . PHP_EOL;  // ✅ se auto-completa (alias de updated_at)

// Actualización y verificación
\$product->update(['price' => 129.99]);
\$product->refresh();
echo \$product->fecha_actualizacion . PHP_EOL;  // ✅ se auto-actualiza tras update
"

---- RESULTADO ESPERADO
// Salida en Tinker (ejemplo)
2025-09-23 03:15:12
2025-09-23 03:15:12
2025-09-23 03:16:05

NOTAS
- `created_at` y `updated_at` siguen existiendo en la BD (Laravel los maneja por defecto).
- Los alias `fecha_creacion` / `fecha_actualizacion` se exponen como **atributos calculados** (accessors) para uso en vistas/exports/API.
- Si no deseas que siempre aparezcan en arrays/JSON, quita la línea `protected $appends = [...]` y accede a los getters manualmente (`$product->fecha_creacion`).

EXTRA (Export CSV y vista imprimible que añadimos en el proyecto)
- Ruta CSV:  GET /productos/export  → genera CSV con línea `sep=,` y BOM UTF-8 para Excel.
- Ruta Print: GET /productos/print   → muestra HTML listo para “Imprimir/Guardar como PDF”.
- Los botones están en Filament → Listado de Productos (ListProducts.php): “Exportar CSV” y “Imprimir listado”.
