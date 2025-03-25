# Decision Log

This file records architectural and implementation decisions using a list format.
YYYY-03-25 14:10:43 - Log of updates made.

*

[2025-03-25 14:19:40] - Selected Ananke theme for the Hugo blog.
[2025-03-25 14:24:12] - Created an About page based on the resume content.
[2025-03-25 14:33:35] - Added the About page to the navigation menu.
[2025-03-25 14:48:09] - Set up auto-deploy to GitHub Pages.
Rationale: To automatically deploy the site to GitHub Pages whenever changes are pushed to the main branch.
Implementation Details: Created a GitHub Actions workflow file in the .github/workflows directory.
Rationale: To make the About page more prominent.
Implementation Details: Added a new entry to the main menu in the hugo.toml file.
Rationale: To provide information about the author of the blog.
Implementation Details: Created a new content file named about.md and populated it with content derived from the resume file.
Rationale: A popular and simple theme to get started quickly.
Implementation Details: Added the theme as a submodule to the Hugo site and configured it in the hugo.toml file.
## Decision

*

## Rationale

*

## Implementation Details

*