document.addEventListener('DOMContentLoaded', function() {
    var checkbox = document.querySelector('.my-custom-checkbox'); 
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