document.addEventListener('DOMContentLoaded', function () {
    const checkbox = document.getElementById('availabilityToggle');
    const hiddenInput = document.getElementById('availability');

    // Update hidden input based on checkbox state
    checkbox.addEventListener('change', function () {
        hiddenInput.value = this.checked ? 'Yes' : 'No';
    });

    // Initialize hidden input value based on checkbox state
    hiddenInput.value = checkbox.checked ? 'Yes' : 'No';
});
