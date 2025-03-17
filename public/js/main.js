let cropper;
const modelImage = document.getElementById('previewImage');
const modal = $('#previewModal');

//add headers to all the ajax requests
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

//show success toaster
function showSuccess(message) {
    toastr.success(message);
}

//show warning toaster
function showInfo(message) {
    toastr.info(message);
}

//show error toaster
function showError(message) {
    toastr.error(message || languages.error_occurred);
}

//ajax call to check if the meeting exist or not
$("#meeting").on("submit", function (e) {
    e.preventDefault();

    if (conferenceId.value.length <= 3) {
        showError(languages.no_meeting);
        return;
    }

    $("#initiate").attr("disabled", true);

    $.ajax({
        url: "check-meeting",
        data: $(this).serialize(),
        type: "post",
    })
        .done(function (data) {
            data = JSON.parse(data);
            $("#initiate").attr("disabled", false);

            if (data.success) {
                location.href = "meeting/" + data.id;
            } else {
                showError(languages.no_meeting);
            }
        })
        .catch(function () {
            showError(languages.no_meeting);
            $("#initiate").attr("disabled", false);
        });
});

//dynamically add google analytics tracking ID
if (googleAnalyticsTrackingId !== "null" && googleAnalyticsTrackingId) {
    let script = document.createElement("script");
    script.src =
        "https://www.googletagmanager.com/gtag/js?id=" +
        googleAnalyticsTrackingId;
    document.body.appendChild(script);

    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag("js", new Date());
    gtag("config", googleAnalyticsTrackingId);
}

//check if the cookie is accepted
if (cookieConsent == "enabled" && !localStorage.getItem("cookieAccepted")) {
    setTimeout(function () {
        $(".cookie").addClass("show-cookie");
    }, 3000);
}

//store in the local storage and hide the cookie dialogue
$(document).on("click", ".confirm-cookie", function () {
    localStorage.setItem("cookieAccepted", true);
    $(".cookie").removeClass("show-cookie");
});

//scroll to top
$('.start-btn').on('click', function (e) {
    e.preventDefault();
    $('html, body').animate({ scrollTop: 0 }, 'slow');
});

//set href into the social links
$('#fbShare').attr('href', 'https://www.facebook.com/sharer/sharer.php?u=' + location.hostname + '&quote=' + socialInvitation);
$('#twitterShare').attr('href', 'https://twitter.com/share?url=' + location.hostname + '&text=' + socialInvitation);
$('#waShare').attr('href', 'https://api.whatsapp.com/send?text=' + socialInvitation + ' \n ' + location.hostname);

//auto login feature for demo mode
$("#autoLogin").on('change', function () {
    let email;

    if (this.value == "admin") {
        email = "admin@thertclabs.com";
    } else if (this.value == "user_1") {
        email = "user1@thertclabs.com";
    } else if (this.value == "user_2") {
        email = "user2@thertclabs.com";
    }

    $("#loginButton").attr('disabled', true);

    $("#email").val(email);
    $("#password").val('123456');
    $("#login").trigger('submit');
});

//to prevent XSS vulnerability
function htmlEscape(input) {
    return input
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

//make sure this document is always at top due to media permission
if (window != top) top.location.href = window.location.href;

//handle remove copoun
$("#remove_coupon").on("click", function (e) {
    $('input[name="coupon"]').val('');
    //remove the submit button
    document.querySelector('#form-payment').submit.remove();

    //submit the form
    document.querySelector('#form-payment').submit();
})

//update summary
let updateSummary = (type) => {
    if (type == 'month') {
        document.querySelectorAll('.checkout-month').forEach(function (element) {
            element.classList.add('d-inline-block');
        });
        document.querySelectorAll('.checkout-year').forEach(function (element) {
            element.classList.remove('d-inline-block');
        });
    } else {
        document.querySelectorAll('.checkout-month').forEach(function (element) {
            element.classList.remove('d-inline-block');
        });
        document.querySelectorAll('.checkout-year').forEach(function (element) {
            element.classList.add('d-inline-block');
        });
    }
};

//update billing type
let updateBillingType = (value) => {
    document.querySelectorAll('.checkout-subscription').forEach(function (element) {
        element.classList.remove('d-none');
    });
    document.querySelectorAll('.checkout-subscription').forEach(function (element) {
        element.classList.add('d-block');
    });
}

//payment form
if (document.querySelector('#form-payment')) {
    let url = new URL(window.location.href);

    document.querySelectorAll('[name="interval"]').forEach(function (element) {
        if (element.checked) {
            updateSummary(element.value);
        }

        //listen to interval changes
        element.addEventListener('change', function () {
            url.searchParams.set('interval', element.value);
            history.pushState(null, null, url.href);
            updateSummary(element.value);
        });
    });

    document.querySelectorAll('[name="payment_gateway"]').forEach(function (element) {
        if (element.checked) {
            updateBillingType(element.value);
        }

        //listen to payment gateway changes
        element.addEventListener('change', function () {
            url.searchParams.set('payment', element.value);
            history.pushState(null, null, url.href);
            updateBillingType(element.value);
        });
    });

    //if the Add a coupon button is clicked
    document.querySelector('#coupon') && document.querySelector('#coupon').addEventListener('click', function (e) {
        e.preventDefault();

        this.classList.add('d-none');
        document.querySelector('#coupon-input').classList.remove('d-none');
        document.querySelector('input[name="coupon"]').removeAttribute('disabled');
    });

    //if the Cancel coupon button is clicked
    document.querySelector('#coupon-cancel') && document.querySelector('#coupon-cancel').addEventListener('click', function (e) {
        e.preventDefault();

        document.querySelector('#coupon').classList.remove('d-none');
        document.querySelector('#coupon-input').classList.add('d-none');
        document.querySelector('input[name="coupon"]').setAttribute('disabled', 'disabled');
    });

    //if the country value changes
    document.querySelector('#i-country').addEventListener('change', function () {
        document.querySelector('#form-payment').submit.remove();
        document.querySelector('#form-payment').submit();
    });
}

//handle plan month click
document.querySelector('#plan-month') && document.querySelector('#plan-month').addEventListener("click", function () {
    document.querySelectorAll('.plan-month').forEach(element => element.classList.add('d-block'));
    document.querySelectorAll('.plan-year').forEach(element => element.classList.remove('d-block'));
});

//handle plan year click
document.querySelector('#plan-year') && document.querySelector('#plan-year').addEventListener("click", function () {
    document.querySelectorAll('.plan-year').forEach(element => element.classList.add('d-block'));
    document.querySelectorAll('.plan-month').forEach(element => element.classList.remove('d-block', 'plan-preload'));
});

//copy api token to the clipboard
$("#copyApiToken").on('click', function () {
    let inp = document.createElement('textarea');
    inp.style.display = 'hidden';
    document.body.appendChild(inp);
    inp.value = api_token.value;
    inp.select();
    document.execCommand('copy', false);
    inp.remove();
    showSuccess(languages.token_copied);
});

//ajax call to delete contact
$(".delete-contact").on("click", function () {
    if (!confirm(languages.confirmation)) return;
    let currentRow = $(this);
    currentRow.attr("disabled", true);

    let deleteUrl = $(this).attr('data-url');

    let form = new FormData();
    form.append("id", currentRow.data("id"));

    $.ajax({
        url: deleteUrl,
        data: form,
        type: "post",
        cache: false,
        contentType: false,
        processData: false,
    })
        .done(function (data) {
            data = JSON.parse(data);

            if (data.success) {
                currentRow.parent().parent().remove();
                showSuccess(data.message);
            } else {
                showError(data.error);
            }
        })
        .catch(function () {
            currentRow.attr("disabled", false);
            showError();
        });
});

//listen on avatar remove event
$('#removeAvatar').on('click', function () {
    if (!confirm(languages.confirmation)) return;

    $(this).attr('disabled', true);

    $.ajax({
        url: "delete-avatar",
        data: {
            id: $('#userid').val()
        },
        type: "post",
    })
        .done(function (data) {
            data = JSON.parse(data);

            if (data.success) {
                window.location.reload();
            } else {
                showError(data.error);
            }

            $('#removeAvatar').attr('disabled', false);
        })
        .catch(function () {
            $('#removeAvatar').attr('disabled', false);
            showError(languages.error_occurred);
        });
});

//hide alerts
setTimeout(() => {
    $(".alert").hide(1000);
}, 3000);

//handle avatar change
$(document).on("change", "#avatarchange", function (e) {
    var files = e.target.files;
    if (files && files.length > 0) {
        var url = URL.createObjectURL(files[0]);
        modelImage.src = url;
        modal.modal('show');
    }
});

//handle avatar preview modal toggle
modal.on('shown.bs.modal', function () {
    cropper = new Cropper(modelImage, {
        aspectRatio: 1,
        viewMode: 2,
        responsive: true,
        minContainerWidth: 465,
        minContainerHeight: 200,
        minCanvasWidth: 465,
        minCanvasHeight: 200,
        minCropBoxWidth: 465,
        minCropBoxHeight: 200,
        preview: '.preview'
    });
}).on('hidden.bs.modal', function () {
    cropper.destroy();
    cropper = null;
});

//crop and upload avatar
$("#crop_button").on('click', function () {
    const imgurl = cropper.getCroppedCanvas().toDataURL();

    $(this).attr('disabled', true);

    cropper.getCroppedCanvas().toBlob((blob) => {
        const formData = new FormData();
        formData.append('image', blob);
        formData.append('extension', blob.type.replace("image/", " "));
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "upload-avatar",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.success) {
                    showSuccess(data.success);
                    modal.modal('hide');
                    $('#imagePreview').attr('src', imgurl).show();
                    $('#profileDropdown').html('<img src="' + imgurl + '" class="user-avatar">');
                    $('#initial').hide();
                } else {
                    showError(data.error);
                }
            },
            complete: function () {
                $("#crop_button").attr('disabled', true);
            }
        });
    })
});

//PWA
if (pwa == 'enabled') {
    let deferredPrompt;

    window.addEventListener("beforeinstallprompt", (e) => {
        e.preventDefault();

        if (!localStorage.getItem("dontWantToInstall")) {
            $("#installationModal").modal("show");
        }

        deferredPrompt = e;
    });

    document.getElementById("installApp").addEventListener("click", () => {
        $("#installationModal").modal("hide");
        deferredPrompt.prompt();
    });

    document
        .getElementById("closeInstallationModal")
        .addEventListener("click", () => {
            localStorage.setItem("dontWantToInstall", "true");
            $("#installationModal").modal("hide");
        });
}
