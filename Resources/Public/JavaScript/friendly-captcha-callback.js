/**
 * Friendly Captcha Callback
 * @param solution
 * @return void
 */
function friendlyCaptchaCallback(solution) {
    const elements = document.querySelectorAll('.frc-captcha-solution');
    const element = getSolutionsField(solution, elements)
    element.closest("form").querySelector('*[type="submit"]').removeAttribute('disabled');
}

/**
 * Compare solutions fields value with provided callback solution
 * @param solution
 * @param elements
 * @returns {*}
 */
function getSolutionsField(solution, elements) {
    for (let i = 0; elements.length; i++) {
        let element = elements[i];
        if (element.value === solution) {
            return element;
        }
    }
}

/**
 * Set submit field as disabled
 * @param elements
 */
function setSubmitDisabled(elements) {
    for (let i = 0; elements.length; i++) {
        elements[i].closest("form").querySelector('*[type="submit"]').setAttribute('disabled', '');
    }
}

/**
 * Dom ready
 * disable submit buttons
 */
document.addEventListener("DOMContentLoaded", function(event) {
    const elements = document.querySelectorAll('.frc-captcha[data-callback="friendlyCaptchaCallback"]');
    setSubmitDisabled(elements);
});

