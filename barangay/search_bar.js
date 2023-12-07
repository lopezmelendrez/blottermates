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
        window.location.href = 'add_lupon_account.php';
    } else if (lowerCaseSearchTerm.startsWith('incident') || lowerCaseSearchTerm.endsWith('incident')) {
        window.location.href = 'incident_reports.php';
    } else if (lowerCaseSearchTerm.startsWith('activity') || lowerCaseSearchTerm.endsWith('activity')) {
        window.location.href = 'activity_history.php';
    } else if (lowerCaseSearchTerm.startsWith('validate') || lowerCaseSearchTerm.endsWith('validate')) {
        window.location.href = 'incident_reports.php';
    } else if (lowerCaseSearchTerm.startsWith('history') || lowerCaseSearchTerm.endsWith('history')) {
        window.location.href = 'activity_history.php';
    } else if (lowerCaseSearchTerm.startsWith('log') || lowerCaseSearchTerm.endsWith('log')) {
        window.location.href = 'activity_history.php';
    } else if (lowerCaseSearchTerm.startsWith('lupon') || lowerCaseSearchTerm.endsWith('lupon')) {
        window.location.href = 'add_lupon_account.php';
    } else if (lowerCaseSearchTerm.startsWith('home') || lowerCaseSearchTerm.endsWith('home')) {
        window.location.href = 'home.php';
    } else if (lowerCaseSearchTerm.startsWith('account') || lowerCaseSearchTerm.endsWith('account')) {
        window.location.href = 'manage_accounts.php';
    } else if (lowerCaseSearchTerm.startsWith('manage') || lowerCaseSearchTerm.endsWith('manage')) {
        window.location.href = 'manage_accounts.php';
    } else if (lowerCaseSearchTerm.startsWith('execution') || lowerCaseSearchTerm.endsWith('execution')) {
        window.location.href = 'incident_reports.php';
    } else if (lowerCaseSearchTerm.startsWith('notice') || lowerCaseSearchTerm.endsWith('notice')) {
        window.location.href = 'incident_reports.php';
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