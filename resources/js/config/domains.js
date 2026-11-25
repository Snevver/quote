/**
 * Each app lives on its own subdomain of the same root domain:
 * portfolio.{root}, quote.{root} and gym.{root}.
 * The root is derived from the current host, so this works the same in
 * production (snev.dev) and locally (e.g. portfolio.localhost:8000).
 */
const SUBDOMAINS = ["portfolio", "quote", "gym"];

const rootHost = () => {
    const [first, ...rest] = window.location.hostname.split(".");
    return SUBDOMAINS.includes(first) && rest.length ? rest.join(".") : window.location.hostname;
};

export const siteUrl = (subdomain, path = "/") => {
    const port = window.location.port ? `:${window.location.port}` : "";
    return `${window.location.protocol}//${subdomain}.${rootHost()}${port}${path}`;
};

export const portfolioUrl = (path = "/") => siteUrl("portfolio", path);
