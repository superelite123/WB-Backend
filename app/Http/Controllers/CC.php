<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use GroceryCrud\Core\GroceryCrud;

use App\Models\Asset;
use App\Models\Batch;
use App\Models\Producttype;
use DB;
use App\Models\Artist;
use App\Models\Counter;
use App\Models\AssetPotal;
use App\Models\Unit;
use App\Models\FGInventory;
use App\Models\HoldingInventory;
use App\Models\UPController;
use App\Models\Strainame;
use App\Models\HarvestTracker;
use App\Models\Harvest;
use App\Models\HarvestDynamics;
use App\Models\HarvestDry;
use App\Models\HarvestHistory;
use App\Models\Curning;
use App\Models\InventoryVault;
use Picqer;
class CC extends Controller
{
    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    private $coa_path = 'assets/upload/files/coa/';
    private function _getDatabaseConnection() {
        $databaseConnection = config('database.default');
        $databaseConfig = config('database.connections.' . $databaseConnection);
        return [
            'adapter' => [
                'driver' => 'Pdo_Mysql',
                'database' => $databaseConfig['database'],
                'username' => $databaseConfig['username'],
                'password' => $databaseConfig['password'],
                'charset' => 'utf8'
            ]
        ];
    }
    private function _getDatabaseConnection2() {
        $databaseConnection = config('database.default');
        $databaseConfig = config('database.connections.mysql2' );
        return [
            'adapter' => [
                'host'  => $databaseConfig['host'],
                'driver' => 'Pdo_Mysql',
                'database' => $databaseConfig['database'],
                'username' => $databaseConfig['username'],
                'password' => $databaseConfig['password'],
                'charset' => 'utf8'
            ]
        ];
    }
    /**
     * Show the datagrid for customers
     *
     * @return \Illuminate\Http\Response
     */


                  public function promo()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('promo');
        $crud->setSubject('Promo', 'Promo');
        $crud->displayAs(array('multiplier' => '% Discount'));
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }
          public function pricematrix()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('pricing_customer');
        $crud->setSubject('Price to Customer', 'Price Matrix');
  //      $crud->unsetColumns(['datelastmodified']);
        $crud->unsetFields(['datelastmodified']);
        $crud->setRelation('customer_id','customers','clientname');
        $crud->setRelation('product_id','productcategory','producttype');
        $crud->displayAs(array('product_id' => 'Product', 'customer_id' => 'Client/Customer', 'price' => 'Price per Unit', 'datelastmodified' => 'Creation Date'));
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

    public function fginventory()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('fginventory');
        $crud->setSubject('a Finished Goods Item, over-riding Process Path', 'ActiveFinished Goods for Sale');
        $crud->columns(['parent_id','metrc_tag', 'strainname', 'asset_type_id', 'upc_fk','coa', 'qtyonhand','status','weight','um','harvested_date' ,'created_at']);
        $crud->setFieldUpload('stockimage', 'assets/upload/files/inv', 'assets/upload/files/inv');
        $crud->fields(['parent_id','metrc_tag','strainname','asset_type_id','upc_fk','weight','um','qtyonhand','harvested_date','status']);
        $crud->setFieldUpload('coa', 'assets/upload/files/coa', 'assets/upload/files/coa');
        $crud->setRelation('coafile_fk','batch','coafile');
        $crud->setRelation('upc_fk','invupccont','upc');
        $crud->setRelation('strainname','strainname','strain');
        $crud->setRelation('asset_type_id','productcategory','producttype');
        $crud->uniqueFields(array('metrc_tag'));
        $crud->displayAs(array('parent_id' => 'Parent Harvest Batch ID', 'stockimage' => 'Stock Image', 'weight' => 'Total Weight of Metrc',
                               'strainname' => 'Strain', 'upc_fk' => 'Pre Built UPC',
                               'coa' => 'COA File', 'qtyonhand' => 'Quantity Avaialble', 'asset_type_id' => 'Type', 'status' => 'Active'));
        $crud->setRelation('parent_id','harvests','harvest_batch_id');
        $crud->setRelation('batch_fk','batch','{status},SID {sampleid} | {batchid}');
        $crud->callbackAddForm(function ($data) {
           $data['um'] = '4';
           $data['qtyonhand'] = '1';
          return $data;
        });
        $crud->fieldType('status', 'dropdown_search', [
            '2' => 'No',
            '1' => 'Yes',
         ]);
   //     $crud->unsetDelete();
        $crud->where([
            'fginventory.status' => 1
        ]);
        $crud->requiredFields(['parent_id','metrc_tag','strainname','asset_type_id','upc_fk','weight','qtyonhand','harvested_date']);
        $crud->setRelation('um','units','{name} - {abbriviation}');
        $crud->callbackColumn('stockimage', function ($value, $row) {
            if($value == "")
                return "No StockImage";
            return "<img src='".url("/")."/assets/upload/files/inv/" . $value . "' height=50>";
        });
        $crud->callbackColumn('upc_fk', function ($value, $row) {
            $upc = UPController::find($value);
            if($upc != null)
            {
                $strain = $upc->Strain != null?$upc->Strain->strain:'No Strain';
                $type  = $upc->p_type != null?$upc->p_type->producttype:'No Product type';
                return $upc->upc.'-'.$strain.'-'.$type;
            }
            return 'No UPC';
        });
        $crud->callbackEditField('upc_fk', function ($value, $primary_key) {

            $upcs = UPController::all();
            $arr = [];
            foreach($upcs as $upc)
            {
                $t = [];
                $t['iteminv_id'] = $upc->iteminv_id;
                $t['upc'] = $upc->upc;
                $t['strain'] = $upc->Strain != null?$upc->Strain->strain:'No Strain';
                $t['p_type'] = $upc->p_type != null?$upc->p_type->producttype:'No Product type';
                $arr[] = $t;
            }
            $strain  = array_column($arr, 'strain');
            $p_type  = array_column($arr, 'p_type');
            array_multisort($strain, SORT_ASC, $p_type, SORT_ASC, $arr);

            $html  = '<select class="form-control select2" style="width: 100%;" name="upc_fk">';
            $html .= '<option value="0"></option>';
            foreach($arr as $item)
            {
                $selected = $item['iteminv_id'] == $value?'selected':'';
                $html .= '<option value="'.$item['iteminv_id'].'" '.$selected.'>';
                $html .= $item['strain'].'-'.$item['p_type'].'</option>';
            }

            $html .= '</select>';
            return $html;
        });
        $crud->callbackAddField('upc_fk', function ($value, $primary_key) {
            $upcs = UPController::all();
            $arr = [];
            foreach($upcs as $upc)
            {
                $t = [];
                $t['iteminv_id'] = $upc->iteminv_id;
                $t['upc'] = $upc->upc;
                $t['strain'] = $upc->Strain != null?$upc->Strain->strain:'No Strain';
                $t['p_type'] = $upc->p_type != null?$upc->p_type->producttype:'No Product type';
                $arr[] = $t;
            }
            $strain  = array_column($arr, 'strain');
            $p_type  = array_column($arr, 'p_type');
            array_multisort($strain, SORT_ASC, $p_type, SORT_ASC, $arr);

            $html  = '<select class="form-control select2" style="width: 100%;" name="upc_fk">';
            $html .= '<option value="0"></option>';
            foreach($arr as $item)
            {
                $html .= '<option value="'.$item['iteminv_id'].'">';
                $html .= $item['strain'].'-'.$item['p_type'].'</option>';
            }
            $html .= '</select>';
            return $html;
        });
        $crud->callbackColumn('coa', function ($value, $row) {
            $batch_fk = Harvest::find($row['parent_id']);
            $batch_fk = $batch_fk != null?$batch_fk->harvest_batch_id:'No';

            $file = $batch_fk.'_COA.pdf';
            //$file = $row['batch_fk'].'_COA.pdf';
            $href = '<a href="'.$this->coa_path.$file.'" target="_blank">'.$file.'</a>';

            if($value != "")
            {
                $file = $value;
                $href = '<a href="'.$this->coa_path.$file.'" target="_blank">'.$file.'</a>';
            }

            return $href;
        });
        $crud->unsetColumns(['updated_at']);
        $crud->unsetFields(['created_at','updated_at','datelastmodified']);

        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }


    //Status Modifier
       public function fgmodifystatus()
     {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('fginventory');
        $crud->setSubject('a Finished Goods Item, over-riding Process Path', 'ActiveFinished Goods for Sale');
        $crud->columns(['parent_id','metrc_tag', 'strainname', 'asset_type_id', 'upc_fk','coa', 'qtyonhand','status','weight','um','harvested_date' ,'created_at']);
        $crud->setFieldUpload('stockimage', 'assets/upload/files/inv', 'assets/upload/files/inv');
        $crud->fields(['parent_id','metrc_tag','strainname','asset_type_id','status','weight','um','qtyonhand','harvested_date']);
        $crud->setFieldUpload('coa', 'assets/upload/files/coa', 'assets/upload/files/coa');
        $crud->setRelation('coafile_fk','batch','coafile');
        $crud->setRelation('upc_fk','invupccont','upc');
        $crud->setRelation('strainname','strainname','strain');
        $crud->setRelation('asset_type_id','productcategory','producttype');
        $crud->uniqueFields(array('metrc_tag'));
        $crud->displayAs(array('parent_id' => 'Parent Harvest Batch ID', 'stockimage' => 'Stock Image', 'weight' => 'Total Weight of Metrc',
                               'strainname' => 'Strain', 'upc_fk' => 'Pre Built UPC',
                               'coa' => 'COA File', 'qtyonhand' => 'Quantity Avaialble', 'asset_type_id' => 'Type', 'status' => 'Active'));
        $crud->setRelation('parent_id','harvests','harvest_batch_id');
        $crud->setRelation('batch_fk','batch','{status},SID {sampleid} | {batchid}');
        $crud->readOnlyFields(['parent_id','metrc_tag','strainname','asset_type_id','upc_fk','weight','um','qtyonhand','harvested_date']);
        $crud->callbackAddForm(function ($data) {
           $data['um'] = '4';
           $data['qtyonhand'] = '1';
          return $data;
        });
        $crud->fieldType('status', 'dropdown_search', [
            '2' => 'No',
            '1' => 'Yes',
         ]);
        $crud->unsetDelete();
        $crud->unsetAdd();
   //     $crud->where([
//            'fginventory.status' => 2
  //      ]);
  //      $crud->requiredFields(['parent_id','metrc_tag','strainname','asset_type_id','upc_fk','weight','qtyonhand','harvested_date']);
        $crud->setRelation('um','units','{name} - {abbriviation}');
        $crud->callbackColumn('stockimage', function ($value, $row) {
            if($value == "")
                return "No StockImage";
            return "<img src='".url("/")."/assets/upload/files/inv/" . $value . "' height=50>";
        });
        $crud->callbackColumn('upc_fk', function ($value, $row) {
            $upc = UPController::find($value);
            if($upc != null)
            {
                $strain = $upc->Strain != null?$upc->Strain->strain:'No Strain';
                $type  = $upc->p_type != null?$upc->p_type->producttype:'No Product type';
                return $upc->upc.'-'.$strain.'-'.$type;
            }
            return 'No UPC';
        });
        $crud->callbackEditField('upc_fk', function ($value, $primary_key) {

            $upcs = UPController::all();
            $arr = [];
            foreach($upcs as $upc)
            {
                $t = [];
                $t['iteminv_id'] = $upc->iteminv_id;
                $t['upc'] = $upc->upc;
                $t['strain'] = $upc->Strain != null?$upc->Strain->strain:'No Strain';
                $t['p_type'] = $upc->p_type != null?$upc->p_type->producttype:'No Product type';
                $arr[] = $t;
            }
            $strain  = array_column($arr, 'strain');
            $p_type  = array_column($arr, 'p_type');
            array_multisort($strain, SORT_ASC, $p_type, SORT_ASC, $arr);

            $html  = '<select class="form-control select2" style="width: 100%;" name="upc_fk">';
            $html .= '<option value="0"></option>';
            foreach($arr as $item)
            {
                $selected = $item['iteminv_id'] == $value?'selected':'';
                $html .= '<option value="'.$item['iteminv_id'].'" '.$selected.'>';
                $html .= $item['strain'].'-'.$item['p_type'].'</option>';
            }

            $html .= '</select>';
            return $html;
        });
        $crud->callbackAddField('upc_fk', function ($value, $primary_key) {
            $upcs = UPController::all();
            $arr = [];
            foreach($upcs as $upc)
            {
                $t = [];
                $t['iteminv_id'] = $upc->iteminv_id;
                $t['upc'] = $upc->upc;
                $t['strain'] = $upc->Strain != null?$upc->Strain->strain:'No Strain';
                $t['p_type'] = $upc->p_type != null?$upc->p_type->producttype:'No Product type';
                $arr[] = $t;
            }
            $strain  = array_column($arr, 'strain');
            $p_type  = array_column($arr, 'p_type');
            array_multisort($strain, SORT_ASC, $p_type, SORT_ASC, $arr);

            $html  = '<select class="form-control select2" style="width: 100%;" name="upc_fk">';
            $html .= '<option value="0"></option>';
            foreach($arr as $item)
            {
                $html .= '<option value="'.$item['iteminv_id'].'">';
                $html .= $item['strain'].'-'.$item['p_type'].'</option>';
            }
            $html .= '</select>';
            return $html;
        });
        $crud->callbackColumn('coa', function ($value, $row) {
            $batch_fk = Harvest::find($row['parent_id']);
            $batch_fk = $batch_fk != null?$batch_fk->harvest_batch_id:'No';

            $file = $batch_fk.'_COA.pdf';
            //$file = $row['batch_fk'].'_COA.pdf';
            $href = '<a href="'.$this->coa_path.$file.'" target="_blank">'.$file.'</a>';

            if($value != "")
            {
                $file = $value;
                $href = '<a href="'.$this->coa_path.$file.'" target="_blank">'.$file.'</a>';
            }

            return $href;
        });
        $crud->unsetColumns(['updated_at']);
        $crud->unsetFields(['created_at','updated_at','datelastmodified']);

        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

    public function delivery_method()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('delivery');
        $crud->setSubject('Delivery Method', 'Delivery Method');
        $crud->columns(['username','van','ca_dl_id']);
        $crud->displayAs(array(
            'username' => 'Name',
            'van'       => 'Registration',
            'ca_dl_id' =>  'CA DL ID'
        ));
        $crud->unsetColumns(['datelastmodified']);
        $crud->unsetFields(['datelastmodified']);
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }
     public function vaultmodifystatus()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
                $crud->setTable('inventory_vault');
        $crud->setSubject('a Bulk Item, over-riding Process Path', 'Active Inventory 1 Bulk for Sale');
        $crud->columns(['parent_id','metrc_tag','strainname', 'asset_type_id', 'upc_fk','coa', 'qtyonhand','status','weight','um','status', 'created_at']);
        $crud->setFieldUpload('stockimage', 'assets/upload/files/inv', 'assets/upload/files/inv');
        $crud->fields(['parent_id','metrc_tag','strainname','asset_type_id','status','weight','um','qtyonhand','harvested_date']);
        $crud->setFieldUpload('coa', 'assets/upload/files/coa', 'assets/upload/files/coa');
        $crud->setRelation('coafile_fk','batch','coafile');
        $crud->setRelation('upc_fk','invupccont','upc');
        $crud->setRelation('strainname','strainname','strain');
        $crud->setRelation('asset_type_id','productcategory','producttype');
        $crud->uniqueFields(array('metrc_tag'));
        $crud->unsetAdd();
        $crud->readOnlyFields(['parent_id','metrc_tag','strainname','asset_type_id','upc_fk','weight','um','qtyonhand','harvested_date']);
        $crud->displayAs(array('parent_id' => 'Parent Harvest Batch ID', 'stockimage' => 'Stock Image', 'weight' => 'Total Weight of Metrc',
                               'strainname' => 'Strain', 'upc_fk' => 'Pre Built UPC',
                               'coa' => 'COA File', 'qtyonhand' => 'Quantity Avaialble', 'asset_type_id' => 'Type', 'status' => 'Active'));
        $crud->setRelation('parent_id','harvests','harvest_batch_id');
        $crud->setRelation('batch_fk','batch','{status},SID {sampleid} | {batchid}');
   //     $crud->where([
//            'inventory_vault.status' => 2
  //      ]);
   //     $crud->unsetDelete();
        $crud->callbackAddForm(function ($data) {
           $data['um'] = '4';
           $data['qtyonhand'] = '1';
          return $data;
        });
         $crud->fieldType('status', 'dropdown_search', [
            '2' => 'No',
            '1' => 'Yes',
         ]);
        $crud->callbackColumn('stockimage', function ($value, $row) {
            if($value == "")
                return "No StockImage";
            return "<img src='".url("/")."/assets/upload/files/inv/" . $value . "' height=50>";
        });
        $crud->callbackColumn('upc_fk', function ($value, $row) {
            $upc = UPController::find($value);
            if($upc != null)
            {
                $strain = $upc->Strain != null?$upc->Strain->strain:'No Strain';
                $type  = $upc->p_type != null?$upc->p_type->producttype:'No Product type';
                return $upc->upc.'-'.$strain.'-'.$type;
            }
            return 'No UPC';
        });
        $crud->callbackEditField('upc_fk', function ($value, $primary_key) {
            $upcs = UPController::all();
            $arr = [];
            foreach($upcs as $upc)
            {
                $t = [];
                $t['iteminv_id'] = $upc->iteminv_id;
                $t['upc'] = $upc->upc;
                $t['strain'] = $upc->Strain != null?$upc->Strain->strain:'No Strain';
                $t['p_type'] = $upc->p_type != null?$upc->p_type->producttype:'No Product type';
                $arr[] = $t;
            }
            $strain  = array_column($arr, 'strain');
            $p_type  = array_column($arr, 'p_type');
            array_multisort($strain, SORT_ASC, $p_type, SORT_ASC, $arr);

            $html  = '<select class="form-control select2" style="width: 100%;" name="upc_fk">';
            $html .= '<option value="0"></option>';
            foreach($arr as $item)
            {
                $selected = $item['iteminv_id'] == $value?'selected':'';
                $html .= '<option value="'.$item['iteminv_id'].'" '.$selected.'>';
                $html .= $item['strain'].'-'.$item['p_type'].'</option>';
            }


            $html .= '</select>';
            return $html;
        });
        $crud->callbackAddField('upc_fk', function ($value, $primary_key) {
            $upcs = UPController::all();
            $arr = [];
            foreach($upcs as $upc)
            {
                $t = [];
                $t['iteminv_id'] = $upc->iteminv_id;
                $t['upc'] = $upc->upc;
                $t['strain'] = $upc->Strain != null?$upc->Strain->strain:'No Strain';
                $t['p_type'] = $upc->p_type != null?$upc->p_type->producttype:'No Product type';
                $arr[] = $t;
            }
            $strain  = array_column($arr, 'strain');
            $p_type  = array_column($arr, 'p_type');
            array_multisort($strain, SORT_ASC, $p_type, SORT_ASC, $arr);

            $html  = '<select class="form-control select2" style="width: 100%;" name="upc_fk">';
            $html .= '<option value="0"></option>';
            foreach($arr as $item)
            {
                $html .= '<option value="'.$item['iteminv_id'].'">';
                $html .= $item['strain'].'-'.$item['p_type'].'</option>';
            }
            $html .= '</select>';
            return $html;
        });
        $crud->callbackColumn('coa', function ($value, $row) {
            $batch_fk = Harvest::find($row['parent_id']);
            $batch_fk = $batch_fk != null?$batch_fk->harvest_batch_id:'No';

            $file = $batch_fk.'_COA.pdf';
            //$file = $row['batch_fk'].'_COA.pdf';
            $href = '<a href="'.$this->coa_path.$file.'" target="_blank">'.$file.'</a>';

            if($value != "")
            {
                $file = $value;
                $href = '<a href="'.$this->coa_path.$file.'" target="_blank">'.$file.'</a>';
            }

            return $href;
        });
        $crud->unsetColumns(['updated_at']);
        $crud->unsetFields(['created_at','updated_at','datelastmodified']);

        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }


     public function vaultinventory()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
                $crud->setTable('inventory_vault');
        $crud->setSubject('a Bulk Item, over-riding Process Path', 'Active Inventory 1 Bulk for Sale');
        $crud->columns(['parent_id','metrc_tag','strainname', 'asset_type_id', 'upc_fk','coa', 'qtyonhand','status','weight','um','status', 'created_at']);
        $crud->setFieldUpload('stockimage', 'assets/upload/files/inv', 'assets/upload/files/inv');
        $crud->fields(['parent_id','metrc_tag','strainname','asset_type_id','upc_fk','weight','um','qtyonhand','status','harvested_date']);
        $crud->setFieldUpload('coa', 'assets/upload/files/coa', 'assets/upload/files/coa');
        $crud->setRelation('coafile_fk','batch','coafile');
        $crud->setRelation('upc_fk','invupccont','upc');
        $crud->setRelation('strainname','strainname','strain');
        $crud->setRelation('asset_type_id','productcategory','producttype');
        $crud->uniqueFields(array('metrc_tag'));
        $crud->displayAs(array('parent_id' => 'Parent Harvest Batch ID', 'stockimage' => 'Stock Image', 'weight' => 'Total Weight of Metrc',
                               'strainname' => 'Strain', 'upc_fk' => 'Pre Built UPC',
                               'coa' => 'COA File', 'qtyonhand' => 'Quantity Avaialble', 'asset_type_id' => 'Type', 'status' => 'Active'));
        $crud->setRelation('parent_id','harvests','harvest_batch_id');
        $crud->setRelation('batch_fk','batch','{status},SID {sampleid} | {batchid}');
        $crud->where([
            'inventory_vault.status' => 1
        ]);
        $crud->unsetDelete();
        $crud->callbackAddForm(function ($data) {
           $data['um'] = '4';
           $data['qtyonhand'] = '1';
          return $data;
        });
         $crud->fieldType('status', 'dropdown_search', [
            '2' => 'No',
            '1' => 'Yes',
         ]);
        $crud->callbackColumn('stockimage', function ($value, $row) {
            if($value == "")
                return "No StockImage";
            return "<img src='".url("/")."/assets/upload/files/inv/" . $value . "' height=50>";
        });
        $crud->callbackColumn('upc_fk', function ($value, $row) {
            $upc = UPController::find($value);
            if($upc != null)
            {
                $strain = $upc->Strain != null?$upc->Strain->strain:'No Strain';
                $type  = $upc->p_type != null?$upc->p_type->producttype:'No Product type';
                return $upc->upc.'-'.$strain.'-'.$type;
            }
            return 'No UPC';
        });
        $crud->callbackEditField('upc_fk', function ($value, $primary_key) {
            $upcs = UPController::all();
            $arr = [];
            foreach($upcs as $upc)
            {
                $t = [];
                $t['iteminv_id'] = $upc->iteminv_id;
                $t['upc'] = $upc->upc;
                $t['strain'] = $upc->Strain != null?$upc->Strain->strain:'No Strain';
                $t['p_type'] = $upc->p_type != null?$upc->p_type->producttype:'No Product type';
                $arr[] = $t;
            }
            $strain  = array_column($arr, 'strain');
            $p_type  = array_column($arr, 'p_type');
            array_multisort($strain, SORT_ASC, $p_type, SORT_ASC, $arr);

            $html  = '<select class="form-control select2" style="width: 100%;" name="upc_fk">';
            $html .= '<option value="0"></option>';
            foreach($arr as $item)
            {
                $selected = $item['iteminv_id'] == $value?'selected':'';
                $html .= '<option value="'.$item['iteminv_id'].'" '.$selected.'>';
                $html .= $item['strain'].'-'.$item['p_type'].'</option>';
            }


            $html .= '</select>';
            return $html;
        });
        $crud->callbackAddField('upc_fk', function ($value, $primary_key) {
            $upcs = UPController::all();
            $arr = [];
            foreach($upcs as $upc)
            {
                $t = [];
                $t['iteminv_id'] = $upc->iteminv_id;
                $t['upc'] = $upc->upc;
                $t['strain'] = $upc->Strain != null?$upc->Strain->strain:'No Strain';
                $t['p_type'] = $upc->p_type != null?$upc->p_type->producttype:'No Product type';
                $arr[] = $t;
            }
            $strain  = array_column($arr, 'strain');
            $p_type  = array_column($arr, 'p_type');
            array_multisort($strain, SORT_ASC, $p_type, SORT_ASC, $arr);

            $html  = '<select class="form-control select2" style="width: 100%;" name="upc_fk">';
            $html .= '<option value="0"></option>';
            foreach($arr as $item)
            {
                $html .= '<option value="'.$item['iteminv_id'].'">';
                $html .= $item['strain'].'-'.$item['p_type'].'</option>';
            }
            $html .= '</select>';
            return $html;
        });
        $crud->callbackColumn('coa', function ($value, $row) {
            $batch_fk = Harvest::find($row['parent_id']);
            $batch_fk = $batch_fk != null?$batch_fk->harvest_batch_id:'No';

            $file = $batch_fk.'_COA.pdf';
            //$file = $row['batch_fk'].'_COA.pdf';
            $href = '<a href="'.$this->coa_path.$file.'" target="_blank">'.$file.'</a>';

            if($value != "")
            {
                $file = $value;
                $href = '<a href="'.$this->coa_path.$file.'" target="_blank">'.$file.'</a>';
            }

            return $href;
        });
        $crud->unsetColumns(['updated_at']);
        $crud->unsetFields(['created_at','updated_at','datelastmodified']);

        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }


        public function bugtracker()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('bugtracker');
        $crud->setSubject('Bug Tracker', 'Submit a bug');
        $crud->setRelation('strain_id','strainname','strain');
        $crud->displayAs(array('steps' => 'How can you re create this issue', 'page' => 'Page or Module you are on'));
        $crud->unsetFields(['datecreated']);
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

        public function coalibrary()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('coalibrary');
        $crud->setSubject('COA to Library', 'COA File upload Portal');
        $crud->setFieldUpload('urllocation', 'assets/upload/files/coa', 'assets/upload/files/coa');
        $crud->unsetFields(['datecreated','datemodified']);
        $crud->displayAs(array('urllocation' => 'File Name', 'datecreated' => 'Date Created', 'datemodified' => 'Last Modified'));
        $crud->unsetDelete();
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }
        public function harvestdata()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('harvests');
        $crud->setSubject('View Harvests', 'View Harvest');
        $crud->columns(['id','harvest_batch_id', 'total_weight', 'strain_id']);
        $crud->setRelation('strain_id','strainname','strain');
        $crud->readOnlyFields(['id','harvest_batch_id','strain_id','created_at']);
        $crud->unsetFields(['unit_weight','cultivator_company_id','complete','cultivator_license_id','status','thc_pct','cbd_pct','tcana','samplesize','updated_at','archived']);
        //$crud->displayAs(array('steps' => 'How can you re create this issue', 'page' => 'Page or Module you are on'));
        //$crud->unsetFields(['datecreated']);
        $crud->unsetDelete();
        $crud->unsetAdd();
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }
         public function harvestover()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('harvests');
        $crud->setSubject('new Harvest overriding process path', 'CAUTION HARVEST MODIFICATION');
       // $crud->columns(['id','harvest_batch_id', 'total_weight', 'strain_id']);
        $crud->setRelation('strain_id','strainname','strain');
        $crud->setRelation('unit_weight','units','{name} - {abbriviation}');
         $crud->setRelation('cultivator_company_id','location_area','{name} - {location_id}');
         $crud->setRelation('cultivator_license_id','cultivator','companyname');
     //   $crud->readOnlyFields(['id','harvest_batch_id','strain_id','created_at']);
        $crud->unsetFields(['created_at','updated_at','archived','complete','status']);
        $crud->displayAs(array('cultivator_company_id' => 'Harvest Room ID'));
        //$crud->unsetFields(['datecreated']);
        $crud->unsetDelete();
        //$crud->unsetAdd();
        $crud->callbackAddForm(function ($data) {
           $data['cultivator_license_id'] = '5';
           $data['unit_weight'] = '4';
          return $data;
        });
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

            public function harvestitem()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('harvest_item');
        $crud->setSubject('View items', 'View Harvest');
        $crud->columns(['id','harvest_id','plant_tag', 'weight', 'created_at']);
        $crud->setRelation('harvest_id','harvests','harvest_batch_id');
        $crud->readOnlyFields(['harvest_id','plant_tag','created_at']);
        $crud->unsetFields(['datelastmodified']);
        //$crud->displayAs(array('steps' => 'How can you re create this issue', 'page' => 'Page or Module you are on'));
        //$crud->unsetFields(['datecreated']);
        $crud->unsetDelete();
        $crud->unsetAdd();
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

     public function strainname()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('strainname');
        $crud->setSubject('Strain', 'Submit a strain');
        $crud->columns(['itemname_id','strain','strainalias', 'strainacro']);
        $crud->unsetFields(['datecreated', 'datelastmodified']);
        $crud->requiredFields(['base_price']);
        $crud->unsetDelete();
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }



    public function harvestdynamics()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTable('harvestdynamics');
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setRelation('unit_weight','units','{name}-{abbriviation}');
        $crud->setRelation('cultivator_company_id','location_area','{location_id} - {name}');
        $crud->setRelation('parent_id','harvests','harvest_batch_id');
        $crud->setRelation('strain_id','strainname','strain');
        $crud->setRelation('cultivator_license_id','cultivator','{companyname}-{license}');
        $crud->setActionButton('Sent to Dry weight', 'fa fa-user', function ($row) {
             return 'javascript:show:confirmSendToDryWeight("'.$row->id.'")';
        });
        $crud->readOnlyFields(['unit_weight', 'cultivator_company_id', 'total_weight', 'strain_id', 'cultivator_license_id', 'parent_id','day_recorded_date','created_at', 'updated_at']);
        $crud->unsetColumns(['created_at', 'updated_at', 'archived', 'complete']);
        $crud->unsetFields(['day_recorded_date','created_at', 'updated_at', 'archived', 'complete']);
        $crud->displayAs(array('parent_id' => 'Harvest Batch ID','cultivator_company_id' =>'Flower Room Location','trimroom_h2o' => 'Trim Room H20 Content %','dryroom_h2o'=>'Cure Room H20 Content %'));
        $crud->where([
            'harvestdynamics.archived' => 0
        ]);
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;

        return view('gc/harvestDynamics', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }



    public function _sendToDryWeightlist(Request $request)
    {
        $id = $request->id;
        $harvestDynamics = HarvestDynamics::find($id);
        $harvestDry = new HarvestDry;
        $harvestDry->parent_id = $harvestDynamics->id;
        $harvestDry->total_weight = $harvestDynamics->total_weight;
        $harvestDry->remain_weight = $harvestDynamics->total_weight;
        $harvestDry->unit_weight = $harvestDynamics->unit_weight;
        $harvestDry->cultivator_company_id = $harvestDynamics->cultivator_company_id;
        $harvestDry->strain_id = $harvestDynamics->strain_id;
        $harvestDry->cultivator_license_id = $harvestDynamics->cultivator_license_id;
        $harvestDry->save();

        $history = HarvestHistory::where('harvest_id',$harvestDynamics->parent_id)->first();
        $history->dry = date('Y-m-d');
        $history->save();

        $harvestDynamics->archived = 1;
        $harvestDynamics->save();

        return '1';
    }
//-------------------------HoldingInventory--------------------------------------
    public function holdinginventory()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('holdinginventory');
        $crud->setSubject('Inventory on Holding', 'Inventory on Holding');
        $crud->columns(['stockimage', 'batch_fk', 'strainname', 'asset_type_id', 'upc_fk','coa', 'qtyonhand','weight','um']);
        $crud->setFieldUpload('stockimage', 'assets/upload/files/inv', 'assets/upload/files/inv');
        $crud->setFieldUpload('coa', 'assets/upload/files/coa', 'assets/upload/files/coa');
        $crud->setRelation('coafile_fk','batch','coafile');
        $crud->setRelation('upc_fk','invupccont','upc');
        $crud->setRelation('strainname','strainname','strain');
        $crud->setRelation('asset_type_id','productcategory','producttype');
        //$crud->setRelation('batch_fk','harvests','harvest_batch_id');
        $crud->setRelation('um','units','{name} - {abbriviation}');
        $crud->displayAs(array('parent_id' => 'Parent Harvest Batch ID', 'stockimage' => 'Stock Image',
                               'strainname' => 'Strain', 'upc_fk' => 'Pre Built UPC',
                               'coa' => 'COA File', 'qtyonhand' => 'Quantity Avaialble', 'asset_type_id' => 'Type'));
        $crud->where(['archived' => 0]);
        $crud->callbackColumn('stockimage', function ($value, $row) {
            if($value == "")
                return "<img src='".url("/")."/assets/upload/files/inv/noimage.png' height=50>";
            return "<img src='".url("/")."/assets/upload/files/inv/" . $value . "' height=50>";
        });
        $crud->callbackColumn('upc_fk', function ($value, $row) {
            $upc = UPController::find($value);
            if($upc != null)
            {
                $strain = $upc->Strain != null?$upc->Strain->strain:'No Strain';
                $type  = $upc->p_type != null?$upc->p_type->producttype:'No Product type';
                return $upc->upc.'-'.$strain.'-'.$type;
            }
            return 'No UPC';
        });
        $crud->callbackEditField('upc_fk', function ($value, $primary_key) {

            $upcs = UPController::all();
            $arr = [];
            foreach($upcs as $upc)
            {
                $t = [];
                $t['iteminv_id'] = $upc->iteminv_id;
                $t['upc'] = $upc->upc;
                $t['strain'] = $upc->Strain != null?$upc->Strain->strain:'No Strain';
                $t['p_type'] = $upc->p_type != null?$upc->p_type->producttype:'No Product type';
                $arr[] = $t;
            }
            $strain  = array_column($arr, 'strain');
            $p_type  = array_column($arr, 'p_type');
            array_multisort($strain, SORT_ASC, $p_type, SORT_ASC, $arr);

            $html  = '<select class="form-control select2" style="width: 100%;" name="upc_fk">';
            $html .= '<option value="0"></option>';
            foreach($arr as $item)
            {
                $selected = $item['iteminv_id'] == $value?'selected':'';
                $html .= '<option value="'.$item['iteminv_id'].'" '.$selected.'>';
                $html .= $item['upc'].'-'.$item['strain'].'-'.$item['p_type'].'</option>';
            }

            $html .= '</select>';
            return $html;
        });
        $crud->callbackAddField('upc_fk', function ($value, $primary_key) {
            $upcs = UPController::all();
            $arr = [];
            foreach($upcs as $upc)
            {
                $t = [];
                $t['iteminv_id'] = $upc->iteminv_id;
                $t['upc'] = $upc->upc;
                $t['strain'] = $upc->Strain != null?$upc->Strain->strain:'No Strain';
                $t['p_type'] = $upc->p_type != null?$upc->p_type->producttype:'No Product type';
                $arr[] = $t;
            }
            $strain  = array_column($arr, 'strain');
            $p_type  = array_column($arr, 'p_type');
            array_multisort($strain, SORT_ASC, $p_type, SORT_ASC, $arr);

            $html  = '<select class="form-control select2" style="width: 100%;" name="upc_fk">';
            $html .= '<option value="0"></option>';
            foreach($arr as $item)
            {
                $html .= '<option value="'.$item['iteminv_id'].'">';
                $html .= $item['upc'].'-'.$item['strain'].'-'.$item['p_type'].'</option>';
            }
            $html .= '</select>';
            return $html;
        });
        //set ActionButton
        $crud->setActionButton('Send to Finished Goods','fa fa-user',function($row){
            $weight = $row->weight?$row->weight:0;
            $um = $row->um?$row->um:1;
            $unit = Unit::where('unit_id',$um)->first();
            return 'javascript:show:confirmDialog("'.$row->id.'")';
         },false);
         //set ActionButton
        $crud->setActionButton('Send to InventoryVault','fa fa-user',function($row){
            $weight = $row->weight?$row->weight:0;
            $um = $row->um?$row->um:1;
            $unit = Unit::where('unit_id',$um)->first();
            return 'javascript:show:confirmVaultDialog("'.$row->id.'")';
         },false);

        $crud->unsetColumns(['datelastmodified']);
        $crud->unsetFields(['datelastmodified','batch_fk', 'bestbefore', 'parent_id', 'archived']);
        $crud->callbackAddForm(function ($data) {$data['um'] = '4';return $data;});
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('harvest/holding_inventory', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

    public function sendHoldingToFG(Request $request)
    {
        $id = $request->id;
        DB::transaction(function () use ($id){
            $holdingInventory = HoldingInventory::find($id);
            $data = $holdingInventory->toarray();
            $holdingInventory->archived = 1;
            $holdingInventory->save();
            //remove id,archived
            unset($data['id']);
            unset($data['archived']);
            unset($data['created_at']);
            unset($data['updated_at']);
            $data['bestbefore'] = date('Y-m-d', strtotime('today - 31 days'));
            $data['datelastmodified'] = date('Y-m-d');
            $data['harvested_date'] = Harvest::find($holdingInventory->parent_id)->created_at;
            $inserted_data = FGInventory::insert($data);


            $history = HarvestHistory::where('harvest_id',$holdingInventory->parent_id)->first();
            $history->fg = date('Y-m-d');
            $history->save();
        });

        return 1;
    }

    public function sendHoldingToVault(Request $request)
    {
        $id = $request->id;
        DB::transaction(function () use ($id){
            $holdingInventory = HoldingInventory::find($id);
            $data = $holdingInventory->toarray();
            $holdingInventory->archived = 1;
            $holdingInventory->save();
            //remove id,archived
            unset($data['id']);
            unset($data['archived']);
            unset($data['created_at']);
            unset($data['updated_at']);
            $data['bestbefore'] = date('Y-m-d', strtotime('today - 31 days'));
            $data['datelastmodified'] = date('Y-m-d');
            $data['harvested_date'] = Harvest::find($holdingInventory->parent_id)->created_at;
            $key = Counter::where('key','vault_id')->first();
            $inserted_data = InventoryVault::insert($data);
            $key->increment('value');

            $history = HarvestHistory::where('harvest_id',$holdingInventory->parent_id)->first();
            $history->fg = date('Y-m-d');
            $history->save();
        });
        return $id;
    }

    public function _checkUpc(Request $request)
    {
        $item = Holdinginventory::find($request->id);
        $res1 = $item->upc_fk == null;
        $res2 = $item->coa == null;
        // if($res1 && $res2)
        // {
        //     return 3;
        // }
        // else{
            //if($res1) return 1;

            if($res2) return 2;

            return 0;
        //}

    }
//-------------------------./Holding Inventory-----------------------------------


    public function descriptions()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('batchdescription');
        $crud->setSubject('Description', 'Strain Description');
        $crud->unsetColumns(['datelastmodified']);
        $crud->unsetFields(['datelastmodified']);
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

    public function upccontroller()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('invupccont');
        $crud->setSubject('Strain-Type Matrix', 'Create Strain Type Matrix');
        $crud->columns(['iteminv_id','strain','type','upc','baseprice','taxexempt','um','weight']);
        $crud->displayAs(array('iteminv_id' => 'Item ID'));
        $crud->setRelation('um','units','{name} - {abbriviation}');
        $crud->setRelation('strain','strainname','{strain}');
       $crud->setRelation('type','productcategory','producttype');
        $crud->fieldType('taxexempt', 'dropdown_search', [
            '0' => 'No',
            '1' => 'Yes',
         ]);
                 $crud->fieldType('nevercombine', 'dropdown_search', [
            '0' => 'No',
            '1' => 'Yes',
         ]);
        $crud->displayAs(array('taxexempt' =>'Tax Exempt','nevercombine' => 'Never Combine'));
        $crud->uniqueFields(['upc']);
       // $crud->requiredFields(['strain_id', 'upc', 'producttype_id']);
        $crud->setFieldUpload('upcimage', '../wd/assets/upload/files/upc', '../../wd/assets/upload/files/upc');
        $crud->callbackAddForm(function ($data) {
           $data['um'] = '4';
           $data['taxexempt'] = '0';
           $data['nevercombine'] = '0';
          return $data;
        });
        $crud->callbackColumn('upc', function ($value, $row) {
            $generatorSVG = new \Picqer\Barcode\BarcodeGeneratorSVG();
            $upc = str_pad($value, 10, '0', STR_PAD_LEFT);

            $barcode_html =  ($generatorSVG->getBarcode($value, $generatorSVG::TYPE_UPC_A)) . ' ';
            $barcode_html .= '<br>';
            $barcode_html .= '<div style="text-align: center;font-size: xx-small;">'.$value.'</div>';
            return $barcode_html;
         });
        $crud->unsetColumns(['datelastmodified']);
        $crud->unsetFields(['datelastmodified','barcode']);
     //   $crud->unsetDelete();
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

       public function producttypes()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('productcategory');
        $crud->setSubject('Product', 'Products');
        $crud->columns(['producttype_id','producttype','weight_volume','units','producttype_desc','promovalue','promocost','onordermenu','pcategory','status' ,'tare']);
        $crud->fields(['producttype','producttype_desc','onordermenu' ,'units','promovalue','promocost','weight_volume','status','tare','pcategory']);
      //  $crud->unsetDelete();
        $crud->setRelation('pcategory','ptweight','type');
        $crud->displayAs(array(
            'producttype_name' => 'Name',
            'producttype_desc' => 'Description',
            'onordermenu' => 'Include on PO menu',
            'weight_volume' => 'Weight or Volume',
            'producttype_id' => 'ID',
            'producttype' => 'Product Type',
            'promovalue' => 'Base Price',
            'promocost' => 'Promo Cost',
            'status' => 'Active'
            ));
        $crud->fieldType('onordermenu', 'dropdown_search', [
            '0' => 'No',
            '1' => 'Yes',
         ]);
        $crud->fieldType('status', 'dropdown_search', [
            '2' => 'No',
            '1' => 'Yes',
         ]);
        $crud->callbackAddForm(function ($data) {
           $data['onordermenu'] = '1';
           $data['status'] = '1';
          return $data;
        });
        $crud->where([
            'productcategory.status' => 1
        ]);
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

     public function vendors()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('vendors');
        $crud->setSubject('Vendor', 'Vendors');
        $crud->setRelation('state','states','abbr');
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

    public function cultivator()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('cultivator');
        $crud->setSubject('Cultivator', 'Cultivator');
        $crud->setFieldUpload('licenseupload', '../assets/upload/files/license', '../../assets/upload/files/license');
        $crud->setRelation('state','states','abbr');
        $crud->unsetColumns(['datelastmodified']);
        $crud->unsetFields(['datelastmodified']);
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

    public function distributor()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('distributor');
        $crud->setSubject('Distributor', 'Distributor');
        $crud->setRelation('state','states','abbr');
        $crud->unsetColumns(['datelastmodified']);
        $crud->unsetFields(['datelastmodified']);
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

    public function ospfacility()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('ospfacility');
        $crud->setSubject('OSP', 'OSP Facility');
        $crud->setFieldUpload('licenseupload', 'assets/upload/files/license', '../../assets/upload/files/license');
        $crud->setRelation('state','states','abbr');
        $crud->unsetColumns(['datelastmodified']);
        $crud->unsetFields(['datelastmodified']);
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

    public function licensetype()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('licensetype');
        $crud->setSubject('License', 'License Type');
        $crud->unsetColumns(['datelastmodified']);
        $crud->unsetFields(['datelastmodified']);
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

     public function status()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('status');
        $crud->setSubject('Status', 'Status');
        $crud->unsetColumns(['datelastmodified']);
        $crud->unsetFields(['datelastmodified']);
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

     public function terms()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('terms');
        $crud->setSubject('Terms', 'Terms');
        
        $crud->unsetColumns(['datelastmodified']);
        $crud->unsetFields(['datelastmodified']);
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

    public function units()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('units');
        $crud->setSubject('Units', 'Units');
        $crud->unsetColumns(['datelastmodified']);
        $crud->unsetFields(['datelastmodified']);
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }


    public function ospmatrix()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('testingmatrix');
        $crud->setSubject('Matrix', 'OSP Matrix');
        $crud->unsetColumns(['datelastmodified']);
        $crud->unsetFields(['datelastmodified']);
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

    public function customers()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('customers');
        $crud->setSubject('Customer', 'Customers');
        $crud->displayAs(array('clientname' => 'Client', 'legalname' => 'Legal Name', 'primarycontact' => 'Primary Contact', 'companyemail' => 'Email to send invoice','companyphone' =>'Company Phone','secondaryc_name' =>'
        2nd Contact Name','secondaryc_phone' =>'2nd Company Phone','secondaryc_email' =>'Retailers email','deliverye' =>'Delivery email','deliveryc' =>'Delivery Contact','deliveryp' =>'Delivery Phone','deliveryday' =>'Delivery Days','salesrep' =>'Sales Rep','accountmanager' =>'Account Manager','licensenumber' =>'License Number','licensetype' =>'License Type','licensevalid' =>'License Valid','licenseexpire' =>'License Expires','licenseul' =>'Uploaded License','servicezone' =>'Service Zone'));
        $crud->setRelation('terms','terms','term');
        $crud->setRelation('status','status','status');
        $crud->setRelation('salesrep','contactperson','{firstname}, {lastname}',['contacttype' => 3]);
        $crud->setRelation('accountmanager','contactperson','{firstname}, {lastname}');
        $crud->setRelationNtoN('deliveryday', 'customerdeliverydays', 'daysofweek', 'customerid', 'dayid', 'day');
        $crud->setFieldUpload('licenseul', 'assets/upload/files/license', '../../walnut/assets/upload/files/license');
        $crud->setRelation('state','states','abbr');
        $crud->setRelation('licensetype','licensetype','name');
        $crud->requiredFields(['clientname','terms','licensenumber','state']);
        $crud->unsetDelete();
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }public function customers2()
    {
        $database = $this->_getDatabaseConnection2();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('customers');
        $crud->setSubject('Customer', 'Customers');
        $crud->displayAs(array('clientname' => 'Client', 'legalname' => 'Legal Name', 'primarycontact' => 'Primary Contact', 'companyemail' => 'Email to send invoice','companyphone' =>'Company Phone','secondaryc_name' =>'
        2nd Contact Name','secondaryc_phone' =>'2nd Company Phone','secondaryc_email' =>'Retailers email','deliverye' =>'Delivery email','deliveryc' =>'Delivery Contact','deliveryp' =>'Delivery Phone','deliveryday' =>'Delivery Days','salesrep' =>'Sales Rep','accountmanager' =>'Account Manager','licensenumber' =>'License Number','licensetype' =>'License Type','licensevalid' =>'License Valid','licenseexpire' =>'License Expires','licenseul' =>'Uploaded License','servicezone' =>'Service Zone'));
        $crud->setRelation('terms','terms','term');
        $crud->setRelation('status','status','status');
        $crud->setRelation('salesrep','contactperson','{firstname}, {lastname}',['contacttype' => 3]);
        $crud->setRelation('accountmanager','contactperson','{firstname}, {lastname}');
        $crud->setRelationNtoN('deliveryday', 'customerdeliverydays', 'daysofweek', 'customerid', 'dayid', 'day');
        $crud->setFieldUpload('licenseul', 'assets/upload/files/license', '../../walnut/assets/upload/files/license');
        $crud->setRelation('state','states','abbr');
        $crud->setRelation('licensetype','licensetype','name');
        $crud->requiredFields(['clientname','terms','licensenumber','state']);
        $crud->unsetDelete();
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

       public function inventorytype()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('inventorytype');
        $crud->setSubject('Inventory Type', 'Inventory Types');
        $crud->unsetColumns(['datelastmodified']);
        $crud->unsetFields(['datelastmodified']);
        $crud->displayAs(array('vendorid' => 'Vendor',));
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

    public function contactperson()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('contactperson');
        $crud->setSubject('Person', 'Contact Person');
        $crud->fieldType('email', GroceryCrud::FIELD_TYPE_EMAIL);
        $crud->setRelation('state','states','abbr');
        $crud->setRelation('contacttype','contacttype','type');
        $crud->unsetColumns(['datelastmodified']);
        $crud->unsetFields(['datelastmodified']);
        $crud->displayAs(array('vendorid' => 'Vendor','uppermanage' => 'Distribution List'));
                $crud->fieldType('uppermanage', 'dropdown_search', [
            '0' => 'No',
            '1' => 'Yes',
         ]);
        $crud->callbackAddForm(function ($data) {
           $data['uppermanage'] = '0';
          return $data;
        });
        $output = $crud->render();

        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

    public function contacttype()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('contacttype');
        $crud->setSubject('Type', 'Contact Type');
        $crud->unsetColumns(['datelastmodified']);
        $crud->unsetFields(['datelastmodified']);
        $crud->displayAs(array('vendorid' => 'Vendor',));
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

     public function inventorycategory()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('inventorycategory');
        $crud->setSubject('Inventory', 'Inventory Category');
        $crud->unsetColumns(['datelastmodified']);
        $crud->unsetFields(['datelastmodified']);
        $crud->displayAs(array('vendorid' => 'Vendor',));
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

    public function clients()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('clients');
        $crud->setSubject('Clients', 'Clients');
        $crud->displayAs(array('clientname' => 'Client', 'legalname' => 'Legal Name', 'primarycontact' => 'Primary Contact', 'companyphone' => 'Phone','licensetype' =>'License Type'));
        $crud->setRelation('primarycontact','contactperson','{firstname} {lastname}');
        $crud->setRelation('state','states','abbr');
        $crud->setRelation('licensetype','licensetype','name');
        $crud->setRelation('terms','terms','term');
        $crud->setRelation('status','status','status');
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

    public function batch()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('batch');
        $crud->setSubject('Batch', 'Batch Creation');
        $crud->setFieldUpload('image', '../assets/upload/files/inv', '../../assets/upload/files/inv');
        $crud->setClone();
        $crud->callbackColumn('image', function ($value, $row) {
            return "<img src='".url('/')."assets/upload/files/inv/" . $value . "' height=50>";
        });

        $crud->requiredFields(['batchid','strainid','um_batch']);

        $crud->setRelation('um_sample','units','{name} - {abbriviation}');
        $crud->setRelation('um_batch','units','{name} - {abbriviation}');
        $crud->setRelation('strainid','strainname','strain');
        $crud->setRelation('strainname_fk','strainname','strain');
        $crud->setRelation('distributor_fk','distributor','companyname');
        $crud->setRelation('cultivator_fk','cultivator','companyname');
        $crud->setRelation('ospfacility_fk','ospfacility','companyname');
        $crud->setRelation('matrix_fk','testingmatrix','matrix');
        $crud->setFieldUpload('coafile', 'assets/upload/files/coa', '../../assets/upload/files/coa');
        $crud->unsetColumns(['datelastmodified']);
        $crud->unsetFields(['datelastmodified']);
        $crud->unsetFields(['creationdate']);
        //------------------------------My work-----------------------------------------------------
        //set ActionButton
        $crud->setActionButton('Create Individual Assets','fa fa-user',function($row){
            $weightremain = $row->weightremain?$row->weightremain:0;
            $um_batch = $row->um_batch?$row->um_batch:1;
            $unit = Unit::where('unit_id',$um_batch)->first();
            return 'javascript:show:assets_generate_modal("'.$row->batch_id.'","'.$weightremain.'","'.$unit->name.'_'.$unit->abbriviation.'")';
         },false);

        $crud->callbackBeforeInsert(function($row){

            $row->data['weightremain'] = $row->data['batchsize'];;

            return $row;
        });
        //load producttypes
        $producttypes = Producttype::all();

        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;

        return view('gc/batch', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files,
            'producttypes' => $producttypes,
        ]);
    }

    function _aa($row)
    {
        $row->weightremain = 232;
        return $row;
    }

    public function assets_generate(Request $request)
    {
        $batch = Batch::find($request->batch_id);
        $before_weight = $batch->weightremain;

        $batch_result = DB::transaction(function () use ($request,$batch){
            //update the batch info
            $batch->assetsgenerated = $batch->assetsgenerated + $request->qty;
            $batch->weightremain = $batch->weightremain - $request->w;
            $batch->save();

            //create the asset potal
            $group_id_counter = Counter::where('key','asset_group_id')->first();
            //new record
            $asset_potal = new AssetPotal;
            //set group_id
            $asset_potal->group_id = $group_id_counter->value;
            //set quantity
            $asset_potal->assetscreated = $request->qty;
            //set batchId
            $asset_potal->batch_id = $batch->batchid;
            //set asset type
            $asset_potal->asset_type = $request->type_id;
            //set coa pdf
            $asset_potal->coa_file = $batch->coafile;
            //save the asset_potal item
            $asset_potal->save();

            //generate the individual asset Data
            $assetsData = [];

            $asset_id_counter = Counter::where('key','asset')->first();

            for($i = 0; $i < $request->qty; $i ++)
            {
                $item = array(
                    'asset_id' => $asset_id_counter->value,
                    'batch_id' => $batch->batchid,
                    'weight'   => $request->w,
                    'type_id'  => $request->type_id,
                    'group_id' => $group_id_counter->value);

                $model = $batch->assets()->getModel();
                $assetsData[] = $model->fill($item);

                $asset_id_counter->increment('value');
            }

            $batch->assets()->saveMany($assetsData);

            //increate the asset_group_id
            $group_id_counter->increment('value');

            if($request->send_fg == 1)
            {
                //create new record;
                $fgInventory = new FGInventory;
                $upc = 0;

                $upc = UPController::where([
                    ['strain',Strainame::find($batch->strainid)->strain],
                    ['types',Producttype::find($request->type_id)->producttype]
                ])->first();

                $fgInventory->strainname    = $batch->strainid;
                $fgInventory->asset_type_id = $request->type_id;
                $fgInventory->upc_fk        = $upc->iteminv_id;
                $fgInventory->batch_fk      = $batch->batchid;
                $fgInventory->qtyonhand     = $request->qty;
                $fgInventory->weight        = $request->w;
                $fgInventory->um            = $batch->um_batch;
                $fgInventory->coa           = $batch->coafile;

                $fgInventory->save();
            }

            return $batch;
        });

        return $batch_result->weightremain == $before_weight?0:1;
    }

    public function allocationbuilder()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('harvest_tracker');
        $crud->setSubject('Harvest Tracker', 'Harvest Tracking  '.date('Y-m-d'));
        $crud->setRelation('um','units','{name} - {abbriviation}');
        $crud->setRelation('harvestid','harvests','harvest_batch_id');

        $crud->setRelation('strain','strainname','strain');
        $crud->setRelation('current_location','location_area','{location_id} - {name}');
        $crud->setRelation('type','productcategory','producttype');
        $crud->where(['archived' => '0']);
        //----------------------Action Button Part---------------------------
        $crud->setActionButton('Print Barcode', 'fa fa-user', function ($row) {
            return 'javascript:show:getBarcode("'.$row->id.'")';
        });
        $crud->setActionButton('Sent to Holding Inventory', 'fa fa-user', function ($row) {
            return 'javascript:show:sendToHoldingInventory("'.$row->id.'")';
        });
        //----------------------/Action Button Part--------------------------

        $crud->unsetExport();
        $crud->unsetPrint();
        $crud->callbackAddForm(function ($data) {$data['um'] = '4';return $data;});
            $crud->callbackColumn('harvestid', function ($value, $row) {
            $harvest = Harvest::find($value);

            $batch_id = $harvest == null?0:$harvest->harvest_batch_id;

            $generatorSVG = new \Picqer\Barcode\BarcodeGeneratorSVG();
            $upc = str_pad($batch_id, 10, '0', STR_PAD_LEFT);

            $barcode_html =  ($generatorSVG->getBarcode($batch_id, $generatorSVG::TYPE_CODE_128)) . ' ';
            $barcode_html .= '<br>';
            $barcode_html .= '<div style="text-align: center;font-size: xx-small;">'.$batch_id.'</div>';
            return $barcode_html;
         });
        $crud->unsetColumns(['datelastmodified','date_created',]);
        $crud->unsetFields(['datelastmodified','date_created']);
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc.harvestTrackerBuilder', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }
    public function _harvestTrackerBuilerBarcode(Request  $request)
    {
        $harvest_batch_id = Harvest::find(HarvestTracker::find($request->id)->harvestid)->harvest_batch_id;
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcode_html = '<div><img src="data:image/png;base64,' . base64_encode($generator->getBarcode($harvest_batch_id, $generator::TYPE_CODE_128)) . '">';
        $barcode_html .= '<br>';
        $barcode_html .= '<span>'.$harvest_batch_id.'</span></div>';

        return $barcode_html;
    }
    public function _harvestTrackerBuilerToHoldingInventory(Request $request)
    {
        $tracker = HarvestTracker::find($request->id);
        $holdingInventory = new HoldingInventory;

        $holdingInventory->strainname    = $tracker->strain;
        $holdingInventory->asset_type_id = $tracker->type;
        $holdingInventory->upc_fk        = null;
        $holdingInventory->batch_fk      = Harvest::find($tracker->harvestid)->harvest_batch_id;
        $holdingInventory->weight        = $tracker->allocatedweight;
        $holdingInventory->um            = 4;

        $holdingInventory->save();

        $tracker->archived = 1;
        $tracker->save();
    }
    public function allocationresults()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTable('harvest_tracker');
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setSubject('Harvest Tracker', 'Harvest Tracking - 24-10-19');
        $crud->setRelation('um','units','{name} - {abbriviation}');
        $crud->setRelation('harvestid','harvests','harvest_batch_id');
        $crud->setRelation('strain','strainname','strain');
        $crud->setRelation('current_location','location_area','{location_id} - {name}');
        $crud->setRelation('type','productcategory','producttype');
        $crud->displayAs(array('type' => 'Product Type','allocatedweight' => 'Allocated Weight','harvestid' => 'Harvest Batch ID'));
        $crud->unsetDelete();
        $crud->unsetEdit();
        $crud->unsetAdd();
        $crud->callbackAddForm(function ($data) {$data['um'] = '4';return $data;});
        $crud->callbackColumn('upc', function ($value, $row) {
            $generatorSVG = new \Picqer\Barcode\BarcodeGeneratorSVG();
            $upc = str_pad($value, 10, '0', STR_PAD_LEFT);

            $barcode_html =  ($generatorSVG->getBarcode($value, $generatorSVG::TYPE_UPC_A)) . ' ';
            $barcode_html .= '<br>';
            $barcode_html .= '<div style="text-align: center;font-size: xx-small;">'.$value.'</div>';
            return $barcode_html;
         });
        $crud->unsetColumns(['datelastmodified']);
        $crud->unsetFields(['datelastmodified','barcode']);
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

    public function get_tracker_list()
    {
        $model = new HarvestTracker;

        $list = $model->get_list();

        return response()->json($list);
    }

        public function waste()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('harvest_item_waist');
        $crud->setSubject('Waste', 'Waste');
        $crud->setRelation('parent_id','harvests','harvest_batch_id');
        $crud->setRelation('waist_type','waist_type','label');
        $crud->displayAs(array('waist_type' => 'Waste Type'));
        $crud->unsetDelete();
        $crud->unsetAdd();
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }

        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;

        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files,
        ]);
    }

    public function assets()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('assets');
        $crud->setSubject('Asset', 'Asset Show');
        $crud->setRelation('type_id','productcategory','producttype');

        $crud->callbackColumn('barcode_id', function ($value, $row) {
            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
            $asset_id = str_pad($row->asset_id, 10, '0', STR_PAD_LEFT);
            $barcode_html = '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($asset_id, $generator::TYPE_CODE_128)) . '">';
            $barcode_html .= '<br>';
            $barcode_html .= '<span>'.$asset_id.'</span>';
            return $barcode_html;
        });

        $crud->displayAs('type_id','Assettype');
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }

        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;

        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files,
        ]);
    }

    public function asset_potal()
    {
        $data['asset_group_list'] = AssetPotal::leftjoin('productcategory','productcategory.producttype_id','=','asset_type')->
                                   select('asset_potal.*','productcategory.producttype')->get();

        return view('gc.asset_potal',$data);
    }

    public function _asset_potal_get_assets(Request $request)
    {
        $asset_group = AssetPotal::where('group_id',$request->group_id)->first()->assets;

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        foreach($asset_group as $item)
        {
            $item->barcode_id = '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($item->asset_id, $generator::TYPE_CODE_128)) . '">';

            $item->group_id_barcode = '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($item->group_id, $generator::TYPE_CODE_128)) . '">';

        }


        return $asset_group->toJson();
    }

    public function credit_reasons()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('invoice_credit_note_reasons');
        $crud->unsetFields(['datelastmodified']);
        $crud->unsetColumns(['datelastmodified']);
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }

        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;

        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files,
        ]);
    }
    public function adminfg()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('fginventory');
        $crud->unsetFields(['datelastmodified','parent_id','stockimage','upc_fk','batch_fk',
        'coa','um','bestbefore']);
        $crud->unsetColumns(['datelastmodified','parent_id','stockimage','upc_fk','batch_fk',
        'coa','um','bestbefore']);
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }

        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;

        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files,
        ]);
    }
    
    public function adminig()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTheme('Walnut');
        $crud->setThemePath('../../beta1/vendor/grocerycrud/enterprise/src/GroceryCrud/Themes/');
        $crud->setTable('invoice_good');
        $crud->unsetFields(['datelastmodified','parent_id','stockimage','upc_fk','batch_fk',
        'coa','um','bestbefore']);
        $crud->unsetColumns(['datelastmodified','parent_id','stockimage','upc_fk','batch_fk',
        'coa','um','bestbefore']);
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }

        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;

        return view('gc', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files,
        ]);
    }
}
