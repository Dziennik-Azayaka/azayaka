<script setup lang="ts">
import { LucideAlertCircle } from "lucide-vue-next";
</script>

<template>
    <section
        role="alert"
        class="light:bg-primary/10 text-primary rounded-md border border-primary flex gap-2 items-center px-4 py-3 text-sm"
    >
        <LucideAlertCircle aria-hidden="true" class="min-w-5 size-5" />
        <p>
            Na adres e-mail <strong>jan@fakelog.cf</strong> została wysłana wiadomość z linkiem do potwierdzenia adresu
            e-mail.
        </p>
    </section>
</template>
