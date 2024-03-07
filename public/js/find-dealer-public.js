var map;
// var bounds;
function findDealerInitMap() {
  map = new google.maps.Map(document.getElementById("find-dealer-map"), {
    center: { lat: 41.850033, lng: -87.6500523 }, // Set the initial center of the map
    zoom: 4, // Set the initial zoom level
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    // disableDefaultUI: true
  });
  // bounds = new google.maps.LatLngBounds();
}

jQuery(document).ready(function () {
  // findDealerInitMap();
  var typingTimer; // Timer identifier
  var zipcode;
  var markers = [];
  var address;
  var phone;
  var state;
  var zipcode;
  var city;
  var dealer_n;
  var url;
  var email;
  var country_code = jQuery('input:radio[name="clark-country-select"]').val();
  var radius_distance = jQuery("#clark-dropdown-milezone :selected").val();

  jQuery(document).on("change", "#clark-dropdown-milezone", function (e) {
    radius_distance = jQuery(this).val();
  });
  jQuery(document).on(
    "change",
    'input:radio[name="clark-country-select"]',
    function (e) {
      country_code = jQuery(this).val();
    }
  );

  jQuery(document).on("click", "#clark-submit-button", function () {
    zipcode = jQuery("#clark-zip-code").val();
    var radius = radius_distance * 1.60934;
    clearTimeout(typingTimer);
    if (zipcode) {
      jQuery.ajax({
        url: frontend_ajax.ajaxurl, // AJAX URL provided by WordPress
        type: "POST",
        data: {
          action: "get_lat_long_ajax_action", // Action name to be handled by PHP
          zipcode: zipcode, // Any parameters you need to pass to PHP
          country_code: country_code,
        },
        beforeSend: function (res) {
          jQuery(".clark-loading-icon").css("display", "block");
        },
        success: function (response) {
          let status = jQuery.isEmptyObject(response[0]);
          jQuery(".not_found").css("display", "none");
          jQuery(".clark-map-location-details").empty();
          clearMarkers();
          map.setZoom(6);
          if (status) {
            jQuery(".clark-map-detalis-extend").empty();
            jQuery(".clark-results-not-found").css("display", "block");
          } else {
            jQuery(".clark-results-not-found").css("display", "none");
            for (var i = 0; i < response[0].length; i++) {
              address =
                response[0][i].dealer_address == null ||
                response[0][i].dealer_address.trim() === ""
                  ? ""
                  : response[0][i].dealer_address;
              phone =
                response[0][i].phone_number == null ||
                response[0][i].phone_number.trim() === ""
                  ? ""
                  : response[0][i].phone_number;
              state =
                response[0][i].state == null ||
                response[0][i].state.trim() === ""
                  ? ""
                  : response[0][i].state;
              zipcode =
                response[0][i].zipcode == null ||
                response[0][i].zipcode.trim() === ""
                  ? ""
                  : response[0][i].zipcode;
              city =
                response[0][i].city == null || response[0][i].city.trim() === ""
                  ? ""
                  : response[0][i].city;
              dealer_n =
                response[0][i].dealer_name == null ||
                response[0][i].dealer_name.trim() === ""
                  ? ""
                  : response[0][i].dealer_name;
              url =
                response[0][i].url == null || response[0][i].url.trim() === ""
                  ? ""
                  : response[0][i].url;
              email =
                response[0][i].email == null ||
                response[0][i].email.trim() === ""
                  ? ""
                  : response[0][i].email;

              let card = `<div class="clark-store-list-container" id="map_block_${i}">
                            <h4 class="clark-loc-name-wrap">${dealer_n}</h4>
                            <ul class="clark-loc-addr-wrap">
                                <li><i class="fa fa-map-marker"></i> ${address} <br /> ${city}, ${state} ${zipcode}</li>
                                <li><i class="fa fa-phone"></i> <a href="tel:${phone}">${phone}</a></li>`;

              if (email.length > 0) {
                card += `<li><i class="fa fa-envelope"></i> <a href="mailto:${email}">${email}</a></li>`;
              }
              if (url.length > 0) {
                card += `<li><i class="fa fa-globe"></i> <a href="https://${url}" target="_blank">${url}</a></li>`;
              }
              `</ul>
                            <hr>
                        </div>`;
              calculateRadius(
                card,
                response[1],
                response[2],
                zipcode,
                parseFloat(response[0][i].lng),
                parseFloat(response[0][i].lat),
                radius,
                address,
                phone,
                state,
                city,
                dealer_n,
                url,
                email
              );
            }
          }
        },
        complete: function (respond) {
          jQuery(".clark-loading-icon").css("display", "none");
        },
        error: function (xhr, status, error) {
          clearMarkers();
          jQuery(".not_found").css("display", "block");
        },
      });
    }
  });

  // Function to clear markers
  function clearMarkers() {
    for (var i = 0; i < markers.length; i++) {
      markers[i].setMap(null);
    }
    markers = [];
  }

  function calculateRadius(
    card,
    x,
    y,
    zipcode,
    longitude,
    latitude,
    radius,
    address,
    phone,
    state,
    city,
    dealer_n,
    url,
    email
  ) {
    // var center = new google.maps.LatLng(latitude, longitude);
    var center = new google.maps.LatLng(x, y);
    var position = new google.maps.LatLng(latitude, longitude);
    var distance = google.maps.geometry.spherical.computeDistanceBetween(
      center,
      position
    );

    // Draw circle
    // new google.maps.Circle({
    //   // strokeColor: "transparent",
    //   strokeOpacity: 0.8,
    //   strokeWeight: 2,
    //   // fillColor: "transparent",
    //   fillOpacity: 0,
    //   map: map,
    //   center: center,
    //   radius: radius * 1000, // Radius in meters
    // });

    // Check if distance is within radius
    if (distance / 1000 <= radius) {
      jQuery(".clark-results-not-found").css("display", "none");
      jQuery(".clark-map-location-details").append(card);
      // Convert meters to kilometers

      // Mark the given location
      var marker = new google.maps.Marker({
        position: position,
        map: map,
        title: address,
        disableDefaultUI: true,
        icon: {
          url: "https://clarkmhcdev.mediawebdev.com/wp-content/uploads/2024/03/map-pin.png", // URL to your custom icon
          scaledSize: new google.maps.Size(40, 40), // Size of the icon
        },
      });
      marker.addListener("click", () => {
        jQuery(".clark-map-location-details").empty();
        jQuery(".clark-map-detalis-extend").empty();
        jQuery("#clarkToggleBlock_1").css("display", "block");
        let extend_card = `<div id="clarkToggleBlock_1" class="clark-toggle-block" aria-hidden=""">
        <div class="clark-dealer-info-header">
          <span class="clark-header-back-content">
            <i class="fa-solid fa-arrow-left"></i>
            <span class="clark-back-to-dealers" tabindex="0">Back to Dealers in
              <span class="clark-dealer-zipcode">${zipcode}</span>
            </span>
          </span>
          <h4>${dealer_n}</h4>
        </div>
        <div class="clark-dealer-address-details">
          <div class="clark-dealer-location dealer-details">
            <i class="fa fa-map-marker"></i>
            <div class="">${address}, <br>${city}, ${state} ${zipcode}</div>
          </div>
          <div class="clark-dealer-phone dealer-details">
            <i class="fa fa-phone"></i>
            <a href="tel:${phone}" data-dealerno="70022" class="link_phone">${phone}</a>
          </div>`;

        if (email.length > 0) {
          extend_card += `<div class="clark-dealer-website dealer-details">
          <i class="fa fa-envelope"></i>
            <a target="_blank" data-dealerno="70022" class="link_email" href="mailto:${email}">${email}</a>
            </div>`;
        }
        if (url.length > 0){
        extend_card += `<div class="clark-dealer-website dealer-details">
            <i class="fa fa-globe"></i>
            <a target="_blank" data-dealerno="70022" class="link_email" href="${url}">${url}</a>
          </div>`;
      }
        `</div>
      </div>`;
        jQuery(".clark-map-detalis-extend").append(extend_card);
      });
      // console.log("marker", marker);
      map.setCenter(center);
      // Center map at the given location
      marker.setMap(map);
      // bounds.extend(marker.position);
      markers.push(marker);
      // map.fitBounds(bounds);
      map.setZoom(11);
    } else {
      jQuery(".clark-results-not-found").css("display", "block");
      map = new google.maps.Map(document.getElementById("find-dealer-map"), {
        center: { lat: 41.850033, lng: -87.6500523 }, // Set the initial center of the map
        zoom: 4, // Set the initial zoom level
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        // disableDefaultUI: true
      });
    }
  }

  jQuery(document).on("click", ".clark-header-back-content", function () {
    jQuery("#clark-submit-button").click();
    jQuery(".clark-map-detalis-extend").empty();
  });
});
