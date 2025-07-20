import axios from "axios";

const apiOrigin = import.meta.env.VITE_API_URL;

if (!apiOrigin || apiOrigin === "") throw new Error('Missing "VITE_API_URL" variable!');

export default axios.create({
    baseURL: import.meta.env.VITE_API_URL,
    withCredentials: true,
});
