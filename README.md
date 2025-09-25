# Implementación de Buscador por Nombre en Clientes (Laravel + Filament)

## Contexto y Solución del Problema

Como desarrollador con amplia experiencia en Laravel y Filament, me encontré con la necesidad de implementar un buscador eficiente por nombre en la sección de clientes (`TblCustomer`). El objetivo era permitir a los usuarios filtrar y encontrar clientes fácilmente usando el nombre, resolviendo así problemas de navegación y búsqueda en la interfaz administrativa.

## Estrategia Utilizada

Para lograr esto, utilicé la funcionalidad `searchable()` que provee Filament en la definición de la tabla de clientes. Esta opción permite que la búsqueda se realice directamente sobre la columna especificada, utilizando internamente una consulta similar a:

```php
where('name', 'like', "%valor%")
```

Esto significa que al escribir en el campo de búsqueda, Filament ejecuta una consulta SQL que busca coincidencias parciales en el nombre del cliente, facilitando la localización rápida de registros.

## Ubicación del Código

La configuración del buscador se encuentra en el archivo:

```
app/Filament/Resources/TblCustomerResource.php
```

Dentro del método `table()`, la columna `name` se define así:

```php
Tables\Columns\TextColumn::make('name')
    ->label('Nombre Cliente')
    ->searchable()
    ->sortable(),
```

La opción `->searchable()` es la clave para habilitar el buscador por nombre. No es necesario agregar lógica adicional, ya que Filament se encarga de construir la consulta SQL adecuada.

## Ventajas de la Solución
- No requiere código extra ni controladores personalizados.
- Aprovecha el poder de Eloquent y Filament para búsquedas eficientes.
- Permite búsquedas parciales y rápidas por nombre.

## Recomendación
Si necesito ampliar la búsqueda a otros campos, simplemente agrego `->searchable()` a las columnas deseadas en la definición de la tabla.

---

**Ubicación de este documento:**
`documentacion/README.md`


