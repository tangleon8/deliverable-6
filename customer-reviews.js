// DOM to load
document.addEventListener("DOMContentLoaded", () => {
    const sortSelect = document.getElementById('sort-select'); // Dropdown for sorting
    const reviewsContainer = document.getElementById('reviews-container'); // Container for reviews

    // Add event listener to the sort dropdown
    sortSelect.addEventListener('change', () => {
        const sortValue = sortSelect.value; // Get selected sorting option
        const reviews = Array.from(reviewsContainer.querySelectorAll('.review-item')); // Get all reviews

        // Sort reviews  on the selected criteria
        reviews.sort((a, b) => {
            if (sortValue === 'highest-rating') {
                return b.dataset.rating - a.dataset.rating; // Descending rating
            } else if (sortValue === 'lowest-rating') {
                return a.dataset.rating - b.dataset.rating; // Ascending rating
            } else if (sortValue === 'restaurant') {
                return a.dataset.restaurant.localeCompare(b.dataset.restaurant); // Alphabetical by restaurant
            } else if (sortValue === 'most-recent') {
                return new Date(b.dataset.date) - new Date(a.dataset.date); // Most recent first
            }
        });

        // Re-append the reviews in sorted order
        reviews.forEach(review => reviewsContainer.appendChild(review));
    });
});
