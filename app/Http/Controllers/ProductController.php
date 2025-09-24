<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    /**
     * Guardar un nuevo producto.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        Product::create($request->validated());

        return redirect()
            ->back()
            ->with('success', '✅ Producto creado correctamente');
    }

    /**
     * Exportar productos a CSV (Excel lo abre en columnas).
     */
    public function export(): StreamedResponse
    {
        $fileName = 'productos_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () {
            $output = fopen('php://output', 'w');

            // 👉 Indicar separador para Excel
            fputs($output, "sep=,\n");

            // Encabezados de columnas
            fputcsv($output, ['ID','Nombre','SKU','Precio','Stock','Descripción','Expira','Creado'], ',');

            // Filas con los datos (en chunks por memoria)
            Product::chunk(200, function ($products) use ($output) {
                foreach ($products as $product) {
                    fputcsv($output, [
                        $product->id,
                        $product->name,
                        $product->sku,
                        $product->price,
                        $product->stock,
                        $product->description,
                        $product->expires_at,
                        $product->created_at,
                    ], ',');
                }
            });

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Vista imprimible (usa resources/views/productos_print.blade.php)
     */
    public function print()
    {
        // Si hay MUCHOS registros, podrías usar ->cursor() en lugar de ->get()
        $products = Product::orderBy('id')->get();

        // 👇 OJO: nombre de la vista sin carpeta, coincide con tu archivo productos_print.blade.php
        return view('productos_print', compact('products'));
    }
}
