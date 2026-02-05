// Modal functionality
const modal = document.getElementById('bookingModal');
const closeBtn = document.querySelector('.modal-close');
const bookNowButtons = document.querySelectorAll('.book-now-btn');

// Open modal when any Book Now button is clicked
bookNowButtons.forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    });
});

// Close modal when X is clicked
if (closeBtn) {
    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto'; // Restore scrolling
    });
}

// Close modal when clicking outside of it
window.addEventListener('click', function(event) {
    if (event.target === modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto'; // Restore scrolling
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && modal.style.display === 'block') {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto'; // Restore scrolling
    }
});
