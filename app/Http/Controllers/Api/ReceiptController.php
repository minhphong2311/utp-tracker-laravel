<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests\ReceiptRequest;
use App\Http\Resources\ReceiptResource;

use App\Models\Receipts;

class ReceiptController extends Controller
{
    public function index($clock_id)
    {
        return ReceiptResource::collection(Receipts::where('clock_id',$clock_id)->orderBy('id','desc')->get());
    }

    public function receipt(ReceiptRequest $request)
    {
        $validated = $request->validated();

        $path = '';

        if ($validated['photo'] && $validated['photo']->isValid()) {
            $file_name = Str::random(10).'_'.time() . '.' . $validated['photo']->extension();
            $validated['photo']->move(public_path('uploads/receipts'), $file_name);
            $path = '/public/uploads/receipts/' . $file_name;
        }

        $param = [
            'clock_id' => $validated['clock_id'],
            'image' => $path,
            'receipt' => $validated['receipt'],
            'amount' => $validated['amount'],
        ];

        Receipts::create($param);

        return response()->json(['msg' => 'Created successfully'], 200);
    }
}
