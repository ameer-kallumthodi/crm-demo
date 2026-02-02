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

    var $config = $("#jsOnlineTeachingFacultyShowConfig");
    if ($config.length === 0) return;

    var uploadUrl = $config.attr("data-upload-url");
    var inlineUrl = $config.attr("data-inline-url");

    // --- Document Upload Handler ---
    function uploadDoc(field, file) {
      var fd = new FormData();
      fd.append("field", field);
      fd.append("file", file);
      fd.append("_token", getCsrfToken());

      return $.ajax({
        url: uploadUrl,
        method: "POST",
        data: fd,
        processData: false,
        contentType: false,
      });
    }

    $(document).off("click", ".js-upload-doc").on("click", ".js-upload-doc", function () {
      var field = $(this).data("field");
      var $input = $('.js-doc-file[data-field="' + field + '"]');
      var input = $input.length ? $input[0] : null;
      if (!input || !input.files || !input.files[0]) {
        if (typeof window.toast_error === "function")
          window.toast_error("Please choose a file.");
        else if (typeof window.showToast === "function")
          window.showToast("Please choose a file.", "error");
        return;
      }

      var btn = $(this);
      var original = btn.html();
      btn.prop("disabled", true).html('<i class="ti ti-loader-2 spin"></i> Uploading...');

      uploadDoc(field, input.files[0])
        .done(function (res) {
          if (res && res.success) {
            var successMsg = res.message || "Uploaded successfully";
            if (typeof window.showToast === "function") {
              window.showToast(successMsg, "success");
            } else if (typeof window.toast_success === "function") {
              window.toast_success(successMsg);
            }
            setTimeout(function () {
              window.location.reload();
            }, 600);
          } else {
            var msg =
              res && (res.error || res.message)
                ? res.error || res.message
                : "Upload failed";
            if (typeof window.showToast === "function") {
              window.showToast(msg, "error");
            } else if (typeof window.toast_error === "function") {
              window.toast_error(msg);
            }
          }
        })
        .fail(function (xhr) {
          var msg = "Upload failed";
          if (xhr && xhr.responseJSON && xhr.responseJSON.error)
            msg = xhr.responseJSON.error;
          if (xhr && xhr.responseJSON && xhr.responseJSON.errors) {
            try {
              msg = Object.values(xhr.responseJSON.errors).flat().join(", ");
            } catch (e) {}
          }
          if (typeof window.showToast === "function") {
            window.showToast(msg, "error");
          } else if (typeof window.toast_error === "function") {
            window.toast_error(msg);
          }
        })
        .always(function () {
          btn.prop("disabled", false).html(original);
        });
    });

    // --- Inline Edit Handler (for show page fields) ---
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

      var field = container.attr("data-field");
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
      } else if (type === "date") {
        html =
          '<div class="edit-form-show">' +
          '<input type="date" class="form-control form-control-sm" value="' +
          escapeAttr(current) +
          '" />' +
          '<div class="btn-group">' +
          '<button type="button" class="btn btn-success btn-sm save-edit-show">Save</button>' +
          '<button type="button" class="btn btn-secondary btn-sm cancel-edit-show">Cancel</button>' +
          "</div>" +
          "</div>";
      } else if (type === "textarea") {
        html =
          '<div class="edit-form-show">' +
          '<textarea class="form-control form-control-sm" rows="4">' +
          escapeHtml(current) +
          "</textarea>" +
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
      container.find("input, select, textarea").first().focus();
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
      var id = container.attr("data-id");
      var field = container.attr("data-field");
      var value = container.find("input, select, textarea").val();

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
          if (xhr && xhr.responseJSON && xhr.responseJSON.errors) {
            try {
              msg = Object.values(xhr.responseJSON.errors).flat().join(", ");
            } catch (e) {}
          }
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
