<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CropController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dataCrop = Crop::latest()->paginate(15);
        return response()->json($dataCrop);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'type_crop' => 'required',
            'image' => 'required',
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        try {
            if ($request->file('image')) {
                $validateData['image'] = $request->file('image')->store('crop-image');
            }

            $crop = Crop::create($validateData);

            if ($crop != null) {
                return response()->json([
                    'success' => true,
                    'message' => 'Crop data created successfully',
                    'data' => $crop
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Crop data failed created',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Err',
                'errors' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $crop = Crop::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $crop
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error',
                'errors' => $e->getMessage()
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'type_crop' => 'required',
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        try {
            $crop = Crop::findOrFail($id);

            if ($request->hasFile('image')) {
                // Hapus gambar lama jika ada
                if ($crop->image) {
                    Storage::delete($crop->image);
                }
                // Simpan gambar baru
                $validatedData['image'] = $request->file('image')->store('crop-image');
            }

            $crop->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Crop data updated successfully',
                'data' => $crop
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error',
                'errors' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $crop = Crop::findOrFail($id);

            // Hapus gambar terkait jika ada
            if ($crop->image) {
                Storage::delete($crop->image);
            }

            $crop->delete();

            return response()->json([
                'success' => true,
                'message' => 'Crop deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error',
                'errors' => $e->getMessage()
            ]);
        }
    }
}
