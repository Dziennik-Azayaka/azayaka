import axios, { isAxiosError } from "axios";

import { ApiError } from "@/api/error";
import { useUserStore } from "@/stores/user.store";

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
            if (error.status === 401 || error.status === 403) window.location.href = "/";
            const apiError = ApiError.fromAxiosError(error);
            console.warn(apiError);
            return Promise.reject(apiError);
        }
        return Promise.reject(error);
    },
);

axiosInstance.interceptors.request.use((config) => {
    const userStore = useUserStore();
    config.headers["Access-ID"] = userStore.access?.id;
    return config;
});

export default axiosInstance;
