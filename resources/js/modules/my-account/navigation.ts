import { LucideHistory, LucideHome, LucideIdCard } from 'lucide-vue-next';

export const menuItems = [
  {
    title: 'myAccount.home.title',
    link: { name: 'myAccount.home' },
    icon: LucideHome,
  },
  {
    title: 'myAccount.accountData.title',
    link: { name: 'myAccount.data' },
    icon: LucideIdCard,
  },
  {
    title: 'myAccount.activityHistory.title',
    link: { name: 'myAccount.activity' },
    icon: LucideHistory,
  },
];
