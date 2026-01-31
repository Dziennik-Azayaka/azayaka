import { userDTOToEntity } from "../mappers/user";

import axios from "@/api";

export default {
    getUserInfo: () => axios.get("/user").then(({ data }) => userDTOToEntity(data)),
};
