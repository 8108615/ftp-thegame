<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AjusteController extends Controller
{
    public function index()
    {
        $divisas = $this->getDivisas();
        $configuracion = Ajuste::query()->first();

        return view('admin.ajustes.index', compact('divisas', 'configuracion'));
    }

    public function store(Request $request)
    {
        $divisas = $this->getDivisas();

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'direccion' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'divisa' => ['required', 'string', 'size:3'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'web' => ['nullable', 'url', 'max:255'],
        ]);

        if (!array_key_exists($validated['divisa'], $divisas)) {
            return back()
                ->withErrors(['divisa' => 'La divisa seleccionada no es valida.'])
                ->withInput();
        }

        $ajuste = Ajuste::query()->first() ?? new Ajuste();

        if ($request->hasFile('logo')) {
            if (!empty($ajuste->logo) && Storage::disk('public')->exists($ajuste->logo)) {
                Storage::disk('public')->delete($ajuste->logo);
            }

            $validated['logo'] = $request->file('logo')->store('ajustes', 'public');
        } else {
            unset($validated['logo']);
        }

        $ajuste->fill($validated);
        $ajuste->save();

        return redirect()->route('admin.ajustes.index')->with('success', 'Ajustes guardados correctamente.');
    }

    private function getDivisas(): array
    {
        $divisas = [];
        $divisasPath = public_path('divisas.json');

        if (File::exists($divisasPath)) {
            $decoded = json_decode(File::get($divisasPath), true);

            if (is_array($decoded)) {
                $divisas = $decoded;
                ksort($divisas);
            }
        }

        return $divisas;
    }
}
