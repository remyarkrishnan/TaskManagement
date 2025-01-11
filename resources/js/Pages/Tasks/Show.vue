<template>
    <div class="task-details">
      <h1>{{ task.title }}</h1>
      <p>{{ task.description }}</p>
  
      <h3>Assigned Users:</h3>
      <ul>
        <li v-for="user in task.users" :key="user.id">
          {{ user.name }} ({{ user.email }})
        </li>
      </ul>
    </div>
  </template>
  
  <script setup>
  defineProps({
    task: Object
  });
  </script>
  
  <style scoped>
  .task-details {
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
  }
  
  h1 {
    font-size: 24px;
    margin-bottom: 10px;
  }
  
  ul {
    list-style-type: none;
    padding: 0;
  }
  
  li {
    margin: 5px 0;
  }
  </style>
  