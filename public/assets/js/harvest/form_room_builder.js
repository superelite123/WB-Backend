$(document).ready(function() {
snackbar = document.getElementById("snackbar");
    $(".makeBtn").click(() => {
        let cnt = 0
        let flag = true
        $("input:text.metrc").each(function(){
            let tag1 = $(this).val()
            cnt = 0
            
            if(tag1 != '')
            {
                $("input:text.metrc").each(function(){
                    let tag2 = $(this).val()

                    if(tag1 === tag2 && tag2 != '')
                    {
                        cnt ++
                    }
                })

                if(tag1.length != 24)
                {
                    flag = false
                }
            }
            
            if(cnt > 1)
            {
                alert('There is duplicated plant tag')
                var sound = document.getElementById("audio");
                sound.play();
                return false
            }

            if(!flag)
            {
                alert('All Plant Tag must be 24 characters')
                var sound = document.getElementById("audio_24");
                sound.play();
                return false
            }
        })

        if(cnt > 1)
        {
            return false
        }

        if(!flag)
        {
            return false
        }
       //$("#myForm").submit()
    })

    $("table").DataTable({
        columnDefs: [{
            orderable: false,
            targets: [1,2,3],
        }],
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
    })

    $("body").addClass('fixed');
});

$('table tbody').on('keydown','td',function(e){
    if (e.keyCode == 13 || e.keyCode == 9) {
        e.preventDefault()
        $(this).next('td').find('.metrc').focus()
       //
       if($(this).next('td').find('.metrc').length == 0)
       {
            let tr = $(this).closest('tr').next('tr')

            if(tr.length == 0)
            {
                let parent_tbody = $(this).closest('tbody')
                
                $('.next').find('a').click()
                parent_tbody.children('tr:first').find('.metrc:first').focus()
            }
            
            tr.find('.metrc:first').focus()
       }
       
        let selected_tag = $(this).find('.metrc').val()
        let cnt = 0

        if(selected_tag != "")
        {
            if(selected_tag.length != 24)
            {
                var sound = document.getElementById("audio_24");
                sound.play();
                alert('Plant Tag must be 24 characters')
                $(this).find('.metrc').focus()
                return false    
            }
            $("input").each(function(){
                let tag2 = $(this).val()
                if(selected_tag == tag2)
                {
                    cnt ++
                }
            })

        }
        
        if(cnt > 1)
        {
            $(this).find('.metrc').focus()
            var sound = document.getElementById("audio");
            sound.play();
            alert('This tag already entered')
            return false
        }
        if($(this).find('.metrc').val() != '')
        {
            $("#myForm").ajaxSubmit({
                url: 'store_room_builder', 
                type: 'post',
                success:function(res)
                {
                    snackbar.className = "show";
                    setTimeout(function(){ snackbar.className = snackbar.className.replace("show", ""); }, 1000);
                }
            })
        }
        
    }
})
