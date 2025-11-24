document.addEventListener('DOMContentLoaded', function() {
    const mainImageInput = document.getElementById('logo');
    const imagePreview = document.getElementById('imagePreview');

    if (mainImageInput) {
        mainImageInput.addEventListener('change', function(event) {
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                }
                reader.readAsDataURL(event.target.files[0]);
            }
        });
    }
});
