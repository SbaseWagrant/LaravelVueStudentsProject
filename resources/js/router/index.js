import { createRouter, createWebHistory } from 'vue-router';
import StudentSession from '../Components/StudentSession.vue';

const routes = [
  {
    path: '/public/student-sessions-view',
    name: 'StudentSession',
    component: StudentSession,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
