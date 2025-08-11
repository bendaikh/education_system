<script setup>
const props = defineProps({
  staff: Array, // [{name, role, image}]
});
</script>

<template>
  <section class="py-16">
    <div class="mx-auto max-w-6xl px-4">
      <h2 class="mb-8 text-center text-2xl font-bold text-[#111218]">Our Experienced Staff</h2>
      <div class="flex gap-8 overflow-x-auto pb-4 [-ms-scrollbar-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        <div v-for="member in staff" :key="member.name" class="min-w-[200px] flex-shrink-0 text-center">
          <img :src="member.image" alt="staff" class="mx-auto mb-4 aspect-square w-32 rounded-full object-cover" />
          <p class="text-base font-medium text-[#111218]">{{ member.name }}</p>
          <p class="text-sm text-[#5f668c]">{{ member.role }}</p>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped></style>
