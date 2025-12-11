function change_tab(tab_name) {
    if(tab_name == '#auth-2') {
        email = document.querySelector("#validateEmail");
        if (!email.checkValidity() || email.value == "") {
            Swal.fire({
                icon: 'danger',
                text: 'Enter a vaild email and try again.'
            });
            return;
        }
        document.querySelector("#email").value = email.value;
    } 

    if(tab_name == '#auth-3') {
        
        if (!document.querySelector('input[name="validateCountry"]:checked')) {
            Swal.fire({
                icon: 'danger',
                text: 'Select your country.'
            });
            return;
        }
    } 
    var someTabTriggerEl = document.querySelector('a[href="' + tab_name + '"]');
    document.querySelector('#auth-active-slide').innerHTML = someTabTriggerEl.getAttribute('data-slide-index');
    var actTab = new bootstrap.Tab(someTabTriggerEl);
    actTab.show();
}


function syncRadioWithDropdown() {
    const selectedRadio = document.querySelector('input[name="validateCountry"]:checked'); // Finds selected radio button
    const dropdown = document.getElementById('country');

    if (selectedRadio) {
      const selectedValue = selectedRadio.value; // Gets value of selected radio
      // Find matching option in dropdown and select it
      Array.from(dropdown.options).forEach(option => {
        option.selected = option.value === selectedValue;
      });
    }
  }

  // Add event listeners to radio buttons to sync on change
  document.querySelectorAll('input[name="validateCountry"]').forEach(radio => {
    radio.addEventListener('change', syncRadioWithDropdown);
  });
