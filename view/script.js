let addCompanyModal;
let editCompanyModal;
let turnoverModal;
let offset = 0;
let limit = 10;

$(document).ready(function () {
  loadTableData();
  addCompanyModal = new bootstrap.Modal($('#addCompanyModal'));
  editCompanyModal = new bootstrap.Modal($('#editCompanyModal'));
  turnoverModal = new bootstrap.Modal($('#turnoverModal'));
});

$(document).on('click', '#addCompanyButton', function() {
  $('#addCompanyForm')[0].reset();
  addCompanyModal.show();
});

$(document).on('submit', '#searchForm', function (e) {
  e.preventDefault();
  let registrationCodes = $('#registrationCodes').val();
  registrationCodes = registrationCodes.split(',');
  $.ajax({
    type: 'post',
    url: 'http://localhost/api/search',
    data: JSON.stringify(registrationCodes),
    contentType: "application/json; charset=utf-8",
    success: function (data) {
      $('#registrationCodes').val('');
      notify('Data will soon be scraped');
    },
    error: function (data) {
      notify(data.responseJSON.error, 'danger');
    }
  });
});

$(document).on('submit', '#filterForm', function(e) {
  e.preventDefault();
  loadTableData();
});

$(document).on('submit', '#addCompanyForm', function (e) {
  e.preventDefault();
  let company = objectifyForm($(this));
  $.ajax({
    type: 'post',
    url: 'http://localhost/api/company',
    data: JSON.stringify(company),
    contentType: "application/json; charset=utf-8",
    success: function (data) {
      addCompanyModal.hide();
      notify('Company Added Successfully');
      loadTableData();
    },
    error: function (data) {
      notify(data.responseJSON.error, 'danger');
    }
  });
});

$(document).on('submit', '#editCompanyForm', function(e) {
  e.preventDefault();
  let company = objectifyForm($(this));
  $.ajax({
    type: 'patch',
    url: `http://localhost/api/company/${company.id}`,
    data: JSON.stringify(company),
    contentType: "application/json; charset=utf-8",
    success: function (data) {
      editCompanyModal.hide();
      notify('Company Updated Successfully');
      loadTableData();
    },
    error: function (data) {
      notify(data.responseJSON.error, 'danger');
    }
  });
});

$(document).on('submit', '#addTurnoverForm', function(e) {
  e.preventDefault();
  let turnover = objectifyForm($(this));
  $.ajax({
    type: 'post',
    url: `http://localhost/api/turnover/${turnover.companyId}`,
    data: JSON.stringify(turnover),
    contentType: "application/json; charset=utf-8",
    success: function (data) {
      $('#addTurnoverForm')[0].reset();
      addTurnoverColumn(data);
      notify('Turnover Added Successfully');
    },
    error: function (data) {
      notify(data.responseJSON.error, 'danger');
    }
  });
});

$(document).on('click', '.deleteCompanyButton', function(e) {
  e.preventDefault();
  let id = $(this).attr('data');
  if(confirm('Are you sure to delete?')) {
    $.ajax({
      type: 'delete',
      url: `http://localhost/api/company/${id}`,
      success: function (data) {
        notify('Company Deleted Successfully');
        loadTableData();
      },
      error: function (data) {
        notify(data.responseJSON.error, 'danger');
      }
    });
  }
});

$(document).on('click', '.editCompanyButton', function(e) {
  e.preventDefault();
  let company = JSON.parse($(this).attr('data'));
  $('#editCompanyForm')[0].reset();
  $('#editCompanyForm input[name="id"]').val(company.id);
  $('#editCompanyForm input[name="name"]').val(company.name);
  $('#editCompanyForm input[name="registrationCode"]').val(company.registrationCode);
  $('#editCompanyForm input[name="vat"]').val(company.vat);
  $('#editCompanyForm input[name="address"]').val(company.address);
  $('#editCompanyForm input[name="mobilePhone"]').val(company.mobilePhone);

  $('#editCompanyModal .modal-title span').html(company.name);

  editCompanyModal.show();
});

$(document).on('click', '.deleteTurnoverButton', function(e) {
  e.preventDefault();
  let id = $(this).attr('data');
  let element = $(this);

  if(confirm('Are you sure to delete?')) {
    $.ajax({
      type: 'delete',
      url: `http://localhost/api/turnover/${id}`,
      success: function (data) {
        notify('Turnover Deleted Successfully');
        loadTableData();
        let index = element.closest('.turnoverDataElement').index();
        $(`#turnoverTable thead tr th:nth-child(${index+1})`).remove();
        $(`#turnoverTable tbody tr td:nth-child(${index+1})`).remove();
      },
      error: function (data) {
        notify(data.responseJSON.error, 'danger');
      }
    });
  }
});

$(document).on('click', '.manageTurnoverButton', function(e) {
  e.preventDefault();
  let company = JSON.parse($(this).attr('data'));

  $.get(`http://localhost/api/turnover/${company.id}`, function(data) {
    if(data.length > 0) {
      $('#turnoverTable').show();
      $('#noTurnoverDataMessage').hide();
      $('.turnoverDataElement').remove();
      for (let i = 0; i < data.length; ++i) {
        addTurnoverColumn(data[i]);
      }
    } else {
      $('#turnoverTable').hide();
      $('#noTurnoverDataMessage').show();
    }

    $('#turnoverModal .modal-title span').html(company.name);
    $('#addTurnoverForm')[0].reset();
    $('#addTurnoverForm input[name="companyId"]').val(company.id)

    turnoverModal.show();
  });
});

function setTableContent(data) {
  let rows = '';
  for (let i = 0; i < data.length; ++i) {
    rows += `<tr>
        <td>${data[i].name}</td>
        <td>${data[i].registrationCode}</td>
        <td>${data[i].vat}</td>
        <td>${data[i].address}</td>
        <td>${data[i].mobilePhone}</td>
        <td>
          <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              Actions
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item editCompanyButton" href="#" data='${JSON.stringify(data[i])}'>Edit</a></li>
              <li><a class="dropdown-item manageTurnoverButton" href="#" data='${JSON.stringify(data[i])}'>Manage Turnover Info</a></li>
              <li><a class="dropdown-item text-danger deleteCompanyButton" href="#" data="${data[i].id}">Delete</a></li>
            </ul>
          </div>
        </td>
      </tr>`;
  }
  $('#companyTable tbody').html(rows);
}

function objectifyForm(form) {
  let serializedData = form.serializeArray();
  let returnArray = {};
  for (let i = 0; i < serializedData.length; i++) {
    returnArray[serializedData[i]['name']] = serializedData[i]['value'];
  }
  return returnArray;
}

function notify(message, status = 'success') {
  $("#notification")
    .removeClass('alert-success')
    .removeClass('alert-error')
    .addClass(`alert-${status}`)
    .html(message)
    .fadeIn();
  setTimeout(function () {
    $("#notification").fadeOut();
  }, 3000);
}

function loadTableData() {
  let filter = $('#filter').val();
  let url = ('' !== filter)
    ? `http://localhost/api/company?search=${filter}`
    : `http://localhost/api/company/${offset}`;
  $.get(url, function (data) {
    setTableContent(data);
    if(0 === data.length) {
      notify('No Data Found', 'warning');
      $('#next').attr('disabled', true);
    } else {
      $('#next').removeAttr('disabled');
    }

    if(0 === offset) {
      $('#previous').attr('disabled', true);
    } else {
      $('#previous').removeAttr('disabled');
    }
  })
}

function addTurnoverColumn(turnover) {
  $('#turnoverTable thead tr').append(`
<th class="turnoverDataElement">
  <div class="dropdown">
    <button class="btn btn-block btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
      ${turnover.year}
    </button>
    <ul class="dropdown-menu">
      <li><a class="dropdown-item text-danger deleteTurnoverButton" href="#" data="${turnover.id}">Delete</a></li>
    </ul>
  </div>
</th>
`);
  $('tr.nonCurrentAssets').append(`<td class="turnoverDataElement">${turnover.nonCurrentAssets}</td>`);
  $('tr.currentAssets').append(`<td class="turnoverDataElement">${turnover.currentAssets}</td>`);
  $('tr.equity').append(`<td class="turnoverDataElement">${turnover.equity}</td>`);
  $('tr.liabilities').append(`<td class="turnoverDataElement">${turnover.liabilities}</td>`);
  $('tr.salesRevenue').append(`<td class="turnoverDataElement">${turnover.salesRevenue}</td>`);
  $('tr.profitBeforeTaxes').append(`<td class="turnoverDataElement">${turnover.profitBeforeTaxes}</td>`);
  $('tr.profitBeforeTaxesMargin').append(`<td class="turnoverDataElement">${turnover.profitBeforeTaxesMargin}</td>`);
  $('tr.netProfit').append(`<td class="turnoverDataElement">${turnover.netProfit}</td>`);
  $('tr.netProfitMargin').append(`<td class="turnoverDataElement">${turnover.netProfitMargin}</td>`);
}

$(document).on('click', '#previous', function() {
  if(offset >= limit) {
    offset -= limit;
    loadTableData();
  }
});

$(document).on('click', '#next', function() {
  offset += limit;
  loadTableData();
});