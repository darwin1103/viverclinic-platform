// Rating module for session reviews
const RatingModule = (function() {
    let selectedFace = null;
    let currentRatedSession = null;

    const elements = {
        modal: null,
        faces: null,
        btnSendRate: null,
        rateComment: null,
        sessionNumberInput: null,
        ratingValueInput: null,
        form: null
    };

    function init() {
        cacheElements();
        attachEventListeners();
    }

    function cacheElements() {
        elements.modal = document.getElementById('modalRate');
        elements.faces = document.getElementById('faces');
        elements.btnSendRate = document.getElementById('btnSendRate');
        elements.rateComment = document.getElementById('rateComment');
        elements.sessionNumberInput = document.getElementById('ratingSessionNumber');
        elements.ratingValueInput = document.getElementById('ratingValueInput');
        elements.form = document.getElementById('ratingForm');
    }

    function attachEventListeners() {
        if (elements.faces) {
            elements.faces.addEventListener('click', handleFaceClick);
        }

        if (elements.modal) {
            elements.modal.addEventListener('show.bs.modal', handleModalShow);
            elements.modal.addEventListener('hidden.bs.modal', handleModalHidden);
        }

        if (elements.form) {
            elements.form.addEventListener('submit', handleFormSubmit);
        }

        // Listen for rating button clicks from the table
        document.addEventListener('click', function(e) {
            const btnRate = e.target.closest('.btn-rate');
            if (btnRate) {
                const sessionNumber = btnRate.getAttribute('data-session');
                openRatingModal(sessionNumber);
            }
        });
    }

    function handleFaceClick(e) {
        const face = e.target.closest('.face');
        if (!face) return;

        selectedFace = face.getAttribute('data-value');

        // Remove selected class from all faces
        const allFaces = elements.faces.querySelectorAll('.face');
        allFaces.forEach(f => f.classList.remove('selected'));

        // Add selected class to clicked face
        face.classList.add('selected');

        // Enable submit button
        if (elements.btnSendRate) {
            elements.btnSendRate.disabled = false;
        }

        // Update hidden input
        if (elements.ratingValueInput) {
            elements.ratingValueInput.value = selectedFace;
        }
    }

    function handleModalShow(event) {
        const button = event.relatedTarget;
        if (button) {
            const sessionNumber = button.getAttribute('data-session');
            currentRatedSession = sessionNumber;

            if (elements.sessionNumberInput) {
                elements.sessionNumberInput.value = sessionNumber;
            }
        }

        resetRating();
    }

    function handleModalHidden() {
        resetRating();
        currentRatedSession = null;
    }

    function handleFormSubmit(e) {
        e.preventDefault();

        if (!selectedFace) {
            return;
        }

        // Here you would normally submit the form via AJAX
        // For now, we'll simulate it with a toast
        const formData = new FormData(elements.form);

        // Close modal
        const modal = bootstrap.Modal.getInstance(elements.modal);
        if (modal) {
            modal.hide();
        }

        // Update table to show rating was submitted
        if (window.updateSessionRating) {
            window.updateSessionRating(currentRatedSession, selectedFace);
        }
    }

    function openRatingModal(sessionNumber) {
        currentRatedSession = sessionNumber;

        if (elements.sessionNumberInput) {
            elements.sessionNumberInput.value = sessionNumber;
        }

        resetRating();

        const modal = new bootstrap.Modal(elements.modal);
        modal.show();
    }

    function resetRating() {
        selectedFace = null;

        // Remove selected class from all faces
        if (elements.faces) {
            const allFaces = elements.faces.querySelectorAll('.face');
            allFaces.forEach(f => f.classList.remove('selected'));
        }

        // Clear comment
        if (elements.rateComment) {
            elements.rateComment.value = '';
        }

        // Disable submit button
        if (elements.btnSendRate) {
            elements.btnSendRate.disabled = true;
        }

        // Clear hidden input
        if (elements.ratingValueInput) {
            elements.ratingValueInput.value = '';
        }
    }

    return {
        init: init,
        openRatingModal: openRatingModal
    };
})();

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', RatingModule.init);
} else {
    RatingModule.init();
}
