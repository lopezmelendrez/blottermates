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

    if (lowerCaseSearchTerm.startsWith('mediation') || lowerCaseSearchTerm.endsWith('mediation')) {
        window.location.href = 'mediation_hearings.php';
    } else if (lowerCaseSearchTerm === 'hearing' || lowerCaseSearchTerm === 'hearings') {
        window.location.href = 'hearings.php';
    } else if (lowerCaseSearchTerm.startsWith('incident')) {
        window.location.href = 'incident_reports.php';
    } else if (lowerCaseSearchTerm.startsWith('conciliation') || lowerCaseSearchTerm.endsWith('conciliation')) {
        window.location.href = 'conciliation_hearings.php';
    } else if (lowerCaseSearchTerm.startsWith('arbitration') || lowerCaseSearchTerm.endsWith('arbitration')) {
        window.location.href = 'arbitration_hearings.php';
    } else if (lowerCaseSearchTerm.startsWith('create') || lowerCaseSearchTerm.endsWith('create')) {
        window.location.href = 'create_report.php';
    } else if (lowerCaseSearchTerm.startsWith('ongoing') || lowerCaseSearchTerm.endsWith('ongoing')) {
        window.location.href = 'ongoing_cases.php';
    } else if (lowerCaseSearchTerm.startsWith('settled') || lowerCaseSearchTerm.endsWith('settled')) {
        window.location.href = 'settled_cases.php';
    } else if (lowerCaseSearchTerm.startsWith('incomplete') || lowerCaseSearchTerm.endsWith('incomplete')) {
        window.location.href = 'incomplete_notices.php';
    } else if (lowerCaseSearchTerm.startsWith('home') || lowerCaseSearchTerm.endsWith('home')) {
        window.location.href = 'home.php';
    } else if (lowerCaseSearchTerm.startsWith('account') || lowerCaseSearchTerm.endsWith('account')) {
        window.location.href = 'my_account.php';
    } else if (lowerCaseSearchTerm.startsWith('profile') || lowerCaseSearchTerm.endsWith('profile')) {
        window.location.href = 'my_account.php';
    }  else {
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