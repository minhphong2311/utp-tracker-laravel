<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function __construct()
    {
        $view = ['title' => 'Users', 'type' => 'users'];
        view()->share($view);
    }

    public function index(Request $request)
    {
        $param = $request->all();
        $page = '20';

        $data = User::where('level','Admin')->orderBy('updated_at', 'desc')->paginate($page);

        return view('admincp.users.index', compact('data'));
    }

    public function create()
    {
        return view('admincp.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|string|unique:users',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $data['password'] = bcrypt($data['password']);
        $data['level'] = 'Admin';
        User::create($data);

        return redirect('admincp/users');
    }

    public function show($id)
    {
        $data = User::where('id', $id)->first();

        return view('admincp.users.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $getData = User::where('id', $id)->first();
        $data = $request->all();

        if (!isset($data['active'])) {
            $data['active'] = 0;
        }
        if (isset($data['changepassword'])) {
            $data['password'] = bcrypt($data['changepassword']);
        }

        if ($getData->count())
            $getData->update($data);

        return redirect()->back()->with('update', 'Update successfully!');
    }

    public function delete($id)
    {

        $data = User::findOrFail($id);
        $data->delete();

        return redirect()->back()->with('delete', 'Delete successfully!');
    }
}
