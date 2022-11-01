// // @string : id from html where the map appears 
// // 
// // return leaflet map object 
// function map_initialize(html_id = 'my_map'){

//     let map = L.map(html_id, scrollWheelZoom = false, keyboard = false, zoomControl = false)
//                     .setView([49.79020826982288, 9.93560301310107], 6.3);

//     L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
//         maxZoom: 18,
//         minZoom: 5,
//         attribution: 'Map data and Imagery &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
//     }).addTo(map);

//     // Get Geocoder für Umkreissuche
//     L.Control.geocoder({
//         collapsed: false,
//         placeholder: 'Umkreissuche (Ort oder PLZ)',
//         defaultMarkGeocode: false
//     }).on('markgeocode', function(e) {
//         map.setView(e.geocode.center, 12); // zoom 10
//     }).addTo(map);

//     L.control.scale().addTo(map);

//     return map;
// }

// const map_html_id = 'my_map';
// const map = map_initialize(map_html_id);


const map = L.map('my_map', scrollWheelZoom = false, keyboard = false, zoomControl = false)
    .setView([49.79020826982288, 9.93560301310107], 6.3);



L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
	maxZoom: 18,
    minZoom: 5,
    attribution: 'Map data and Imagery &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
}).addTo(map);


// Get Geocoder für Umkreissuche
L.Control.geocoder({
    collapsed: false,
    placeholder: 'Umkreissuche (Ort oder PLZ)',
    defaultMarkGeocode: false
}).on('markgeocode', function(e) {
    map.setView(e.geocode.center, 12); // zoom 10
}).addTo(map);

L.control.scale().addTo(map);



async function geojson() {
    let url = '/wp-json/22uhr-plugin/v1/unternehmen/g-u-t';
    const response = await fetch(url)
    const unternehmen = await response.json();
    //console.log(unternehmen);
    return unternehmen;
}



function centerLeafletMapOnMarker(map, marker) {
    var latLngs = [ marker.getLatLng() ];
    var markerBounds = L.latLngBounds(latLngs);
    map.fitBounds(markerBounds);
    map.setZoom(13.5);
    marker.openPopup();
}


//Defing Layer groups for Filtering

var mcgLayerSupportGroup_auto = L.markerClusterGroup.layerSupport(),
    group_abschaltung_all = L.layerGroup();

//Set group names with data-group in filter select options 
var options = document.getElementById('abschaltung_uhrzeit').options;
// start i with 0, 0 position for default option -- wahlen sie Uhrzeit -- 
for (let i = 1; i < options.length; i++) { 
    // <option data-group="abschaltung_19_uhr" value="38"> Spätestens 19 Uhr</option>
    let abschaltung_data_group = options[i].getAttribute("data-group"); 
    // set variable for colletion of html by Name
    // data-group  = target class name
    eval('var group_' +  abschaltung_data_group + '= L.layerGroup();');
    //console.log('var group_' +  abschaltung_data_group + '= L.layerGroup();');
    //ex) var abschaltung_22_uhr = document.getElementsByClassName("abschaltung_22_uhr");
}

mcgLayerSupportGroup_auto.addTo(map);


var control = L.control.layers(null, null, { collapsed: false });

//save markerLayer_id as a value with tag 'map_id_'+post_id
function save_layerId_in_html(markers, option_name='post_id'){
    markers.eachLayer(marker => {
        var post_id = marker['options'][option_name];
        var map_id = markers.getLayerId(marker);
        console.log(post_id);
        document.getElementById('map_id_'+post_id).setAttribute('value',map_id)
    })
}

// Note Pondo: Make map Link Point Clickable
function build_link (markers){
    const divs = document.querySelectorAll('.map_link_point');

    divs.forEach(el => el.addEventListener('click', event => {

        let map_id = parseInt(event.target.getAttribute("value"));
        var marker = markers.getLayer(map_id);
        centerLeafletMapOnMarker(map, marker);
    }))
}

async function main() {

    const json = await geojson();

    //generate marker groups from geojson data
    json.features.forEach(feature => {

        if(feature.firmengruppen_hierarchie != 0){ 
            let popuptext = "<a href = '" + feature.properties.url + "' target=\"_blank\">" + feature.properties.name + "</a>";
            if (feature.filter.abschaltung.slug == "nicht-vorhanden") {
                popuptext = popuptext+ "<p class='" + feature.filter.abschaltung.slug + "'>" + "<span>Seit jeher kein Werbelicht vorhanden</span></p>";
            }
            else{
                popuptext = popuptext+ "<p class='" + feature.filter.abschaltung.slug + "'>" + "<span>Späteste Abschaltung</span> "+feature.filter.abschaltung.name +"!</p>";
            }

            console.log(feature.properties.name);
            let marker = L.marker([
                feature.geometry.coordinates[1],
                feature.geometry.coordinates[0],
            ], {
                name: feature.properties.name,
                post_id: feature.properties.post_id
            });
            console.log(marker);

            marker.bindPopup(popuptext);

            //dynamic
            let abschaltung_slug = feature.filter.abschaltung.slug;
            let abschaltung_slug_unter = 'abschaltung_' + abschaltung_slug.replace(/\-/g, "_");
            let temp_string = 'group_' + abschaltung_slug_unter;
            let group_abschaltung_uhrzeit = window[temp_string];

            //console.log('marker.addTo(group_' +  abschaltung_slug_unter + ');');
            //eval('marker.addTo(group_' +  abschaltung_slug_unter + ');');
            marker.addTo(group_abschaltung_uhrzeit);
            marker.addTo(group_abschaltung_all);
        }




    })


    //checkIn ist eine Methode aus der Marker Cluster Layer Support Library- checkOut siehe https://github.com/ghybs/Leaflet.MarkerCluster.LayerSupport
    mcgLayerSupportGroup_auto.checkIn([group_abschaltung_all]);

    group_abschaltung_all.addTo(map);

    save_layerId_in_html(group_abschaltung_all);
    build_link(group_abschaltung_all);


    for (i = 0; i < selection.length; i++) {
        selection[i].addEventListener('change', toggleGroup_dynamic);
        //selection[i].addEventListener('change', unternehmen_list_filter); // D
    }


    function toggleGroup_dynamic(event) {

        let group_name = this.options[this.selectedIndex].getAttribute('data-group');
        console.log(group_name);
        eval('let group = group_' + group_name + ';');
        //console.log(group);
        let temp_string = 'group_' + group_name;
        let group = window[temp_string];
        mcgLayerSupportGroup_auto["removeLayer"]([group_abschaltung_all]);
        mcgLayerSupportGroup_auto["addLayer"](group);

        map.fitBounds(mcgLayerSupportGroup_auto.getBounds(),{
            padding: [50, 50]
        });
    }



}

main();

/*Toogle Map Fullscreen */
jQuery('.firmen-hide').click(function() {
        jQuery('body').addClass('firmen-hide');
});

jQuery('.firmen-show').click(function() {
    jQuery('body').removeClass('firmen-hide');
});

document.getElementById("change").onclick = function(){
    map.invalidateSize();    
};

