$(document).ready(function() {
   
    if( $("#list-alertrules").length ){
        getAlertRules();
    }

    if( $("#list-recipients").length ){
        getAlertRuleRecipientsList($("#alertrule-detail").data("alertrule-id"));
    }

    getSensorsList();
   
    //$('#btn-save-alertrule').on('click', saveAlertRule);
    //$('#btn-delete-alertrule').on('click', deleteAlertRule);
    $('#btn-add-recipient').on('click', addRecipient);

    $("#list-recipients").on("click", ".btn-delete-alertrulerecipient", deleteRecipient);
});

function getAlertRules(){
    $.getJSON( "/api/alertrules", function( data ) {
        //console.log(data);

        const Item = (alertrule) => `
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">${alertrule.name}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">#${alertrule.id}</h6>
                    <p class="card-text">${alertrule.sensor_name} | ${alertrule.site_name}</p>
                    <a href="/alertrules/${alertrule.id}" class="card-link stretched-link">go</a>
                </div>
            </div>
        </div>
        `;

        $('#list-alertrules').html(data.map(Item).join(''));
        
    });
}

/* function saveAlertRule(event){
    event.preventDefault();
    event.st
    let data ={
        sensor_id: $("#sensor_id").val(), 
        name : $("#name").val(),
        field_name : $("#field_name").val(),
        field_operator : $("#field_operator").val(),
        field_theshold : $("#field_theshold").val(),
        recipient_type : $("#recipient_type").val(),
        recipient : $("#recipient").val(),
    }
    if(data.name.trim() === "" ||
       data.sensor_id.trim() === "" ||
       data.field_name.trim() === "" ||
       data.field_operator.trim() === "" ||
       data.field_theshold.trim() === "" ||
       data.recipient.trim() === "" 
    ){
        alert("Fill form!");
        return;
    }
    $.post( "/api/alertrules", data, function( data ) {
        getAlertRules();
        $("#name").val(""),
        $("#field_name").val(""),
        $("#field_operator").val(""),
        $("#field_theshold").val(""),
        $("#recipient").val("")
        $('#addAlertRuleModal').modal('toggle');
    });
} */

function getSensorsList(){
    $.getJSON( "/api/sensors", function( data ) {
        //console.log(data);

        const Item = (sensor) => `<option value="${sensor.id}">#${sensor.id} - ${sensor.name}</option>`;

        $('#sensor_id').html(data.map(Item).join(''));
        
    });
}

function getAlertRuleRecipientsList(alertrule_id){
    $.getJSON( "/api/alertrules/" +alertrule_id + "/recipients" , function( data ) {
        //console.log(data);

        const Item = (rec) => `<tr>
                                <td>${rec.recipient_type}</td>
                                <td>${rec.recipient}</td>
                                <td>
                                <button class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm btn-delete-alertrulerecipient"
                                    data-alertrulerecipient-id="${rec.id}" >
                                    <i class="fas fa-trash fa-sm text-white-50"></i> Delete 
                                </button>
                                </td>
                            </tr>`;

        $('#list-recipients tbody').html(data.map(Item).join(''));
        
    });
}

function addRecipient(event){
    event.preventDefault();
    let alertrule_id = $("#alertrule-detail").data("alertrule-id");
        
    let data ={
        recipient_type : $("#recipient_type").val(),
        recipient : $("#recipient").val(),
    }
    if(data.recipient.trim() === ""){
        alert("Fill form!");
        return;
    }
    $.post( "/api/alertrules/" + alertrule_id + "/recipients", data, function( data ) {
        getAlertRuleRecipientsList(alertrule_id);
        $("#recipient").val("")
        $('#addRecipientModal').modal('toggle');
    });
}

function deleteRecipient(event){
    let id = $(event.target).data("alertrulerecipient-id");
    let alertrule_id = $("#alertrule-detail").data("alertrule-id");
    
    if( window.confirm("Delete recipient #" + id + " ?") ){
        
        $.ajax({
            url: "/api/alertrulerecipients/" + id,
            type: 'DELETE'
        }).done(function() {           
            getAlertRuleRecipientsList(alertrule_id);
        });
    }
}
/* function deleteAlertRule(event){
    event.preventDefault();
    event.stopPropagation();
    let id = $(event.target).data("alertrule-id");
        
    if( window.confirm("Delete alert rule #" + id + " ?") ){
        
        $.ajax({
            url: "/api/alertrules/" + id,
            type: 'DELETE'
        }).done(function(data) {
            alert("Alert rule deleted");
            window.location.href = "/alertrules";
        });
    }
    
} */