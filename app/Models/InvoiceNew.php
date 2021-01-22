<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helper\HasManyRelation;
use DB;
use Config;
class InvoiceNew extends Model
{
    //
    use HasManyRelation;

    protected $table = 'invoices_new';

    protected $fillable = ['number','customer_id','distuributor_id',
                           'salesperson_id','note','fulfillmentnote','date','total',
                           'term_id','paid','coainbox','delivered','status','tax_allow','m_m_str',
                            'tbp','discount','e_discount','extended',
                            'promotion','pr_value','tax','total','qty','credit_amount'];
    public function getDateAttribute($value)
    {
        return date("m/d/Y", strtotime($value) );
    }
    public function customer(){
        return $this->belongsTo(Customer::class,'customer_id')->with('Term');
    }
    public function distuributor(){
        return $this->belongsTo(Distributor::class,'distuributor_id');
    }
    public function Term(){
        return $this->belongsTo(Term::class,'term_id');
    }
    public function itemAP()
    {
        return $this->hasMany(InvoiceItemAP::class,'invoice_id');
    }
    public function Priority()
    {
        return $this->belongsTo(Priority::class,'priority_id');
    }
    public function PaymentLog()
    {
        return $this->hasMany(InvoicePaymentLog::class,'order_id')->orderBy('updated_at');
    }
    public function ContactInfo()
    {
        return $this->hasOne(InvoiceContact::class,'invoice_id');
    }
    public function getTotalQtyAttribute()
    {
        $apItems = $this->itemAP;
        return $apItems->sum('qty');
    }
    public function fulfilledItem()
    {
        return $this->hasMany(InvoiceFulfilledItem::class,'invoice_id')
                    ->whereIn('m_parent_id',[0,-1])->with(['asset','ap_item']);
    }

    public function fulfilledNItem()
    {
        return $this->hasMany(InvoiceFulfilledItem::class,'invoice_id')
                    ->where('m_parent_id','!=',-1)->with(['asset','ap_item']);
    }

    public function shipping_method()
    {
        return $this->hasOne(ShippingDetail::class,'invoice_id')->withDefault();
    }

    public function salesperson()
    {
        return $this->belongsTo(ContactPerson::class,'salesperson_id');
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class,'distuributor_id');
    }

    public function rPDiscount()
    {
        return $this->hasOne(InvoiceOption::class,'order_id');
    }

    public function rDevlieryer()
    {
        return $this->belongsTo(Delivery::class,'deliveryer');
    }

    public function getSignDateDAttribute()
    {
        return ($this->sign_date != null && $this->sign_date != '0000-00-00')?date('m/d/Y',strtotime($this->sign_date)):'';
    }
    public function getSignDateHAttribute()
    {
        return ($this->sign_date != null && $this->sign_date != '0000-00-00')?date('m/d/Y H',strtotime($this->sign_date)):'';
    }
    public function getCNameAttribute()
    {
        if($this->customer == null) return 'previous system';
        return $this->customer['clientname'] != ''?$this->customer['clientname']:'previous system';
    }
    public function getCLegalNameAttribute()
    {
        $customer = $this->customer;
        if($customer == null)
        {
            return 'previous system';
        }
        $customer = $customer->legalname != '' ? $customer->legalname : 'previous system';
        return $customer;
    }
    public function getSalesRepNameAttribute()
    {
        $salesperson = $this->salesperson;
        if($salesperson == null)
        {
            return 'previous system';
        }
        
        return $salesperson->firstname.' '.$salesperson->lastname;
    }

    public function getCPNameAttribute()
    {
        return $this->distributor['companyname'] != ''?$this->distributor['companyname']:Config::get('constants.order.no_distributor');
    }

    public function getPayDateAttribute()
    {
        $term = $this->Term == null?0:$this->Term->days;
        return date('Y-m-d',strtotime($this->date." +".$term." days"));
    }
    public function getTermLabelAttribute()
    {
        return $this->Term != null?$this->Term->term:'No Term';
    }
    public function getTermDaysAttribute()
    {
        return $this->Term != null?$this->Term->days:0;
    }
    public function getDTermdayAttribute()
    {
        return date('Y-m-d',strtotime($this->delivered." +".$this->TermDays." days"));
    }
    public function getTotalInfoAttribute()
    {
        $items = $this->itemAP;
        $base_price                 = 0;
        $base_price_for_tax         = 0;
        $discounted                 = 0;
        $e_discount                 = 0;
        $extended                   = 0;
        $adjust_price               = 0;
        $promotionCost              = 0;
        $base_price_for_promotion   = 0;
        $taxed                      = 0;
        $qty                        = 0;
        $weight = 0;
        foreach($items as $key => $item)
        {
            $base_price     += $item->BasePrice;
            $discounted     += $item->discount;
            $e_discount     += $item->e_discount;

            $extended       += $item->extended;
            $adjust_price   += $item->AdjustPrice;
            $qty            += $item->qty;
            if($item->Taxexempt != 1)
            {
                $base_price_for_tax += $item->BasePrice;
            }
            if($item->producttype != null)
            {
                $promotionCost += $item->producttype->promocost * $item->qty;
            }
        }

        //Calculate Extra Discount
        $option = InvoiceOption::where(['order_id' => $this->id,'type' => 1])->first();
        if($option != null)
        {
            $e_discount += $option->value;
            $extended   -= $option->value;
            $adjust_price -= $option->value;
        }
        //deduct creditnote
        $extended -= $this->credit_amount;
        //sum of weight
        $n_items = $this->fulfilledNItem()->get();
        foreach($n_items as $item)
        {
            if($item->asset != null)
                $weight += $item->asset->weight;
            else
                $weight += 0;
        }

        $base_price_for_promotion = $base_price_for_tax;
        if($this->tax_type == 1)
        {
            $taxed = $base_price_for_tax * 0.8;
            $taxed = ($taxed + $base_price_for_tax) * 0.15;
        }
        if($this->tax_type == 2)
        {
            $taxed = ($base_price - $discounted - $e_discount) * 0.27;
        }
        if($this->tax_allow == 1)
            $taxed = 0;
        $adjust_price += $taxed;
        $result = [];
        $result['base_price']   = number_format((float)$base_price, 2, '.', '');
        $result['discount']     = number_format((float)$discounted, 2, '.', '');
        $result['e_discount']   = number_format((float)$e_discount, 2, '.', '');
        $result['extended']     = number_format((float)$extended, 2, '.', '');
        $result['promotion']    = number_format((float)$base_price - $base_price_for_promotion, 2, '.', '');
        $result['prValue']      = number_format((float)$promotionCost, 2, '.', '');
        $result['tax']          = number_format((float)$taxed, 2, '.', '');
        $result['adjust_price'] = number_format((float)$adjust_price, 2, '.', '');
        $result['credit_total'] = $this->customer != null?$this->customer->CreditNoteTotal:number_format((float)$this->credit_amount, 2, '.', '');
        $result['credit_amount']= $this->credit_amount;
        $result['qty']          = $qty;
        $result['pay_date']     = $this->PayDate;
        $result['payment']      = $this->paid == null?'No Paid':'Paid';
        $result['customername'] = $this->CName;
        $result['companyname']  = $this->CPName;
        $result['term']         = $this->TermDays;
        $result['termLabel']    = $this->TermLabel;
        $result['weight']       = number_format((float)$weight, 2, '.', '');
        $result['total_debt']   = $this->customer != null?$this->customer->TotalDebt:0;
        $result['fTime']        = gmdate("H:i:s", $qty * 1.5 * 60);
        $result['priorityLabel']= $this->Priority != null?$this->Priority->name:'';
        return $result;
    }

    public function getTotalInfoForExportAttribute()
    {
        $base_price                 = 0;
        $base_price_for_tax         = 0;
        $discounted                 = 0;
        $e_discount                 = 0;
        $extended                   = 0;
        $adjust_price               = 0;
        $qty                        = 0;
        $weight                     = 0;

        $items = $this->itemAP;
        foreach($items as $key => $item)
        {
            $base_price += $item->BasePrice;
            $discounted   += $item->discount;
            $e_discount   += $item->e_discount;
            $extended     += $item->Extended;
            $adjust_price += $item->AdjustPrice;
            $qty += $item->qty;
            if($item->Taxexempt != 1)
            {
                $base_price_for_tax += $item->BasePrice;
            }
        }
        //Calculate Extra Discount
        $option = InvoiceOption::where(['order_id' => $this->id,'type' => 1])->first();
        if($option != null)
        {
            $e_discount += $option->value;
            $extended   -= $option->value;
            $adjust_price -= $option->value;
        }
        //deduct creditnote
        $extended -= $this->credit_amount;
        $weight = DB::select('SELECT sum(weight) totalWeight from invoice_fulfilled_item ifi
                              JOIN invoice_good ig ON ifi.asset_id=ig.id
                              WHERE ifi.m_parent_id != -1
                              AND invoice_id='.$this->id)[0]->totalWeight;
        if($this->tax_type == 1)
        {
            $taxed = $base_price_for_tax * 0.8;
            $taxed = ($taxed + $base_price_for_tax) * 0.15;
        }
        if($this->tax_type == 2)
        {
            $taxed = ($base_price - $discounted - $e_discount) * 0.27;
        }
        if($this->tax_allow == 1)
            $taxed = 0;
        $adjust_price += $taxed;

        //ptweight
        $ptweight = [];
        $ptweightTypes = PtWeight::all();
        foreach($ptweightTypes as $type)
        {
            $ptweight['pt'.$type->ptweight] = 0;
        }
        $n_items = $this->fulfilledNItem()->get();
        foreach($n_items as $item)
        {
            if($item->asset != null && $item->asset->AssetType != null)
            {
                if($item->asset->AssetType->ptWeight != null)
                {
                    $ptweight['pt'.$item->asset->AssetType->pcategory] += $item->asset->weight;
                }
            }
        }

        $result = [];
        $result['base_price']   = number_format((float)$base_price, 2, '.', '');
        $result['discount']     = number_format((float)$discounted, 2, '.', '');
        $result['e_discount']   = number_format((float)$e_discount, 2, '.', '');
        $result['extended']     = number_format((float)$extended, 2, '.', '');
        $result['credit_amount']= $this->credit_amount;
        $result['qty']          = $qty;
        $result['weight']       = number_format((float)$weight, 1, '.', '');
        $result['tax']          = number_format((float)$taxed, 2, '.', '');
        $result['adjust_price'] = number_format((float)$adjust_price, 2, '.', '');
        $result['term']         = $this->TermDays;
        $result['ptweight']     = $ptweight;
        return $result;


    }

    public function rDeliveryStatus()
    {
        return $this->belongsTo(DeliveryStatus::class,'delivery_status');
    }

    public function getDeliveryStatusNameAttribute()
    {
        return $this->rDeliveryStatus != null?$this->rDeliveryStatus->name:'';
    }
    public function getDeliveryStatusLabelAttribute()
    {
        if($this->status < 4)
            return 'Not Delivered';

        switch($this->status)
        {
            case 4:
                return 'Delivered';
            case 5:
                return 'Returned';
            case 6:
                return 'Partial Rejected';
            default:
                return 'Not Delivered';
        }
    }
    public function getFinancialTotalInfoAttribute()
    {
        $paidSubTotal   = 0;
        $paidTax        = 0;
        $logs = [];
        $logs['subTotal'] = [];
        $logs['tax'] = [];
        //fetch verified payments
        foreach($this->PaymentLog as $item)
        {
            if($item->allowed == 1)
            {
                $paidTax += $item->type == 1?$item->amount:0;
                $paidSubTotal += $item->type == 2?$item->amount:0;
            }
            $temp = [];
            $temp['amount'] = $item->amount;
            $temp['id'] = $item->id;
            $temp['date'] = $item->updated_at->format('m/d/Y');
            $temp['hDate'] =  $item->updated_at->format('m/d/Y H:i:s');
            $temp['deliveryerName'] = $item->d_personame;
            $temp['sign_filename'] = $item->sign_filename;
            $temp['allowed'] = $item->allowed;
            if($item->type == 1)
            {
                $temp['type'] = 'Tax';
                $logs['tax'][] = $temp;
            }
            else
            {
                $temp['type'] = 'Sub Total';
                $logs['subTotal'][] = $temp;
            }
        }
        $temp = $this->PaymentLog()->
                    whereRaw('Date(created_at) = ?',date('Y-m-d'))->get();
        $todayLog = [];
        $todayLog['subTotal'] = [];
        $todayLog['tax'] = [];
        foreach($temp as $item)
        {
            $tmp = [];
            $tmp['amount'] = $item->amount;
            $tmp['date'] = date('Y-m-d',strtotime($item->updated_at));
            $tmp['allowed'] = $item->allowed;
            if($item->type == 1)
            {
                $todayLog['tax'][] = $tmp;
            }
            else
            {
                $todayLog['subTotal'][] = $tmp;
            }
        }
        $totalInfo = $this->TotalInfo;
        $result = [];
        $result['tLog'] = $todayLog;
        $result['pSubTotal'] = $paidSubTotal;
        $result['pTax']      = $paidTax;
        $result['rSubTotal'] = number_format((float)$totalInfo['extended'] - $paidSubTotal, 2, '.', '');;
        $result['rTax'] = number_format((float)$totalInfo['tax'] - $paidTax, 2, '.', '');;
        $result['logs'] = $logs;
        $result['oTotal'] = $totalInfo['adjust_price'];
        $result['taxCompleted'] = $result['rTax'] <= 0?1:0;
        $result['subTotalCompleted'] = $result['rSubTotal'] <= 0?1:0;
        $result['completed'] = $result['subTotalCompleted'] && $result['taxCompleted']?1:0;
        return $result;
    }

    public function getSalesEmailAttribute()
    {
        return $this->salesperson != null?$this->salesperson->email:null;
    }
    public function get_items_for_fullfilled_list()
    {
        $this['shipping_method'] = $this->shipping_method()->with('carrier')->get();

        $this->clientname   = $this->CName;
        $this->companyname  = $this->CPName;
        $this->salesemail   = $this->SalesEmail;
        $this->total_info   = $this->TotalInfo;
        $this->total_financial = $this->FinancialTotalInfo;
        $this->companyemail = $this->customer != null?$this->customer->companyemail:'No';

        $this->items = $this->fulfilledItem()
                            ->whereIn('m_parent_id',[0,-1])->with(['asset','ap_item'])->get();
        if(count($this->items) == 0)
        {
            $this->items = $this->itemAP;
            foreach($this['items'] as $item)
            {
                $item->description  = $item->StrainLabel.','.$item->PTypeLabel;
                $item->coa          = '';
                $item->qty          = $item->qty;
                $item->units        = $item->units;
                $item->weight       = 0;
                $item->unit_price   = $item->unit_price;
                $item->cpu          = $item->CPU;
                $item->base_price   = $item->BasePrice;
                $item->extended     = $item->Extended;
                $item->discount     = $item->discount;
                $item->discount_label= $item->DisType;
                $item->tax          = $item->tax;
                $item->tax_note     = $item->TNote;
                $item->adjust_price = $item->AdjustPrice;
            }
        }
        else
        {
            foreach($this['items'] as $item)
            {
                $item->description  = $item->asset->Description;
                $item->coa          = $item->asset->coa;
                if($item->m_parent_id == -1)
                    $item->qty          = $item->ap_item->qty;
                else
                    $item->qty          = 1;

                $item->units        = $item->DividedUnit;
                $item->weight       = $item->asset->weight;
                $item->unit_price   = $item->ap_item->unit_price;
                $item->cpu          = $item->ap_item->CPU;
                $item->unit_label   = $item->UnitLabel;
                $item->base_price   = $item->DividedBasePrice;
                $item->extended     = $item->DividedExtended;
                $item->discount     = $item->DividedDiscount;
                $item->discount_label= $item->ap_item->DisType;
                $item->tax          = $item->DividedTax;
                $item->tax_note     = $item->ap_item->TNote;
                $item->adjust_price = $item->DividedAdjustPrice;
            }
        }

    }

    public function getFulfilledItems()
    {
        $items = $this->fulfilledItem()
                        ->whereIn('m_parent_id',[0,-1])->with(['asset','ap_item'])->get();
        $result = [];
        if(count($items) == 0)
        {
            $items = $this->itemAP;
            foreach($items as $item)
            {
                $temp = [];
                $temp['description']  = $item->StrainLabel.','.$item->PTypeLabel;
                $temp['coa']          = '';
                $temp['qty']          = $item->qty;
                $temp['units']        = $item->units;
                $temp['weight']       = 0;
                $temp['unit_price']   = $item->unit_price;
                $temp['cpu']          = $item->CPU;
                $temp['base_price']   = $item->BasePrice;
                $temp['extended']     = $item->Extended;
                $temp['discount']     = $item->discount;
                $temp['discount_label']= $item->DisType;
                $temp['e_discount']   = $item->e_discount;
                $temp['tax']          = $item->tax;
                $temp['tax_note']     = $item->TNote;
                $temp['adjust_price'] = $item->AdjustPrice;
                $result[] = $temp;
            }
        }
        else
        {
            foreach($items as $item)
            {
                $temp['description']  = $item->asset->Description;
                $temp['coa']          = $item->asset->coa;
                if($item->m_parent_id == -1)
                    $temp['qty']          = $item->ap_item->qty;
                else
                    $temp['qty']          = 1;

                $temp['units']          = $item->DividedUnit;
                $temp['weight']         = $item->asset->weight;
                $temp['unit_price']     = $item->ap_item != null?$item->ap_item->unit_price:0;
                $temp['cpu']            =  $item->ap_item != null?$item->ap_item->CPU:0;
                $temp['unit_label']     = $item->UnitLabel;
                $temp['base_price']     = $item->DividedBasePrice;
                $temp['extended']       = $item->DividedExtended;
                $temp['discount']       = $item->DividedDiscount;
                $temp['discount_label'] =  $item->ap_item != null?$item->ap_item->DisType:0;
                $temp['e_discount']     = $item->DividedEDiscount;
                $temp['tax']            = $item->DividedTax;
                $temp['tax_note']       =  $item->ap_item != null?$item->ap_item->TNote:0;
                $temp['adjust_price']   = $item->DividedAdjustPrice;
                $result[] = $temp;
            }
        }
        return $result;
    }
}
