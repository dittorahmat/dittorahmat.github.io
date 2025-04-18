[2025-03-28 17:50:00] - Updated baseURL in hugo.toml from example.org to dittorahmat.github.io to point the XML generator to the correct website.
# Decision Log

This file records architectural and implementation decisions using a list format.
YYYY-03-25 14:10:43 - Log of updates made.

*

[2025-03-25 14:19:40] - Selected Ananke theme for the Hugo blog.
[2025-03-25 14:24:12] - Created an About page based on the resume content.
[2025-03-25 14:33:35] - Added the About page to the navigation menu.
[2025-03-25 14:48:09] - Set up auto-deploy to GitHub Pages.
[2025-03-25 14:56:22] - Added Google Search Console verification tag.
[2025-03-25 15:16:53] - Formatted the front matter for content/import-large-tsv-file-to-mysql.md.
[2025-03-25 15:19:29] - Added, committed, and pushed the changes.
Rationale: To save the changes to the repository.
Implementation Details: Used git add, git commit, and git push commands.
[2025-04-17 11:50:20] - Built Hugo site and pushed changes to GitHub.
Rationale: To ensure the file is properly formatted for Hugo.
Implementation Details: Added the title, date, and draft properties to the front matter.
Rationale: To update the live site with the latest content and build artifacts.
Rationale: To verify the site on Google Search Console.
Implementation Details: Added the HTML tag to the <head> section of the themes/ananke/layouts/_default/baseof.html file.
Implementation Details: Used `hugo`, `git add .`, `git commit -m "Build and update site content"`, and `git push origin main` commands.
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