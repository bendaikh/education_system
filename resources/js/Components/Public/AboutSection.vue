<script setup>
import { ref } from 'vue';

const openIdx = ref(null);

const props = defineProps({
  title: String,
  intro: String,
  image: String,
  items: Array, // [{title, content}]
});
</script>

<template>
  <section class="mx-auto max-w-6xl px-4 py-16 grid gap-8 lg:grid-cols-2 items-center">
    <img :src="image" alt="about image" class="w-full rounded-lg shadow-md" />
    <div>
      <h2 class="mb-4 text-2xl font-bold text-[#111218]">{{ title }}</h2>
      <p class="mb-6 text-[#5f668c]">{{ intro }}</p>

      <div v-for="(item, idx) in items" :key="idx" class="mb-2 border rounded">
        <button
          @click="openIdx = openIdx === idx ? null : idx"
          class="flex w-full items-center justify-between bg-[#f0f1f5] px-4 py-3 text-left font-medium text-[#111218]"
        >
          {{ item.title }}
          <span>{{ openIdx === idx ? '-' : '+' }}</span>
        </button>
        <div v-if="openIdx === idx" class="px-4 py-3 text-sm text-[#5f668c] bg-white">
          {{ item.content }}
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
</style>
