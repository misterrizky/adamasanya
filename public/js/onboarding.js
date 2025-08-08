const stepperEl = document.querySelector("#step_onboarding"),
options = {startIndex: 1},
stepper = new KTStepper(stepperEl, options),
form = document.getElementById("form_onboarding"),
validator = [];

stepper.on("kt.stepper.next", function(event) {
    var currentStepValidator = validator[event.getCurrentStepIndex() - 1];
    currentStepValidator ? currentStepValidator.validate().then(function (validationResult) {
        "Valid" == validationResult ? (event.goNext(), KTUtil.scrollTop()) : Swal.fire({
            text: "Maaf, sepertinya ada beberapa kesalahan yang terdeteksi, silakan coba lagi.",
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "Ok, mengerti!",
            customClass: {
                confirmButton: "btn btn-light",
            },
        }).then(function () {
            KTUtil.scrollTop();
        });
    }) : (event.goNext(), KTUtil.scrollTop());
    // console.log("kt.stepper.next event is fired");
});
stepper.on("kt.stepper.previous", function(event) {
    stepper.goPrevious();
    // console.log("kt.stepper.previous event is fired");
});
stepper.on("kt.stepper.change", function(event) {
    // console.log("kt.stepper.change event is fired");
});
stepper.on("kt.stepper.changed", function(event) {
    // console.log("kt.stepper.changed event is fired");
});
stepper.on("kt.stepper.click", function(event) {
    stepper.goTo(stepper.getClickedStepIndex()); // go to clicked step
});
window.addEventListener('onboarding-step-1-finish', function(event) {
    stepper.goNext();
});
window.addEventListener('onboarding-step-2-finish', function(event) {
    stepper.goNext();
});
window.addEventListener('onboarding-step-3-finish', function(event) {
    stepper.goNext();
});
window.addEventListener('onboarding-step-4-finish', function(event) {
    stepper.goNext();
});
window.addEventListener('onboarding-step-1-back', function(event) {
    stepper.goPrevious();
});
window.addEventListener('onboarding-step-2-back', function(event) {
    stepper.goPrevious();
});
window.addEventListener('onboarding-step-3-back', function(event) {
    stepper.goPrevious();
});
window.addEventListener('onboarding-step-4-back', function(event) {
    stepper.goPrevious();
});