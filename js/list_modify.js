//Show always default option -- wahlen sie Uhrzeit -- 
document.getElementById('abschaltung_uhrzeit').selectedIndex = 0;

//Check how many options in Selection <- options already definded by tags, options = abschaltung_tags 
var options = document.getElementById('abschaltung_uhrzeit').options;

// start i with 0, 0 position for default option -- wahlen sie Uhrzeit -- 
for (let i = 1; i < options.length; i++) { 
    // <option data-group="abschaltung_19_uhr" value="38"> Spätestens 19 Uhr</option>
    let abschaltung_data_group = options[i].getAttribute("data-group"); 
    // data-group  = target class name
    eval('var ' +  abschaltung_data_group + '= document.getElementsByClassName("' +  abschaltung_data_group  + '");');
    //ex) var abschaltung_22_uhr = document.getElementsByClassName("abschaltung_22_uhr");
}

// set variable for colletion of html by Name for all Unternehmen
var all_unternehmen = document.getElementsByClassName("unternehmenseintrag-filter");



document.getElementById('abschaltung_uhrzeit').addEventListener('change', function() {


    for (i = 0; i < all_unternehmen.length; i++) {
        all_unternehmen[i].style.display = 'none';
    }

    
    let uhr_group_text = this.options[this.selectedIndex].getAttribute("data-group");

    if (uhr_group_text =="abschaltung_all"){
        for (i = 0; i < all_unternehmen.length; i++) all_unternehmen[i].style.display = 'flex';
    }else if (uhr_group_text =="abschaltung_nicht_vorhanden"){


        let elements = document.getElementsByClassName(uhr_group_text);
        
        for (i = 0; i < elements.length; i++) {
            elements[i].style.display = "flex";
        }
    }else{
        
        for (let j = 1; j < this.options.length-1; ++j) {
            if (this.options[j].getAttribute("uhr_value") <= this.options[this.selectedIndex].getAttribute("uhr_value")){
                
                let target_group = this.options[j].getAttribute("data-group");
                let elements = document.getElementsByClassName(target_group);
        
                for (i = 0; i < elements.length; i++) {
                    elements[i].style.display = "flex";
                }
            }
          }
        
    }
  });

//used for some where
var selection = document.getElementsByTagName("select");



function sortlist() {

var my_options = jQuery("#abschaltung_uhrzeit");
var selected = jQuery("#abschaltung_uhrzeit").val();
console.log(selected);

my_options.sort(function(a,b) {
    console.log(a.uhr_value);
    if (a.text > b.text) return 1;
    if (a.text < b.text) return -1;
    return 0
})

jQuery("#my_select").empty().append( my_options );
jQuery("#my_select").val(selected);

}

sortlist();







        



