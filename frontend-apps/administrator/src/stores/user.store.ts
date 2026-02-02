import { defineStore } from "pinia";
import { ref } from "vue";

import type { UserAccessEntity, UserEntity } from "@/api/entities/user";
import UserApiService from "@/api/services/user";

export const useUserStore = defineStore("user", () => {
    const user = ref<UserEntity | null>(null);
    const access = ref<UserAccessEntity | null>(null);

    async function fetchUser() {
        user.value = await UserApiService.getUserInfo();
        return user.value;
    }

    async function getUser() {
        return user.value || (await fetchUser());
    }

    function setAccess(newAccess: UserAccessEntity) {
        access.value = newAccess;
    }

    return { user, fetchUser, getUser, setAccess, access };
});
