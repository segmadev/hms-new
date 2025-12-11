// Variable to hold request
var request;
var closeOnClick = "Close on click";
var displayClose = "Display close button";
var position = "nfc-top-right";
var duration = "3000";
var theme = "warning";

// get browser theme and set it as a cookie for gobal use in the project
if(getCookieValue('browser_theme') == null){
    getBrowserTheme();
}

function copy_text(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert(`Copied: ${text}`);
    }).catch(err => {
        console.error('Failed to copy text: ', err);
    });
}
    // console.log(getBrowserTheme());
function getBrowserTheme() {
  if (
    window.matchMedia &&
    window.matchMedia("(prefers-color-scheme: dark)").matches
  ) {
    browserTheme = "dark";
  } else {
    browserTheme = "light";
  }


  // Set a cookie that expires in 12 hours
  let expirationDate = new Date();
  expirationDate.setTime(expirationDate.getTime() + 12 * 60 * 60 * 1000);
  document.cookie =
    "browser_theme=" +
    browserTheme +
    "; expires=" +
    expirationDate.toUTCString();
}

// copy to clipboard
function copytext(text) {
    // console.log("copied");
    var textarea = document.createElement("textarea");
    textarea.value = text;
    document.body.appendChild(textarea);
    textarea.select();
    copy = document.execCommand("copy");
    document.body.removeChild(textarea);
    if(copy) {
        alert("Copied");
    }
  }

// Function to get the value of a cookie by name
function getCookieValue(cookieName) {
    const cookies = document.cookie.split(';');
    for (let i = 0; i < cookies.length; i++) {
      const cookie = cookies[i].trim();
      if (cookie.startsWith(cookieName + '=')) {
        return cookie.substring(cookieName.length + 1);
      }
    }
    return null;
  }
  
  // Usage example
  const myCookieValue = getCookieValue('browser_theme');
//   console.log(myCookieValue);
  
  const elements = document.querySelectorAll("#foo");
  $i = 0;
  elements.forEach((element) => {
    iniForm(element);
    $i++;
  });

  function utf8ToBase64(str) {
    return btoa(unescape(encodeURIComponent(str)));
}

// ini forms with passed element
function iniForm(element, action = "passer") {
    element.addEventListener("submit", event => {
        // Prevent default posting of form - put here to work in case of errors
        event.preventDefault();

        // Abort any pending request
        if (request) {
            request.abort();
        }

        // Setup some local variables
        var $form = $(event.target);
        var fd = new FormData(element);

        // Remove existing login_details[] from FormData to avoid duplicates
        fd.delete('login_details[]');
        
        // Convert all login_details[] values to base64
        $form.find('textarea[name="login_details[]"]').each(function() {
            var originalValue = $(this).val();
            var encodedValue = utf8ToBase64(originalValue) // Convert to base64
            fd.append('login_details[]', encodedValue); // Append base64 value to FormData
        });

        if (window.location.href != $form[0].action) {
            action = $form[0].action;
        }

        // Let's select and cache all the fields
        var $inputs = $form.find("input, select, button, textarea");
       
        // Serialize the data in the form
        var serializedData = $form.serialize();

        // Let's disable the inputs for the duration of the Ajax request.
        $inputs.prop("disabled", true);
        const params = new URLSearchParams(serializedData);

        // Confirm logic
        if (params.has("confirm")) {
            var span = document.createElement("span");
            span.innerHTML = params.get("confirm");
            swal({
                html: true,
                title: "Attention!",
                content: span,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    runjax(event, $inputs, fd, action);
                } else {
                    // Reinitialize the form and re-enable inputs
                    iniForm(element, action);
                    $inputs.prop("disabled", false);
                }
            });
        } else {
            // console.log(fd);
            // return;
            // Run AJAX request if no confirmation is required
            runjax(event, $inputs, fd, action);
        }
    });
}

// Add this new reusable function
function handleAjaxResponse(response, $inputs, customMessageElement) {
    // Re-enable form inputs
    if ($inputs) {
        $inputs.prop("disabled", false);
    }
    
    // Clear loading message if element exists
    if (customMessageElement) {
        customMessageElement.innerHTML = "";
    }

    // Handle JSON responses
    if (testJSON(response)) {
        proceessjson(response);
        return;
    }

    // Handle HTML responses
    if (customMessageElement) {
        const previewResponse = response.substring(0, 30);
        if (previewResponse === "<div class='card card-primary'") {
            $("#accordion").prepend(response);
        } else {
            customMessageElement.innerHTML = response;
        }
    }
}

// Update runjax to use the new function
function runjax(event, $inputs, fd, action = "passer") {
    const form = event.srcElement;
    let customMessageElement = form.querySelector('.custommessage');

    // Create custom message element if it doesn't exist
    if (!customMessageElement) {
        customMessageElement = document.createElement('div');
        customMessageElement.classList.add('custommessage');
        form.appendChild(customMessageElement);
    }

    // Display loading spinner
    customMessageElement.innerHTML = `
        <div style='min-height: 100px;' class="loading-spinner-container">
            <div style='border: 4px solid rgba(0, 0, 0, 0.1); border-top: 4px solid #fa5a15; width: 40px; height: 40px;' class="spinner"></div>
        </div>`;

    // AJAX request
    $.ajax({
        url: action,
        type: "post",
        cache: false,
        processData: false,
        contentType: false,
        data: fd,
    })
    .done((response) => {
        handleAjaxResponse(response, $inputs, customMessageElement);
    })
    .fail((jqXHR, textStatus, errorThrown) => {
        // Handle error response
        handleAjaxResponse(jqXHR.responseText, $inputs, customMessageElement);
        console.error("The following error occurred:", textStatus, errorThrown);
    });
}

// Update submitform to use the same handler
function submitform(id = "foo2", messageid = "custommessage") {
    var myform = document.getElementById(id);
    var button = document.getElementById("Button");
    var messageElement = document.getElementById(messageid);
    
    button.disabled = true;
    messageElement.innerHTML = "Please wait...";
    
    var fd = new FormData(myform);
    $.ajax({
        url: "passer",
        data: fd,
        cache: false,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (response) {
            handleAjaxResponse(response, null, messageElement);
            button.disabled = false;
        },
        error: function(jqXHR, textStatus, errorThrown) {
            handleAjaxResponse(jqXHR.responseText, null, messageElement);
            button.disabled = false;
        }
    });
}

// Update simple_ajax to use the same handler
function simple_ajax(data, url = "passer", type = "POST") {
    setCookie("isSave", false, 1);
    $.ajax({
        url: url,
        data: data,
        type: type,
        success: function (response) {
            setCookie("isSave", true, 1);
            handleAjaxResponse(response);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            handleAjaxResponse(jqXHR.responseText);
        }
    });
}

function loadpage(url, holder) {
    setTimeout(() => {
        if (url.includes("https://") || url.includes("http://")) {
            window.open(url, "_blank");
            // window.open(url, "_blank");
        } else {
            window.location.replace(url);
        }
    }, parseInt(holder)); 
}
function removediv(id, type="id"){
    divElement = document.querySelector(id);
    divElement.parentNode.removeChild(divElement);
}
function followuser(id, userid){
    document.getElementById(id).innerHTML = '<img src="img/loading.gif" alt="please wait..." style="width:48px;">';
    $.ajax({
        type: 'post',
        url: 'passer.php',
        data: {
            userid: userid,
            follow: "follow",
        },
        success: function (response) {
            if (testJSON(response)) {
                proceessjson(response);
            } else {
                document.getElementById(id).innerHTML = response;
            }
        }
    });
}

function input_value(id, value) {
    // console.log("clear");
    if(document.getElementById(id)) {
        document.getElementById(id).value = value;
    }
}
function removethis(id, what){
    if(confirm("Are you sure you want to delete this "+what)){
        document.getElementById("message"+id).innerHTML = '<img src="img/loading.gif" alt="please wait..." style="width:48px;">';
        $.ajax({
            type: 'post',
            url: 'passer.php',
            data: {
                removethis:id,
                whatto:what,
            },
            success: function (response) {
                if (testJSON(response)) {
                    proceessjson(response);
                } else {
                    document.getElementById(id).innerHTML = response;
                }
                document.getElementById("message"+id).innerHTML = "";
            }
        });
    }
}
function bookmark(id) {
    save = document.getElementById(id).innerHTML;
    $.ajax({
        type: 'post',
        url: 'passer.php',
        data: {
            saveproduct: save,
            id: id,
        },
        success: function (response) {
            if (testJSON(response)) {
                proceessjson(response);
            } else {
                document.getElementById(id).innerHTML = response;
                // console.log(response);
            }
        }
    });


}

function testJSON(text) {
    if (typeof text !== "string") {
        return false;
    }
    try {
        JSON.parse(text);
        return true;
    } catch (error) {
        return false;
    }
}


function proceessjson(response) {
    obj = JSON.parse(response);

    // Handle API-style response format
    if (obj.hasOwnProperty("code")) {
        // Determine message type based on code
        let type = obj.code === 200 ? "success" : "error";
        
        // Build message content including validation errors if present
        let message = obj.message;
        if (obj.errors && Array.isArray(obj.errors)) {
            message += "<ul class='list-unstyled mt-2 mb-0'>";
            obj.errors.forEach(error => {
                message += `<li>• ${error}</li>`;
            });
            message += "</ul>";
        }

        // Show message using notify function
        notify(type === "success" ? "Success" : "Error", message, type);

        // If success, redirect if data contains redirect URL
        if (obj.code === 200 && obj.data && obj.data.redirect) {
            loadpage(obj.data.redirect, 2000);
        }
        
        return;
    }

    // Handle existing response format
    if (obj.hasOwnProperty("function")) {
        window.settings = { functionName: `${obj.function[0]}` };
        var fn = window[settings.functionName];
        if (typeof fn === 'function') {
            fn(obj.function['data'][0], obj.function['data'][1]);
        }
    }

    if (obj.hasOwnProperty("message")) {
        notify(obj.message[0], obj.message[1], obj.message[2]);
    }

    if (obj.closemodal && obj.closemodal === true) {
        $('#modal-lg').modal('hide');
    }
}

function restcomment(data1, data2){
    document.getElementById("makecomment").innerHTML = "";
    document.getElementById("replyID").value = "";
    document.getElementById("message").value = "";
    productcomments(data1);
}

function notify(title, message, type) {
    try {
        // Primary method using SweetAlert
        var span = document.createElement("span");
        span.innerHTML = message;
        swal({
            title: title,
            content: span,
            icon: type,
            buttons: true,
            dangerMode: type == "success" ? false : true,
        });
    } catch (error) {
        // Fallback method using Bootstrap alerts
        try {
            // Find or create a container for notifications
            let notificationContainer = document.getElementById('notification-container');
            if (!notificationContainer) {
                notificationContainer = document.createElement('div');
                notificationContainer.id = 'notification-container';
                notificationContainer.style.position = 'fixed';
                notificationContainer.style.top = '20px';
                notificationContainer.style.right = '20px';
                notificationContainer.style.zIndex = '9999';
                document.body.appendChild(notificationContainer);
            }

            // Create the notification element
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
            notification.innerHTML = `
                <strong>${title}</strong><br>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;

            // Add to container
            notificationContainer.appendChild(notification);

            // Auto remove after duration
            setTimeout(() => {
                notification.remove();
            }, parseInt(duration) || 3000);
        } catch (secondError) {
            // Last resort fallback - simple alert
            console.warn('Notification system failed:', secondError);
            alert(`${title}: ${message}`);
        }
    }
}

  function showPreview(event, id = "file-ip-1-preview"){
    if(event.target.files.length > 0){
      var src = URL.createObjectURL(event.target.files[0]);
      var preview = document.getElementById(id);
      preview.innerHTML = "<img src='"+src+"' style='width: 100px' />";
    //   preview.style.display = "block";
    }
  }

  function checkusername(id) {
    //   alert('true woeking');
     id =  document.getElementById(id);
        $.ajax({
            type: 'post',
            url: 'passer.php',
            data: {
                checkusername: id.value,
            },
            success: function (response) {
                if(response == "wrong"){
                    id.style.border = "2px solid red";
                    document.getElementById("errormessagedisplay").innerHTML = "Username taken or can not be use.";
                    document.getElementById("errormessagedisplay").className = "text-danger"
                    document.getElementById('displaystorename').innerHTML = '';
                }else{
                    document.getElementById('displaystorename').innerHTML = response;
                    id.style.border = "2px solid green";
                    id.value = response;
                    document.getElementById("errormessagedisplay").innerHTML = "";
                }
            }
        });
    
}

function showcontent(id) {
    var value = document.getElementById(id);
    title = value.dataset.title;
    link = value.dataset.url;
    modaltitle = document.getElementById('modal-title').innerHTML = title;
    $.ajax({
        type: 'post',
        url: 'modaldisplay.php',
        data: {
            urlink: link,
            secured:"yes",
            id: id,
        },
        success: function (response) {
            document.getElementById("modal-body").innerHTML = response;
        }
    });
}

// function deletecat(id) {
//     var r = confirm("You are about to delete an item!");
//     if (r == true) {
//         $.ajax({
//             type: 'post',
//             url: 'ajax.php',
//             data: {
//                 deletecat: id,
//                 subid: id,
//             },
//             success: function (response) {
//                 // console.log("yes");
//                 document.getElementById("group" + id).innerHTML = "";

//             }
//         });
//     }
// }

// function deletemaincat(id) {
//     var r = confirm("You are about to delete an item!");
//     if (r == true) {
//         $.ajax({
//             type: 'post',
//             url: 'ajax.php',
//             data: {
//                 deletemaincat: id,
//                 catid: id,
//             },
//             success: function (response) {
//                 document.getElementById("mcat" + id).innerHTML = "";
//                 // console.log("mcat" + id);
//             }
//         });
//     }
// }

// function editcat(id) {
//     catvalue = document.getElementById("input" + id).value;
//     $.ajax({
//         type: 'post',
//         url: 'ajax.php',
//         data: {
//             editcat: id,
//             catvalue: catvalue,
//         },
//         success: function (response) {
//             document.getElementById("custommessage").innerHTML = response;
//         }
//     });
// }

function addinput(id) {
    document.getElementById("catid").value = id;
}

function addnewinput() {
    randvalue2 = Math.floor(Math.random() * 100);
    randvalue3 = Math.floor(Math.random() * 100);
    $('#upload-area').append('<div id="' + randvalue2 + '" class="preview-image readme"> <div class="remove-image bg-danger" onclick="removeinput(\'' + randvalue2 + '\')"><span class="fa fa-times"></span></div><label for="' + randvalue3 + '"><li class="fa fa-upload"></li> <br><span>Click here to select image</span></label><input type="file" id="' + randvalue3 + '" onchange="showPreview(event, \'' + randvalue2 + '\')" name="uploaded_file[]" class=""></div>');

    //  '<div class="input-group mb-2" id="' + randvalue2 + '"><input type="file" name="uploaded_file[]" class="form-control"> <span onclick="removeinput(\'' + randvalue2 + '\')" class="input-group-prepend btn btn-danger">x</span></div><div id="file' + randvalue + '"></div>';

}

function addsubcat() {
    document.getElementById("subcustommessage").innerHTML = "Please Wait...";
    catid = document.getElementById("catid").value;
    subcatname = document.getElementById("subcatname").value;
    $.ajax({
        type: 'post',
        url: 'ajax.php',
        data: {
            catid: catid,
            subcatname: subcatname,
        },
        success: function (response) {
            var res = response.substring(0, 30);
            if (res === "<div class='card card-primary'") {
                document.getElementById("subcustommessage").innerHTML = response;
            } else {
                $("#value" + catid).prepend(response);
                document.getElementById("subcustommessage").innerHTML = "Item Added";
                document.getElementById("subcatname").value = "";
                // console.log("value" + catid);
                // document.getElementById("value"+id).innerHTML = response;
            }
        }
    });
}



// check task
function checktask(id) {
    var r = confirm("You are about to confirm a task. Please note that you might not be able to undo this action");
    if (r == true) {
        document.getElementById('button-' + id).disabled = true;
        paid_amount = document.getElementById('pay-' + id).value;
        $.ajax({
            type: 'post',
            url: 'ajax.php',
            data: {
                comfirmtask: id,
                paid_amount: paid_amount,
            },
            success: function (response) {
                if (response === 1) {
                    document.getElementById('tr-' + id).innerHTML = "";
                } else {
                    var res = response.substring(0, 5);
                    // console.log(res);
                    if (res === "Error") {
                        alert(response);
                    }
                }
            }
        });
    }
}


// check task
function cooperative_payin_form(id, name, amount) {
    $.ajax({
        type: 'post',
        url: 'ajax.php',
        data: {
            cooperative_payin_form: id,
            amount: amount,
            name: name,
        },
        success: function (response) {
            document.getElementById('modal-body').innerHTML = response;

        }
    });
}

// onclick(updateinfo())
function updateinfo(what, id) {
    $.ajax({
        type: 'post',
        url: 'ajax.php',
        data: {
            what: what,
            id: id,
        },
        success: function (response) {
            document.getElementById('modal-body').innerHTML = response;

        }
    });
}

function removeimage(id) {
    var r = confirm("You are about to remove an image!");
    var what = "removeimage";
    if (r == true) {
        // document.getElementById('image'+id).style.display = "none";
        $.ajax({
            type: 'post',
            url: 'passer.php',
            data: {
                removeimage: what,
                id: id,
            },
            success: function (response) {
                document.getElementById('image' + id).remove();
            }
        });
    }
}



function moreproducts(id){
    $.ajax({
        type: 'post',
        url: 'passer.php',
        data: {
            moreproducts: id,
        },
        success: function (response) {
            document.getElementById('moreproducts').innerHTML = response;
        }
    });
}

function productcomments(id, value = ""){
    // alert(id);
    $.ajax({
        type: 'post',
        url: 'passer.php',
        data: {
            productcomments: id,
        },
        success: function (response) {
            document.getElementById('productcomments').innerHTML = response;
        }
    });
}

function removecomment(id){
    if(confirm("You are about to remove a comment")){
        $.ajax({
            type: 'post',
            url: 'passer.php',
            data: {
                removecomment: id,
            },
            success: function (response) {
                if(testJSON(response)){
                    proceessjson(response);
                }
            }
        });
    }
}

function removecommentcontent(id, value = ""){
    document.getElementById("c"+id).remove();
}

function allusers(id){
    // confirm(id);
    $.ajax({
        type: 'post',
        url: 'passer.php',
        data: {
            allusers: "all",
        },
        success: function (response) {
            // confirm(response);
            document.getElementById(id).innerHTML = response;
        }
    });
}

function changetext(id, text){
    if(id == "message-no") {
        var divs = document.querySelectorAll("#"+id);
        for (var i = 0; i < divs.length; i++) {
            divs[i].innerHTML = text; // Replace 'New content' with your desired content
          }
    }else{
        document.getElementById(id).disabled = true;
        document.getElementById(id).innerHTML = text;
    }
}

function get_no_notification() {
    var data = {
        get_no_notification: "",
        page: "notifications",
    };
    update = simple_ajax(data);
}
function get_no_messages() {
    var data = {
        get_no_messages: "",
        page: "notifications",
    };
    update = simple_ajax(data);
}

  function loadnotification() {
    document.getElementById("offcanvasRight").innerHTML = "Loading...";
    var data = {
        get_recent_notification: "",
        page: "notifications",
    };
  
    $.ajax({
        url: "passer?=notifications",
        data: data,
        type: 'POST',
        success: function (response) {
            document.getElementById("offcanvasRight").innerHTML = response;
        }
    });
  }

  function update_last_seen() {
    var data = {
        update_last_seen: "",
        page: "chat",
    };
    update = simple_ajax(data);
    // console.log("last seen updated");
  }

//   notification function start here.

// get notification of a specific type and display.
function get_notifications(what) {
      var data = {
        get_notifications: what,
        page: "notifications",
      };
      simple_ajax(data);
}

function isCookieExpired(cookieName) {
    if (getCookieValue("Nonotifications") == null) {
        return  true;
    }
    var cookie = document.cookie.split(';').find(function(cookie) {
      return cookie.trim().startsWith(cookieName + '=');
    });
  
    if (cookie) {
      var cookieValue = cookie.split('=')[1];
      var expirationDate = new Date(cookieValue);
  
      return expirationDate < new Date();
    }
  
    return true; // Cookie doesn't exist
}

// get all pending chat notifications and display them.
  function get_pending_chat_notifications() {
   
        if (isCookieExpired('Nonotifications')) {
      var data = {
        get_pending_chat_notifications: "",
        page: "notifications",
      };
      simple_ajax(data);
      // Calculate the expiration date
      var expirationDate = new Date();
      expirationDate.setTime(expirationDate.getTime() + 2 * 60 * 60 * 1000); // 5 hours in milliseconds
    // expirationDate.setTime(expirationDate.getTime() + (1 * 60 * 1000)); // 1 minute in milliseconds 
    document.cookie =
        "Nonotifications=yes; expires=" +
        expirationDate.toUTCString() +
        "; path=/";
    }
  }

  function display_notification(data, holder) {
    const params = new URLSearchParams(data);
    // params.get("confirm")
    if (!check_notification_permission()) {
      return false;
    }
    var options = {
      body: params.get("body"),
      icon: params.get("icon"),
      tag: params.get("tag"),
      data: { url: params.get("url") },
    };
    const notification = new Notification(params.get("title"), options);
    notification.addEventListener("click", function () {
      // Open the URL when the notification is clicked
      window.open(notification.data.url);
    });
  }

  function check_notification_permission(){
    if (!("Notification" in window)) {
        // Check if the browser supports notifications
        // alert("This browser does not support desktop notification");
        return false;
      } else if (Notification.permission === "granted") {
        // Check whether notification permissions have already been granted;
        // if so, create a notification
        return true;
        // …
      } else if (Notification.permission !== "denied") {
        // We need to ask the user for permission
        Notification.requestPermission().then((permission) => {
          // If the user accepts, let's create a notification
          if (permission === "granted") {
           return true;
            // …
          }
        });
      }
      return false;
  }
 function send_notify() {
        if (!("Notification" in window)) {
          // Check if the browser supports notifications
          alert("This browser does not support desktop notification");
        } else if (Notification.permission === "granted") {
          // Check whether notification permissions have already been granted;
          // if so, create a notification
          const notification = new Notification("Hi there!");
          // …
        } else if (Notification.permission !== "denied") {
          // We need to ask the user for permission
          Notification.requestPermission().then((permission) => {
            // If the user accepts, let's create a notification
            if (permission === "granted") {
              const notification = new Notification("Hi there!");
              // …
            }
          });
        } 
        // At last, if the user has denied notifications, and you
        // want to be respectful there is no need to bother them anymore.  
 }

 function onset_chat(value1, value2) {
    cancel_reply();
    document.getElementById("message-input-box").value = "";
    document.getElementById("image-preview-upload").innerHTML = "";
    document.getElementById("upload").value = "";
    if(value2 != "") document.getElementById("chatnew").innerHTML += value2;
    // eraseCookie("isSave");
    // console.log(getCookie("isSave"));
    setCookie("isSave", true, 1);
    // console.log(getCookie("isSave"));
    iniFromChat();
    // return true;
 }

 function handleG(fileID, message) {
   onset_chat("", message);
   document.getElementById("file-"+fileID).innerHTML = "<small class='text-warning'><b>We are still proccessing some part of your video please do not close this tab.</b></small>";
   var data = {
     Gupload: "yes",
     page: "chat",
     fileID: fileID,
   };
   simple_ajax(data, "chat-passer?videoprocess");
 }

 function removeAfterSubstring(str, substring) {
    // Find the index of the substring within the string
    const index = str.indexOf(substring);
  
    // If the substring is found, return the substring up to that point
    if (index !== -1) {
      return str.substring(0, index);
    }
  
    // If the substring is not found, return the original string
    return str;
  }


 function cancel_reply() {
    if (
      document.getElementById("reply_div") &&
      document.getElementById("reply_message") &&
      document.getElementById("reply_to")
    ) {
      document.getElementById("reply_to").value = "";
      document.getElementById("reply_message").innerHTML = "";
      document
        .getElementById("reply_div")
        .style.setProperty("display", "none", "important");

        if(document.getElementById("moremessage")) {
            var moremessage = document.getElementById("moremessage");
            // var moremessage_url = ;
            moremessage.setAttribute('data-url', removeAfterSubstring(moremessage.dataset.url, "&reply_to="));
        }
    }
  }

 function make_visible(id_name) {
    document.getElementById(id_name).style.setProperty("visibility", "visible", "important");
}


function imageviwer(url) {
    image = document.getElementById("imageviewer");
    if(image) {
        image.src = url;
    }
}



function confirmRedirect(message = "Are you sure you want to leave this page?") {
    const userConfirmed = confirm(message);
    return userConfirmed; // If the user clicks "OK", return true to proceed with the link, otherwise return false.
}


function setNavActive(t = null) {
    t = t ?? window.location.pathname;
    t.split("index");
    let a;
    t === "/" ? a = "home" : a = t.replace("index", "");
    let e = document.getElementById(a);
    e && (e.classList.remove("text-neutral-600", ":text-neutral-400",
            "hover:text-neutral-500", ":hover:text-neutral-500"), e.classList
        .add("text-orange-400", ":text-orange-300"), e.setAttribute(
            "aria-current", "page"))
}

function jsInis(elements) {
    newelements = elements.querySelectorAll('[data-search-list]');
    iniSearch(newelements);
}

function iniSearch(elements = null) {
    // search items
    let listelements = elements || document.querySelectorAll('[data-search-list]');
    listelements.forEach(function(input) {
    input.addEventListener('input', function() {
        let filter = this.value.toLowerCase();
        let listId = this.getAttribute('data-search-list');
        let attribute = this.getAttribute('data-search-attribute');
        let rows = document.querySelectorAll(`.${listId}`);    
        rows.forEach(function(row) {
            let value = row.querySelector(`[data-${attribute}]`).getAttribute(`data-${attribute}`).toLowerCase();
            // console.log(value);    
            row.style.display = value.includes(filter) ? '' : 'none';
        });

        // Sort rows if needed
        let sortedRows = Array.from(rows).sort(function(a, b) {
            let valueA = a.querySelector(`[data-${attribute}]`).getAttribute(`data-${attribute}`).toLowerCase();
            let valueB = b.querySelector(`[data-${attribute}]`).getAttribute(`data-${attribute}`).toLowerCase();
            
            return valueA.localeCompare(valueB);
        });

        let tbody = document.getElementsByClassName(listId);
        sortedRows.forEach(function(row) {
            console.log(tbody);
            // row.className += "d-none";
            tbody.appendChild(row); // Reorder the rows
        });
    });
});
}

    function togglePassword(inputId, button) {
        var input = document.getElementById(inputId);
        var icon = button.querySelector('i'); // Find the icon inside the button

        if (input.type === "password") {
            input.type = "text"; // Show the password
            icon.classList.remove('fa-eye'); // Remove the "eye" icon
            icon.classList.add('fa-eye-slash'); // Add the "eye-slash" icon
        } else {
            input.type = "password"; // Hide the password
            icon.classList.remove('fa-eye-slash'); // Remove the "eye-slash" icon
            icon.classList.add('fa-eye'); // Add the "eye" icon
        }
    }



iniSearch();