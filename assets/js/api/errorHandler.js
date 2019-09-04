
export default function (error) {
    if (error.response.status === 401) {
        document.location.href='/login' + document.location.hash;
    }

    return Promise.reject(error);
};

