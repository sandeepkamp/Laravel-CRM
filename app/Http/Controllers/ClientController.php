<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Projects\ClientModel;
use App\DataTables\ClientDataTable;
use App\Models\Projects\ProjectModel;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function list(ClientDataTable $dataTable)
    {
        return $dataTable->render('clients.list');
    }

    public function create()
    {
        return view('clients.add');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:200',
            'last_name' => 'required|max:200',
            'email' => 'required|email:rfc,dns|unique:clients,email|max:255',
            'password' => 'required|string|min:8|same:confirm_password',
            'confirm_password' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else{
            $client = new ClientModel;

            $client->first_name = $request->get('first_name', '');
            $client->last_name = $request->get('last_name', '');
            $client->email = $request->get('email', '');
            $client->phone = $request->get('phone', null);
            $client->status = $request->get('status', 1);
            $client->password = Hash::make($request->get('password'));
            $client->created_at = now();
            $client->updated_at = now();

            $client->save();

            return redirect(route('client.list'))->withSuccess('Client has been added successfully');
        }
    }

    public function details($client_id) 
    {
        // ---
    }


    public function edit($client_id)
    {
        $client = ClientModel::find($client_id);
        if($client){
            return view('clients.edit', compact('client'));
        }else{
            return redirect(route('client.list'))->withSuccess('Client has been added successfully');
        }
    }

    public function update(Request $request, $client_id)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:200',
            'last_name' => 'required|max:200',
            'email' => 'required|email:rfc,dns|unique:clients,email,'.$client_id.'|max:255',
            'password' => 'sometimes|nullable|string|same:confirm_password|required_with:confirm_password',
            'confirm_password' => 'sometimes|nullable'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $client = ClientModel::find($client_id);
            if (!$client) {
                return redirect()->back()->withErrors(['error' => 'Client not found.']);
            }

            $client->first_name = $request->get('first_name', '');
            $client->last_name = $request->get('last_name', '');
            $client->email = $request->get('email', '');
            $client->phone = $request->get('phone', null);
            $client->status = $request->get('status', 1);
            if ($request->filled('password')) {
                $client->password = Hash::make($request->get('password'));
            }
            $client->updated_at = now();
            $client->save();

            return redirect(route('client.list'))->withSuccess('Client has been updated successfully');
        }
    }

    public function destroy(Request $request, $id)
    {
        $user = ClientModel::where('id', $id)->first();
        if($user){
            ProjectModel::where('client_id', $id)->update([
                'client_id' => null
            ]);
            $user->delete();
            return redirect()->back()->withSuccess('Client has been deleted');
        }else{
            return redirect(route('client.list'))->withError('Client not found');
        }
    }


    // ajax calls
    public function get_clients(Request $request)
    {
        if($request->ajax()){
            $clients = ClientModel::all();
            return response()->json([
                'items' => $clients,
            ]);
        }
    }


}
