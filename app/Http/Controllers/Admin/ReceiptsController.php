<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Receipts;
use App\Models\Clocks;

class ReceiptsController extends Controller
{
    public function update(Request $request, $id)
    {
        $getData = Receipts::where('id', $id)->first();
        $data = $request->all();

        $validator = Validator::make($data, [
            'status' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($getData->count())
            $getData->update($data);

            $getClock = Clocks::where('id',$getData->clock_id)->first();
            $receipts = Receipts::where('clock_id', $getClock->id)->where('status', 'Approved')->get();
            $update['bonus_pay'] = 0;
            if ($receipts) :
                foreach ($receipts as $index) :
                    $update['bonus_pay'] += $index['amount'];
                endforeach;
            endif;
            if ($getClock->count())
                $getClock->update($update);

        return redirect()->back()->with('update', 'Update successfully!');
    }
}
