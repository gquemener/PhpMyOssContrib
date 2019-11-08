export const fetchContributions = (page = 1) => {
    return fetch(`/api/contributions?page=${page}`)
        .then(async response => {
            const contributions = await response.json();
            const pagesCount = parseInt(response.headers.get('Pages-Count')) || 0;

            return { contributions, pagesCount };
        });
};
