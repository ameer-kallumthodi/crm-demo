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

    // Table is initialized in the blade (window.ONLINE_TEACHING_FACULTY_TABLE); used for reload after file upload

    $("#jsAddOnlineTeachingFaculty").on("click", function () {
      var url = $(this).data("url");
      if (typeof window.show_ajax_modal === "function" && url) {
        window.show_ajax_modal(url, "Add Online Teaching Faculty");
      }
    });

    // Inline edit open
    $(document).off("click", ".edit-btn").on("click", ".edit-btn", function (e) {
      e.preventDefault();
      e.stopPropagation();

      var container = $(this).closest(".inline-edit");
      if (container.hasClass("editing")) return;

      // Close any existing edit forms
      $(".inline-edit.editing")
        .not(container)
        .each(function () {
          $(this).removeClass("editing");
        });
      $(".edit-form-overlay").remove();

      var field = container.data("field");
      var type = container.data("type") || "text";
      var current =
        container.data("current") !== undefined
          ? String(container.data("current"))
          : "";

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
          '<div class="edit-form">' +
          '<select class="form-select form-select-sm">' +
          opts +
          "</select>" +
          '<div class="btn-group">' +
          '<button type="button" class="btn btn-success btn-sm save-edit">Save</button>' +
          '<button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>' +
          "</div>" +
          "</div>";
      } else if (type === "date") {
        html =
          '<div class="edit-form">' +
          '<input type="date" class="form-control form-control-sm" value="' +
          escapeAttr(current) +
          '" />' +
          '<div class="btn-group">' +
          '<button type="button" class="btn btn-success btn-sm save-edit">Save</button>' +
          '<button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>' +
          "</div>" +
          "</div>";
      } else if (type === "textarea") {
        html =
          '<div class="edit-form">' +
          '<textarea class="form-control form-control-sm" rows="4">' +
          escapeHtml(current) +
          "</textarea>" +
          '<div class="btn-group">' +
          '<button type="button" class="btn btn-success btn-sm save-edit">Save</button>' +
          '<button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>' +
          "</div>" +
          "</div>";
      } else {
        html =
          '<div class="edit-form">' +
          '<input type="text" class="form-control form-control-sm" value="' +
          escapeAttr(current) +
          '" autocomplete="off" />' +
          '<div class="btn-group">' +
          '<button type="button" class="btn btn-success btn-sm save-edit">Save</button>' +
          '<button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>' +
          "</div>" +
          "</div>";
      }

      container.addClass("editing");

      // Calculate position relative to viewport
      var offset = container.offset();
      var scrollTop = $(window).scrollTop();
      var scrollLeft = $(window).scrollLeft();
      var top = offset.top - scrollTop;
      var left = offset.left - scrollLeft;

      // Adjust positioning to prevent going off-screen
      var windowWidth = $(window).width();
      var windowHeight = $(window).height();
      var formWidth = 320;

      if (left + formWidth > windowWidth) {
        left = windowWidth - formWidth - 20;
      }
      if (left < 10) {
        left = 10;
      }
      if (top < 10) {
        top = 10;
      }

      // Create overlay wrapper with fixed positioning
      var $overlay = $('<div class="edit-form-overlay"></div>');
      $overlay.css({
        position: 'fixed',
        top: top + 'px',
        left: left + 'px',
        zIndex: 9999
      });
      $overlay.html(html);
      $overlay.data('container', container);

      $('body').append($overlay);
      $overlay.find("input, select, textarea").first().focus();
    });

    // Inline edit cancel
    $(document).off("click", ".cancel-edit").on("click", ".cancel-edit", function (e) {
      e.preventDefault();
      e.stopPropagation();
      var $overlay = $(this).closest(".edit-form-overlay");
      var container = $overlay.data('container');
      if (container) {
        container.removeClass("editing");
      }
      $overlay.remove();
    });

    // Inline edit save
    $(document).off("click", ".save-edit").on("click", ".save-edit", function (e) {
      e.preventDefault();
      e.stopPropagation();

      var $overlay = $(this).closest(".edit-form-overlay");
      var container = $overlay.data('container');
      if (!container) return;

      var id = container.data("id");
      var field = container.data("field");
      var value = $overlay.find("input, select, textarea").val();

      var btn = $(this);
      if (btn.data("busy")) return;
      btn.data("busy", true);
      btn.prop("disabled", true).html('<i class="ti ti-loader-2 spin"></i>');

      $.ajax({
        url: "/admin/online-teaching-faculties/" + id + "/inline-update",
        method: "POST",
        data: {
          field: field,
          value: value,
          _token: getCsrfToken(),
        },
        success: function (res) {
          if (res && res.success) {
            container.find(".display-value").text(res.value || "N/A");
            container.data("current", value);
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
          if (xhr && xhr.responseJSON && xhr.responseJSON.error) msg = xhr.responseJSON.error;
          if (xhr && xhr.responseJSON && xhr.responseJSON.errors) {
            try {
              msg = Object.values(xhr.responseJSON.errors).flat().join(", ");
            } catch (e) { }
          }
          if (typeof window.showToast === "function") {
            window.showToast(msg, "error");
          } else if (typeof window.toast_error === "function") {
            window.toast_error(msg);
          }
        },
        complete: function () {
          btn.data("busy", false);
          btn.prop("disabled", false).text("Save");
          container.removeClass("editing");
          $overlay.remove();
          // keep table responsive; no reload needed
        },
      });
    });

    // Inline file upload - trigger file input
    $(document).off("click", ".js-inline-upload-btn").on("click", ".js-inline-upload-btn", function (e) {
      e.preventDefault();
      e.stopPropagation();
      var $container = $(this).closest(".inline-file-upload");
      var $input = $container.find(".js-inline-file-input");
      $input.trigger("click");
    });

    // Inline file upload - handle file selection
    $(document).off("change", ".js-inline-file-input").on("change", ".js-inline-file-input", function (e) {
      e.preventDefault();
      e.stopPropagation();

      var input = this;
      var $input = $(this);
      var file = input.files && input.files[0];

      if (!file) return;

      var field = $input.data("field");
      var id = $input.data("id");
      var $container = $input.closest(".inline-file-upload");
      var $btn = $container.find(".js-inline-upload-btn");

      var originalBtnHtml = $btn.html();
      $btn.prop("disabled", true).html('<i class="ti ti-loader-2 spin"></i>');

      var fd = new FormData();
      fd.append("field", field);
      fd.append("file", file);
      fd.append("_token", getCsrfToken());

      $.ajax({
        url: "/admin/online-teaching-faculties/" + id + "/upload-document",
        method: "POST",
        data: fd,
        processData: false,
        contentType: false,
        success: function (res) {
          if (res && res.success) {
            var successMsg = res.message || "Uploaded successfully";
            if (typeof window.showToast === "function") {
              window.showToast(successMsg, "success");
            } else if (typeof window.toast_success === "function") {
              window.toast_success(successMsg);
            }
            // Reload the table to show the updated file
            var dt = window.ONLINE_TEACHING_FACULTY_TABLE;
            if (dt && typeof dt.ajax === "object" && typeof dt.ajax.reload === "function") {
              dt.ajax.reload(null, false);
            }
          } else {
            var msg = res && (res.error || res.message) ? res.error || res.message : "Upload failed";
            if (typeof window.showToast === "function") {
              window.showToast(msg, "error");
            } else if (typeof window.toast_error === "function") {
              window.toast_error(msg);
            }
          }
        },
        error: function (xhr) {
          var msg = "Upload failed";
          if (xhr && xhr.responseJSON && xhr.responseJSON.error) msg = xhr.responseJSON.error;
          if (xhr && xhr.responseJSON && xhr.responseJSON.errors) {
            try {
              msg = Object.values(xhr.responseJSON.errors).flat().join(", ");
            } catch (e) { }
          }
          if (typeof window.showToast === "function") {
            window.showToast(msg, "error");
          } else if (typeof window.toast_error === "function") {
            window.toast_error(msg);
          }
        },
        complete: function () {
          $btn.prop("disabled", false).html(originalBtnHtml);
          // Reset file input
          $input.val("");
        },
      });
    });

    // Copy Form Link
    $(document).off("click", ".js-copy-form-link").on("click", ".js-copy-form-link", function (e) {
      e.preventDefault();
      e.stopPropagation();

      var $btn = $(this);
      var facultyId = $btn.data("faculty-id");

      if ($btn.data("busy")) return;
      $btn.data("busy", true);

      var originalHtml = $btn.html();
      $btn.prop("disabled", true).html('<i class="ti ti-loader-2 spin"></i>');

      $.ajax({
        url: "/admin/online-teaching-faculties/" + facultyId + "/generate-form-link",
        method: "GET",
        dataType: "text",
        success: function (link) {
          // Copy to clipboard
          if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(link).then(function () {
              if (typeof window.showToast === "function") {
                window.showToast("Form link copied to clipboard!", "success");
              } else if (typeof window.toast_success === "function") {
                window.toast_success("Form link copied to clipboard!");
              }
            }).catch(function () {
              fallbackCopyToClipboard(link);
            });
          } else {
            fallbackCopyToClipboard(link);
          }
        },
        error: function (xhr) {
          var msg = "Failed to generate link";
          if (xhr && xhr.responseJSON && xhr.responseJSON.error) msg = xhr.responseJSON.error;
          if (typeof window.showToast === "function") {
            window.showToast(msg, "error");
          } else if (typeof window.toast_error === "function") {
            window.toast_error(msg);
          }
        },
        complete: function () {
          $btn.data("busy", false);
          $btn.prop("disabled", false).html(originalHtml);
        },
      });
    });

    // Open Form Link
    $(document).off("click", ".js-open-form-link").on("click", ".js-open-form-link", function (e) {
      e.preventDefault();
      e.stopPropagation();

      var $btn = $(this);
      var facultyId = $btn.data("faculty-id");

      if ($btn.data("busy")) return;
      $btn.data("busy", true);

      var originalHtml = $btn.html();
      $btn.prop("disabled", true).html('<i class="ti ti-loader-2 spin"></i>');

      $.ajax({
        url: "/admin/online-teaching-faculties/" + facultyId + "/generate-form-link",
        method: "GET",
        dataType: "text",
        success: function (link) {
          window.open(link, '_blank');
          if (typeof window.showToast === "function") {
            window.showToast("Form opened in new tab", "success");
          } else if (typeof window.toast_success === "function") {
            window.toast_success("Form opened in new tab");
          }
        },
        error: function (xhr) {
          var msg = "Failed to generate link";
          if (xhr && xhr.responseJSON && xhr.responseJSON.error) msg = xhr.responseJSON.error;
          if (typeof window.showToast === "function") {
            window.showToast(msg, "error");
          } else if (typeof window.toast_error === "function") {
            window.toast_error(msg);
          }
        },
        complete: function () {
          $btn.data("busy", false);
          $btn.prop("disabled", false).html(originalHtml);
        },
      });
    });

    // Fallback copy to clipboard function
    function fallbackCopyToClipboard(text) {
      var $temp = $("<textarea>");
      $temp.css({
        position: "fixed",
        top: 0,
        left: 0,
        width: "2em",
        height: "2em",
        padding: 0,
        border: "none",
        outline: "none",
        boxShadow: "none",
        background: "transparent"
      });
      $("body").append($temp);
      $temp.val(text).select();
      $temp[0].setSelectionRange(0, 99999);
      try {
        document.execCommand("copy");
        if (typeof window.showToast === "function") {
          window.showToast("Form link copied to clipboard!", "success");
        } else if (typeof window.toast_success === "function") {
          window.toast_success("Form link copied to clipboard!");
        }
      } catch (err) {
        if (typeof window.showToast === "function") {
          window.showToast("Failed to copy link", "error");
        } else if (typeof window.toast_error === "function") {
          window.toast_error("Failed to copy link");
        }
      }
      $temp.remove();
    }

    // Close edit form when clicking outside
    $(document).on("click", function (e) {
      if (!$(e.target).closest(".edit-form-overlay").length &&
        !$(e.target).closest(".edit-btn").length) {
        var $overlay = $(".edit-form-overlay");
        if ($overlay.length) {
          var container = $overlay.data('container');
          if (container) {
            container.removeClass("editing");
          }
          $overlay.remove();
        }
      }
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();

