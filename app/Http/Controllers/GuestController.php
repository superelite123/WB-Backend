<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CustomerType;
use App\Models\State;
use App\Models\Nda;
use App\Models\NDALog;
use Mail;
use App\Mail\NDASender;
//LIB
use Storage;
use PDF;
use File;
use Auth;
class GuestController extends Controller
{
    public function NDAIndex()
    {
        return view('guest.nda_index');
    }
    public function NDAHome()
    {
        return view('guest.nda_home');
    }

    public function _NDAEmailCheck($email)
    {
        return Nda::where('email',$email)->first() != null?1:0;
    }
    public function _storeNDAE(Request $request)
    {
        //save Image
        $imagedata = base64_decode($request->signImage);
        $filename = uniqid().'.png';
        //Location to where you want to created sign image
        $file_name = 'ndaSigns/'.$filename;

        if(!Storage::disk('public')->put($file_name, $imagedata))
        {
            return -1;
        }

        //update NDA
        $nda = Nda::where('email',$request->email)->first();
        if($nda == null) return -1;
        $nda->signature_file    = $filename;
        $nda->save();
        $nda->pdf_file = $this->generateNDAPdf('pdfTemplate.nda',$nda);
        $nda->save();
        //$this->sendNDAToCustomer($nda->id);
        $ndalog = new NDALog;
        $ndalog->user_id = auth()->user()->id;
        $ndalog->nda_id  = $nda->id;
        $ndalog->type    = 2;
        $ndalog->save();
        return $nda->id;
    }
    //
    public function NDAPage()
    {
        $data = [];
        $data['states'] = State::all();
        $data['customerTypes'] = CustomerType::all();
        $data['nda'] = Nda::find(3);
        //$this->generaeNDAPdf('guest.nda',$data);
        return view('guest.nda',$data);
    }

    public function storeNDA(Request $request)
    {
        /*
        *encrypt id image file
        */
        $idFileName = uniqid().'.png';
        if(!Storage::disk('id_uploads')->put($idFileName, encrypt(base64_decode($request->captureData))))
        {
            return false;
        }

        // if(!Storage::disk('id_uploads')->put('id1.png', decrypt(Storage::disk('id_uploads')->get('id.png'))))
        // {
        //     return false;
        // }

        //return file_get_contents(Storage::path('ndaIDs').'\id.png');
        //end encrypt image
        //save Image
        $imagedata = base64_decode($request->signImage);
        $filename = uniqid().'.png';
        //Location to where you want to created sign image
        $file_name = 'ndaSigns/'.$filename;

        if(!Storage::disk('public')->put($file_name, $imagedata))
        {
            return false;
        }

        $nda = new Nda;
        $nda->customer_name     = $request->customerName;
        $nda->company_name      = $request->companyName;
        $nda->email             = strtolower($request->email);
        $nda->city              = $request->city;
        $nda->street            = $request->street;
        $nda->title             = $request->title;
        $nda->state_id          = $request->state;
        $nda->user_id           = auth()->user()->id;
        $nda->customer_type_id  = $request->customerType;
        $nda->signature_file    = $filename;
        $nda->id_file           = $idFileName;
        $nda->zip_pwd           = '';
        $nda->save();
        $nda->pdf_file = $this->generateNDAPdf('pdfTemplate.nda',$nda);
        $nda->save();
        //log
        $ndalog = new NDALog;
        $ndalog->user_id = auth()->user()->id;
        $ndalog->nda_id  = $nda->id;
        $ndalog->type    = 1;
        $ndalog->save();
        $this->sendNDAToCustomer($nda->id);
        return $nda->id;
    }

    public function sendNDAToCustomer($id)
    {
        $nda = Nda::find($id);
        Mail::to('gary@pcrealms.com')->send(new NDASender($nda));
        Mail::to($nda->email)->send(new NDASender($nda));
    }

    public function generateNDAPdf($viewName,$data)
    {
        $pdf = PDF::loadView($viewName, ['nda' => $data]);
        $filename = 'WalnutNDA_'.date('Y-m-d').'_'.$data->customer_name.'.pdf';
        $file_name = 'ndaPdfs/'.$filename;
        if(!Storage::disk('public')->put($file_name, $pdf->output()))
        {
            return false;
        }
        return $filename;
    }

    public function NDASignoutP()
    {
        $ndas = Nda::all();
        return view('guest.nda_signout_p',['ndas' => $ndas]);
    }

    public function NDASignout($id)
    {
        $nda = Nda::find($id);
        if($nda != null)
            Storage::disk('id_uploads')->delete($nda->id_file);
        $nda->delete();
        //Auth::logout();
        return view('guest.nda_signout');
    }
}
