var originalValue = "";

  
var inputElementForm = document.getElementById("openedx_enrollment_course_id");
var inputElementProduct = document.getElementById("_course_id");
var inputElement = null;

if (inputElementForm !== null) {
    inputElement = inputElementForm;
} else if (inputElementProduct !== null) {
    inputElement = inputElementProduct;
}

if (inputElement !== null) {
    
    inputElement.addEventListener("input", function() {
        updateCourseId(this);
    });

    
    inputElement.addEventListener("focus", function() {
        saveOriginalValue(this);
    });

    
    inputElement.addEventListener("blur", function() {
        restoreOriginalValue(this);
    });

    function saveOriginalValue(input) {
        originalValue = input.value;
    }

    function restoreOriginalValue(input) {
        if (input.value !== originalValue && !input.value.startsWith("course-v1:")) {
            input.value = originalValue;
        }
    }

    function updateCourseId(input) {
        var prefix = "course-v1:";
        var currentValue = input.value;

        if (currentValue !== "" && !currentValue.startsWith(prefix)) {
            input.value = prefix;
        }
    }
}
