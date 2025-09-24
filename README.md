# ProyectoLogisticaGH - Exportar Clientes a PDF con Laravel y Filament

## Prompt

Eres un programador de Laravel y Filament. Te piden hacer una funcionalidad de exportar los clientes en formato PDF.

## Tecnologías y Paquetes Utilizados
- **Laravel** (v12)
- **Filament** (v3)
- **barryvdh/laravel-dompdf** (PDF export)

## Comandos de Instalación

1. Instalar Filament:
   ```bash
   composer require filament/filament
   ```
2. Instalar DomPDF:
   ```bash
   composer require barryvdh/laravel-dompdf
   ```
3. Agregar la Facade en `config/app.php`:
   ```php
   'aliases' => [
       // ...otros aliases...
       'Pdf' => Barryvdh\DomPDF\Facade\Pdf::class,
   ],
   ```
4. Actualizar la caché de configuración:
   ```bash
   php artisan config:cache
   ```
5. Regenerar autoload:
   ```bash
   composer dump-autoload
   ```
6. Reiniciar el servidor:
   ```bash
   php artisan serve
   ```

## Funcionalidad Implementada
- Botón "Exportar PDF" en la vista de clientes de Filament.
- Controlador `CustomerExportController` para generar el PDF.
- Vista Blade `resources/views/exports/customers.blade.php` para el formato del PDF.
- Ruta `/clientes/export/pdf` para descargar el PDF.

## Ediciones Realizadas
- Corrección de relaciones Eloquent en los modelos (`tbl_municipality`, `tbl_department`).
- Ajuste de los resources y vistas para usar los nombres correctos de las relaciones.
- Agregado el alias de la Facade `Pdf` en `config/app.php`.
- Creación de la acción personalizada en Filament para exportar.

## Problemas Encontrados y Soluciones
- **Error de relación Eloquent:** Se corrigió usando los nombres correctos (`tbl_municipality`).
- **Class "Barryvdh\DomPDF\Facade\Pdf" not found:** Se solucionó instalando el paquete, agregando el alias y actualizando la caché/configuración.
- **Acción de exportar en Filament:** Se corrigió usando `Filament\Actions\Action` en vez de `Filament\Tables\Actions\Action`.

## Recomendaciones
- Verifica que los nombres de las relaciones en los modelos coincidan con los usados en los resources y vistas.
- Siempre actualiza la caché de configuración y el autoload después de instalar nuevos paquetes.
- Reinicia el servidor de Laravel tras cambios en configuración o instalación de paquetes.

---

**¡Funcionalidad de exportar clientes a PDF lista y funcionando!**
