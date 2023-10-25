//Show always default option -- wahlen sie Uhrzeit --
document.getElementById("abschaltung_uhrzeit").selectedIndex = 0;

//Check how many options in Selection <- options already definded by tags, options = abschaltung_tags
var options = document.getElementById("abschaltung_uhrzeit").options;

// start i with 0, 0 position for default option -- wahlen sie Uhrzeit --
for (let i = 1; i < options.length; i++) {
  // <option data-group="abschaltung_19_uhr" value="38"> Spätestens 19 Uhr</option>
  let abschaltung_data_group = options[i].getAttribute("data-group");
  // data-group  = target class name
  eval(
    "var " +
      abschaltung_data_group +
      '= document.getElementsByClassName("' +
      abschaltung_data_group +
      '");'
  );
  //ex) var abschaltung_22_uhr = document.getElementsByClassName("abschaltung_22_uhr");
}

// set variable for colletion of html by Name for all Unternehmen
var all_unternehmen = document.getElementsByClassName("unternehmenseintrag");
var comment = document.querySelector(".abschaltung_filter_comment");
document
  .getElementById("abschaltung_uhrzeit")
  .addEventListener("change", function () {
    for (i = 0; i < all_unternehmen.length; i++) {
      all_unternehmen[i].style.display = "none";
      comment.style.display = "none";
    }
    // select somthing
    let uhr_group_text =
      this.options[this.selectedIndex].getAttribute("data-group");
    if (uhr_group_text == "abschaltung_all") {
      for (i = 0; i < all_unternehmen.length; i++)
        all_unternehmen[i].style.display = "flex";
    } else if (uhr_group_text == "abschaltung_nicht_vorhanden") {
      let elements = document.getElementsByClassName(uhr_group_text);
      for (i = 0; i < elements.length; i++) {
        elements[i].style.display = "flex";
      }
      comment.style.display = "block";
    } else if (uhr_group_text == "abschaltung_sonderfall") {
      let elements = document.getElementsByClassName(uhr_group_text);
      for (i = 0; i < elements.length; i++) {
        elements[i].style.display = "flex";
      }
    } else {
      let selected_uhr_value =
        this.options[this.selectedIndex].getAttribute("uhr_value");
      for (i = 0; i < all_unternehmen.length; i++) {
        let abschaltung = all_unternehmen[i].getAttribute("value");
        if (abschaltung != "" && abschaltung <= selected_uhr_value) {
          all_unternehmen[i].style.display = "flex";
        }
      }
    }
  });

//used for some where
var selection = document.getElementsByTagName("select");
