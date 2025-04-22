<script setup>
import { Combobox, TransitionRoot } from "@headlessui/vue";
import { cn } from "@/lib/utils";

defineProps({
    class: String,
});
</script>

<template>
    <TransitionRoot
        leave="transition ease-in duration-100"
        leaveFrom="opacity-100"
        leaveTo="opacity-0"
    >
        <Combobox.Options
            :class="
                cn(
                    'absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md bg-popover p-1',
                    'text-popover-foreground shadow-md border border-border',
                    props.class,
                )
            "
        >
            <slot />
        </Combobox.Options>
    </TransitionRoot>
</template>
