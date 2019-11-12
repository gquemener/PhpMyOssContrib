export const fetchContributions = (page = 1) => {
    return fetch('/api/contributions', { cache: 'no-cache' })
        .then(response => response.json())
        .then(contributions => ({ contributions }))
    ;
};
