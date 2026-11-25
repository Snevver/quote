import { portfolioUrl } from "./domains";

/**
 * Centralized menu items configuration
 * Used across the application for navigation
 */
export const getMenuItems = (t) => [
    {
        title: t("menu.about"),
        href: portfolioUrl("/about"),
        icon: "person",
        description: "Learn about me, my interests and career",
        gridSize: "col-span-1 row-span-1",
    },
    {
        title: t("menu.projects"),
        href: portfolioUrl("/projects"),
        icon: "code",
        description: "Explore my portfolio of applications and websites",
        gridSize: "col-span-2 row-span-2",
    },
    {
        title: t("menu.skills"),
        href: portfolioUrl("/skills"),
        icon: "verified",
        description: "View my technical skills and expertise",
        gridSize: "col-span-1 row-span-1",
    },
    {
        title: t("menu.blog"),
        href: portfolioUrl("/blog"),
        icon: "article",
        description: "Read my thoughts, insights and technical explorations",
        gridSize: "col-span-1 row-span-1",
    },
    // {
    //     title: t("menu.drawbook"),
    //     href: "/drawbook",
    //     icon: "brush",
    //     description: "View community drawings and create your own",
    //     gridSize: "col-span-2 row-span-1",
    // },
    {
        title: t("menu.contact"),
        href: portfolioUrl("/contact"),
        icon: "mail",
        description: "Get in touch with me",
        gridSize: "col-span-1 row-span-1",
    },
];
