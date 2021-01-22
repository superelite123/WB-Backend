var salesChart
var s_date = windowvar.start_date
var e_date = windowvar.end_date
$(() =>{
    $("body").addClass('fixed')
    
    create_harvest_chart(s_date,e_date)
    create_strain_chart(s_date,e_date)

    $("#reservation_harvest").daterangepicker({
      locale: {
          format: 'YYYY-MM-DD'
      },
      startDate: s_date,
      endDate: e_date
    }).on("change", function() {
      var s = $(this).val().substring(0, 10)
      var e = $(this).val().substring(13, 24)
      create_harvest_chart(s,e)
    })

    $("#reservation_strain").daterangepicker({
      locale: {
          format: 'YYYY-MM-DD'
      },
      startDate: s_date,
      endDate: e_date
    }).on("change", function() {
      var s = $(this).val().substring(0, 10)
      var e = $(this).val().substring(13, 24)
      create_strain_chart(s,e)
    })
    
})

var create_harvest_chart = (s,e) => {

  $.ajax({
    url:'get_harvest_chart_data',
    type:'post',
    async:false,
    data:'s_date=' + s + "&e_date=" + e,
    success:function(res){
      res = JSON.parse(res)
      salesChart = new FusionCharts({
        type: 'stackedcolumn3d',
        dataFormat: 'json',
        renderAt: 'chart_container_harvest',
        width: '100%',
        height: '400',
        dataSource: {
          "chart": {
            "theme": "zune",
            "caption": "Harvest",
            "xAxisName": "Harvest ID",
            "yAxisName": "Weight(Pound)",
            "numberPrefix": "lbs",
            "lineThickness": "3",
            "flatScrollBars": "1",
            "scrollheight": "10",
            "numVisiblePlot": "12",
            "showHoverEffect": "1",
            "exportEnabled": "1"
          },
          "categories": [{
            "category": res.label,
          }],
          "dataset": [{
            "data": res.value
          }]
        }
      }).render()
    }
  })
}

var create_strain_chart = (s,e) => {
  $.ajax({
    url:'get_strain_chart_data',
    type:'post',
    async:false,
    data:'s_date=' + s + "&e_date=" + e,
    success:function(res){
      res = JSON.parse(res)
      salesChart = new FusionCharts({
        type: 'msbar3d',
        dataFormat: 'json',
        renderAt: 'chart_container_strain',
        width: '100%',
        height: '400',
        dataSource: {
          "chart": {
            "theme": "fusion",
            "caption": "Strain",
            "subCaption": "Strain Yields in Date Range",
            "xAxisName": "Strain",
            "yAxisName": "Total Plants Harvested",
            "numberPrefix": "",
            "lineThickness": "3",
            "flatScrollBars": "1",
            "scrollheight": "10",
            "numVisiblePlot": "12",
            "showHoverEffect": "1",
            "exportEnabled": "1"
          },
          "categories": [{
            "category": res.label,
          }],
          "dataset": [{
            "data": res.value
          }]
        }
      }).render()
    }
  })
}

$.ajaxSetup({
  headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});