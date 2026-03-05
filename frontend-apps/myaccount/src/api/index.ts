import axios, { isAxiosError } from "axios";

import { ApiError } from "@/api/error";

const apiOrigin = import.meta.env.VITE_API_URL;

if (!apiOrigin || apiOrigin === "") throw new Error('Missing "VITE_API_URL" variable!');

const axiosInstance = axios.create({
    baseURL: import.meta.env.VITE_API_URL,
    withCredentials: true,
});

axiosInstance.interceptors.response.use(
    (response) => response,
    (error) => {
        if (isAxiosError(error)) {
            const apiError = ApiError.fromAxiosError(error);
            if (apiError.code === "USER_NOT_LOGGED_IN") window.location.href = "/";
            console.warn(apiError);
            return Promise.reject(apiError);
        }
        return Promise.reject(error);
    },
);

export default axiosInstance;
