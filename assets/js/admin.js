// main.js - JavaScript para el admin headless

jQuery(document).ready(function ($) {
  // Inicializar color picker
  if (typeof $.fn.wpColorPicker === "function") {
    $(".color-picker").wpColorPicker();
  } else {
    console.warn("wpColorPicker no disponible, usando input type=color");
    $(".color-picker").attr("type", "color");
  }

  // Test GraphQL endpoint
  $("#test-graphql").on("click", function () {
    const button = $(this);
    button.prop("disabled", true).text("Probando...");

    $.post(
      headlessAdmin.ajaxurl,
      {
        action: "headless_test_endpoint",
        endpoint: "graphql",
        _ajax_nonce: headlessAdmin.nonce,
      },
      function (response) {
        if (response.success) {
          alert(headlessAdmin.strings.graphqlTestSuccess);
        } else {
          alert(headlessAdmin.strings.graphqlTestError + ": " + response.data);
        }
      }
    )
      .fail(function () {
        alert(headlessAdmin.strings.graphqlTestError);
      })
      .always(function () {
        button.prop("disabled", false).text("Probar");
      });
  });

  // Reset settings
  $("#reset-settings").on("click", function () {
    if (confirm(headlessAdmin.strings.confirmReset)) {
      $.post(
        headlessAdmin.ajaxurl,
        {
          action: "headless_reset_settings",
          nonce: headlessAdmin.nonce,
        },
        function (response) {
          if (response.success) {
            location.reload();
          } else {
            alert("Error: " + response.data);
          }
        }
      );
    }
  });

  // Preview blocked page
  $("#preview-blocked-page").on("click", function () {
    window.open(headlessAdmin.homeUrl, "_blank");
  });

  // Mejoras de UX para formularios
  $(
    ".headless-form-row input, .headless-form-row textarea, .headless-form-row select"
  )
    .on("focus", function () {
      $(this).closest(".headless-form-row").addClass("focused");
    })
    .on("blur", function () {
      $(this).closest(".headless-form-row").removeClass("focused");
    });

  // Validación básica de formularios
  $("form").on("submit", function (e) {
    let valid = true;
    $(
      ".headless-form-row input[required], .headless-form-row textarea[required]"
    ).each(function () {
      if (!$(this).val().trim()) {
        $(this).addClass("invalid");
        valid = false;
      } else {
        $(this).removeClass("invalid");
      }
    });

    if (!valid) {
      e.preventDefault();
      alert("Por favor, complete todos los campos requeridos.");
    }
  });
});
