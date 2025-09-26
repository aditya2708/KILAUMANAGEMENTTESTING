@if(Request::segment(1) == 'diroh_handsome')
<script>
    var multitabs = $('#content_wrapper').multitabs({
        init:[
            {
                type : 'main',
                title : 'Yamete',
                url : 'https://en.wikipedia.org/wiki/Main_Page'
            }
        ]
    });
</script>
@endif