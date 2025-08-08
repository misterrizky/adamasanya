function printDiv(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;
}
function text_only(obj) {
    $('#' + obj).bind('keypress', function (event) {
        var regex = new RegExp("^[A-Z a-z 0-9]+$");
        var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
        if (!regex.test(key)) {
            event.preventDefault();
            return false;
        }
    });
}

function username(obj) {
    $('#' + obj).bind('keypress', function (event) {
        var regex = new RegExp("^[A-Za-z0-9]+$");
        var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
        if (!regex.test(key)) {
            event.preventDefault();
            return false;
        }
    });
}

function number_only(obj) {
    $('#' + obj).bind('keypress', function (event) {
        var regex = new RegExp("^[0-9]+$");
        var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
        if (!regex.test(key)) {
            event.preventDefault();
            return false;
        }
    });
}

function format_email(obj) {
    $('#' + obj).bind('keypress', function (event) {
        var regex = new RegExp("^[A-Za-z0-9@_.]+$");
        var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
        if (!regex.test(key)) {
            event.preventDefault();
            return false;
        }
    });
}

function format_ribuan(nStr) {
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}
function ribuan(obj) {
    $('#' + obj).keyup(function (event) {
        if (event.which >= 37 && event.which <= 40) return;
        // format number
        $(this).val(function (index, value) {
            return value
                .replace(/\D/g, "")
                .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        });
        var id = $(this).data("id-selector");
        var classs = $(this).data("class-selector");
        var value = $(this).val();
        var noCommas = value.replace(/,/g, "");
        $('#' + id).val(noCommas);
        $('.' + classs).val(noCommas);
    });
}
function copyText(target){
    // Select elements
    var target = document.getElementById(target);
    var button = target.nextElementSibling;

    // Init clipboard -- for more info, please read the offical documentation: https://clipboardjs.com/
    clipboard = new ClipboardJS(button, {
        target: target,
        text: function () {
            return target.innerHTML;
        }
    });

    // Success action handler
    clipboard.on('success', function (e) {
        var checkIcon = button.querySelector('.ki-check');
        var copyIcon = button.querySelector('.ki-copy');

        // Exit check icon when already showing
        if (checkIcon) {
            return;
        }

        // Create check icon
        checkIcon = document.createElement('i');
        checkIcon.classList.add('ki-duotone');
        checkIcon.classList.add('ki-check');
        checkIcon.classList.add('fs-2x');

        // Append check icon
        button.appendChild(checkIcon);

        // Highlight target
        const classes = ['text-success', 'fw-boldest'];
        target.classList.add(...classes);

        // Highlight button
        button.classList.add('btn-success');

        // Hide copy icon
        copyIcon.classList.add('d-none');

        // Revert button label after 3 seconds
        setTimeout(function () {
            // Remove check icon
            copyIcon.classList.remove('d-none');

            // Revert icon
            button.removeChild(checkIcon);

            // Remove target highlight
            target.classList.remove(...classes);

            // Remove button highlight
            button.classList.remove('btn-success');
        }, 3000)
    });
}
async function recordAudio() {
    if (window.recorder && window.recorder.state === "recording") {
        window.recorder.stop();
    } else {
        let toggle = document.getElementById("recording-button");
        let stream = await navigator.mediaDevices.getUserMedia({
            audio: true,
            video: false,
        });
        window.recorder = new MediaRecorder(stream);

        let chunks = [];
        window.recorder.ondataavailable = function (event) {
            if (event.data.size <= 0) {
                return;
            }
            chunks.push(event.data);
        };

        window.recorder.onstart = function () {
            toggle.innerHTML = `<i class="fa fa-square"></i>`;
        };

        window.recorder.onstop = function () {
            let blob = new Blob(chunks, { type: "audio/mp3" });
            toggle.innerHTML = `<i class="fa fa-circle"></i>`;
            document.getElementById("audio-element").src =
                URL.createObjectURL(blob);
            let tracks = stream.getTracks();
            tracks.forEach((track) => track.stop());
        };

        window.recorder.start();
    }
}

function launchBarcodeScanner() {
    if (
        !navigator.mediaDevices ||
        !navigator.mediaDevices.getUserMedia ||
        !window.BarcodeDetector
    ) {
        // console.log(
        //     "Your device does not support the Barcode Detection API. Try again on Chrome Desktop or Android"
        // );
    } else {
        startDetection();
    }
}

async function startDetection() {
    //we start the device's camera
    let video = document.getElementById("barcode-detection-video");
    let stream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: "environment" },
    });
    video.srcObject = stream;
    video.play();

    //for the purpose of this demo, we're only detecting QR codes, but there are plenty of other barcodes formats we could detect
    //see https://developer.mozilla.org/en-US/docs/Web/API/Barcode_Detection_API#supported_barcode_formats
    let barcodeDetector = new BarcodeDetector({ formats: ["qr_code"] });

    video.addEventListener("loadedmetadata", async function () {
        let canvas = document.createElement("canvas");
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        let context = canvas.getContext("2d");

        let checkForQrCode = async function () {
            //we draw the current view from the camera on a canvas
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            //then we pass that canvas to the barcode detector
            let barcodes = await barcodeDetector.detect(canvas);

            if (barcodes.length > 0) {
                let barcodeData = barcodes[0].rawValue;
                // console.log(
                //     "Detected QR code with the following content: " +
                //         barcodeData
                // );
            }

            requestAnimationFrame(checkForQrCode);
        };

        checkForQrCode();
    });
}

//the challenge is a crucial part of the authentication process,
//and is used to mitigate "replay attacks" and allow server-side authentication
//in a real app, you'll want to generate the challenge server-side and
//maintain a session or temporary record of this challenge in your DB
function generateRandomChallenge() {
    let length = 32;
    let randomValues = new Uint8Array(length);
    window.crypto.getRandomValues(randomValues);
    return randomValues;
}

async function createPasskey() {
    if (
        !navigator.credentials ||
        !navigator.credentials.create ||
        !navigator.credentials.get
    ) {
        // return console.log(
        //     "Your browser does not support the Web Authentication API"
        // );
    }

    let credentials = await navigator.credentials.create({
        publicKey: {
            challenge: generateRandomChallenge(),
            rp: { name: "Progressier", id: window.location.hostname },
            //here you'll want to pass the user's info
            user: {
                id: new Uint8Array(16),
                name: "johndoe@example.com",
                displayName: "John Doe",
            },
            pubKeyCredParams: [
                { type: "public-key", alg: -7 },
                { type: "public-key", alg: -257 },
            ],
            timeout: 60000,
            authenticatorSelection: {
                residentKey: "preferred",
                requireResidentKey: false,
                userVerification: "preferred",
            },
            attestation: "none",
            extensions: { credProps: true },
        },
    });
    //in a real app, you'll store the credentials against the user's profile in your DB
    //here we'll just save it in a global variable
    window.currentPasskey = credentials;
    console.log(credentials);

    //we update our demo buttons
    document.getElementById("authenticate-btn").innerHTML = "Authenticated";
    document.getElementById("authenticate-btn").classList.add("disabled");
    document.getElementById("verify-btn").classList.remove("disabled");
}

async function verifyPasskey() {
    try {
        //to verify a user's credentials, we simply pass the
        //unique ID of the passkey we saved against the user profile
        //in this demo, we just saved it in a global variable
        let credentials = await navigator.credentials.get({
            publicKey: {
                challenge: generateRandomChallenge(),
                allowCredentials: [
                    { type: "public-key", id: window.currentPasskey.rawId },
                ],
            },
        });
        console.log(credentials);
        // console.log("Biometric authentication successful!");
    } catch (err) {
        // console.log(err);
    }
}

async function pickContacts() {
    if (!navigator.contacts || !window.ContactsManager) {
        console.log(
            "Your device does not support the Contact Picker API. Open this page on Android Chrome to give it a try!"
        );
    } else {
        //first we ask the browser to tell us which properties the device supports
        //available properties include 'name', 'tel', 'email', 'address', and 'icon'
        let propertiesAvailable = await navigator.contacts.getProperties();

        //then we open the contact picker with these properties
        let contacts = await navigator.contacts.select(propertiesAvailable, {
            multiple: true,
        });
        addContactsToTable(contacts);
    }
}

function addContactsToTable(contacts) {
    let table = document.querySelector(".contacts-table");

    contacts.forEach(function (contact) {
        let newRow = document.createElement("tr");
        newRow.innerHTML =
            `<td><img src="https://progressier.com/assets/img/profile-picture.svg" alt="default avatar"/></td>
          <td>` +
            (contact.name || "unknown") +
            `</td>
          <td>` +
            (contact.email || "unknown") +
            `</td>
          <td>` +
            (contact.tel || "unknown") +
            `</td>
          <td>` +
            (contact.address || "unknown") +
            `</td>
       `;
        table.appendChild(newRow);
    });
}

async function getMotion() {
    if (
        !window.DeviceMotionEvent ||
        !window.DeviceMotionEvent.requestPermission
    ) {
        return console.log(
            "Your current device does not have access to the DeviceMotion event"
        );
    }

    let permission = await window.DeviceMotionEvent.requestPermission();
    if (permission !== "granted") {
        return console.log(
            "You must grant access to the device's sensor for this demo"
        );
    }
}


async function getOrientation() {
    if (
        !window.DeviceOrientationEvent ||
        !window.DeviceOrientationEvent.requestPermission
    ) {
        return console.log(
            "Your current device does not have access to the DeviceOrientation event"
        );
    }

    let permission = await window.DeviceOrientationEvent.requestPermission();
    if (permission !== "granted") {
        return console.log(
            "You must grant access to the device's sensor for this demo"
        );
    }
}
async function chooseAFile() {
    if (!window.showOpenFilePicker) {
        console.log(
            "Your current device does not support the File System API. Try again on desktop Chrome!"
        );
    } else {
        //here you specify the type of files you want to allow
        let options = {
            types: [
                {
                    description: "Images",
                    accept: {
                        "image/*": [".png", ".gif", ".jpeg", ".jpg", ".svg"],
                        "text/*": [".txt", ".json"],
                        "application/*": [".json"],
                    },
                },
            ],
            excludeAcceptAllOption: true,
            multiple: false,
        };

        // Open file picker and choose a file
        let fileHandle = await window.showOpenFilePicker(options);
        if (!fileHandle[0]) {
            return;
        }

        // get the content of the file
        let file = await fileHandle[0].getFile();
        previewFile(file);
    }
}

function previewFile(file) {
    let previewContainer = document.getElementById("file-preview-container");
    previewContainer.innerHTML = "";

    if (file.type.startsWith("image/")) {
        let imgPreview = document.createElement("img");
        imgPreview.src = URL.createObjectURL(file);
        previewContainer.appendChild(imgPreview);
    } else if (
        file.type.startsWith("text/") ||
        file.type.startsWith("application/")
    ) {
        let reader = new FileReader();
        reader.onload = function (event) {
            let textPreview = document.createElement("div");
            textPreview.textContent = event.target.result;
            previewContainer.appendChild(textPreview);
        };
        reader.readAsText(file);
    } else {
        console.log(
            "This demo does not support this specific type of file, but your own implementation could!"
        );
    }
}

function updateMap(position) {
    // These are the coordinates returned by the Geolocation API
    let latitude = position.coords.latitude;
    let longitude = position.coords.longitude;

    // Recenter our map around the coordinates
    window.demoMap.setView([latitude, longitude], 13);

    // Add a marker at the specified coordinates
    var marker = L.marker([latitude, longitude]).addTo(window.demoMap);

    // Add a popup to the marker
    marker.bindPopup("Hello, this is your location!");

    // For demo purposes only, display the latitude and longitude to the user
    console.log("Latitude:" + latitude + ", Longitude:" + longitude);
}

function geolocationInaccessible(err) {
    //for demo purposes only, we show why location data can't be accessed
    console.log(err.message);
}

function requestLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            updateMap,
            geolocationInaccessible
        );
    } else {
        console.log("Your current browser does not support the Geolocation feature.");
    }
}

function createMap() {
    // Create a default map
    window.demoMap = L.map("map").setView([113.64, 1.08], 5);
    // Add a tile layer from OpenStreetMap's default tile server
    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png?{foo}", {
        foo: "bar",
        attribution:
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(window.demoMap);
}
function renderFilePreview(base64Data) {
    let previewContainer = document.getElementById("file-preview-container");
    previewContainer.innerHTML = "";
    let imgPreview = document.createElement("img");
    imgPreview.src = base64Data;
    previewContainer.appendChild(imgPreview);
}

function processFileSelection(event) {
    //we save the file selected in base64 format
    let file = event.target.files[0];
    if (!file) {
        return console.log("No file selected");
    }
    let reader = new FileReader();
    reader.onload = function (e) {
        let base64Data = e.target.result;
        renderFilePreview(base64Data);
        localStorage.setItem("uploadedFile", base64Data);
    };
    reader.readAsDataURL(file);
}

async function initializePage() {
    //we add the file picker functionality to the input file we have on the page
    document
        .getElementById("upload-file")
        .addEventListener("change", processFileSelection);

    //if we have a file uploaded from a previous session,
    //we get it from localStorage and render it on the page
    let base64Data = localStorage.getItem("uploadedFile");
    if (!base64Data) {
        return;
    }
    renderFilePreview(base64Data);
}
//initialize the demo tracks
let tracks = [
    {
        title: "Random Song Demo",
        artist: "John Doe",
        album: "Random Album Name",
        source: "https://raw.githubusercontent.com/kbstt/pwa-demos/main/track1.mp3",
        artwork: [
            {
                src: "https://raw.githubusercontent.com/kbstt/pwa-demos/main/track1.jpg",
                sizes: "512x512",
                type: "image/jpeg",
            },
        ],
    },
    {
        title: "Awesome Track Demo",
        artist: "Jane Doe",
        album: "Another Album",
        source: "https://raw.githubusercontent.com/kbstt/pwa-demos/main/track2.mp3",
        artwork: [
            {
                src: "https://raw.githubusercontent.com/kbstt/pwa-demos/main/track2.jpg",
                sizes: "512x512",
                type: "image/jpeg",
            },
        ],
    },
];

//these are our basic custom audio controls
//in a real-world scenario, there would be more controls (previous track, next track, volume, etc)
//in this example, it only allows to pause and play
let playBtn = function () {
    return document.getElementById("play-btn");
};
let pauseBtn = function () {
    return document.getElementById("pause-btn");
};
let audio = function () {
    return document.getElementById("audio-player");
};

//this variable keeps in memory the index of the currently-playing song from the "tracks" array
let currentTrack = 0;

//this changes the src of the <audio> element to that of the next track (or back to the first once we've reached the end of the list)
function nextTrack() {
    currentTrack += 1;
    if (currentTrack >= tracks.length) {
        currentTrack = 0;
    }
    audio().src = tracks[currentTrack].source;
    playTrack();
}

//this changes the src of the <audio> element to that of the previous track (or the last one in the list if we're pressing previous when we're already playing the first)
function prevTrack() {
    currentTrack -= 1;
    if (currentTrack < 0) {
        currentTrack = tracks.length - 1;
    }
    audio().src = tracks[currentTrack].source;
    playTrack();
}

//call the play() function of the native <audio> element
//then update the compact player to show the details of the song with MediaSession
function playTrack() {
    //the first time we play a track, we get the first item in our track list and set it as the current track
    //you'd probably do this differently in a real-world scenario
    //when the player finishes a track, we need to tell the <audio> play the next one
    //reference: https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/ended_event
    if (!audio().src) {
        audio().src = tracks[currentTrack].source;
        audio().addEventListener("ended", nextTrack);
    }
    audio().play();
    setMediaSession();
    //some utility functions to hide/show our play/pause buttons custom controls in our app
    pauseBtn().classList.remove("hidden");
    playBtn().classList.add("hidden");
}

function pauseTrack() {
    audio().pause();
    //some utility functions to hide/show our play/pause buttons custom controls in our app
    playBtn().classList.remove("hidden");
    pauseBtn().classList.add("hidden");
    //we pause the playback state of the compact player
    navigator.mediaSession.playbackState = "paused";
}

//we reload the audio, and pause it.
function stopTrack() {
    audio().load();
    audio().pause();
}

//here is how you update the compact player with the details of the track/album/artist along with a artwork
//reference: https://developer.mozilla.org/en-US/docs/Web/API/MediaSession
function setMediaSession() {
    if (!navigator.mediaSession) {
        return;
    }
    let trackPlaying = tracks[currentTrack];
    navigator.mediaSession.metadata = new MediaMetadata({
        title: trackPlaying.title,
        artist: trackPlaying.artist,
        album: trackPlaying.album,
        artwork: trackPlaying.artwork,
    });
    //we tie the controls of the compact player to the controls of the <audio> element on the page
    //haven't implemented functions for seekbackward, seekforward, and seekto. See reference: https://developer.mozilla.org/en-US/docs/Web/API/MediaSession
    navigator.mediaSession.setActionHandler("play", playTrack);
    navigator.mediaSession.setActionHandler("pause", pauseTrack);
    navigator.mediaSession.setActionHandler("stop", stopTrack);
    navigator.mediaSession.setActionHandler("previoustrack", prevTrack);
    navigator.mediaSession.setActionHandler("nexttrack", nextTrack);
    //navigator.mediaSession.setActionHandler('seekbackward', function(){});
    //navigator.mediaSession.setActionHandler('seekforward',  function(){});
    //navigator.mediaSession.setActionHandler('seekto', function(){});
    navigator.mediaSession.playbackState = "playing";
}

//we report the playback position to the compact player every 300ms
//https://developer.mozilla.org/en-US/docs/Web/API/MediaSession/setPositionState
setInterval(function () {
    if (!audio() || audio().paused) {
        return;
    }
    if (!navigator.mediaSession) {
        return;
    }
    if (audio().duration > 0 === false) {
        return;
    }
    navigator.mediaSession.setPositionState({
        duration: parseInt(audio().duration),
        playbackRate: audio().playbackRate,
        position: parseInt(audio().currentTime),
    });
}, 300);
function getNetworkInformation() {
    if (!navigator.connection) {
        console.log("Your device does not support the NetworkInformation API");
    } else {
        let data = navigator.connection;
        console.log(
            "downlink:" +
                data.downlink +
                "\n" +
                "effectiveType: " +
                data.effectiveType +
                "\n" +
                "rtt: " +
                data.rtt +
                "\n" +
                "saveData: " +
                data.saveData +
                "\n" +
                "downlinkMax: " +
                data.downlinkMax +
                "\n" +
                "type: " +
                data.type +
                "\n"
        );
    }
}
async function connectToNFC() {
    if (!window.NDEFReader) {
        return console.log("Web NFC API is not supported in this browser.");
    }

    let reader = new NDEFReader();

    let btn = document.querySelector("#demo-btn button");
    btn.classList.add("disabled");
    btn.innerHTML = "Scanning...";

    let container = document.querySelector("#tag-data");

    let scan = await reader.scan();
    scan.onreadingerror = function (event) {
        container.innerHTML =
            "Error! Cannot read data from the NFC tag. Try a different one?";
    };
    scan.onreading = function (event) {
        container.innerHTML = JSON.stringify(event);
    };
}
async function startPayment() {
    let checkoutDetails = {
        id: "pwa-demo-order",
        displayItems: [
            {
                label: "Progressier PWA Demo",
                amount: { currency: "USD", value: "1" },
            },
        ],
        total: { label: "Total", amount: { currency: "USD", value: "1" } },
    };

    let paymentMethods = [
        {
            supportedMethods: "https://google.com/pay",
            data: {
                environment: "TEST",
                apiVersion: 2,
                apiVersionMinor: 0,
                merchantInfo: {
                    // A merchant ID is available after approval by Google: https://developers.google.com/pay/api/web/guides/test-and-deploy/integration-checklist}
                    // merchantId: '12345678901234567890',
                    merchantName: "Progressier",
                },
                allowedPaymentMethods: [
                    {
                        type: "CARD",
                        parameters: {
                            allowedAuthMethods: ["PAN_ONLY", "CRYPTOGRAM_3DS"],
                            allowedCardNetworks: [
                                "AMEX",
                                "DISCOVER",
                                "INTERAC",
                                "JCB",
                                "MASTERCARD",
                                "VISA",
                            ],
                        },
                        tokenizationSpecification: {
                            type: "PAYMENT_GATEWAY",
                            // Check with your payment gateway on the parameters to pass: https://developers.google.com/pay/api/web/reference/request-objects#gateway
                            parameters: {
                                gateway: "example",
                                gatewayMerchantId: "exampleGatewayMerchantId",
                            },
                        },
                    },
                ],
            },
        },
        {
            supportedMethods: "https://apple.com/apple-pay",
            data: {
                version: 3,
                merchantIdentifier: "progressier.com",
                merchantCapabilities: [
                    "supports3DS",
                    "supportsCredit",
                    "supportsDebit",
                ],
                supportedNetworks: [
                    "amex",
                    "discover",
                    "masterCard",
                    "visa",
                    "maestro",
                ],
                countryCode: "US",
            },
        },
    ];

    let paymentRequest = new PaymentRequest(paymentMethods, checkoutDetails);
    let response = await paymentRequest.show();
    console.log(response);
}
let patterns = [
    2000, //vibrate one time for 2 seconds
    [2000, 1000, 2000, 1000, 2000, 1000, 2000],
    [
        400, 200, 400, 200, 400, 200, 800, 200, 800, 200, 400, 200, 400, 200,
        200, 200,
    ], //vibrate "Twinkle, Twinkle, Little Star"
    [150, 50, 150, 50, 300, 100, 150, 50, 150, 50, 300, 100, 150, 50, 150, 50], //vibrate "Super Mario Bros" theme
    [
        300, 200, 300, 200, 300, 400, 300, 200, 300, 200, 300, 400, 300, 200,
        600, 200,
    ], //vibrate "Jingle Bells"
];

function vibrationPattern(index) {
    if (!window.navigator.vibrate) {
        console.log(
            "Your device does not support the Vibration API. Try on an Android phone!"
        );
    } else {
        window.navigator.vibrate(patterns[index]);
    }
}
async function recordScreen() {
    try {
        if (!navigator.mediaDevices.getDisplayMedia) {
            throw "Your device does not support the Screen Capture API";
        }
        let toggle = document.getElementById("recording-button");

        //we ask the user for permission to share their screen with our app
        let stream = await navigator.mediaDevices.getDisplayMedia({
            video: true,
            audio: false,
        });
        let track = stream.getVideoTracks()[0];

        //when users stop sharing, the event below will be fired
        track.onended = () => {
            window.recorder.stop();
        };

        //we record the stream from the screen-sharing
        window.recorder = new MediaRecorder(stream);

        //whenever our MediaRecorder receives data, we save it in a variable
        let chunks = [];
        window.recorder.ondataavailable = function (event) {
            if (event.data.size <= 0) {
                return;
            }
            chunks.push(event.data);
        };

        //when the screen sharing is over, we turn our chunks into a blob,
        //then create a object URL to add to our <video> element,
        //allowing instant playback of our screen recording
        window.recorder.onstop = function () {
            let blob = new Blob(chunks, { type: "video/mp4" });
            toggle.classList.remove("disabled");
            document.getElementById("video-element").src =
                URL.createObjectURL(blob);
            let tracks = stream.getTracks();
            tracks.forEach((track) => track.stop());
        };

        //stop the screen sharing typically happens in another browser tab or window
        //so it's not very necessary to update the status of our "start recording" button,
        //besides disabling it while the screen is being recorded
        window.recorder.onstart = function () {
            toggle.classList.add("disabled");
        };

        window.recorder.start();
    } catch (err) {
        console.log(err);
    }
}
async function sendCode() {
    try {
        let otp = await navigator.credentials.get({otp: {transport: ["sms"]}});
        document.getElementById('#code-field').value = otp.code;
    } catch (error) {
        console.error('Failed to get OTP:', error);
        // Optionally, show an error message to the user
    }
}
function toggleSpeechRecognition() {
    if (!window.webkitSpeechRecognition && !window.SpeechRecognition) {
        console.log("Your browser does not support the SpeechRecognition API");
    } else if (window.transcriptionInProgress) {
        window.transcriptionInProgress.stop();
    } else {
        let btn = document.getElementById("transcribe-now");
        window.transcriptionInProgress = window.webkitSpeechRecognition
            ? new webkitSpeechRecognition()
            : new SpeechRecognition();
        window.transcriptionInProgress.lang =
            document.getElementById("language-selector").value || "en-US";
        window.transcriptionInProgress.interimResults = true;
        window.transcriptionInProgress.addEventListener("result", function (e) {
            document.getElementById("results").innerHTML =
                e.results[0][0].transcript;
        });
        window.transcriptionInProgress.addEventListener("end", function (e) {
            window.transcriptionInProgress = null;
            btn.innerHTML = '<i class="fa fa-circle"></i>Start';
        });
        window.transcriptionInProgress.addEventListener("start", function (e) {
            btn.innerHTML = '<i class="fa fa-square"></i>Stop';
        });
        window.transcriptionInProgress.start();
    }
}

function createSelectorForLanguageCodes() {
    //this is not an exhaustive list. Check https://en.wikipedia.org/wiki/IETF_language_tag for all available codes.
    let commonLanguageLocales = [
        "ar-SA",
        "bg-BG",
        "bn-BD",
        "cs-CZ",
        "da-DK",
        "de-DE",
        "el-GR",
        "en-AU",
        "en-CA",
        "en-GB",
        "en-IE",
        "en-NZ",
        "en-US",
        "en-ZA",
        "es-AR",
        "es-BO",
        "es-CL",
        "es-CO",
        "es-CR",
        "es-CU",
        "es-DO",
        "es-EC",
        "es-ES",
        "es-GT",
        "es-HN",
        "es-MX",
        "es-NI",
        "es-PA",
        "es-PE",
        "es-PR",
        "es-PY",
        "es-SV",
        "es-UY",
        "es-VE",
        "et-EE",
        "fa-IR",
        "fi-FI",
        "fr-CA",
        "fr-CH",
        "fr-BE",
        "fr-FR",
        "gu-IN",
        "hi-IN",
        "hr-HR",
        "hu-HU",
        "id-ID",
        "it-IT",
        "ja-JP",
        "jv-ID",
        "km-KH",
        "kn-IN",
        "ko-KR",
        "lt-LT",
        "lv-LV",
        "ml-IN",
        "mr-IN",
        "mt-MT",
        "my-MM",
        "nl-NL",
        "no-NO",
        "or-IN",
        "pa-IN",
        "pl-PL",
        "pt-BR",
        "ro-RO",
        "ru-RU",
        "sk-SK",
        "sl-SI",
        "sr-RS",
        "sv-SE",
        "ta-IN",
        "te-IN",
        "th-TH",
        "tr-TR",
        "uk-UA",
        "ur-PK",
        "vi-VN",
        "zh-CN",
        "zh-TW",
    ];
    let container = document.getElementById("language-selector");
    commonLanguageLocales.forEach(function (code) {
        let opt = document.createElement("option");
        opt.innerHTML = code;
        opt.setAttribute("value", code);
        container.appendChild(opt);
    });
}

window.addEventListener("load", createSelectorForLanguageCodes);

function synthesiseSpeech() {
    if (!window.speechSynthesis) {
        console.log("Your device does not support the SpeechSynthesis API");
    } else {
        let availableVoices = speechSynthesis.getVoices();
        let utterance = new SpeechSynthesisUtterance();
        utterance.text = document.getElementById("text-to-read").value;
        utterance.voice =
            availableVoices.find(
                (o) =>
                    o.voiceURI === document.getElementById("voice-choice").value
            ) || availableVoices[0];
        utterance.pitch = document.getElementById("pitch").value;
        utterance.rate = document.getElementById("rate").value;
        speechSynthesis.speak(utterance);
    }
}
async function recordVideo() {
    if (window.recorder && window.recorder.state === "recording") {
        window.recorder.stop();
    } else {
        let toggle = document.getElementById("recording-button");

        //we request permission to access the device's microphone and camera
        let stream = await navigator.mediaDevices.getUserMedia({
            audio: true,
            video: true,
        });

        //then we add that stream to our <video> element
        let videoEl = document.getElementById("video-element");
        videoEl.srcObject = stream;
        videoEl.play();

        //next, we need to actively record that stream
        window.recorder = new MediaRecorder(stream);

        //whenever new data has been recorded, we add it to an array of chunks
        let chunks = [];
        window.recorder.ondataavailable = function (event) {
            if (event.data.size <= 0) {
                return;
            }
            chunks.push(event.data);
        };

        window.recorder.onstop = function () {
            //when the recording is over, we take all these chunks of video/audio, then create a blob with it
            //see: https://developer.mozilla.org/en-US/docs/Web/API/Blob
            let blob = new Blob(chunks, { type: "video/mp4" });

            //we change our "stop" button back to a "record" button
            toggle.innerHTML = `<i class="fa fa-circle"></i>`;

            //we replace the source of the video object by removing the stream we added to it earlier
            //and instead we use the URL of the blob we just created with all the recorded chunks -- this will allow our <video> element to play our recorded video
            videoEl.srcObject = null;
            videoEl.src = URL.createObjectURL(blob);

            //last but not least, we tell the browser we don't need access to the user's camera and microphone anymore
            let tracks = stream.getTracks();
            tracks.forEach((track) => track.stop());
        };

        //below is just a helper function to change the record button to a stop button
        window.recorder.onstart = function () {
            toggle.innerHTML = `<i class="fa fa-square"></i>`;
        };

        window.recorder.start();
    }
}
let script = document.createElement("script");
script.setAttribute("src", "https://aframe.io/releases/1.2.0/aframe.min.js");
script.onload = function () {
    AFRAME.registerComponent("play-video-once", {
        init: function () {
            this.el.addEventListener("loadeddata", () => {
                this.el.components.material.material.map =
                    this.el.components.material.videoTexture;
                this.el.components.material.material.map.needsUpdate = true;
                this.el.components.material.material.needsUpdate = true;
            });
        },
    });
};

async function startVR() {
    if (!navigator.xr) {
        return console.log("WebXR is not supported in this browser.");
    }

    const videoElement = document.getElementById("vr-video");
    videoElement.play();

    // Enter WebXR when the button is clicked
    const session = await navigator.xr.requestSession("immersive-vr");
    const scene = document.querySelector("a-scene");
    session.updateRenderState({
        baseLayer: new XRWebGLLayer(session, scene.renderer),
    });
    const referenceSpace = await session.requestReferenceSpace("local");
    const xrElement = scene.querySelector("[xr-webgl]");
    xrElement.setAttribute("xr-webgl", {
        session: session,
        space: referenceSpace,
    });
}
function toggleWakeLock() {
    if (!navigator.wakeLock) {
        console.log(
            "Your device does not support the Wake Lock API. Try on an Android phone or on a device running iOS 16.4 or higher!"
        );
    } else if (window.currentWakeLock && !window.currentWakeLock.released) {
        releaseScreen();
    } else {
        lockScreen();
    }
}

async function lockScreen() {
    try {
        window.currentWakeLock = await navigator.wakeLock.request();
        document.getElementById("wake-lock-btn").innerHTML =
            "Release Wake Lock";
        console.log("Wake Lock enabled");
    } catch (err) {
        console.log(err);
    }
}

async function releaseScreen() {
    window.currentWakeLock.release();
    document.getElementById("wake-lock-btn").innerHTML = "Start Wake Lock";
    console.log("Wake Lock released");
}

async function connectToBluetoothDevice() {
    if (!navigator.bluetooth || !navigator.bluetooth.requestDevice) {
        console.log(
            "Your device does not support the Web Bluetooth API. Try again on Chrome on Desktop or Android!"
        );
    } else {
        //in this example, we'll simply allow connecting to any device nearby
        //in a real-life example, you'll probably want to use filter so that your app only connects to certain types of devices (e.g. a heart rate monitor)
        //more on this here: https://developer.mozilla.org/en-US/docs/Web/API/Bluetooth/requestDevice
        let device = await navigator.bluetooth.requestDevice({
            acceptAllDevices: true,
        });
        console.log("Successfully connected to " + device.name);
    }
}
async function recordVideo() {
    if (window.recorder && window.recorder.state === "recording") {
        window.recorder.stop();
    } else {
        let toggle = document.getElementById("recording-button");

        //we request permission to access the device's microphone and camera
        let stream = await navigator.mediaDevices.getUserMedia({
            audio: true,
            video: true,
        });

        //then we add that stream to our <video> element
        let videoEl = document.getElementById("video-element");
        videoEl.srcObject = stream;
        videoEl.play();

        //next, we need to actively record that stream
        window.recorder = new MediaRecorder(stream);

        //whenever new data has been recorded, we add it to an array of chunks
        let chunks = [];
        window.recorder.ondataavailable = function (event) {
            if (event.data.size <= 0) {
                return;
            }
            chunks.push(event.data);
        };

        window.recorder.onstop = function () {
            //when the recording is over, we take all these chunks of video/audio, then create a blob with it
            //see: https://developer.mozilla.org/en-US/docs/Web/API/Blob
            let blob = new Blob(chunks, { type: "video/mp4" });

            //we change our "stop" button back to a "record" button
            toggle.innerHTML = `<i class="fa fa-circle"></i>`;

            //we replace the source of the video object by removing the stream we added to it earlier
            //and instead we use the URL of the blob we just created with all the recorded chunks -- this will allow our <video> element to play our recorded video
            videoEl.srcObject = null;
            videoEl.src = URL.createObjectURL(blob);

            //last but not least, we tell the browser we don't need access to the user's camera and microphone anymore
            let tracks = stream.getTracks();
            tracks.forEach((track) => track.stop());
        };

        //below is just a helper function to change the record button to a stop button
        window.recorder.onstart = function () {
            toggle.innerHTML = `<i class="fa fa-square"></i>`;
        };

        window.recorder.start();
    }
}
async function connectToBluetoothDevice() {
    if (!navigator.bluetooth || !navigator.bluetooth.requestDevice) {
        console.log(
            "Your device does not support the Web Bluetooth API. Try again on Chrome on Desktop or Android!"
        );
    } else {
        //in this example, we'll simply allow connecting to any device nearby
        //in a real-life example, you'll probably want to use filter so that your app only connects to certain types of devices (e.g. a heart rate monitor)
        //more on this here: https://developer.mozilla.org/en-US/docs/Web/API/Bluetooth/requestDevice
        let device = await navigator.bluetooth.requestDevice({
            acceptAllDevices: true,
        });
        console.log("Successfully connected to " + device.name);
    }
}
function shareContent() {
    let url = document.getElementById("content-url").value;
    let title = document.getElementById("content-title").value;
    let text = document.getElementById("content-text").value;
    let data = { url: url, text: text, title: title };
    console.log(data);

    if (!navigator.share) {
        console.log(
            "Your device does not support the Web Share API. Try on an iPhone or Android phone!"
        );
    } else {
        navigator.share(data);
    }
}
$(document).ready(function() {
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toastr-top-right",
        "preventDuplicates": true,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
});
window.addEventListener('toast-success', function(event) {
    toastr.success(event.detail.message, event.detail.title);
});
window.addEventListener('toast-info', function(event) {
    toastr.info(event.detail.message, event.detail.title);
});
window.addEventListener('toast-warning', function(event) {
    toastr.warning(event.detail.message, event.detail.title);
});
window.addEventListener('toast-danger', function(event) {
    toastr.danger(event.detail.message, event.detail.title);
});
window.addEventListener("cookieConsoleconsole.logAccept", function() {
    // do something
});
async function requestMicrophonePermission() {
    try {
        await navigator.mediaDevices.getUserMedia({ audio: true });
        checkSoundPermission();
    } catch (e) {
        // console.log("Izin mikrofon ditolak.");
    }
}
async function requestCameraPermission() {
    try {
        await navigator.mediaDevices.getUserMedia({ video: true });
    } catch (e) {}
    checkCameraPermission();
}
async function requestNotificationPermission() {
    if (!("Notification" in window)) {
        // alert("Browser kamu tidak mendukung notifikasi.");
        return;
    }

    const permission = await Notification.requestPermission();
    if (permission !== "granted") {
        // alert("Kamu menolak izin notifikasi. Aktifkan di pengaturan browser.");
        return;
    }
    checkNotificationPermission();
    await subscribeToPush();
}

async function subscribeToPush() {
    try {
        const registration = await navigator.serviceWorker.register('/serviceworker.js');
        console.log("Service Worker berhasil didaftarkan!");

        await navigator.serviceWorker.ready;

        const subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: 'BIkSm9KVMrwk7BuG8vZIrdZ0craKlU18HGVgGHKLbuTyZulSS-fQFaRTX7vm9tzZVjI9WTS32WLH8uAkXW5zbQI'
        });

        await fetch('/push-subscribe', {
            method: 'POST',
            body: JSON.stringify(subscription),
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        console.log("Berlangganan push berhasil!");
    } catch (error) {
        console.error("Gagal berlangganan push:", error);
    }
}

async function registerServiceWorkerAndSendNotification() {
    try {
        const registration = await navigator.serviceWorker.register('/serviceworker.js');
        // console.log("Service Worker registered!", registration);

        registration.showNotification("Notifikasi Baru!", {
            body: "Selamat datang di aplikasi kami!",
            icon: "/media/icons/icon-128x128.png",
            badge: "/media/icons/icon-96x96.png",
            vibrate: [200, 100, 200],
            data: {
                dateOfArrival: Date.now(),
                primaryKey: 1
            }
        });
    } catch (error) {
        console.error("Service Worker registration failed:", error);
    }
}
function checkNotificationPermission() {
    updateStatus("notification", Notification.permission === "granted");
}
function checkSoundPermission() {
    navigator.permissions?.query({ name: "microphone" }).then((res) => {
        updateStatus("sound", res.state === "granted");
    }).catch(() => updateStatus("sound", false));
}
function checkCameraPermission() {
    navigator.permissions?.query({ name: "camera" }).then((res) => {
        updateStatus("camera", res.state === "granted");
    }).catch(() => updateStatus("camera", false));
}
function checkLocationPermission() {
    navigator.permissions?.query({ name: "geolocation" }).then((res) => {
        updateStatus("location", res.state === "granted");
    }).catch(() => updateStatus("location", false));
}

function requestLocationPermission() {
    navigator.geolocation.getCurrentPosition(
        () => checkLocationPermission(),
        () => checkLocationPermission()
    );
}
function updateStatus(elementId, granted) {
    const status = document.getElementById(`${elementId}-status`);
    const bgIcon = document.getElementById(`${elementId}-bg-icon`);
    const icon = document.getElementById(`${elementId}-icon`);
    const button = document.getElementById(`enable-${elementId}`);
    const text = document.getElementById(`${elementId}-text`);

    if (granted) {
        status.classList.replace("bg-light", "bg-success");
        bgIcon.classList.replace("bg-danger", "bg-success");
        icon.classList.replace("ki-cross", "ki-check");
        icon.classList.replace("text-danger", "text-success");
        text.classList.replace("text-danger", "text-success");
        text.textContent = 'Aktif'
        button.classList.add("d-none");
    } else {
        status.classList.replace("bg-success", "bg-warning");
        bgIcon.classList.replace("bg-success", "bg-danger");
        icon.classList.replace("ki-check", "ki-cross");
        icon.classList.replace("text-success", "text-danger");
        text.classList.replace("text-success", "text-danger");
        text.textContent = 'Tidak Aktif'
        button.classList.remove("d-none");
    }
}
// Di akhir function.js
// Di akhir function.js
navigator.serviceWorker.addEventListener('message', function(event) {
    if (event.data.action === 'openThread' && event.data.threadId) {
        // Update state Livewire untuk membuka thread
        Livewire.emit('selectThread', event.data.threadId);
        // Buka drawer
        const toggle = document.querySelector('#kt_drawer_chat_toggle');
        if (toggle) {
            toggle.click();
            toastr.success('Membuka percakapan baru!', 'Notifikasi');
        } else {
            console.error('Toggle button not found');
            toastr.error('Gagal membuka drawer chat', 'Error');
        }
    }
});