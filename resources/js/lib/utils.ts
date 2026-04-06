import { i18n } from '@/config/i18n';
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

export const useDownloadPDFPage = () => {
  const { t } = i18n.global;
  const pdfWindow = window.open('about:blank', '_blank');
  if (!pdfWindow) throw new Error('pdfWindow is null!');

  pdfWindow.document.body.style.backgroundColor = 'white';
  pdfWindow.document.body.innerHTML = `<p style="text-align:center;margin:50px;font-size:30px;font-family:sans-serif;">${t('common.print.loading')}</p>`;

  function displayPDF(blob: Blob) {
    const url = window.URL.createObjectURL(blob);
    pdfWindow!.document.location.href = url;
    setTimeout(() => window.URL.revokeObjectURL(url), 60000);
  }

  function displayError() {
    pdfWindow!.document.querySelector('p')!.textContent = t('common.print.error');
  }

  return { displayPDF, displayError };
};
