document.addEventListener("DOMContentLoaded", function (e) {
  document
    .querySelector("#hey-notify-cpt-refresh")
    .addEventListener("click", heyNotifyRefreshCPT);
});

async function heyNotifyRefreshCPT(e) {
  e.preventDefault();
  var originalText = e.target.innerHTML;
  e.target.innerHTML = heynotify.messages.running;
  e.target.setAttribute("disabled", "");

  var apiURL = wpApiSettings.root + `heynotify/v1/cptrefresh`;
  var wpnonce = wpApiSettings.nonce;
  var response = await fetch(apiURL, {
    method: "POST",
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      _wpnonce: wpnonce,
    }),
  });
  if (200 === response.status) {
    e.target.innerHTML = heynotify.messages.done;
  } else {
    e.target.innerHTML = heynotify.messages.error;
  }
  setTimeout(function () {
    e.target.innerHTML = originalText;
    e.target.removeAttribute("disabled");
  }, 3000);
}
