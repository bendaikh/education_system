<script setup>
const props = defineProps({
  items: {
    type: Array,
    required: true,
  },
});
</script>

<template>
  <div class="bg-white py-8 shadow-sm">
    <div class="mx-auto max-w-6xl grid gap-6 sm:grid-cols-3 px-4">
      <div
        v-for="(item, idx) in items"
        :key="idx"
        class="flex flex-col items-center rounded-lg bg-[#f0f1f5] p-6 text-center"
      >
        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-primary-600/10 text-primary-600">
          <component :is="item.icon" class="h-8 w-8" />
        </div>
        <h3 class="mb-2 text-lg font-bold text-[#111218]">{{ item.title }}</h3>
        <p class="text-sm text-[#5f668c]">{{ item.description }}</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
</style>
