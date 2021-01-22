<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\InvoiceNew;
use App\Models\InvoiceCreditNote;
use App\Models\InvoiceCreditNoteLog;
use App\Models\InvoiceCreditNoteReason;
use App\Models\Customer;
use App\Models\Term;
use App\Models\Strainame;
use App\Models\Producttype;
use App\Models\Counter;
use App\Models\OurDetail;
use App\Mail\CreditNoteSender;
use JavaScript;
use App\Helper\CommonFunction;

use DB;
use PDF;
use Storage;
use Mail;
use File;
class CreditNoteController extends Controller
{
    use CommonFunction;
    //
    public function form($id)
    {
        $order = InvoiceNew::find($id);
        $counter = Counter::where('key', 'credit_note')->first();
        $data = [
            'order'           => $order,
            'strains'         => Strainame::orderby('strain')->get(),
            'producttypes'    => Producttype::where('onordermenu',1)->orderby('producttype')->get(),
            'date'            => $order->date,
            'number'          => $order->number,
            'term'            => Term::find($order->term_id),
            'reasons'         => InvoiceCreditNoteReason::all(),
            'index'           => $counter->prefix.$counter->value,
        ];
        return view('creditNote.form',$data);
    }
    public function store(Request $request)
    {
        $counter = Counter::where('key', 'credit_note')->first();
        $creditNote = new InvoiceCreditNote;
        $creditNote->fill($request->except(['items']));
        $creditNote->original_total = $request->total_price;
        $creditNote->no = $counter->prefix.$counter->value;
        $creditNote->archive = 2;
        $creditNote->save();
        $creditNote->storeHasMany(['rItems' => $request->items]);
        $counter->increment('value');
        return response()->json(['id' => $creditNote->id]);
    }
    public function archive()
    {
        // $data['creditNote'] = InvoiceCreditNote::find(9);
        // $data['invoice'] = $data['creditNote']->rInvoice;
        // $data['invoice']->company_detail = OurDetail::all()->first();
        // return view('pdfTemplate.credit_note', $data);
        $data = [
            'start_date' => date('m/d/Y', strtotime('today - 31 days')),
            'end_date' => Date('m/d/Y'),
        ];
        return view('creditNote.archive',$data);
    }
    public function _archives(Request $request)
    {
        $date_range = $request->date_range;
        $date_range = $this->convertDateRangeFormat($date_range);
        $bCond = Customer::select('client_id','clientname')
                         ->with('rCreditNote');
        if($request->who != null)
        {
            $bCond = $bCond->where('client_id',$request->who);
            $data = $bCond->get();
        }
        else
        {
            $bCond = $bCond->whereHas('rCreditNote',function($query) use($date_range){
                $query->whereBetween('created_at', [
                    $date_range['start_date']." 00:00:00",
                    $date_range['end_date']." 23:59:59"
                ]);
            });
            //check order column
            $orderingColumn = $request->input('order.0.column');
            $dir = $request->input('order.0.dir');
            switch($orderingColumn)
            {
                case '1':
                    $bCond = $bCond->orderBy('clientname',$dir);
                break;
                default:
                    $bCond = $bCond->orderBy('clientname','desc');
            }

            $totalData = $bCond->count();
            $limit = $request->input('length') != -1?$request->input('length'):$totalData;
            $start = $request->input('start');
            $totalFiltered  = $bCond->count();
            if(!empty($request->input('search.value'))){
                $search = $request->input('search.value');
                $bCond = $bCond->Where(function($query) use ($search){
                            $query->where('clientname','like',"%{$search}%");
                        });
                $totalFiltered  = $bCond->count();
            }

            $data = $bCond->offset($start)->limit($limit)->get();
        }
        $responseData = [];
        foreach($data as $key => $item)
        {
            $temp = [];
            $temp['no'] = $key + 1;
            $temp['id'] = $item->client_id;
            $temp['name'] = $item->clientname;
            $temp['items'] = [];
            $balancePrice = 0;
            $totalPrice = 0;
            //()->where('archive','!=','1')->get()
            foreach($item->rCreditNote as $creditNote)
            {
                $creditNoteTemp = [];
                $creditNoteTemp['id'] = $creditNote->id;
                $creditNoteTemp['id_inv'] = $creditNote->rInvoice->id;
                $creditNoteTemp['so'] = $creditNote->rInvoice->number.' / '.$creditNote->rInvoice->number2;
                $creditNoteTemp['no'] = $creditNote->no;
                $creditNoteTemp['date'] = $creditNote->created_at->format('m/d/Y H:i:s');
                $creditNoteTemp['total_price'] = '$'.$creditNote->total_price;
                $creditNoteTemp['note'] = $creditNote->rReason != null?$creditNote->rReason->name : '';
                $creditNoteTemp['detail'] = $creditNote->detail != null ? $creditNote->detail : '';
                $creditNoteTemp['approved_by'] = $creditNote->Approver;
                $creditNoteTemp['approved_at'] = $creditNote->approved_at != null ? date('m/d/Y H:i', strtotime($creditNote->approved_at)) : '';
                $creditNoteTemp['status'] = $creditNote->archive;
                $temp['items'][] = $creditNoteTemp;
                
                $balancePrice += $creditNote->total_price;
                $totalPrice   += $creditNote->original_total;
            }

            $temp['balancePrice'] = '$'.$balancePrice;
            $temp['totalPrice']   = '$'.$totalPrice;
            //applied credits
            $appliedCredits = InvoiceCreditNoteLog::whereHas('rInvoice',function($query) use($item){
                                $query->where('customer_id',$item->client_id);
                            })->get();
            $temp['appliedCreditsData'] = [];
            foreach($appliedCredits as $appliedCredit)
            {
                $appliedCreditTemp = [];
                $invoice = $appliedCredit->rInvoice;
                $appliedCreditTemp['id_inv'] = $invoice->id;
                $appliedCreditTemp['id_cn'] = $appliedCredit->rCreditNote->id;
                $appliedCreditTemp['so'] = $invoice->number.' / '.$invoice->number2;
                $appliedCreditTemp['cn_no'] = $appliedCredit->rCreditNote->no;
                $appliedCreditTemp['date'] = $appliedCredit->created_at->format('m/d/Y H:i');
                $appliedCreditTemp['amount'] = '$'.$appliedCredit->amount;
                $temp['appliedCreditsData'][] = $appliedCreditTemp;
            }
            $responseData[] = $temp;
        }
        if($request->who != null)
        {
            return response()->json($responseData[0]);
        }
        else
        {
            return array(
                "draw"			=> intval($request->input('draw')),
                "recordsTotal"	=> intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"			=> $responseData,
            );
        }

    }
    public function approve(Request $request)
    {
        $result = InvoiceCreditNote::find($request->id)->update([
            'archive' => 0,
            'approved_by'=> auth()->user()->id,
            'approved_at' => date('Y-m-d H:i:s')
            ]) ? 1 : 0;
        return response()->json(['success' => $result]);
    }

    public function snap($id)
    {
        $data['creditNote'] = InvoiceCreditNote::find($id);
        $data['invoice'] = $data['creditNote']->rInvoice;
        $data['invoice']->company_detail = OurDetail::all()->first();
        return view('creditNote.snap', $data);
    }
    
    public function download($id)
    {
        $data['creditNote'] = InvoiceCreditNote::find($id);
        $data['invoice'] = $data['creditNote']->rInvoice;
        $data['invoice']->company_detail = OurDetail::all()->first();
        $view_name = 'pdfTemplate.credit_note';
        $pdf = PDF::loadView($view_name, $data);
        return $pdf->download('note_'.$data['creditNote']->no.'.pdf');;
    }
    public function delete($id)
    {
        $creditNote = InvoiceCreditNote::find($id);
        $creditNote->rItems()->delete();
        $creditNote->delete();
        return response()->json(['result' => 1]);
    }
    public function email(Request $request)
    {
        $data['creditNote'] = InvoiceCreditNote::find($request->id);
        $data['invoice'] = $data['creditNote']->rInvoice;
        $data['invoice']->company_detail = OurDetail::all()->first();
        $view_name = 'pdfTemplate.credit_note';
        $pdf = PDF::loadView($view_name, $data);
        $filename = 'credit note_'.$data['creditNote']->no.'.pdf';
        if(!Storage::disk('public')->put($filename, $pdf->output()))
        {
            
        }
        foreach($request->emails as $email)
        {
            Mail::to($email)->send(new CreditNoteSender(['filename' => $filename, 'creditNote' => $data['creditNote'],'invoice' => $data['invoice']]));
        }
        File::delete(public_path().'/storage/'.$filename);
        return response()->json(['success' => 1]);
    }
}
