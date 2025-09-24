# Proyecto Logística GH - Implementación de Descripción en Productos

Este proyecto se basa en **Laravel** con el paquete **Filament** para la gestión administrativa de productos. La funcionalidad que implementamos en este proyecto es agregar un campo de descripción a los productos y permitir visualizar esa descripción en una ventana de detalles.

## Pasos para Implementar la Descripción en los Productos

### Creación de la Migración para `tbl_products`

Primero, Se creo la migración para la tabla `tbl_products`, incluyendo los campos necesarios: `name`, `stock`, `price`, `status`, y `descripcion`. 

```bash
php artisan make:migration create_tbl_products_table
```

Modificación de la migración:
```php
public function up(): void
{
    Schema::create('tbl_products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->integer('stock')->default(0);
        $table->decimal('price', 10, 2)->nullable();
        $table->text('descripcion')->nullable(); // Campo de descripción agregado
        $table->tinyInteger('status')->default(1);
        $table->timestamps();
    });
}
```

---

### 2. Creación del Modelo TblProduct

Se creo el modelo que corresponde a la tabla tbl_products. Utilizando el comando:

```bash
php artisan make:model TblProduct -m
```

Modificación del Modelo:
```php
class TblProduct extends Model
{
    protected $fillable = [
        'name', 'stock', 'price', 'descripcion', 'status'
    ];
}
```

---

### 3. Creación de Seeder para Productos

El Seeder permite insertar algunos productos de ejemplo. Utilizando el comando:

```bash
php artisan make:seeder ProductosSeeder
```

Contenido de ProductosSeeder.php:
```bash
public function run(): void
{
    TblProduct::create([
        'name' => 'HP Laptop',
        'stock' => 20,
        'price' => 7000,
        'descripcion' => 'Laptop de alta gama con procesador i7.',
        'status' => 1,
    ]);
    // Agregar más productos según sea necesario
}
```

Luego se registra el seeder en DatabaseSeeder.php para que se ejecute junto con los demás seeders.
```php
$this->call(ProductosSeeder::class);
```

---

### 4. Creación del Recurso Filament para Productos

Genere el recurso con Filament para manejar la interfaz de administración de productos. Utilizamos:
```bash
php artisan make:filament-resource TblProduct
```

Modificación de TblProductResource.php:
```php
public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\TextInput::make('name')->label('Nombre')->required(),
        Forms\Components\TextInput::make('stock')->numeric()->label('Stock')->default(0),
        Forms\Components\TextInput::make('price')->numeric()->label('Precio')->prefix('Q'),
        Forms\Components\TextArea::make('descripcion')->label('Descripción'),  // Descripción agregada
    ]);
}
```

---

### 5. Creación de una Vista de Detalle con Modal para la Descripción

Con el comando Filament para crear una página personalizada que muestre la descripción del producto en un modal cuando se haga clic en un botón de Ver Descripción:
```bash
php artisan make:filament-page ProductDescription --resource=TblProductResource --type=view
```

Modificación de ProductDescription.php:
```php
public function getTitle(): string
{
    return 'Descripción del Producto';
}
```

---

### Pruebas
Se ejecutan los seeders y se verifica en el panel de Filament que la descripción del producto aparezca correctamente tanto en la lista de productos como en la vista detallada con el modal.
```bash
php artisan db:seed
```

---

### Procedmientos almacenados
Creee los procedimientos almacenados desde DataGrip, hacerlo para que funcione mis cambios

Procedimiento almacenado insertart
```sql
DELIMITER $$

CREATE PROCEDURE sp_products_insert(
    IN p_name VARCHAR(150),
    IN p_stock INT,
    IN p_price DECIMAL(10,2),
    IN p_status TINYINT,
    IN p_descripcion TEXT
)
BEGIN
    INSERT INTO tbl_products (name, stock, price, status, descripcion, created_at, updated_at)
    VALUES (p_name, p_stock, p_price, p_status, p_descripcion, NOW(), NOW());

    SELECT LAST_INSERT_ID() AS id;
END$$

DELIMITER ;
```
Procedmiento almacenado actualizar
```sql
DELIMITER $$

CREATE PROCEDURE sp_products_update(
    IN p_id INT,
    IN p_name VARCHAR(150),
    IN p_stock INT,
    IN p_price DECIMAL(10,2),
    IN p_status TINYINT,
    IN p_descripcion TEXT
)
BEGIN
    UPDATE tbl_products
    SET name = p_name,
        stock = p_stock,
        price = p_price,
        status = p_status,
        descripcion = p_descripcion,
        updated_at = NOW()
    WHERE id = p_id;
END$$

DELIMITER ;
```

---

## 🧠 Apoyo con IA (ChatGPT)

Durante el desarrollo de esta funcionalidad, me apoyé en ChatGPT para resolver diversos problemas y optimizar el proceso. Para guiar la solución, compartí la información más relevante de mi proyecto, incluyendo las tecnologías que utilizamos como Laravel y Filament.

Aquí algunos de los prompts que utilicé para obtener ayuda de la IA:

“Necesito agregar un campo de descripción a la tabla tbl_products y asegurarme de mostrarlo correctamente en la interfaz de administración con Filament. ¿Puedes guiarme paso a paso en este proceso, usando comandos Artisan para mantener un flujo ordenado?”

“Acabo de generar el modelo TblProduct con Artisan. ¿Me puedes ayudar a configurarlo correctamente, incluyendo los campos definidos en la migración (nombre, stock, precio, estado y descripción), para que luego pueda usarlo en los recursos de Filament?”

“He creado el seeder ProductosSeeder. ¿Podrías ayudarme a completarlo con algunos registros de ejemplo y mostrarme cómo registrarlo en DatabaseSeeder.php para que se ejecute junto con los demás seeders del proyecto?”

“Ya tengo generado el recurso TblProductResource en Filament con los comandos Artisan. Necesito que el formulario muestre todos los campos del producto, incluyendo el nuevo campo descripcion. También quiero que se vea en la tabla y que se pueda editar fácilmente. ¿Cómo lo configuro?”

“Quiero mejorar la experiencia del usuario. En lugar de mostrar la descripción en la tabla, necesito un botón que abra una vista de detalle (o modal) donde se muestre claramente la descripción del producto. Estoy trabajando con Filament, así que me interesa una solución que siga su estilo.”

“Ejecuté el comando php artisan make:filament-page ProductDescription --resource=TblProductResource --type=view para crear una página de detalle del producto. Te comparto el código que se generó. ¿Cómo puedo personalizar esta vista para que muestre únicamente el campo descripcion, ya que es lo que me solicitaron en el requerimiento?”

“Finalmente, ayúdame a documentar todo lo que hice: los comandos ejecutados, las modificaciones realizadas y la justificación de cada paso. La idea es dejar un README claro para que cualquier desarrollador pueda replicar el proceso. Incluye también la parte en la que usé un modal para ver la descripción del producto.”
