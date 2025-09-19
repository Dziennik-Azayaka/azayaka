<script setup lang="ts">
import { LucidePlus } from "lucide-vue-next";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

import {
    Button,
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@azayaka-frontend/ui";

const { t } = useI18n();
const showDialog = ref(false);

const emit = defineEmits(["continue"]);

function onContinueClick() {
    showDialog.value = false;
    emit("continue");
}
</script>

<template>
    <Dialog v-model:open="showDialog">
        <DialogTrigger as-child>
            <Button>
                <LucidePlus aria-hidden="true" />
                {{ t("addUnit") }}
            </Button>
        </DialogTrigger>
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Wymagane stworzenie zespołu</DialogTitle>
                <DialogDescription>
                    Do stworzenia drugiej jednostki trzeba stworzyć zespół, w którym będą znajdowały się wszystkie
                    jednostki. Kontynuuj, by stworzyć zespół.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <DialogClose as-child>
                    <Button variant="outline" type="button">{{ t("cancel") }}</Button>
                </DialogClose>
                <Button @click="onContinueClick">
                    {{ t("continue") }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
