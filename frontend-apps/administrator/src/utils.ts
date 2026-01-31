export function getCurrentSchoolYear() {
    const date = new Date();
    return date.getFullYear() + Number(date.getMonth() > 9);
}

export const getSchoolYearString = (number: number) => `${number}/${number + 1}`;
