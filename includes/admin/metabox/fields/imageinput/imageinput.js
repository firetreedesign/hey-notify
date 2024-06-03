document.addEventListener("DOMContentLoaded", function (e) {
  document.addEventListener("hey-notify-metabox-repeater-init", function (e) {
    HeyNotifyImageInputField.init();
  });

  HeyNotifyImageInputField = {
    init: function () {
      HeyNotifyImageInputField._bind();
    },

    _unbind: function () {
      var inputs = document.querySelectorAll(
        ".hey-notify-imageinput-field-input"
      );
      inputs.forEach((input) => {
        input.replaceWith(input.cloneNode(true));
      });
      var buttons = document.querySelectorAll(
        ".hey-notify-imageinput-field-button"
      );
      buttons.forEach((button) => {
        button.replaceWith(button.cloneNode(true));
      });

      var removeButtons = document.querySelectorAll(
        ".hey-notify-imageinput-field-remove"
      );
      removeButtons.forEach((button) => {
        button.replaceWith(button.cloneNode(true));
      });
    },

    _bind: function () {
      HeyNotifyImageInputField._unbind();

      var buttons = document.querySelectorAll(
        ".hey-notify-imageinput-field-button"
      );
      buttons.forEach((button) => {
        button.addEventListener("click", HeyNotifyImageInputField._buttonClick);
      });

      var removeButtons = document.querySelectorAll(
        ".hey-notify-imageinput-field-remove"
      );
      removeButtons.forEach((button) => {
        button.addEventListener("click", HeyNotifyImageInputField._removeImage);
      });
    },

    _buttonClick: function (e) {
      e.preventDefault();
      var container = e.target.closest(
        ".hey-notify-imageinput-field-container"
      );
      var media_input = container.querySelector(
        ".hey-notify-imageinput-field-input"
      );
      var image_frame;
      if (image_frame) {
        image_frame.open();
      }

      // Define image_frame as wp.media object
      image_frame = wp.media({
        title: "Select Image",
        multiple: false,
        library: {
          type: "image",
        },
      });

      image_frame.on("close", async function () {
        var selection = image_frame.state().get("selection");
        var gallery_ids = new Array();
        var my_index = 0;
        selection.each(function (attachment) {
          gallery_ids[my_index] = attachment["id"];
          my_index++;
        });
        var ids = gallery_ids.join(",");
        media_input.value = ids;
        await HeyNotifyImageInputField._getImage(e, ids);
      });

      image_frame.on("open", function () {
        var selection = image_frame.state().get("selection");
        var ids = media_input.value.split(",");
        ids.forEach(function (id) {
          var attachment = wp.media.attachment(id);
          attachment.fetch();
          selection.add(attachment ? [attachment] : []);
        });
      });

      image_frame.open();
    },

    _removeImage: function (e) {
      var container = e.target.closest(
        ".hey-notify-imageinput-field-container"
      );
      var preview = container.querySelector(
        ".hey-notify-imageinput-field-preview"
      );
      var input = container.querySelector(".hey-notify-imageinput-field-input");

      if (!preview) {
        return;
      }
      input.value = "";
      preview.innerHTML = "";
      e.target.classList.add("hidden");
    },

    _getImage: async function (e, id) {
      var apiURL = wpApiSettings.root + `heynotify/v1/service_avatar`;
      var wpnonce = wpApiSettings.nonce;
      var response = await fetch(apiURL, {
        method: "POST",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          _wpnonce: wpnonce,
          id: id,
        }),
      })
        .then((response) => response.json())
        .then((data) => {
          var container = e.target.closest(
            ".hey-notify-imageinput-field-container"
          );
          var preview = container.querySelector(
            ".hey-notify-imageinput-field-preview"
          );
          if (!preview) {
            return;
          }
          preview.innerHTML = data.image_url;
          if (id) {
            container.querySelector(".dashicons").classList.remove("hidden");
          }
        });
    },
  };

  HeyNotifyImageInputField.init();
});
