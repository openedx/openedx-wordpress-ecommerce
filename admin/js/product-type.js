document.addEventListener('DOMContentLoaded', function() {
    var checkbox = document.getElementById("is_openedx_course");
    handleCheckboxChange(checkbox);
});

document.addEventListener('change', function() {
    var checkbox = document.getElementById("is_openedx_course");
    handleCheckboxChange(checkbox);
});

function handleCheckboxChange(checkbox) {
    var optionsGroup = document.querySelector('.custom_options_group');
    if (checkbox.checked) {
        optionsGroup.style.display = 'unset';
    } else {
        optionsGroup.style.display = 'none';
    }
}
