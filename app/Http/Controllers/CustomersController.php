<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Helper\GC;

class CustomersController extends Controller
{
    use GC;
    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->middleware('auth');
    }

    /**
     * Show the datagrid for customers
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $attr = config('gc.'.$request->mode);

        $crud = $this->_getGroceryCrudEnterprise();
        $crud->setTable($attr['table']);
        $crud->setSubject($attr['subject'], $attr['subject']);

        //field_upload
        if(isset($attr['field_upload']))
        {
            foreach($attr['field_upload'] as $field_upload)
            {
                $crud->setFieldUpload($field_upload['fieldName'], $field_upload['private_dir'], $field_upload['public_dir']);
            }
        }

        //relation
        if(isset($attr['relation']))
        {
            foreach($attr['relation'] as $relation)
            {
                $crud->setRelation($relation['field'],$relation['related_table'],$relation['r_field']);                
            }
        }

        //unset columns
        if(isset($attr['unset_columns']))
        {
            $crud->unsetColumns($attr['unset_columns']);
        }

        //unset field
        if(isset($attr['unset_fields']))
        {
            $crud->unsetColumns($attr['unset_fields']);
    
        }

        //callback
        switch($request->mode)
        {
        case 'upcinventory':
        $crud->callbackColumn('upcimage', function ($value, $row) {
        return "<img src='http://walnutdistro.com/system/assets/uploads/files/upc/" . $value . "' width=100>";
        });
        break;
        case 'batchcreation':
        $crud->callbackColumn('image', function ($value, $row) {
        return "<img src='http://walnutdistro.com/system/assets/uploads/files/inv/" . $value . "' width=100>";
        });
        break;
        }

        $output = $crud->render();
        
        return $this->_show_output($output,'gc/show');
    }
}