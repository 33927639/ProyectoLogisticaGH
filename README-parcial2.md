PROMPT
Toma en cuenta que eres un desarrollador como mucho tiempo de experiencia trabajando con laravel, php storm, necesito agregar un timestamps personalizados a los registros de productos (created at, updated at)


QUE OCUPE?
---Ejecutar Tinker y verificar la tabla
PS C:\Users\JAVIER LU\PhpstormProjects\ProyectoLogisticaGH> php artisan tinker

---COMO NO EXISTIA SE CREO
PS C:\Users\JAVIER LU\PhpstormProjects\ProyectoLogisticaGH> php artisan make:migration create_products_table

   INFO  Migration [C:\Users\JAVIER LU\PhpstormProjects\ProyectoLogisticaGH\database\migrations\2025_09_21_173146_create_products_table.php] created successfully.



-- LUEGO REALICE LA MIGRACION

PS C:\Users\JAVIER LU\PhpstormProjects\ProyectoLogisticaGH> notepad "database\migrations\2025_09_21_173146_create_products_table.php"
PS C:\Users\JAVIER LU\PhpstormProjects\ProyectoLogisticaGH> php artisan migrate


--- SE CREO EL MODELO

PS C:\Users\JAVIER LU\PhpstormProjects\ProyectoLogisticaGH> php artisan make:model Product

   INFO  Model [C:\Users\JAVIER LU\PhpstormProjects\ProyectoLogisticaGH\app\Models\Product.php] created successfully.

---- VALIDE LA CREACION DEL MISMO
PS C:\Users\JAVIER LU\PhpstormProjects\ProyectoLogisticaGH> Get-ChildItem app\Models\Product.php


--- REALICE LA MODIFCIACION DEL NOTEPAD
PS C:\Users\JAVIER LU\PhpstormProjects\ProyectoLogisticaGH> notepad app\Models\Product.php

-- PROBE LA CREACION DEL TIMESTAMPS
PS C:\Users\JAVIER LU\PhpstormProjects\ProyectoLogisticaGH> # Probar la creación con timestamps personalizados
PS C:\Users\JAVIER LU\PhpstormProjects\ProyectoLogisticaGH> php artisan tinker --execute="



---- PRUEBA REALIZADA

// Creación de producto
$product = Product::create([
    'name' => 'Producto Test',
    'price' => 99.99,
    'sku' => 'TEST001',
    'stock' => 10
]);

// Verificación de timestamps
echo $product->fecha_creacion; // ✅ Se auto-completa
echo $product->fecha_actualizacion; // ✅ Se auto-completa

// Actualización
$product->update(['price' => 129.99]);
echo $product->fecha_actualizacion; // ✅ Se auto-actualiza
