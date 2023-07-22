// Javascript für die Seite mit Karte
//  Der hier geschriebene Code sollte nichts leaflet.js zu tun haben.

jQuery(document).ready(function ($) {
  $(".site-header-primary-section-right").click(function () {
    $("body").addClass("nav-show-22");
  });

  $(".navicon-close").click(function () {
    $("body").removeClass("nav-show-22");
  });
});

let dropdown_wrapper = document.querySelectorAll(".dropdown-wrapper");
dropdown_wrapper.forEach((entry) => {
  let dropdown_button = entry.querySelector(".icon-click-area");
  let dropdown_title = entry.querySelector("h3");
  let dropdown_image = entry.querySelector(".logo-wrapper");
  let dropdown_alle = entry.querySelector(".alle");
  [dropdown_button, dropdown_title, dropdown_image, dropdown_alle].forEach(
    (element) => {
      element.addEventListener("click", () => {
        entry.classList.toggle("child-block-open");
      });
    }
  );
});
