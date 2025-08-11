<script setup>
import { computed, ref, onMounted } from 'vue';

const props = defineProps({
  stats: Array, // [{label, value, icon}]
});

const counters = props.stats.map(() => ref(0));

const animate = () => {
  props.stats.forEach((s, idx) => {
    const step = Math.ceil(s.value / 60);
    const interval = setInterval(() => {
      if (counters[idx].value < s.value) {
        counters[idx].value += step;
      } else {
        counters[idx].value = s.value;
        clearInterval(interval);
      }
    }, 16);
  });
};

onMounted(animate);
</script>

<template>
  <section class="bg-[#f0f1f5] py-16">
    <div class="mx-auto max-w-6xl grid gap-8 sm:grid-cols-4 px-4 text-center">
      <div v-for="(s, idx) in stats" :key="s.label" class="space-y-2">
        <component :is="s.icon" class="mx-auto h-10 w-10 text-primary-600" />
        <p class="text-3xl font-extrabold text-[#111218]">{{ counters[idx].value }}</p>
        <p class="text-sm font-medium text-[#5f668c] uppercase">{{ s.label }}</p>
      </div>
    </div>
  </section>
</template>

<style scoped></style>
