let table_data = []
let canvas_tip
let plants = windowvar.plants;

let canvas_width,canvas_height
canvas_width = $("#table1").parent('div').closest('div').width()*0.7
canvas_height = 5000

let radius = 7
let canvas_margin_width  = 80
let canvas_margin_height = 30
let real_width  = canvas_width - 2* canvas_margin_width
let real_height = canvas_height - 2* canvas_margin_height

let line_count = windowvar.line_count
let matrix_col = windowvar.matrix_type
let colors     = windowvar.colors
let total_count = line_count*(matrix_col + 4)
console.log(plants)
function table_data_model(id,type,weight,tag){
    this.id=id
    this.coord=[]
    this.type = type
    this.weight = weight
    this.tag = tag
    this.context = new Path2D()
}

$("canvas").click(function(e){
    
    $tip=$('#tip')
    var hit = false;
    let id = $(this).attr('id').split('table')[1] - 1
    let canvas = $(this)[0]
    let ctx = canvas.getContext("2d")
    for(let i = 0; i < table_data[id]['points'].length; i ++)
    {
        if(ctx.isPointInPath(table_data[id]['points'][i].context, event.offsetX, event.offsetY))
        {
            hit = true

            let html
            if(table_data[id]['points'][i].type == 100)
            {
                html = 'Empty'
            }
            else
            {
                html = "Plant Tag<br>"
                html += table_data[id]['points'][i].tag
                html += "<br>Wet Weight<br>"
                html += table_data[id]['points'][i].weight
            }
            
            $tip.html(html);
            $tip.css({left:e.clientX+3 - 250,top:e.clientY-78}).show();
        }
    }

    if (!hit) {
        $tip.hide();
    }
})

let initData = () => {
    // $.ajax({
    //     url:'_get_satistic_'
    // })
    let current_x_1 = canvas_margin_width
    let current_x_2 = current_x_1
    let current_y = canvas_margin_height
    let segment_width_1 = real_width / 3
    let segment_height  = real_height / 250
    let segment_width_2 = segment_width_1
    let cnt

    switch(matrix_col)
    {
        case 3:
            segment_width_2 = segment_width_1
            current_x_2 = current_x_1 + segment_width_2 / 2
            break;
        case 4:
            segment_width_2 = segment_width_1
            current_x_2 = current_x_1
            break;
        case 5:
            segment_width_2 = real_width / 4
            current_x_2 = current_x_1
            break;
    }

    for(let i = 0; i < 3; i ++)
    {
        table_data[i]['points'] = []
        table_data[i]['lines']  = []
        //prepare data
        for(let j = 0; j < total_count; j ++)
        {
            let temp = plants[i][j]
            if(temp != null)
                table_data[i]['points'].push(new table_data_model(j,plants[i][j].type,plants[i][j].weight,plants[i][j].tag))
            else
                table_data[i]['points'].push(new table_data_model(j,100,0,0))
        }

        cnt = 0
        current_y = canvas_margin_height
        //calc xy coord from it's index
        while(cnt < total_count)
        {
            for(let k = 0; k < 4 && cnt < total_count; k ++)
            {
                table_data[i]['points'][cnt].coord[0] = current_x_1 + segment_width_1 * k
                table_data[i]['points'][cnt].coord[1] = current_y
                cnt ++
            }

            table_data[i]['lines'].push(table_data[i]['points'][cnt - 4].coord[1])

            current_y += segment_height
            for(k = 0; k < matrix_col && cnt < total_count; k ++)
            {
                table_data[i]['points'][cnt].coord[0] = current_x_2 + segment_width_2 * k
                table_data[i]['points'][cnt].coord[1] = current_y
                cnt ++
            }
            
            table_data[i]['lines'].push(table_data[i]['points'][cnt - matrix_col].coord[1])
            current_y += segment_height
        }
    }
}
let initCanvas = function(){
    for(let i = 0; i < 3; i ++)
        table_data[i] = []
    table_data[0]['canvas'] = document.getElementById("table1")
    table_data[1]['canvas'] = document.getElementById("table2")
    table_data[2]['canvas'] = document.getElementById("table3")
    canvas_tip      = document.getElementById("tip")
    let ctx
    ctx = table_data[0]['canvas'].getContext("2d");
    ctx.canvas.width  = canvas_width;
    ctx.canvas.height = canvas_height;
    ctx = table_data[1]['canvas'].getContext("2d");
    ctx.canvas.width  = canvas_width;
    ctx.canvas.height = canvas_height;
    ctx = table_data[2]['canvas'].getContext("2d");
    ctx.canvas.width  = canvas_width;
    ctx.canvas.height = canvas_height;
}

let drawCanvas = function() {
    let ctx
    let point_x
    point_x = table_data[0]['points'][0].coord[0] - 50
    for(let k = 0; k < table_data.length; k ++)
    {
        ctx = table_data[k]['canvas'].getContext("2d");
        let line_cnt = 1
        let flag = 0
        let must_4_i = 2
        let must_m_i = 1
        for(let i = 0; i < total_count; i ++)
        {
            table_data[k]['points'][i].context.arc(table_data[k]['points'][i].coord[0], table_data[k]['points'][i].coord[1], radius, 0, 2 * Math.PI)
            
            ctx.fillStyle = colors[table_data[k]['points'][i].type]
            ctx.fill(table_data[k]['points'][i].context)

        }
        console.log(table_data[k].lines)
        for(i = 0; i < table_data[k].lines.length; i ++)
        {
            ctx.fillStyle = 'silver'
            ctx.font = "12px Arial";
            ctx.fillText(i + 1, canvas_margin_width - 50 ,table_data[k].lines[i] +4)
        }
    }
}

$(() => {
    initCanvas()
    
    initData()
    drawCanvas()
    $("body").addClass('fixed');
})
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});