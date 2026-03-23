import {
    LucideBuilding,
    LucideCalendarCog,
    LucideGrid2X2,
    LucideLogIn,
    LucideShapes,
    LucideUsers,
} from "lucide-vue-next";

export const menuItems = [
    {
        title: "schoolStructure",
        link: { name: "schoolStructure" },
        icon: LucideBuilding,
    },
    {
        title: "employees",
        link: { name: "employees" },
        icon: LucideUsers,
    },
    {
        title: "systemAccess",
        link: { name: "systemAccess" },
        icon: LucideLogIn,
    },
    {
        title: "classes",
        link: { name: "classes" },
        icon: LucideGrid2X2,
    },
    {
        title: "classificationPeriods",
        link: { name: "classificationPeriods" },
        icon: LucideCalendarCog,
    },
    {
        title: "subjects",
        link: { name: "subjects" },
        icon: LucideShapes,
    },
];
