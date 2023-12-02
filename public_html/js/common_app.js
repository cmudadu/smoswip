/**
 *  Common functions to use
 */

let alertCounter = 0;

$(document).ready(function () {
  /*
  $(".nav-link-app").on("click", function(e){
    e.preventDefault();
    let page = $(this).attr("href");
    $.get(page, function(res){
      $(".container-fluid").html(res);
    });
  });
  */
  //sse();
  $("#site").on("change", loadPageSite);
  $("#form-save-site").on("submit", saveSite);
  $("#btn-save-sensor").on("click", saveSensor);
  $('#addSensorModal').on('show.bs.modal', function (event) {
        
        getSensorTypeList();
    });

  setInterval(getLastAlerts, 5000);
  getLastAlerts();
})

function loadPageSite(event) {
  let site_id = $(this).val();
  if (site_id > 0)
    document.location.href = "/sites/" + $(this).val() + "/overview";
  else
    document.location.href = "/dashboard";
}
function sse() {
  // establish stream and log responses to the console
  var es = new EventSource("/sse");
  var listener = function (event) {

    if (typeof event.data !== 'undefined') {
      //console.log(`new data event ${event.data}`);
      let new_measure = JSON.parse(event.data);
      alertCounter++;
      $(".alert-counter").text("+" + alertCounter);

      let msg = `<a class="dropdown-item d-flex align-items-center" href="/sensors/${new_measure.sensor_id}">
                          <div class="mr-3">
                          <div class="icon-circle bg-warning">
                            <i class="fas fa-exclamation-triangle text-white"></i>
                        </div>  
                          </div>
                          <div>
                              <div class="small text-gray-500">${new_measure.timestamp}</div>
                              <span class="font-weight-bold">${new_measure.site_name} # ${new_measure.sensor_name}</span>
                          </div>
                      </a>`;
      $(msg).insertAfter(".alert-center");
    }

  };
  es.addEventListener("open", listener);
  es.addEventListener("message", listener);
  es.addEventListener("error", listener);
}

function getLastAlerts() {
  $.getJSON("/api/alerts?top=5", function (data) {
    if (data.length > 0) {
      $(".alert-counter").text("+" + data.length);
    }
    let Item = (alert) => `<a class="dropdown-item d-flex align-items-center" href="/alerts?site_id=${alert.site_id}">
            <div class="mr-3">
                <div class="icon-circle bg-${alert.alertrule_level}">
                    <i class="fas fa-exclamation-triangle text-white"></i>
                </div>                
            </div>
            <div>
                <div class="small text-gray-500">${alert.alertrule_name}</div>
                <span class="font-weight-bold">${alert.site_name} # ${alert.sensor_name}</span>
            </div>
        </a>`;

    $("#last-alert-container").html($(data.map(Item).join('')));

  });
}

function saveSite(event) {
  event.preventDefault();

  if ($('#form-save-site')[0].checkValidity() === false) {
    event.stopPropagation();
  } else {
    let data = {
      name: $("#site_name").val(),
      description: $("#site_description").val(),
      lat: $("#site_lat").val(),
      lon: $("#site_lon").val(),
    }
    if (data.name.trim() === "") {
      alert("Fill form!");
      return;
    }
    $.post("/api/sites", data, function (site_id) {
        document.location.href = "/sites/" + site_id;
    });
  }
  $('#form-save-site').addClass('was-validated');


}


function getSensorTypeList() {
  $.getJSON("/api/sensors/type", function (data) {
      //console.log(data);

      const Item = (sensor) => `<option value="${sensor.id}">${sensor.name}</option>`;

      $('#sensortype_id').html(data.map(Item).join(''));

  });
}

function saveSensor(event) {
  event.preventDefault();
  if ($('#form-save-sensor')[0].checkValidity() === false) {
    event.stopPropagation();
  } else {
    let data = {
        site_id: $("#site_id").val(),
        sensortype_id: $("#sensortype_id").val(),
        name: $("#name").val(),
        description: $("#description").val(),
        lat: $("#lat").val(),
        lon: $("#lon").val(),
        alt: $("#alt").val(),
    }
    
    $.post("/api/sensors", data, function (data) {
        window.location.href = "/sites/" + $("#site_id").val();
    });
  }
  $('#form-save-sensor').addClass('was-validated');

}


