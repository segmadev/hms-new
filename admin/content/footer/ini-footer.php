<?php if (in_array("roles", $script)) { ?>
<script src="js/v1/roles.js?m=10"></script>
<?php } ?>
<!--  modal content -->
<?php if (in_array("modal", $script)) {
    require_once "content/modal.php";
} ?>
<?php if (in_array("qrcode", $script)) { ?>
<link rel="stylesheet" href="assets/css/plugins/dragula.min.css"><!-- quill css -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
function generateAllQRCodes() {
    // Get all elements with the 'qrcode' class
    const qrCodeElements = document.querySelectorAll('.qrcode');

    qrCodeElements.forEach(element => {
        // Get the text from the data-text attribute
        const text = element.getAttribute('data-text');

        // Clear any existing QR code in the element
        element.innerHTML = "";

        // Generate the QR code
        new QRCode(element, {
            text: text,
            width: 100, // Adjust width to fit nicely within the container
            height: 100, // Adjust height to fit nicely within the container
            colorDark: "#9d9d9d", // Dark color (blue)
            // colorLight: "#f9f9f9", // Light color (matches container background)
            correctLevel: QRCode.CorrectLevel.H
        });
    });
}

// Generate all QR codes on page load
document.addEventListener("DOMContentLoaded", generateAllQRCodes);
</script>
<?php } ?>

<?php if (in_array('showme', $script)) { ?>
<script>
document.getElementById("showme").click();
</script>
<?php } ?>

<!-- qr code js -->
<?php if (in_array("qrcode", $script)) { ?>
<script src="qrcodejs/qrcode.min.js"></script>
<script src="dist/js/qrcode.js?n=1"></script>
<script>
const qrcodelist = document.querySelectorAll('#genqr');

qrcodelist.forEach(element => {
    console.log(element);
    data = element.getAttribute("data-info");
    showhere = element.getAttribute("data-id");
    // Get the container element
    var container = document.getElementById(showhere);
    // Text or data you want to encode in the QR code
    // QR code options (optional)
    var options = {
        // width: 200, // Width of the QR code (pixels)
        // height: 200, // Height of the QR code (pixels)
    };

    // Generate the QR code
    var qrcode = new QRCode(container, options);

    qrcode.makeCode(data);

});
</script>
<?php } ?>

<?php if (in_array("fetcher", $script)) { ?>
<script src="js/v1/fetcher.js?n=7736454"></script>
<?php } ?>
<!-- Mail -->
<?php if (in_array("mail", $script)) { ?>
<script data-cfasync="false" src="cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
<script src="assets/js/plugins/quill.min.js"></script>
<script>
// (function() {
//     var quill = new Quill('#pc-quil-1', {
//         modules: {
//             toolbar: [
//                 [{
//                     header: [1, 2, false]
//                 }],
//                 ['bold', 'italic', 'underline'],
//                 ['image', 'code-block']
//             ]
//         },
//         placeholder: 'Type your text here...',
//         theme: 'snow'
//     });
//     mainTextArea =  document.getElementById('output-textarea');
//     mainTextArea.innerHTML = quill.root.innerHTML;
//     quill.on('text-change', function(delta, source) {
//    var justHtml = quill.root.innerHTML;
//    mainTextArea.innerHTML = justHtml;
// });

// })({

// });
// textarea = document.getElementById('output-textarea');
// textarea.value = quill.container.innerHTML;

// Function to update the textarea
function updateTextarea() {
    const div = document.getElementById('pc-quil-1');
    const textarea = document.getElementById('output-textarea');

    // Get the content of the div
    const content = div.getElementsByClassName('ql-editor').innerHTML;

    // Get the name attribute or default to "message"
    const name = div.getAttribute('name') || 'message';

    // Update the textarea's name attribute and content
    textarea.name = name;
    textarea.value = content;
}

// Attach a real-time listener to the div
// const div = document.getElementById('pc-quil-1');
// div.addEventListener('input', updateTextarea);

// // Initialize the textarea on page load
// updateTextarea();

// scroll-block
var tc = document.querySelectorAll('.scroll-block');
for (var t = 0; t < tc.length; t++) {
    new SimpleBar(tc[t]);
}
var toggle_mail_list = document.querySelector('#toggle-mail-list-height');
var toggle_mail_wrapper = document.querySelector('.mail-wrapper');
if (toggle_mail_list) {
    toggle_mail_list.addEventListener('click', function() {
        if (toggle_mail_wrapper.classList.contains('mini-mail-list')) {
            toggle_mail_wrapper.classList.remove('mini-mail-list');
        } else {
            toggle_mail_wrapper.classList.add('mini-mail-list');
        }
    });
}

var toggle_mail_dialog = document.querySelector('#toggle_mail_dialog');
var toggle_mail_modal = document.querySelector('.compose_mail_modal');
if (toggle_mail_dialog) {
    toggle_mail_dialog.addEventListener('click', function() {
        if (toggle_mail_modal.classList.contains('modal-pos-down')) {
            toggle_mail_modal.classList.remove('modal-pos-down');
        } else {
            toggle_mail_modal.classList.add('modal-pos-down');
        }
    });
}

var tc = document.querySelectorAll('.mail-table tr td:nth-child(2), .mail-table tr td:nth-child(3)');
for (var t = 0; t < tc.length; t++) {
    tc[t].addEventListener('click', function() {
        active_details('a[id="list-mailtab-details"]');
    });
}

document.querySelector('#mail-back_inbox').addEventListener('click', function() {
    active_details('a[id="list-mailtab-1"]');
});

function active_details(tab_name) {
    var someTabTriggerEl = document.querySelector(tab_name);
    var actTab = new bootstrap.Tab(someTabTriggerEl);
    actTab.show();
}
</script>
<?php } ?>
<!-- fetch data -->

<!-- sweetalert -->
<?php if (in_array("sweetalert", $script)) { ?>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php } ?>
<?php if (in_array("mselect", $script)) { ?>
<script>
// Initialize the selectpicker
$(document).ready(function() {
    $('.selectpicker').selectpicker();
});
</script>
<?php } ?>