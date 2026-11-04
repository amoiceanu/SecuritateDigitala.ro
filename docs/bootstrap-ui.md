# Bootstrap UI

The browser UI uses Bootstrap **5.3.8** (pinned in package.json and package-lock.json). Tailwind CSS and Alpine.js are no longer runtime or build dependencies.

Product branding and domain layouts remain in the existing SCSS/CSS. The SCSS entrypoint imports Bootstrap first, then project styles and reusable Bootstrap overrides. `_bootstrap-brand.scss` preserves bespoke layout, spacing and colors as ordinary CSS; `_bootstrap-components.scss` contains shared adjustments. Use Bootstrap components/utilities for new controls.

Install with `npm ci` and compile production assets with `npm run build`. Use a Node version supported by the project Vite version (Node 24 was used for migration verification). Laravel pagination uses Bootstrap 5 templates.

Migration snapshots and verification logs are stored under `E:/PHP/_audit/work/bootstrap-portfolio-before` and `E:/PHP/_audit/work/bootstrap-portfolio-tools`.
