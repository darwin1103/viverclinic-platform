document.addEventListener('DOMContentLoaded', function () {
    const copyUrlCells = document.querySelectorAll('.copy-url-cell');

    copyUrlCells.forEach(cell => {
        const urlText = cell.innerText.trim();

        // Create the copy icon
        const copyIcon = document.createElement('i');
        copyIcon.className = 'bi bi-copy';
        copyIcon.style.cursor = 'pointer';
        copyIcon.style.marginLeft = '8px';
        copyIcon.title = 'Copy to clipboard';

        // Append the icon to the cell
        cell.appendChild(copyIcon);

        // Add click event listener to the icon
        copyIcon.addEventListener('click', () => {
            navigator.clipboard.writeText(urlText).then(() => {
                // Optional: Provide feedback to the user
                const originalIcon = copyIcon.className;
                copyIcon.className = 'bi bi-check-lg text-success';
                setTimeout(() => {
                    copyIcon.className = originalIcon;
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        });
    });
});
