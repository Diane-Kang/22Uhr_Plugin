

//  Marker fucus functions

function centerLeafletMapOnMarker(map, marker) {

  let latLngs = [ marker.getLatLng() ];
  let markerBounds = L.latLngBounds(latLngs);
  
  map.fitBounds(markerBounds);
  map.setZoom(13);
  //map.setView(marker.getLatLng(),13);
  setTimeout(function(){ 
      var parent = getParentAtCurrentZoom(marker);
      //console.log(marker__parent.spiderfy);
      if(parent instanceof L.MarkerCluster){

          parent.spiderfy();        //
          //parent.fire('click');        //
          
          //marker.__parent.spiderfy();
          
      }else{
         // map.setZoom(13);
      }
      marker.openPopup();
      console.log(map.getZoom());
   }, 500);
}


function getParentAtCurrentZoom(marker) {
  var currentZoom = map.getZoom();
  while (marker.__parent && marker.__parent._zoom >= currentZoom) {
    marker = marker.__parent;
  }
  console.log(marker);
  return marker;
}


//save markerLayer_id as a value with tag 'map_id_'+post_id
function save_layerId_in_html(markers, option_name='post_id'){
  console.log('any')
  markers.eachLayer(marker => {
      var post_id = marker['options'][option_name];

      var map_id = markers.getLayerId(marker);     
      console.log(marker['options']);
      document.getElementById('map_id_'+post_id).setAttribute('value',map_id)
  })
}

// Note Pondo: Make map Link Point Clickable
function build_link (markers){
  const divs = document.querySelectorAll('.map_link_point');

  divs.forEach(el => el.addEventListener('click', event => {

      let map_id = parseInt(event.target.getAttribute("value"));
      var marker = markers.getLayer(map_id);
      centerLeafletMapOnMarker(map, marker, mcgLayerSupportGroup_auto);
  }))
}



function build_marker_popup_content(feature){

  let popup_title_text = "<a href = '" + feature.properties.url + "' target=\"_blank\">" + feature.properties.name + "</a>";

  let popup_mittel_text;
  if (feature.filter.abschaltung.slug == "nicht-vorhanden") {
    popup_mittel_text = "<p class='" + feature.filter.abschaltung.slug + "'>" + "<span>Seit jeher kein Werbelicht vorhanden</span></p>";
  }
  else{
    popup_mittel_text = "<p class='" + feature.filter.abschaltung.slug + "'>" + "<span>Späteste Abschaltung</span> "+feature.filter.abschaltung.name +"!</p>";
  }

  let popup_ending_text="";
  let url = feature.properties.url.slice(0,-1);
  if(feature.firmengruppen_hierarchie==2){
    popup_title_text = feature.properties.name;
    popup_ending_text = "<a href = '" + url.substring(0, url.lastIndexOf('/')) + "' target=\"_blank\"> zum Hauphaus</a>";
  }

  return popup_title_text + popup_mittel_text + popup_ending_text;
}

function build_marker_object(feature){
  let marker = L.marker([
    feature.geometry.coordinates[1],
    feature.geometry.coordinates[0],
  ], {
    name: feature.properties.name,
    post_id: feature.properties.post_id
  });
  return marker;
}