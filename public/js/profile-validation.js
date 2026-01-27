//FONCTIONS UTILITAIRES
function showError(input, message) {
    input.classList.add('border-red-500', 'ring-red-500', 'text-red-500');
    input.classList.remove('border-gray-300', 'dark:border-gray-600', 'focus:ring-blue-500');

    let errorElement = input. parentNode.querySelector('.error-text');

    if(!errorElement) {
        errorElement = document.createElement('p');
        errorElement.classList.add('error-text', 'text-red-500', 'text-xs', 'mt-1', 'font-medium');
        input.parentNode.appendChild(errorElement);
    }

    errorElement.textContent = message;
}

function hideError(input) {
    input.classList.remove('border-red-500', 'ring-red-500', 'text-red-500');
    input.classList.add('border-gray-300', 'dark:border-gray-600');

    const errorElement = input.parentNode.querySelector('.error-text');

    if(errorElement) {
        errorElement.remove();
    }
}

function initProfileValidation(config) {
    //REGEX
    const nameRegex = /^[a-zA-ZÀ-ÿ\s\-\']+$/;
    const phoneRegex = /^0(?!.*(.)\1{8})(?!123456789)[0-9]{9}$/;

    //Récupération des éléments
    const firstnameInput = document.getElementById(config.firstnameId);
    const lastnameInput = document.getElementById(config.lastnameId);
    const phoneInput = document.getElementById(config.phoneId);
    const companyNameInput = document.getElementById(config.companyNameId);
    const submitBtn = document.querySelector('button[type="submit"]');

    //Fonction qui bloque le bouton submit si le formulaire n'est pas rempli comme il faut
    function checkFormValidity(){
        const isFirstnameValid = firstnameInput.value.length >= 2 && nameRegex.test(firstnameInput.value);
        const isLastnameValid = lastnameInput.value.length >= 2 && nameRegex.test(lastnameInput.value);
        const isphoneValid = phoneInput.value.length === 10 && phoneRegex.test(phoneInput.value);
        const isCompanyNameValid = !companyNameInput || (companyNameInput.value.length >= 2 && nameRegex.test(companyNameInput.value));

        if (isFirstnameValid && isLastnameValid && isphoneValid && isCompanyNameValid) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            submitBtn.classList.add('hover:scale-105');
        }else{
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitBtn.classList.remove('hover:scale-105');
        }
    }

    //fonction qui valide le nom et prénom
    function validateName(input, regex) {
        const value = input.value;
        if(value.length > 0 && (value.length < 2 || !regex.test(value))) {
                showError(input, "Format invalide : minimum 2 lettres et pas de chiffres.");
            } else {
                hideError(input);
            }
        checkFormValidity();
    }
    //Validation prénom
    if(firstnameInput) {
        firstnameInput.addEventListener('input', function() {
            validateName(firstnameInput, nameRegex);
        });
    }

    //Validation nom
    if(lastnameInput) {
        lastnameInput.addEventListener('input', function() {
            validateName(lastnameInput, nameRegex);
        });
    }

    //fonction qui valide le numéro de téléphone
    function validatePhone(input, regex) {
        const value = input.value;

        if(value.length > 0) {
            if(value[0] !== '0') {
                showError(input, "Le numéro doit commencer par 0")
            }else if(!regex.test(value)) {
                showError(input, "Numéro invalide");
            }else{
                hideError(input);
            }
        }
        checkFormValidity();
    }
    //Validation du numéro de téléphone
    if(phoneInput) {
        phoneInput.addEventListener('input', function() {
            validatePhone(phoneInput, phoneRegex);
        })
    }

        //fonction qui valide le nom de la compagnie
    function validateCompanyName(input, regex) {
        const value = input.value;
        if(value.length > 0 && (value.length < 2 || !regex.test(value))) {
            showError(input, "Le format de la société n'est pas valide");
        }else{
            hideError(input);
        }
        checkFormValidity();
    }
    //Validation du nom de la compagnie
    if(companyNameInput){
        companyNameInput.addEventListener('input', function() {
            validateCompanyName(companyNameInput, nameRegex);
        });
    }

    checkFormValidity();
}