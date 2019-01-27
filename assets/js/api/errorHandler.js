
export default function (error) {
    if (error.response.status === 401) {
        document.location.href='/login';
    }

    return Promise.reject(error);
};

