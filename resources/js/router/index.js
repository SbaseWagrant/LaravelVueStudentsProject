import { createRouter, createWebHistory } from 'vue-router';
import StudentSession from '../Components/StudentSession.vue';

const routes = [
  {
    path: '/public/student-sessions',
    name: 'StudentSession',
    component: StudentSession,
  },
  // Other routes...
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
