const activeFetches = new Map();
  let searchM = document.querySelector("#searchMarket");
  let searchIsSave = true;
if(searchM) {
  searchM.addEventListener("keydown", (e) => {
    searchMarket();
  });
}


function searchMarket() {
  if (!searchIsSave) return false;
    searchIsSave = false;
    var fetchID = searchM.getAttribute("data-id");
    fetchDiv = document.querySelector(fetchID);
    var platform = "";
    var category = "all";
    if (document.querySelector("#searchMarketPlaform")) {
      platform = document.querySelector("#searchMarketPlaform").value;
    }
    if (document.querySelector("#searchMarketcategory")) {
      category = document.querySelector("#searchMarketcategory").value;
    }
    fetchDiv.setAttribute(
      "data-path",
      "passer?a=account&s=" + searchM.value + "&platform=" + platform +"&category=" + category
    );
    if (loadFetchData(fetchDiv)) searchIsSave = true;
}

function addPlatfrom(value, name = "") {
  document.querySelector("#PlaformName").innerHTML = name;
  document.querySelector("#searchMarketPlaform").value = value;
  searchMarket();
}
document.querySelectorAll("[data-load]").forEach(loaddata => {
  loadFetchData(loaddata);
});

if(document.querySelectorAll('.fetcher-form')) {
  document.querySelectorAll('.fetcher-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission
  
        const targetId = this.getAttribute('data-target');
        const targetDiv = document.getElementById(targetId);
  
        if (!targetDiv) {
            console.warn(`Target div with ID "${targetId}" not found.`);
            return;
        }
  
        // Get current data-path base (before the query parameters)
        let basePath = targetDiv.getAttribute('data-path').split('?')[0];
        const formData = new FormData(this);
        const urlParams = new URLSearchParams();

        // Add all form fields, even empty ones
        formData.forEach((value, key) => {
            urlParams.set(key, value);
        });
  
        // Reconstruct the data-path with only the form parameters
        const updatedDataPath = basePath + '?' + urlParams.toString();
        targetDiv.setAttribute('data-path', updatedDataPath);
  
        // Reset start to 0 for new searches
        targetDiv.setAttribute('data-start', '0');
  
        // Reload data based on the updated path
        loadFetchData(targetDiv);
    });
  });
}


// Track fetch requests and timeouts for each element to prevent overlapping requests
function loadFetchData(loaddata) {
  const what = loaddata.getAttribute("data-load");

  // Check if `data-displayId` exists, if not, use `id` of `loaddata`, or generate a new ID if neither exist
  let displayId = loaddata.getAttribute("data-displayId") || loaddata.id;
  if (!displayId) {
      displayId = `loaddata-${Math.random().toString(36).substr(2, 9)}`;
      loaddata.id = displayId; // Assign the generated ID to the element
  }
  // document.getElementById("displayId").innerHTML = "";
  const start = loaddata.getAttribute("data-start") ?? 0;
  const limit = loaddata.getAttribute("data-limit") ?? 100;
  const path = loaddata.getAttribute("data-path") ?? "passer";
  const isReplace = loaddata.getAttribute("data-isreplace") ?? 'false';
  const interval = loaddata.getAttribute("data-interval") ?? 3000;

  // Abort any ongoing fetch request and clear timeout for this `displayId`
  if (activeFetches.has(displayId)) {
      const { controller, timeoutId } = activeFetches.get(displayId);
      controller.abort(); // Abort the ongoing fetch
      clearTimeout(timeoutId); // Clear the timeout if set
  }

  // Initialize a new AbortController for this fetch
  const controller = new AbortController();
  activeFetches.set(displayId, { controller, timeoutId: null });

  // Clear the content of the element with the determined `displayId`
  document.querySelector("#" + displayId).innerHTML = "";
  // modalcontent(displayId);
  // Start fetching data
  fetchData(what, displayId, limit, start, path, isReplace, interval, controller);
}

function fetchData(what, displayId, limit = 1, start = 0, path = "passer", isReplace="false", interval = 3000, controller) {
  
  const displayHere = document.getElementById(displayId);
  if(start == 0) {
    const loadingMessage = "<p class='h4' id='loadingData'><b>Loading Data</b></p>";
    if (displayHere.innerHTML === "") displayHere.innerHTML = loadingMessage;
  }
  const data = { page: what, what: what, start: start, limit: limit };
  // console.log("Fetching data:", data);

  const request = $.ajax({
      type: "POST",
      url: path === "passer" ? path + gets() : path,
      data: data,
      signal: controller.signal, // Use AbortController's signal to cancel if needed
  });

  request.done(function (response) {
    if(start == 0)  document.getElementById("loadingData").style.display = "none";
      // Stop if the response is empty or invalid
      if (!response || response.trim() === "") {
          start = 0;
          return null;
      }
      
      // Parse JSON response if possible
      if (checkJSON(response)) {
          const obj = JSON.parse(response);
          if (obj['status'] !== "ok") return null;
          response = obj['data'];
      }

      // Update the display with the new response
      displayHere.innerHTML = (isReplace === "false") ? displayHere.innerHTML + response : response;

      // Update start for pagination
      start = parseInt(start) + parseInt(limit);

      if(displayHere.querySelectorAll('[data-url]')) {
        console.log("Works here")
        modalelements = displayHere.querySelectorAll('[data-url]');
        iniModal(modalelements);
      }else{
        console.log("Nothing here")
      }

      if(displayHere.querySelectorAll("#foo")) {
        const elements = displayHere.querySelectorAll("#foo");
        $i = 0;
        console.log("Got some");
        elements.forEach((element) => {
          iniForm(element);
          $i++;
        });
      }
      


      // Schedule the next fetch, but clear any previous timeout
      if (document.getElementById(displayId)) {
          const timeoutId = setTimeout(() => {
              fetchData(what, displayId, limit, start, path, isReplace, interval, controller);
          }, parseInt(interval));

          // Update the timeout ID for this fetch to allow canceling if needed
          activeFetches.get(displayId).timeoutId = timeoutId;
      }
  });

  // Handle fetch cancellation errors silently
  request.fail(function (jqXHR, textStatus) {
      if (textStatus !== "abort") {
          console.error("Fetch failed:", textStatus);
      }
  });
}

function checkJSON(text) {
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

function get_user_info(userID) {
    if(!document.getElementById(userID)) { return null; }
    data = document.getElementById(userID);
    if(data.innerHTML != "" || data.innerHTML != "loading...") { return null;}
    request = $.ajax({
        type: "POST",
        url: "passer",
        data: {userdetails: userID},
      });
      request.done(function (response) {
        data.innerHTML = response;
      });
}

function display_content(data) {
    document.querySelectorAll('.chat-list').forEach(function(el) {
        el.style.visibility = 'hidden';
        el.style.display = 'none';
     });
   var id = $(data).data('user-id');
   if(!document.getElementById("content"+id)) {
    fetchUserData("displayDetails", id);
}else{
    document.getElementById("content"+id).style.visibility = "visible";
    document.getElementById("content"+id).style.display = "block";
}


}

function fetchUserData(displayId, id) {
    request = $.ajax({
        type: "POST",
        url: "passer",
        data: { page: "userdetails", what: "userdetails", ID: id, start: 0 },
      });
      request.done(function (response) {
        if (response == null || response == "null" || response == "") {
            start = 0;
            return null;
        }
        document.getElementById(displayId).innerHTML += response;
        document.getElementById("content"+id).style.visibility = "visible";
        document.getElementById("content"+id).style.display = "block";
      });
}

function getwalletinfo(id, displayId='display-wallet-info') {
  document.getElementById(displayId).innerHTML = "<b class='text-warning'>getting data...</b>";

  request = $.ajax({
    type: "POST",
    url: "passer",
    data: { page: "wallets", what: "wallet", ID: id},
  });
  request.done(function (response) {
    document.getElementById(displayId).innerHTML = response;
    qr = document.getElementById("genqr");
    get_qr_code(qr);
  });
}


function gets() {
  const urlParams = new URLSearchParams(window.location.search);
  var params = "?o=0";

  for (const [key, value] of urlParams) {
      params += "&"+key+"="+value;
  }
  return params;
}


