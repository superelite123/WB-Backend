var select_company = $("#company");
var select_strain  = $("#strain");
var select_license = $("#license");
var select_unit    = $("#unit");

var input_plant  = $("#plant");
var input_weight = $("#weight");

var harvest_items = windowvar.harvest_items == null?[]:windowvar.harvest_items;
var mode = windowvar.mode;
var harvest_id = windowvar.harvest_id;

$(".makeBtn").click(function(){
    let top_info = get_top_info()
    
    if(harvest_items.length == 0)
    {
        swal("Warning!", "You must enter information about this Harvest!", "info")
        return;
    }

    if(top_info == false)
    {
        swal("Info!", "You need to fill the all required field!", "info")
        return;
    }

    submit_harvest(false).then(function(res){
        res = JSON.parse(res)
        switch(res.code)
        {
            case '0':
                ask_target()
            break;
            case '2':
                swal({
                    title: "Are You Sure",
                    text: "The " + res.harvest_batch_id +" exist already\nAre you going to merge it to existing record?",
                    type: "info",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                    }, function () {
                        submit_harvest(true).then(function(res){
                            res = JSON.parse(res)
                            switch(res.code)
                            {
                                case '0':
                                    swal('There is no info to update','info')
                                break;
                                case '2':
                                    alert('d')
                                break;
                                case '1':
                                    ask_target()
                                break;
                            }
                        })
                    //submit_harvest();
                    })
            break;
            case '1':
                swal('Success','Harvest saved successfully','success')
                location.href="list";
            break;
        }
    })

})

let ask_target = () => {
    swal({
        title: "Harvest Saved Successfully",
        text: "What do you want",
        type: "success",
        showCancelButton: true,
        confirmButtonClass: "btn-success",
        confirmButtonText: "Go to List",
        cancelButtonText: "Stay Here",
        closeOnConfirm: false,
        closeOnCancel: false
      },
      function(isConfirm) {
        if (isConfirm) {
            location.href="list"
        } else {
            location.reload()
        }
    });
    
}

let havest_unique = () => {
    return new Promise(function(fulfill){
        $.ajax({
            url:'check_existing_record',
            headers:{"content-type" : "application/json"},
            data:JSON.stringify(get_top_info()),
            type:'post',
            async:false,
            success:(res) => {
                fulfill(res);
            }

        })
    })
}

let get_top_info = (merge = false) => {
    
    if(select_company.val() == '0' || select_strain == '0' || select_license == '0')
    {
        return false;
    }

    return {
        harvest_id:harvest_id,
        unit_weight:select_unit.val(),
        cultivator_company_id:select_company.val(),
        strain_id:select_strain.val(),
        cultivator_license_id:select_license.val(),
        merge:merge,
    }
}

var get_post_data = () => {
    var items = Array();
    items.push({plant_tag:input_plant.val(),weight:input_weight.val()})
    return {
            id:harvest_id,
            unit_weight:select_unit.val(),
            cultivator_company_id:select_company.val(),
            strain_id:select_strain.val(),
            items:items,
            cultivator_license_id:select_license.val(),
            mode:mode,
    };
};


var submit_harvest = (merge = false) => {
    return new Promise(function(fulfill){
        $.ajax({
            url:'store',
            headers:{"content-type" : "application/json"},
            data:JSON.stringify(get_top_info(merge)),
            type:'post',
            async:false,
            success:(res) => {
                fulfill(res);
            }

        })
    })
};

$("#add_row").click(function(e) {
    e.preventDefault();

    var plant = input_plant.val().replace(/\s/g, '');
    var weight = input_weight.val();

    if(select_company.val() == '0' || select_strain.val() == '0' || select_license.val() == '0')
    {
        alert('You must enter Licence, Flower Room and Strain before continuing');
        return;
    }

     if(weight == 0)
    {
        alert("You must enter a Weight");
        return;
    }
    
        if(plant == 0)
    {
        alert("You must enter a Plant Tag");
        return;
    }

    if(plant.length != 24)
    {
        alert("Plant tag must be 24 character");
        return;
    }

    SaveRow(plant,weight).then(function(res){
        add_harvest_item(plant,weight,res);
    },function(){
        alert('The Plant Tag you entered is already in the system\n Please check and validate last Plant Tag scanned');
        $("#plant").focus();
        return;
    })
});

var add_harvest_item = (plant,weight,res) => {
    harvest_id = res.id;
    
    if(res.merge == true)
    {
        for(var i = res.items.length - 1; i >= 0; i --)
        harvest_items.push(res.items[i]);
        var x = document.getElementById("snackbar");
        x.className = "show";
        setTimeout(function(){ x.className = x.className.replace("show", ""); }, 5000);
        
    }

    var now = parseInt($("#session_count").html());
    $("#session_count").html(now + 1);

    harvest_items.push({plant_tag:plant,weight:weight});
    CreateTable();
    input_plant.val('');
    input_weight.val('');
    input_plant.focus('');
}

var SaveRow =  (plant,weight) => {
    return new Promise(function(fulfill,reject){
        let post_data = get_post_data();
        $.ajax({
            url:'saverow',
            type:'post',
            headers:{"content-type" : "application/json"},
            data: JSON.stringify(post_data),
            async:false,
            success:function(res)
            {
                res = JSON.parse(res);
                if(res.success == '999')
                {
                    reject();
                }
                else
                {
                    fulfill(res);
                }
            }
        });
    })   
}

var findIndexWithAttr =  function(array, attr, value){
    for(var i = 0; i < array.length; i += 1) {
        if(array[i][attr] == value) {
            return i;
        }
    }
    return -1;
};

input_weight.keyup(function(event) {
    if (event.keyCode === 13) {
     event.preventDefault();
     $("#add_row").click();
     input_plant.focus();
    }
});

input_plant.keyup(function(event) {
    if (event.keyCode === 13) {
        event.preventDefault();
        var plant = input_plant.val();
        if(plant == 0 || weight == 0)
        {
            alert("can't enter the zero value");
            return;
        }
        input_weight.focus();
    }
});

var CreateTable = () => {
    var html = "";
    var cnt = 0;
    let element;
    
    for(let i = harvest_items.length - 1; i >= 0 ; i --)
    {
        element = harvest_items[i];
        html += "<tr data-plant='"+element.plant_tag+"' data-weight='"+element.weight+"' item_id='" + i +"'>";
        html += "<td>" + (i+1) + "</td>";
        html += "<td>"+element.plant_tag+"</td>";
        html +="<td>"+element.weight+"</td>";
    }

    $("#item_count").html(harvest_items.length);

    $(".data-table tbody").html(html);
}

$(function(){
    CreateTable();
    $("body").addClass('fixed');
    $('.select2').select2();
})


$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});