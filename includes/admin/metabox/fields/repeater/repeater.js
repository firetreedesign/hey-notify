document.addEventListener("DOMContentLoaded", function (e) {
  class HeyNotifyMetaboxRepeaterField {
    constructor() {
      this.#init();
      this.#initSortable();
      this.#customEventListeners();
      this.#bind();
    }

    #init() {
      // Select all of the metaboxes
      var metaboxes = document.querySelectorAll(".hey-notify-metabox");
      // Check if we found any
      if (metaboxes) {
        // Loop over the metaboxes
        metaboxes.forEach((metabox) => {
          this.#setupTemplate(metabox);
        });
      }
    }

    #setupTemplate(metabox) {
      var template = metabox.querySelector(".hey-notify-repeater-template");
      if (template) {
        var fields = template.querySelectorAll("[data-field-name]");
        if (fields) {
          fields.forEach((field) => {
            field.removeAttribute("name");
          });
        }
      }
    }

    #customEventListeners() {
      // Renumber repeater fields after sort, insert, and remove.
      document.addEventListener("hey-notify-metabox-repeater-renumber", (e) =>
        this.#renumber(e)
      );

      // Reinitialize sortable repeater.
      document.addEventListener("hey-notify-metabox-repeater-init", (e) =>
        this.#initSortable(e)
      );

      jQuery(".hey-notify-repeater-field-inner").on("sortstop", (e, ui) => {
        var fieldGroups = e.target.querySelectorAll("[data-repeater-index]");
        if (fieldGroups) {
          fieldGroups.forEach((group, index) => {
            group.dataset.repeaterIndex = index;
          });
          this.#dispatchEventRenumber({
            target: e.target,
          });
        }
      });
    }

    #initSortable() {
      jQuery(".hey-notify-repeater-field-inner").sortable({
        handle: ".hey-notify-repeater-field-toolbar",
        axis: "y",
        cursor: "grabbing",
      });
    }

    #unbind() {
      // Select all of the metaboxes
      var metaboxes = document.querySelectorAll(".hey-notify-metabox");
      // Check if we found any
      if (metaboxes) {
        // Loop over the metaboxes
        metaboxes.forEach((metabox) => {
          this.#unbindInsertButtons(metabox);
          this.#unbindRemoveButtons(metabox);
        });
      }
    }

    #bind() {
      this.#unbind();

      // Select all of the metaboxes
      var metaboxes = document.querySelectorAll(".hey-notify-metabox");
      // Check if we found any
      if (metaboxes) {
        // Loop over the metaboxes
        metaboxes.forEach((metabox) => {
          this.#bindInsertButtons(metabox);
          this.#bindRemoveButtons(metabox);
        });
      }
    }

    #unbindInsertButtons(metabox) {
      // Select all of the repeater insert buttons
      var repeaterInsertButtons = metabox.querySelectorAll(
        ".hey-notify-metabox-repeater-insert"
      );
      // Loop over the repeater insert buttons
      repeaterInsertButtons.forEach((button) => {
        // Clone it to remove any existing event listeners
        button.replaceWith(button.cloneNode(true));
      });
    }

    #bindInsertButtons(metabox) {
      this.#unbindInsertButtons(metabox);

      // Select all of the repeater insert buttons
      var repeaterInsertButtons = metabox.querySelectorAll(
        ".hey-notify-metabox-repeater-insert"
      );
      // Loop over the repeater insert buttons
      repeaterInsertButtons.forEach((button) => {
        // Add on onClick listener
        button.addEventListener("click", (e) => this.#insertClick(e));
      });
    }

    #unbindRemoveButtons(metabox) {
      // Select all of the repeater remove buttons
      var repeaterRemoveButtons = metabox.querySelectorAll(
        ".hey-notify-repeater-field-toolbar-action.remove"
      );
      // Loop over the repeater remove buttons
      repeaterRemoveButtons.forEach((button) => {
        // Add on onClick listener
        button.replaceWith(button.cloneNode(true));
      });
    }

    #bindRemoveButtons(metabox) {
      this.#unbindRemoveButtons(metabox);

      // Select all of the repeater remove buttons
      var repeaterRemoveButtons = metabox.querySelectorAll(
        ".hey-notify-repeater-field-toolbar-action.remove"
      );
      // Loop over the repeater remove buttons
      repeaterRemoveButtons.forEach((button) => {
        // Add on onClick listener
        button.addEventListener("click", (e) => this.#removeClick(e));
      });
    }

    #removeClick(e) {
      var group = e.target.closest("[data-repeater-field-name]");
      var parent = group.closest(".hey-notify-repeater-field-inner");
      if (group) {
        group.remove();
        this.#dispatchEventRenumber({
          target: parent,
        });
      }
    }

    #renumber(e) {
      var repeaterGroups = e.detail.target.querySelectorAll(
        "[data-repeater-field-name]"
      );
      if (!repeaterGroups) {
        return;
      }
      repeaterGroups.forEach((group, index) => {
        // Update repeater group index.
        group.dataset.repeaterFieldIndex = index;
        // Update the field counter.
        var fieldCounter = group.querySelector(
          ".hey-notify-repeater-field-count"
        );
        if (fieldCounter) {
          fieldCounter.innerHTML = index + 1;
        }
        var fields = group.querySelectorAll("[data-field-name]");
        if (fields) {
          fields.forEach((field) => {
            field.setAttribute(
              "name",
              `${group.dataset.repeaterFieldName}[${index}][${field.dataset.fieldName}]`
            );
          });
        }
      });
    }

    #insertClick(e) {
      e.preventDefault();
      var parent = e.target.closest(".hey-notify-repeater-field-container");
      var template = parent.querySelector(".hey-notify-repeater-template");
      var inner = parent.querySelector(".hey-notify-repeater-field-inner");

      if (template) {
        var newGroup = template.cloneNode(true);
        newGroup.removeAttribute("hidden");
        newGroup.classList.remove("hey-notify-repeater-template");
        var fields = newGroup.querySelectorAll("[data-field-name]");
        if (fields) {
          fields.forEach((field) => {
            field.setAttribute(
              "name",
              `${template.dataset.repeaterFieldName}[][${field.dataset.fieldName}]`
            );
          });
        }
        inner.append(newGroup);
        // Dispatch event to reset fields in new repeater
        this.#dispatchEventInit({
          target: newGroup,
        });

        // Dispatch event to renumber the repeater fields
        this.#dispatchEventRenumber({
          target: inner,
        });

        this.#bindRemoveButtons(inner);
      }
    }

    #dispatchEventInit(data) {
      document.dispatchEvent(
        new CustomEvent("hey-notify-metabox-repeater-init", {
          bubbles: true,
          detail: data,
        })
      );
    }

    #dispatchEventRenumber(data) {
      document.dispatchEvent(
        new CustomEvent("hey-notify-metabox-repeater-renumber", {
          bubbles: true,
          detail: data,
        })
      );
    }
  }

  new HeyNotifyMetaboxRepeaterField();
});
