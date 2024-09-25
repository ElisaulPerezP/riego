<?php

namespace App\Http\Controllers;

use App\Models\Qr;
use Illuminate\Http\Request;
use App\Models\Cosecha;
use Illuminate\Support\Str;
use App\Http\Actions\QRCodeGenerator;


class QrController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obtener el ID de la cosecha desde la solicitud
        $cosecha_id = $request->input('cosecha_id');
    
        // Filtrar los códigos QR que pertenezcan a esa cosecha
        $qrs = Qr::where('cosecha_id', $cosecha_id)->get();
    
        // Determinar si hay códigos QR
        $hayCodigosQr = $qrs->isNotEmpty(); // True si hay códigos QR
    //dd($cosecha_id);
        // Pasar los QRs, el ID de la cosecha y la variable hayCodigosQr a la vista
        return view('cosechas.qrs.index', compact('qrs', 'cosecha_id', 'hayCodigosQr'));
    }
    
    
    /**
     * Show the form for creating a new resource.
     */
   // App\Http\Controllers\QrController.php

    public function create(Request $request)
    {
        $cosecha_id = $request->input('cosecha_id');

        // Validar que el cosecha_id existe
        $cosecha = Cosecha::findOrFail($cosecha_id);

        return view('cosechas.qrs.create', compact('cosecha'));
    }


    /**
     * Store a newly created resource in storage.
     */

    
    public function store(Request $request)
    {
        // Validar que el `cosecha_id` sea válido
        $request->validate([
            'cosecha_id' => 'required|integer|exists:cosechas,id',
        ]);
    
        // Obtener la cosecha correspondiente
        $cosecha = Cosecha::findOrFail($request->input('cosecha_id'));
    
        // Generar los UUIDs para cada tipo de cajas
        $uuid125List = $this->generateUuidArray($cosecha->cajas125);
        $uuid250List = $this->generateUuidArray($cosecha->cajas250);
        $uuid500List = $this->generateUuidArray($cosecha->cajas500);
    
        // Instanciar el generador de QR
        $qrGenerator = new QRCodeGenerator();
    
        // URL base para los QR
        $baseUrl = 'https://arandanosdemipueblo.online';
    
        // Generar las imágenes de QR para cada tipo de caja
        try {
            $qr125Path = null;
            if (count($uuid125List) > 0) {
                $qr125FileName = 'qr125_' . time() . '.jpg';
                $qr125Path = $qrGenerator->generarImagenQR($uuid125List, $baseUrl, $qr125FileName);
            }
    
            $qr250Path = null;
            if (count($uuid250List) > 0) {
                $qr250FileName = 'qr250_' . time() . '.jpg';
                $qr250Path = $qrGenerator->generarImagenQR($uuid250List, $baseUrl, $qr250FileName);
            }
    
            $qr500Path = null;
            if (count($uuid500List) > 0) {
                $qr500FileName = 'qr500_' . time() . '.jpg';
                $qr500Path = $qrGenerator->generarImagenQR($uuid500List, $baseUrl, $qr500FileName);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al generar las imágenes QR: ' . $e->getMessage());
        }
    
        // Crear el nuevo QR asociado a la cosecha
        $qr = Qr::create([
            'cosecha_id' => $cosecha->id,
            'qr125' => $qr125Path,
            'qr250' => $qr250Path,
            'qr500' => $qr500Path,
            'uuid125' => implode(',', $uuid125List),
            'uuid250' => implode(',', $uuid250List),
            'uuid500' => implode(',', $uuid500List),
        ]);
    
        // Redirigir al detalle del QR creado
        return redirect()->route('qrs.show', ['qr' => $qr->id])->with('success', 'QR creado exitosamente');
    }
    
    /**
     * Genera un array de UUIDs truncados a 8 caracteres.
     *
     * @param int $quantity
     * @return array
     */
    private function generateUuidArray($quantity)
    {
        $uuids = [];
    
        for ($i = 0; $i < $quantity; $i++) {
            $uuids[] = substr((string) Str::uuid(), 0, 8);
        }
    
        return $uuids;
    }
    
    
    /**
     * Generar una cadena de UUIDs separados por comas.
     *
     * @param int $quantity
     * @return string
     */
    private function generateUuids($quantity)
    {
        $uuids = [];
    
        // Generar tantos UUIDs como indique la cantidad
        for ($i = 0; $i < $quantity; $i++) {
            // Generar un UUID y tomar los primeros 8 caracteres
            $uuids[] = substr(Str::uuid()->toString(), 0, 8);
        }
    
        // Unir los UUIDs en una cadena separada por comas
        return implode(',', $uuids);
    }

    /**
     * Display the specified resource.
     */
    public function show(Qr $qr)
    {
        return view('cosechas.qrs.show', compact('qr'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Qr $qr)
    {
        return view('cosechas.qrs.edit', compact('qr'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Qr $qr)
    {
        // Validar los datos ingresados
        $request->validate([
            'cosecha_id' => 'required|integer',
            'qr125' => 'nullable|string',
            'qr250' => 'nullable|string',
            'qr500' => 'nullable|string',
            'uuid125' => 'nullable|array',
            'uuid250' => 'nullable|array',
            'uuid500' => 'nullable|array',
        ]);

        // Actualizar el QR existente
        $qr->update($request->all());

        // Redirigir con un mensaje de éxito
        return redirect()->route('qrs.index')->with('success', 'QR actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Qr $qr)
    {
        // Eliminar el registro
        $qr->delete();

        // Redirigir con un mensaje de éxito
        return redirect()->route('cosecha.index')->with('success', 'QR eliminado exitosamente');
    }
}
