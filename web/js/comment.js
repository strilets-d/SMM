var $jq = jQuery.noConflict();
$jq(document).ready(function () {
    $jq('a.eye-button').click(function () {
        var params = {
            'comment-id' : $jq(this).attr('comment-id'),
            'source' : $jq(this).attr('source'),
        };
        $jq.post('/admin/default/comment', params, function(data){
            if(data.success){
                if(data.see){
                    $jq('a#'+data.id).html('<i class="fas fa-eye-slash"></i>');
                }
                else {
                    $jq('a#'+data.id).html('<i class="fas fa-eye"></i>');
                }
            }
        });
        return false;
    });
});