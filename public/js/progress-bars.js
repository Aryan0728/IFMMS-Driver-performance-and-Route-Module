// Function to safely set progress bar width with JavaScript
function setProgressBarWidth(element, width) {
    if (element) {
        element.style.width = width + '%';
    }
}

// After page load, update all progress bars
document.addEventListener('DOMContentLoaded', function() {
    // Route progress bars
    document.querySelectorAll('.route-progress').forEach(function(bar) {
        const progress = parseInt(bar.dataset.progress || 0);
        setProgressBarWidth(bar, progress);
    });

    // Driver performance bars
    document.querySelectorAll('.performance-progress').forEach(function(bar) {
        const score = parseInt(bar.dataset.score || 0);
        setProgressBarWidth(bar, score);
    });

    // Vehicle maintenance progress
    document.querySelectorAll('.maintenance-progress').forEach(function(bar) {
        const progress = parseInt(bar.dataset.progress || 0);
        setProgressBarWidth(bar, progress);
    });
});