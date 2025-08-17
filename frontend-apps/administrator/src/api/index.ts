import axios, { isAxiosError } from "axios";

const apiOrigin = import.meta.env.VITE_API_URL;

if (!apiOrigin || apiOrigin === "") throw new Error('Missing "VITE_API_URL" variable!');

const axiosInstance = axios.create({
    baseURL: import.meta.env.VITE_API_URL,
    withCredentials: true,
});

axiosInstance.interceptors.response.use(
    (response) => response,
    (error) => {
        if (isAxiosError(error) && error.response?.data.errors[0] === "USER_NOT_LOGGED_IN")
            window.location.pathname = "/";
        else return Promise.reject(error);
    },
);

export default axiosInstance;
