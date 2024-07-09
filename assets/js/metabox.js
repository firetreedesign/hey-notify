document.addEventListener("DOMContentLoaded", function (e) {
  const helpLinks = document.querySelectorAll(
    "table.hey-notify-form-table .dashicons-editor-help"
  );
  helpLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const helpLinkButton = document.querySelector(
        "button#contextual-help-link"
      );
      if (helpLinkButton) {
        helpLinkButton.click();
      }
    });
  });

  // Retrieve all of the metaboxes.
  var metaboxes = document.querySelectorAll(".hey-notify-metabox");
  if (metaboxes) {
    metaboxes.forEach((metabox) => {
      metabox.addEventListener("change", function (e) {
        evalutateConditionalLogic();
      });
    });
  }

  evalutateConditionalLogic();

  function evalutateConditionalLogic() {
    // Retrieve all of the metaboxes.
    var metaboxes = document.querySelectorAll(".hey-notify-metabox");
    if (metaboxes) {
      metaboxes.forEach((metabox) => {
        // Retrieve the conditional logic fields.
        var fields = metabox.querySelectorAll("div[data-conditional-logic]");
        fields.forEach((field) => {
          // Retrieve the conditions.
          var conditions = field.dataset.conditionalLogic;
          conditions = JSON.parse(conditions);
          // Check that the conditions are an array.
          if (!Array.isArray(conditions)) {
            return;
          }
          // Default to hiding the field.
          var showField = false;
          // Loop over the conditions.
          conditions.forEach((conditionGroup) => {
            // Check that the condition group is an array.
            if (!Array.isArray(conditionGroup)) {
              return;
            }
            // Default to showing the group.
            var showGroup = true;
            // Loop over the condition group.
            conditionGroup.forEach((condition) => {
              // Check that the 'field' property exists.
              if (typeof condition.field === undefined) {
                showGroup = false;
                return;
              }
              // Check that the 'value' property exists.
              if (typeof condition.value === undefined) {
                showGroup = false;
                return;
              }
              // Define the conditional field.
              var conditionalField = null;
              // Check if we are in a repeater field.
              var repeaterFields = field.closest(".hey-notify-repeater-fields");
              var isTemplate = field.closest(".hey-notify-repeater-template");
              if (isTemplate) {
                conditionalField = isTemplate.querySelector(
                  `[data-field-name='${condition.field}']`
                );
              } else if (repeaterFields) {
                conditionalField = repeaterFields.querySelector(
                  `[data-field-name='${condition.field}']`
                );
              } else {
                var metaboxContainer = field.closest(".hey-notify-metabox");
                if (metaboxContainer) {
                  conditionalField = metaboxContainer.querySelector(
                    `[data-field-name='${condition.field}']`
                  );
                }
              }

              // Check that it exists.
              if (!conditionalField) {
                showGroup = false;
                return;
              }

              // Compare the values
              if (conditionalField.value !== condition.value) {
                showGroup = false;
                return;
              }
            });
            if (showGroup) {
              showField = showGroup;
            }
          });

          if (showField) {
            field.removeAttribute("hidden");
          } else {
            field.setAttribute("hidden", "");
          }
        });
      });
    }
  }
});
