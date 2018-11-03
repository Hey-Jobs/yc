var affirmSwals = function (title, text, type, func) {
    swal({
        title: title,
        type: type,
        text: text || '',
        confirmButtonText: "确定",
        cancelButtonText: '取消',
    },function(){
        if(typeof func!='undefined') {
            func();
        }
    });
};

function confirmFunc()
{
    window.location.reload();
}

function placeholder()
{
    return true;
}