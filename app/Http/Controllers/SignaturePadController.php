<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class SignaturePadController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index(Request $request)
    {
        return view('signaturePad', [
            'oa_id' => $request['oa_id'],
        ]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function upload(Request $request)
    {
        $folderPath = public_path('upload/');

        $image_parts = explode(";base64,", $request->signed);

        $image_type_aux = explode("image/", $image_parts[0]);

        $image_type = $image_type_aux[1];

        $image_base64 = base64_decode($image_parts[1]);

        $file_name = uniqid() . '.'.$image_type;
        $file = $folderPath . $file_name;

        file_put_contents($file, $image_base64);

        $order = Order::where('oa_number', $request->oa_id)->first();
        $order->host_signature = $file_name;
        $order->save();

        return redirect(route('order.agreements.view.print', $request->oa_id));
    }
}
