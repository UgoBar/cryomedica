;(function($) {
    "use strict";

    const step1NextButton  = $('#step1NextButton'),
          step2NextButton  = $('#step2NextButton'),
          commitmentInputs = $('input[type=radio]'),
          step3 = $('#step3');

    let commitment;

    //--- MAP ---//
    // On initialise la latitude et la longitude de Paris (centre de la carte)
    let map = L.map('map').setView([48.52, 2.19], 5),
        coordinates, isOpen,
        centerInput   = document.querySelector('input[name=center]'),
        firstClick    = true,
        clickedMarker = false,
        isSelectedCenterOpen;

    let greenIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });
    let redIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    })
    // Fonction d'initialisation de la carte
    function initMap() {
        // Créer l'objet "macarte" et l'insèrer dans l'élément HTML qui a l'ID "map"
        L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png?api_key=62ab9aba-563d-41e4-b951-9d75bf50e6ca', {
            maxZoom: 19,
            type: 'normal',
            format: 'png',
            center: [46.11, -0.01],
            attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>, &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'
        }).addTo(map);


        Object.entries(centers).forEach(center => {

            coordinates = [center[1].lat, center[1].lng];
            isOpen = center[1].isOpen;
            let popupHtml = `<h6>${center[1].name}</h6><p>${center[1].city}</p>`

            if(isOpen) {
                L.marker(coordinates, {icon: greenIcon}).bindPopup(popupHtml).addTo(map).on('click', onPinClick)
            } else {
                L.marker(coordinates, {icon: redIcon}).bindPopup(popupHtml).addTo(map).on('click', onPinClick)
            }
        });
    }

    window.onload = function(){
        // Fonction d'initialisation qui s'exécute lorsque le DOM est chargé
        initMap();
        let popup = L.popup();
    };

    function onPinClick(e) {

        const latLgn = [e.latlng.lat, e.latlng.lng];

        // Retirer le highlight du marker si il est différent
        if(clickedMarker !== e.target._icon && !firstClick) {
            clickedMarker.classList.remove('marker-highlighted')
        }

        // Mettre le highlight sur le marker et définir le nouveau marker
        clickedMarker = e.target._icon
        firstClick = false;
        e.target._icon.classList.add('marker-highlighted')


        Object.entries(centers).forEach(center => {

            coordinates = [center[1].lat, center[1].lng];

            if(coordinates.toString() === latLgn.toString()) {

                // Garder en mémoire le choix du centre
                isSelectedCenterOpen = !!center[1].isOpen;

                console.log('center-id', center[1].id)
                // On update la valeur du champ caché "center" avec l'iD du centre sélectionné
                // addInput(center[1].id);
                centerInput.setAttribute('value', center[1].id)

                // On autorise la suite
                step1NextButton.attr("disabled", false);
            }
        });
    }

    // function addInput(val) {
    //     var input = document.createElement('input');
    //     input.setAttribute('type', 'hidden');
    //     input.setAttribute('name', 'center');
    //     input.setAttribute('value', val);
    //     document.querySelector('form').appendChild(input);
    // }

    map.on('click', (e) => {

        if(!firstClick) {
            let popup = L.popup();

            // On click outside of marker
            if(!popup.isOpen()) {
                centerInput.setAttribute('value', null) // Clear input center's value
                console.log('GOING BY HERE')
                step1NextButton.attr("disabled", true); // Disabled "next" button
                clickedMarker.classList.remove('marker-highlighted')// remove highlight on previous marker
            }
        }

    })

    //* Form js
    function verificationForm() {
        //jQuery time
        let current_fs, next_fs, previous_fs; //fieldsets
        let left, opacity, scale; //fieldset properties which we will animate
        let animating; //flag to prevent quick multi-click glitches
        let step = 1;

        // Lors du clic sur un des 2 engagements
        commitmentInputs.on('change', () => {
            // On autorise la suite du formulaire
            step2NextButton.attr("disabled", false);
            // On sauvegarde du choix d'engagement
            commitment = $('input[name=commitment]:checked').val();
        });

        $(".next").click(function () {
            if (animating) return false;
            animating = true;

            step++;

            if(step === 3) {

                // Pré-reserver
                if(commitment === 'pre-order') {
                    // Centre ouvert
                    if(isSelectedCenterOpen) {
                        step3.html(preOrderHtml);
                    } else {
                        // Centre fermé
                        step3.html(interestHtml);
                    }
                } else {
                    // Ou devenir partenaire
                    step3.html(partnerHtml);

                }
            }

            current_fs = $(this).parent();
            next_fs = $(this).parent().next();

            // activate next step on progressbar using the index of next_fs
            $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

            //show the next fieldset
            next_fs.show();
            //hide the current fieldset with style
            current_fs.animate({
                opacity: 0,
                position: 'absolute'
            }, {
                step: function (now, mx) {
                    //as the opacity of current_fs reduces to 0 - stored in "now"
                    //1. scale current_fs down to 80%
                    scale = 1 - (1 - now) * 0.2;
                    //2. bring next_fs from the right(50%)
                    left = (now * 50) + "%";
                    //3. increase opacity of next_fs to 1 as it moves in
                    opacity = 1 - now;
                    current_fs.css({
                        'transform': 'scale(' + scale + ')',
                        'position': 'absolute'
                    });
                    next_fs.css({
                        'left': left,
                        'opacity': opacity,
                    });
                },
                duration: 800,
                complete: function () {
                    current_fs.hide();
                    animating = false;
                },
                //this comes from the custom easing plugin
                easing: 'easeInOutBack'
            });
        });

        $(".previous").click(function () {
            if (animating) return false;
            animating = true;

            step--;

            current_fs = $(this).parent();
            previous_fs = $(this).parent().prev();

            //de-activate current step on progressbar
            $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

            //show the previous fieldset
            previous_fs.show();
            //hide the current fieldset with style
            current_fs.animate({
                opacity: 0
            }, {
                step: function (now) {
                    //as the opacity of current_fs reduces to 0 - stored in "now"
                    //1. scale previous_fs from 80% to 100%
                    scale = 0.8 + (1 - now) * 0.2;
                    //2. take current_fs to the right(50%) - from 0%
                    left = ((1 - now) * 50) + "%";
                    //3. increase opacity of previous_fs to 1 as it moves in
                    opacity = 1 - now;
                    current_fs.css({
                        'left': left
                    });
                    previous_fs.css({
                        'transform': 'scale(' + scale + ')',
                        'opacity': opacity,
                        'position': 'unset'
                    });
                },
                duration: 800,
                complete: function () {
                    current_fs.hide();
                    animating = true;
                },
                //this comes from the custom easing plugin
                easing: 'easeInOutBack'
            });
        });

        // $(".submit").click(function () {
        //     return false;
        // })
    };


    /*Function Calls*/
    verificationForm ();
})(jQuery);

