var $jq = jQuery.noConflict();
$jq(document).ready(function () {
    $jq('a.compare-button').click(function () {
        var params = {
            'post-id' : $jq(this).attr('post-id'),
            'user-id' : $jq(this).attr('user-id'),
            'source' :$jq(this).attr('source')
        }
        $jq.post('/site/add', params, function(data){
            if(data.success){
                        if(data.error){
                        alert(data.message);}

                $jq('div.count').text(data.count);

            }
        });
        return false;
    });
});