@extends('layouts.mantis')

@section('title', 'Team Registration Details')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Team Registration Details - {{ $team->name }}</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.teams.index') }}">Teams</a></li>
                    <li class="breadcrumb-item">Details</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div id="jsTeamDetailsConfig" data-inline-url="{{ route('admin.teams.update-details', $team->id) }}" style="display: none;"></div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Registration Information</h5>
                <div>
                     <a href="{{ route('admin.teams.export-details-pdf', $team->id) }}" class="btn btn-danger btn-sm me-2" target="_blank"><i class="ti ti-file-type-pdf"></i> Export PDF</a>
                     <a href="{{ route('admin.teams.index') }}" class="btn btn-secondary btn-sm">Back to Teams</a>
                 </div>
            </div>
            <div class="card-body">
                <h5 class="mb-3 text-primary">Institutional Legal Details</h5>
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small">Legal Name</label>
                        {!! renderInlineEdit($detail, 'legal_name', 'text') !!}
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small">Institution Category</label>
                        {!! renderInlineEdit($detail, 'institution_category', 'select', [
                            'School' => 'School',
                            'College' => 'College',
                            'Academy' => 'Academy',
                            'Training Centre' => 'Training Centre',
                            'Skill Development Centre' => 'Skill Development Centre',
                            'Learning Centre' => 'Learning Centre',
                            'Self' => 'Self'
                        ]) !!}
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small">Telephone</label>
                        {!! renderInlineEdit($detail, 'telephone', 'text') !!}
                    </div>
                </div>

                <hr>

                <h5 class="mb-3 text-primary">Registered Address</h5>
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small">Building Name</label>
                        {!! renderInlineEdit($detail, 'building_name', 'text') !!}
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small">Street Name</label>
                        {!! renderInlineEdit($detail, 'street_name', 'text') !!}
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small">Locality</label>
                        {!! renderInlineEdit($detail, 'locality_name', 'text') !!}
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small">City</label>
                        {!! renderInlineEdit($detail, 'city', 'text') !!}
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small">District</label>
                        {!! renderInlineEdit($detail, 'district', 'text') !!}
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small">State</label>
                        {!! renderInlineEdit($detail, 'state', 'text') !!}
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small">PIN Code</label>
                        {!! renderInlineEdit($detail, 'pin_code', 'text') !!}
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small">Country</label>
                        {!! renderInlineEdit($detail, 'country', 'text') !!}
                    </div>
                </div>

                <hr>

                <h5 class="mb-3 text-primary">Communication Officer Details</h5>
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small">Name</label>
                        {!! renderInlineEdit($detail, 'comm_officer_name', 'text') !!}
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small">Mobile</label>
                        {!! renderInlineEdit($detail, 'comm_officer_mobile', 'text') !!}
                    </div>
                     <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small">Alt Mobile</label>
                        {!! renderInlineEdit($detail, 'comm_officer_alt_mobile', 'text') !!}
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small">WhatsApp</label>
                        {!! renderInlineEdit($detail, 'comm_officer_whatsapp', 'text') !!}
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small">Email</label>
                        {!! renderInlineEdit($detail, 'comm_officer_email', 'text') !!}
                    </div>
                </div>

                <hr>

                <h5 class="mb-3 text-primary">Authorized Stakeholder Details</h5>
                <div class="row mb-4">
                     <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small">Name</label>
                        {!! renderInlineEdit($detail, 'auth_person_name', 'text') !!}
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small">Designation</label>
                        {!! renderInlineEdit($detail, 'auth_person_designation', 'text') !!}
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small">Mobile</label>
                        {!! renderInlineEdit($detail, 'auth_person_mobile', 'text') !!}
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small">Email</label>
                        {!! renderInlineEdit($detail, 'auth_person_email', 'text') !!}
                    </div>
                </div>
                
                <hr>

                <h5 class="mb-3 text-primary d-flex justify-content-between align-items-center">
                    Interested Courses & Delivery Structures
                    <button class="btn btn-sm btn-primary" id="btn-manage-courses">
                        <i class="ti ti-pencil"></i> Manage Courses
                    </button>
                </h5>
                
                <div id="courses-display-view">
                    @if(count($interestedCourses) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Course</th>
                                        <th>Delivery Structures</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($interestedCourses as $item)
                                    <tr>
                                        <td>{{ $item['course'] }}</td>
                                        <td>
                                            @if(count($item['structures']) > 0)
                                                <ul class="mb-0 ps-3">
                                                    @foreach($item['structures'] as $structure)
                                                        <li>{{ $structure }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="text-muted">No specific structures selected</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No course preferences found.</p>
                    @endif
                </div>

                <div id="courses-edit-view" style="display: none;">
                    <form id="form-manage-courses">
                        <div id="course-rows-container">
                            <!-- Rows will be populated by JS -->
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mb-3" id="btn-add-course-row">
                            <i class="ti ti-plus"></i> Add Course
                        </button>
                        <div class="d-flex gap-2">
                             <button type="button" class="btn btn-success btn-sm" id="btn-save-courses">Save Changes</button>
                             <button type="button" class="btn btn-secondary btn-sm" id="btn-cancel-courses">Cancel</button>
                        </div>
                    </form>
                </div>

                <hr class="my-4">
                <h5 class="mb-4 text-primary">OFFICE INFORMATION RECORD</h5>

                <h6 class="mb-3 text-secondary">1. Partner Identification Details - only editable</h6>
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">B2B Partner ID</label>
                        {!! renderInlineEdit($detail, 'b2b_partner_id', 'text') !!}
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">B2B Code</label>
                        {!! renderInlineEdit($detail, 'b2b_code', 'text') !!}
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">Date of Joining</label>
                        {!! renderInlineEdit($detail, 'date_of_joining', 'date') !!}
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">Partner Status</label>
                        {!! renderInlineEdit($detail, 'partner_status', 'select', [
                            'Active' => 'Active',
                            'Inactive' => 'Inactive',
                            'Suspended' => 'Suspended'
                        ]) !!}
                    </div>
                </div>

                <h6 class="mb-3 text-secondary">2. Assigned Officer Details</h6>
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">B2B Officer Name</label>
                        <div class="p-2 bg-light rounded">{{ $detail->b2b_officer_name ?: 'Anshad Tk' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">Employee ID</label>
                        <div class="p-2 bg-light rounded">{{ $detail->employee_id ?: 'FTM010' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">Designation</label>
                        <div class="p-2 bg-light rounded">{{ $detail->designation ?: 'B2B Manager' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">Official Contact Number</label>
                        <div class="p-2 bg-light rounded">{{ $detail->official_contact_number ?: '+91 95679 81443' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">WhatsApp Business Number</label>
                        <div class="p-2 bg-light rounded">{{ $detail->whatsapp_business_number ?: '+91 95679 81443' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">Official Email ID</label>
                        <div class="p-2 bg-light rounded">{{ $detail->official_email_id ?: 'btob@natdemy.com' }}</div>
                    </div>
                </div>

                <h6 class="mb-3 text-secondary">3. Office Address</h6>
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">Building Name / Floor / Room Number</label>
                        <div class="p-2 bg-light rounded">{{ $detail->building_name ?: 'Nisa Pre College of Arts' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">Street / Road Name</label>
                        <div class="p-2 bg-light rounded">{{ $detail->street_name ?: 'Murikkal Road' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">Locality / Area Name</label>
                        <div class="p-2 bg-light rounded">{{ $detail->locality_name ?: 'Palathingal' }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">City</label>
                        <div class="p-2 bg-light rounded">{{ $detail->city ?: 'Parappanangadi' }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">PIN Code</label>
                        <div class="p-2 bg-light rounded">{{ $detail->pin_code ?: '676303' }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">District</label>
                        <div class="p-2 bg-light rounded">{{ $detail->district ?: 'Malappuram' }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">State</label>
                        <div class="p-2 bg-light rounded">{{ $detail->state ?: 'Kerala' }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">Country</label>
                        <div class="p-2 bg-light rounded">{{ $detail->country ?: 'India' }}</div>
                    </div>
                </div>

                <h6 class="mb-3 text-secondary">4. Operational Schedule</h6>
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">Working Days</label>
                        <div class="p-2 bg-light rounded">{{ $detail->working_days ?: 'Monday – Saturday' }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">Office Hours</label>
                        <div class="p-2 bg-light rounded">{{ $detail->office_hours ?: '09:00 AM – 05:00 PM' }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">Break Time</label>
                        <div class="p-2 bg-light rounded">{{ $detail->break_time ?: '01:15 PM – 02:00 PM' }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">Public Holiday Policy</label>
                        <div class="p-2 bg-light rounded">{{ $detail->holiday_policy ?: 'As per Head Office Circular' }}</div>
                    </div>
                </div>

                <h6 class="mb-3 text-secondary">6. Banking & Payment Details</h6>
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">Account Holder Name</label>
                        <div class="p-2 bg-light rounded">{{ $detail->account_holder_name ?: 'FUTURE AND TREE EDU OLUTION PVT LTD' }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">Bank Name</label>
                        <div class="p-2 bg-light rounded">{{ $detail->bank_name ?: 'Axis Bank, Kallai Road, Kozhikode' }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">Account Number</label>
                        <div class="p-2 bg-light rounded">{{ $detail->account_number ?: '921020041902527' }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="fw-bold text-muted small d-block mb-1">IFSC Code</label>
                        <div class="p-2 bg-light rounded">{{ $detail->ifsc_code ?: 'UTIB0001908' }}</div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@php
function renderInlineEdit($model, $field, $type = 'text', $options = [], $override = null) {
    $value = $override !== null ? $override : ($model->{$field} ?? '');
    
    if ($type === 'date' && $value && is_object($value)) {
        $value = $value->format('Y-m-d');
    }
    
    $displayValue = $value;
    if ($type === 'select' && !empty($options)) {
        $displayValue = $options[$value] ?? ($value === '' ? 'N/A' : $value);
    } elseif ($type === 'date') {
        $displayValue = $value ?: 'N/A';
    } else {
        $displayValue = ($value === '' || $value === null) ? 'N/A' : $value;
    }
    
    $optionsJson = !empty($options) ? htmlspecialchars(json_encode($options, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') : '';
    
    return sprintf(
        '<div class="inline-edit-show" data-field="%s" data-id="%d" data-type="%s" data-current="%s" data-options-json=\'%s\'>
            <span class="display-value d-inline-block">%s</span>
            <a href="#" class="edit-btn-show text-primary ms-2"><i class="ti ti-pencil"></i></a>
        </div>',
        htmlspecialchars($field, ENT_QUOTES),
        $model->id,
        htmlspecialchars($type, ENT_QUOTES),
        htmlspecialchars((string)$value, ENT_QUOTES),
        $optionsJson,
        htmlspecialchars((string)$displayValue, ENT_QUOTES)
    );
}
@endphp

@push('styles')
<style>
    .inline-edit-show {
        position: relative;
        display: block;
        padding: 8px 12px;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .inline-edit-show:hover {
        background: #fff;
        border-color: #7367f0;
        box-shadow: 0 2px 8px rgba(115, 103, 240, 0.1);
    }

    .inline-edit-show .display-value {
        font-weight: 500;
        color: #333;
        min-height: 20px;
    }

    .inline-edit-show .edit-btn-show {
        opacity: 0.4;
        transition: opacity 0.2s;
    }

    .inline-edit-show:hover .edit-btn-show {
        opacity: 1;
    }

    .inline-edit-show.editing {
        background: #fff;
        border-color: #7367f0;
        box-shadow: 0 4px 12px rgba(115, 103, 240, 0.15);
    }

    .inline-edit-show.editing .display-value,
    .inline-edit-show.editing .edit-btn-show {
        display: none;
    }

    .inline-edit-show .edit-form-show {
        display: none;
    }

    .inline-edit-show.editing .edit-form-show {
        display: block;
    }

    .inline-edit-show .edit-form-show input,
    .inline-edit-show .edit-form-show select,
    .inline-edit-show .edit-form-show textarea {
        width: 100%;
        padding: 6px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 13px;
    }

    .inline-edit-show .edit-form-show textarea {
        resize: vertical;
        min-height: 80px;
    }

    .inline-edit-show .edit-form-show .btn-group {
        margin-top: 8px;
        display: flex;
        gap: 8px;
    }

    .inline-edit-show .edit-form-show .btn {
        flex: 1;
    }

    .spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endpush

@push('scripts')
<script>
(function () {
  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function escapeAttr(str) {
    return escapeHtml(str).replace(/`/g, "&#096;");
  }

  function getCsrfToken() {
    var el = document.querySelector('meta[name="csrf-token"]');
    return el ? el.getAttribute("content") : "";
  }

  function init() {
    if (typeof window.jQuery === "undefined") return;
    var $ = window.jQuery;

    var $config = $("#jsTeamDetailsConfig");
    if ($config.length === 0) return;

    var inlineUrl = $config.attr("data-inline-url");

    // --- Inline Edit Handler ---
    $(document).off("click", ".edit-btn-show").on("click", ".edit-btn-show", function (e) {
      e.preventDefault();
      e.stopPropagation();

      var container = $(this).closest(".inline-edit-show");
      if (container.hasClass("editing")) return;

      $(".inline-edit-show.editing")
        .not(container)
        .each(function () {
          $(this).removeClass("editing");
          $(this).find(".edit-form-show").remove();
        });

      var type = container.attr("data-type") || "text";
      var current = container.attr("data-current") || "";

      var html = "";
      if (type === "select") {
        var json = container.attr("data-options-json") || "{}";
        var options = {};
        try {
          options = JSON.parse(json);
        } catch (err) {
          options = {};
        }

        var opts = "";
        Object.keys(options).forEach(function (k) {
          var selected = String(k) === String(current) ? "selected" : "";
          opts +=
            '<option value="' +
            escapeHtml(String(k)) +
            '" ' +
            selected +
            ">" +
            escapeHtml(String(options[k])) +
            "</option>";
        });

        html =
          '<div class="edit-form-show">' +
          '<select class="form-select form-select-sm">' +
          opts +
          "</select>" +
          '<div class="btn-group">' +
          '<button type="button" class="btn btn-success btn-sm save-edit-show">Save</button>' +
          '<button type="button" class="btn btn-secondary btn-sm cancel-edit-show">Cancel</button>' +
          "</div>" +
          "</div>";
      } else {
        html =
          '<div class="edit-form-show">' +
          '<input type="text" class="form-control form-control-sm" value="' +
          escapeAttr(current) +
          '" autocomplete="off" />' +
          '<div class="btn-group">' +
          '<button type="button" class="btn btn-success btn-sm save-edit-show">Save</button>' +
          '<button type="button" class="btn btn-secondary btn-sm cancel-edit-show">Cancel</button>' +
          "</div>" +
          "</div>";
      }

      container.addClass("editing");
      container.append(html);
      container.find("input, select").first().focus();
    });

    $(document).off("click", ".cancel-edit-show").on("click", ".cancel-edit-show", function (e) {
      e.preventDefault();
      e.stopPropagation();
      var container = $(this).closest(".inline-edit-show");
      container.removeClass("editing");
      container.find(".edit-form-show").remove();
    });

    $(document).off("click", ".save-edit-show").on("click", ".save-edit-show", function (e) {
      e.preventDefault();
      e.stopPropagation();

      var container = $(this).closest(".inline-edit-show");
      var field = container.attr("data-field");
      var value = container.find("input, select").val();

      var btn = $(this);
      if (btn.data("busy")) return;
      btn.data("busy", true);
      btn.prop("disabled", true).html('<i class="ti ti-loader-2 spin"></i>');

      $.ajax({
        url: inlineUrl,
        method: "POST",
        data: {
          field: field,
          value: value,
          _token: getCsrfToken(),
        },
        success: function (res) {
          if (res && res.success) {
            container.find(".display-value").text(res.value || "N/A");
            container.attr("data-current", value);
            var successMsg = res.message || "Updated successfully";
            if (typeof window.showToast === "function") {
              window.showToast(successMsg, "success");
            } else if (typeof window.toast_success === "function") {
              window.toast_success(successMsg);
            }
             
            // Reload if specific fields change that might affect other things is optional
          } else {
            var msg =
              res && (res.error || res.message)
                ? res.error || res.message
                : "Update failed";
            if (typeof window.showToast === "function") {
              window.showToast(msg, "error");
            } else if (typeof window.toast_error === "function") {
              window.toast_error(msg);
            }
          }
        },
        error: function (xhr) {
          var msg = "Update failed";
          if (xhr && xhr.responseJSON && xhr.responseJSON.error)
            msg = xhr.responseJSON.error;
          if (typeof window.showToast === "function") {
            window.showToast(msg, "error");
          } else if (typeof window.toast_error === "function") {
            window.toast_error(msg);
          }
        },
        complete: function () {
          btn.data("busy", false);
          btn.prop("disabled", false).html("Save");
          container.removeClass("editing");
          container.find(".edit-form-show").remove();
        },
      });
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();

// --- Course Management Logic ---
(function() {
    var allCourses = @json($allCourses);
    var currentData = @json($detail->interested_courses_details ?? []);
    
    // Normalize currentData to array of objects {course_id, structure_id}
    // The DB stores it as { course_id: [structure_id1, structure_id2] } OR sometimes flat array depending on how it was saved.
    // The controller logic suggests it is stored as { "course_id": ["structure_id"] }
    
    var rowsData = [];
    if (Array.isArray(currentData)) {
        // If it's a flat array (from old format if any), we can't easily map back without more info, 
        // but let's assume valid object format as per controller save
    } else if (typeof currentData === 'object') {
        Object.keys(currentData).forEach(function(courseId) {
            var structures = currentData[courseId];
            if (Array.isArray(structures)) {
                structures.forEach(function(sId) {
                    rowsData.push({
                        course_id: courseId,
                        structure_id: sId
                    });
                });
            } else {
                // Single value case
                 rowsData.push({
                    course_id: courseId,
                    structure_id: structures
                });
            }
        });
    }

    var $container = $('#course-rows-container');
    
    function renderRow(data, index) {
        var courseOptions = '<option value="">Select Course</option>';
        allCourses.forEach(function(c) {
            var selected = String(c.id) === String(data.course_id) ? 'selected' : '';
            courseOptions += '<option value="' + c.id + '" ' + selected + '>' + c.title + '</option>';
        });

        var structureOptions = '<option value="">Select Delivery Structure</option>';
        if (data.course_id) {
            var selectedCourse = allCourses.find(c => String(c.id) === String(data.course_id));
            if (selectedCourse && selectedCourse.academic_delivery_structures) {
                selectedCourse.academic_delivery_structures.forEach(function(s) {
                    var selected = String(s.id) === String(data.structure_id) ? 'selected' : '';
                    structureOptions += '<option value="' + s.id + '" ' + selected + '>' + s.title + '</option>';
                });
            }
        }

        var html = `
            <div class="row mb-2 course-row" data-index="${index}">
                <div class="col-md-5">
                    <select class="form-select course-select" name="courses[]" required>
                        ${courseOptions}
                    </select>
                </div>
                <div class="col-md-5">
                    <select class="form-select structure-select" name="interested_courses_details[]" required>
                        ${structureOptions}
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-course-row"><i class="ti ti-trash"></i></button>
                </div>
            </div>
        `;
        $container.append(html);
    }

    // Initial Render
    function renderAll() {
        $container.empty();
        if (rowsData.length === 0) {
            renderRow({}, 0);
        } else {
            rowsData.forEach(function(row, idx) {
                renderRow(row, idx);
            });
        }
    }

    $('#btn-manage-courses').on('click', function() {
        $('#courses-display-view').hide();
        $('#courses-edit-view').show();
        $(this).hide();
        renderAll();
    });

    $('#btn-cancel-courses').on('click', function() {
        $('#courses-display-view').show();
        $('#courses-edit-view').hide();
        $('#btn-manage-courses').show();
    });

    $('#btn-add-course-row').on('click', function() {
        renderRow({}, $('.course-row').length);
    });

    $(document).on('click', '.remove-course-row', function() {
        $(this).closest('.course-row').remove();
    });

    $(document).on('change', '.course-select', function() {
        var courseId = $(this).val();
        var $structureSelect = $(this).closest('.row').find('.structure-select');
        
        var options = '<option value="">Select Delivery Structure</option>';
        if (courseId) {
            var selectedCourse = allCourses.find(c => String(c.id) === String(courseId));
            if (selectedCourse && selectedCourse.academic_delivery_structures) {
                selectedCourse.academic_delivery_structures.forEach(function(s) {
                    options += '<option value="' + s.id + '">' + s.title + '</option>';
                });
            }
        }
        $structureSelect.html(options);
    });

    $('#btn-save-courses').on('click', function() {
        var $btn = $(this);
        var formData = $('#form-manage-courses').serializeArray();
        var payload = {
            courses: [],
            interested_courses_details: []
        };
        
        // Extract arrays from form data
        $('#form-manage-courses .course-row').each(function() {
            var c = $(this).find('.course-select').val();
            var s = $(this).find('.structure-select').val();
            if (c && s) {
                payload.courses.push(c);
                payload.interested_courses_details.push(s);
            }
        });

        // Use a special endpoint or reusing the update-details one but handling JSON update?
        // The update-details endpoint expects field/value pair. 
        // Since 'interested_courses_details' is a single column (JSON), we can send it as a value, 
        // BUT we have complex mapping logic in controller for store().
        // Let's create a specific update for this complex field by formatting it right here.
        
        // The controller `updateDetails` simply updates the field `interested_courses_details` with the value.
        // We need to format the value to match the DB structure: { "course_id": ["structure_id", ...] }
        
        var structuredData = {};
        for(var i=0; i<payload.courses.length; i++) {
            var cId = payload.courses[i];
            var sId = payload.interested_courses_details[i];
            
            if(!structuredData[cId]) structuredData[cId] = [];
            structuredData[cId].push(sId);
        }
        
        // Save
         $btn.prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i> Saving...');
         
         $.ajax({
            url: $("#jsTeamDetailsConfig").attr("data-inline-url"),
            method: "POST",
            data: {
                field: 'interested_courses_details',
                value: structuredData, // Sending object, middleware/controller should cast/handle
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (res) {
                 if (res && res.success) {
                    window.location.reload(); // Reload to refresh the table view
                } else {
                    alert('Error updating courses');
                }
            },
            error: function() {
                alert('Error updating courses');
            },
            complete: function() {
                 $btn.prop('disabled', false).text('Save Changes');
            }
         });
    });

})();
</script>
@endpush
@endsection
