const searchIcon = document.querySelector('.search-box .icon');
    const searchInput1 = document.getElementById('searchInput1');

    searchIcon.addEventListener('click', function () {
    const searchTerm = searchInput1.value.trim().toLowerCase();

    if (searchTerm !== '') {
        handleSearch(searchTerm);
    }
    });

searchInput1.addEventListener('keyup', function (e) {
    if (e.key === 'Enter') {
        const searchTerm = searchInput1.value.trim().toLowerCase();

        if (searchTerm !== '') {
            handleSearch(searchTerm);
        }
    }
});

function handleSearch(searchTerm) {
    const lowerCaseSearchTerm = searchTerm.trim().toLowerCase();

    if (lowerCaseSearchTerm.startsWith('add') || lowerCaseSearchTerm.endsWith('add')) {
        window.location.href = 'add-barangay-account.php';
    } else if (lowerCaseSearchTerm.startsWith('barangay') || lowerCaseSearchTerm.endsWith('barangay')) {
        window.location.href = 'add-barangay-account.php';
    } else if (lowerCaseSearchTerm.startsWith('analytic') || lowerCaseSearchTerm.endsWith('analytic')) {
        window.location.href = 'analytics.php';
    } else if (lowerCaseSearchTerm.startsWith('incident') || lowerCaseSearchTerm.endsWith('incident')) {
        window.location.href = 'analytics.php';
    } else if (lowerCaseSearchTerm.startsWith('ongoing') || lowerCaseSearchTerm.endsWith('ongoing')) {
        window.location.href = 'analytics.php';
    } else if (lowerCaseSearchTerm.startsWith('monthly') || lowerCaseSearchTerm.endsWith('monthly')) {
        window.location.href = 'transmittal_reports.php';
    } else if (lowerCaseSearchTerm.startsWith('transmittal') || lowerCaseSearchTerm.endsWith('transmittal')) {
        window.location.href = 'transmittal_reports.php';
    } else if (lowerCaseSearchTerm.startsWith('home') || lowerCaseSearchTerm.endsWith('home')) {
        window.location.href = 'home.php';
    } else if (lowerCaseSearchTerm.startsWith('account') || lowerCaseSearchTerm.endsWith('account')) {
        window.location.href = 'manage_accounts.php';
    } else if (lowerCaseSearchTerm.startsWith('manage') || lowerCaseSearchTerm.endsWith('manage')) {
        window.location.href = 'manage_accounts.php';
    } else if (lowerCaseSearchTerm.startsWith('report') || lowerCaseSearchTerm.endsWith('report')) {
        window.location.href = 'transmittal_reports.php';
    } else {
    searchInput1.value = `'${searchTerm.charAt(0).toUpperCase() + searchTerm.slice(1)}' was not found`;
    }
}

function restrictInput(input) {

// Remove special characters and numbers
input.value = input.value.replace(/[^a-zA-Z\s]/g, '');

// Restrict spacebar only if it's the first character
if (input.value.length > 0 && input.value[0] === ' ') {
  input.value = input.value.substring(1); // Remove the leading space
}
}