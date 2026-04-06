import router from '@/router';
import type { ClassValue } from 'clsx';
import { clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export async function backOrPush(routeName: string) {
  const backUrl = router.options.history.state['back']?.toString();
  const backRoute = backUrl ? router.resolve(backUrl) : null;

  if (backRoute?.name === routeName) router.back();
  else await router.push({ name: routeName });
}

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

export function navigatorLanguage() {
  return navigator.language.split('-')[0];
}

export function currentSchoolYear() {
  const date = new Date();
  return date.getFullYear() - Number(date.getMonth() < 9);
}

export const schoolYearString = (id: number) => `${id}/${id + 1}`;
