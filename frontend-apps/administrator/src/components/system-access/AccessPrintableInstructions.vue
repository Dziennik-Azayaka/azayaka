<script setup lang="ts">
import { onMounted, useTemplateRef } from "vue";

import styles from "@/styles/printable-instruction.css?inline";

defineProps<{ accesses: { role: "employee"; header: string; code: string[] }[] }>();
const emit = defineEmits(["done"]);

const getRegistrationUrl = () => window.location.origin + "/rejestracja";
const containerRef = useTemplateRef("container");

onMounted(() => {
    const printWindow = window.open("about:blank", "_blank");
    if (printWindow) {
        printWindow.document.open();
        printWindow.document.write(`
            <html>
                <head>
                    <title>Wydruk - Instrukcja aktywacji dostępu</title>
                    <meta charset="utf-8">
                    <style>${styles}</style>
                </head>
                <body>
                    ${containerRef.value!.innerHTML}
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.document.fonts.ready.then(() => {
            printWindow.print();
            printWindow.close();
        });
    }
    emit("done");
});
</script>

<template>
    <div ref="container" style="display: none">
        <div class="access" v-for="(access, index) in accesses" :key="index">
            <h2>
                <span class="role">
                    <template v-if="access.role === 'employee'">Pracownik</template>
                </span>
                {{ access.header }}
            </h2>
            <h1>Dostęp do dziennika elektronicznego</h1>
            <p>
                Aby aktywować Twój dostęp do dziennika elektronicznego, wejdź na stronę
                <strong style="text-wrap: nowrap">{{ getRegistrationUrl() }}</strong> i postępuj zgodnie z instrukcjami.
            </p>
            <p>Twój <strong>kod aktywacji</strong>, który będzie Ci potrzebny do aktywacji dostępu do dziennika:</p>
            <ol class="code">
                <li v-for="(word, index) in access.code" :key="index">{{ word }}</li>
            </ol>
        </div>
    </div>
</template>
