<script setup lang="ts">
import { LucideChevronsUpDown } from "lucide-vue-next";

import { Button } from "#ui/components/ui/button";

defineProps<{ initials: string; emailAddress: string }>();
</script>

<template>
    <Button size="lg" variant="ghost" class="h-[unset] py-2 !px-3">
        <div
            class="size-8 font-medium rounded-md bg-primary text-primary-foreground content-center text-center text-sm"
            aria-hidden="true"
        >
            {{ initials }}
        </div>
        <p class="font-medium text-sm not-md:hidden">{{ emailAddress }}</p>
        <LucideChevronsUpDown class="size-4 not-md:hidden" aria-label="Otwórz/zamknij menu" />
    </Button>
</template>
