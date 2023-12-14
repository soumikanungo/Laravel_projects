<?php

namespace App\Http\Controllers;

use App\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Stevebauman\Location\Facades\Location;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        $search=$request['search'] ?? "";
        if($search!=""){
            $clients=Client::where('name','LIKE',"%$search%")
            ->orWhere('ip','LIKE',"%$search%")
            ->orWhere('month','LIKE',"%$search%")
            ->get();
        }
        else{
            $clients=Client::all(); 
        }
        $data=compact('clients', 'search');
        return view('clients.index')->with($data)->with('i',(request()->input('page',1) - 1) * 5);;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       return view('clients.create');
    }

    
    public function store(Request $request)
    {
        
        $file=$request->file('image');
        $destinationPath = storage_path('app/public/images');
        $fileName=rand(11111, 99999).''.$file->getClientOriginalName();
        $filePath = $file->move($destinationPath, $fileName);
 
        $client=new Client();
        $client->name=$request->name;
        $client->email=$request->email;
        $client->ip=$request->ip;
        $client->month=$request->month;
        $client->image= $fileName;
        $client->save();
        return response()->json(['res'=>'Client added successfully.']);
       
    }
  public function edit( $id)
    {
        $client=Client::find($id);
        return view('clients.edit',compact('client'))->with('i',(request()->input('page',1) - 1) * 5);;
    }

   public function reset(){
    $clients=Client::all();
    return view('clients.index',compact('clients'));
   } 
    public function update(Request $request, $id)
    {
    
        $client=Client::find($id);

        $client->name=$request->name;
        $client->email=$request->email;
        $client->ip=$request->ip;
        $client->month=$request->month;
       if($request->file('image')){
        $file=$request->file('image');
        $destinationPath = storage_path('app/public/images');
        $fileName=rand(11111, 99999).''.$file->getClientOriginalName();
        $filePath = $file->move($destinationPath, $fileName);
        $client->image= $fileName;
       }
        $client->save();

       return response()->json(['response'=>'Client updated successfully.']);
       
    }

    
    public function destroy($id)
    {
      $client=Client::find($id);
      $client->delete();
      return redirect()->route('allClients')->withSuccess('client has been deleted successfully.');
    }

    public function deleteAll(Request $request){
        $ids=$request->ids;
        Client::whereIn('id',$ids)->delete();
        return response()->json(["success"=>"clients have been deleted successfully"]);
    }

    public function captcha(){
        return response()->json(['captcha'=>captcha_img()]);
    }
    public function getLocation($id){
        $client=Client::find($id);
        $clientIP=$client->ip;
        //dd($clientIP);
        //or
        //$clientIP= $SERVER['REMOTE_ADDDOR'];
        //$clientIP= '184.169.250.146';
        $location=Location::get($clientIP);
        return view('clients.location',compact('location'));
    }
    public function barGraph(){
        $clients=Client::selectRaw()
    }
}
