export const fetchContributions = (page = 1) => {
    return fetch(`/api/contributions?page=${page}`).then(response => response.json());
};
