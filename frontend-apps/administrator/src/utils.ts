export function getCurrentSchoolYear() {
    const date = new Date();
    return date.getFullYear() - Number(date.getMonth() < 9);
}

export const schoolYearString = (id: number) => `${id}/${id + 1}`;
