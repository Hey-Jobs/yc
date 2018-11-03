function getCashiers(){
    var dataStore;
    $.ajax({
        dataType : 'json',
        type : 'get',
        url : '/form/cashiers',
        async : false,
        success: function(data){
            dataStore=data;
        }
    });
    return dataStore;
}

function getReceptionist(){
    var dataStore;
    $.ajax({
        dataType : 'json',
        type : 'get',
        url : '/form/receptionist',
        async : false,
        success: function(data){
            dataStore=data;
        }
    });
    return dataStore;
}

function getPersons(){
    var dataStore;
    $.ajax({
        dataType : 'json',
        type : 'get',
        url : '/form/persons',
        async : false,
        success: function(data){
            dataStore=data;
        }
    });
    return dataStore;
}

function getSysActions(){
    var dataStore;
    $.ajax({
        dataType : 'json',
        type : 'get',
        url : '/form/sys-actions',
        async : false,
        success: function(data){
            dataStore=data;
        }
    });
    return dataStore;
}

function getAllUser()
{
    var dataStore;
    $.ajax({
        dataType : 'json',
        type : 'get',
        url : '/form/allusers',
        async : false,
        success: function(data){
            dataStore=data;
        }
    });
    return dataStore;
}

function getDepts()
{
    var dataStore;
    $.ajax({
        dataType : 'json',
        type : 'get',
        url : '/form/dept',
        async : false,
        success: function(data){
            dataStore=data;
        }
    });
    return dataStore;
}

