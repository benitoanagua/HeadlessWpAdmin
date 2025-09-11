jQuery(document).ready(function ($) {
  // Initialize color pickers
  $(".color-picker").wpColorPicker();

  // Test endpoints
  $("#test-graphql").click(function () {
    testEndpoint("graphql");
  });

  $("#test-rest").click(function () {
    testEndpoint("rest");
  });

  // Reset settings
  $("#reset-settings").click(function () {
    if (confirm("Are you sure you want to reset all settings?")) {
      resetSettings();
    }
  });

  // Preview blocked page
  $("#preview-blocked-page").click(function () {
    window.open(headless_admin.home_url, "_blank");
  });

  // Export settings
  $("#export-settings").click(function () {
    exportSettings();
  });

  // Import settings
  $("#import-settings").click(function () {
    $("#import-file").click();
  });

  $("#import-file").change(function () {
    if (this.files.length > 0) {
      importSettings(this.files[0]);
    }
  });

  // Tab functionality
  $(".nav-tab").click(function (e) {
    e.preventDefault();
    var tab = $(this).data("tab");
    showTab(tab);
  });

  // Initialize first tab
  var activeTab = $(".nav-tab-active").data("tab") || "general";
  showTab(activeTab);

  // Functions
  function testEndpoint(endpoint) {
    $.post(
      headless_admin.ajax_url,
      {
        action: "headless_test_endpoint",
        endpoint: endpoint,
        nonce: headless_admin.nonce,
      },
      function (response) {
        if (response.success) {
          alert(response.data);
        } else {
          alert("Error: " + response.data);
        }
      }
    ).fail(function () {
      alert("Request failed. Please try again.");
    });
  }

  function resetSettings() {
    $.post(
      headless_admin.ajax_url,
      {
        action: "headless_reset_settings",
        nonce: headless_admin.nonce,
      },
      function (response) {
        if (response.success) {
          alert("Settings reset successfully");
          location.reload();
        } else {
          alert("Error: " + response.data);
        }
      }
    ).fail(function () {
      alert("Request failed. Please try again.");
    });
  }

  function exportSettings() {
    window.location.href =
      headless_admin.ajax_url +
      "?action=headless_export_settings&nonce=" +
      headless_admin.nonce;
  }

  function importSettings(file) {
    var formData = new FormData();
    formData.append("action", "headless_import_settings");
    formData.append("nonce", headless_admin.nonce);
    formData.append("import_file", file);

    $.ajax({
      url: headless_admin.ajax_url,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.success) {
          alert("Settings imported successfully");
          location.reload();
        } else {
          alert("Error: " + response.data);
        }
      },
      error: function () {
        alert("Import failed. Please try again.");
      },
    });
  }

  function showTab(tab) {
    // Hide all tab content
    $(".tab-content").hide();

    // Show selected tab content
    $("#" + tab + "-tab").show();

    // Update active tab
    $(".nav-tab").removeClass("nav-tab-active");
    $('.nav-tab[data-tab="' + tab + '"]').addClass("nav-tab-active");

    // Update URL
    var url = new URL(window.location);
    url.searchParams.set("tab", tab);
    window.history.replaceState({}, "", url);
  }

  // Live preview for color changes
  $(".color-picker").on("change", function () {
    updateColorPreview($(this));
  });

  function updateColorPreview($input) {
    var previewId = $input.attr("id") + "-preview";
    var $preview = $("#" + previewId);
    var color = $input.val();

    if ($preview.length === 0) {
      $preview = $('<div class="color-preview" id="' + previewId + '"></div>');
      $input.after($preview);
    }

    $preview.css("background-color", color);
  }

  // Initialize color previews
  $(".color-picker").each(function () {
    updateColorPreview($(this));
  });

  // Toggle advanced options
  $(".toggle-advanced").click(function () {
    $(this).toggleClass("active");
    $(this).next(".advanced-options").slideToggle();
  });
});
