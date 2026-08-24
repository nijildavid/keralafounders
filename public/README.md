# Kerala Founders — Static UI V4 with V3 visual design

This version is intentionally **no database, no MySQL, no PHP required**.

Open `index.html` directly from your desktop to test the website.

Included:
- Current polished Kerala Founders visual direction
- Homepage hero and stats
- Directory search/filter/sort
- Card/List views
- Clickable company profiles
- Verified badges
- Multiple founders
- Founder email privacy
- Branch countries
- Country/city exploration pages
- Google Maps links/embeds
- Add company form
- Local demo submission storage using browser localStorage
- Demo admin page at `admin.html`
- Shared JavaScript dataset
- All pages linked together

### Important
The Add Company form is DEMO ONLY. It stores submissions in your browser using localStorage. It does not email anyone and it does not use MySQL.

When the UI is finished, we can replace the localStorage layer with PHP + MySQL without redesigning the website.

### Testing
1. Unzip this folder.
2. Double-click `index.html`.
3. Browse the site.
4. Submit a company from `add-company.html`.
5. Return to the homepage/directory — the demo submission will appear.
6. Open `admin.html` to see/delete local submissions.

### Reset demo data
Open browser developer tools and run:
`localStorage.removeItem('kf_submissions')`
Then refresh.


## Visual design
This package uses the V3 visual direction: white/stone surfaces, orange accent, restrained rounded cards, simple typography, and the V3-style homepage/form hierarchy. Functionality remains the static/localStorage version.

## V5 feedback implemented
- Logo stays left; public navigation is right-aligned.
- Admin sign-in removed from the public navigation.
- All public CTAs say “Add your company”.
- Removed the “Kerala × EU” hero tag.
- Homepage section renamed to “Recently added / What Keralites are building.”
- Replaced the decorative ecosystem oval with a full-width Europe-style map abstraction and country/company bubbles.
- Removed the duplicate map from the bottom CTA.
- Added more vertical space after the directory before the footer.
- Reduced list-view row height so it reads as a compact list rather than elongated cards.
- Increased native select right padding so dropdown arrows don't sit against the edge.


## Select arrow refinement
All select controls now use a custom arrow with 16px right spacing so the arrow does not sit against the edge.


## V6 footer
The footer now uses the requested full-width dark-teal CTA layout with a centered 'Building something from Europe?' message and a compact four-column navigation row underneath, while preserving the site's V3 visual language.


## V7 fixes
- Footer typography now matches the site's standard sans-serif UI language.
- First founder remains mandatory; additional founder blocks can be removed with a close button.
- Directory City filter now depends on the selected Country and resets appropriately.


## V8 changes
- Country, City and Company Location / Address are grouped into a dedicated 'The location' section.
- Footer now uses the same light V3 UI language as the rest of the site.
- Removed the homepage 'Explore the ecosystems' section.
- Removed the homepage 'Add your company to the map' section above the footer.


## V9 changes
- Renamed company section to 'About your company / startup'.
- Moved Company description into the company section.
- Made location a separate full-width section.
- Replaced Email visibility dropdown with a switch.
- Removed Verified from the public UI and directory filtering.
- Made directory list rows compact and horizontally aligned.


## V10 fixes
- Location section now spans the form width with properly sized Country and City controls.
- Restored sample directory entries and compact horizontal directory presentation.
- Restored all EU country options and dependent city filtering.
- Top navigation keeps the centered site container, with the logo left and public navigation right.


## V11 fixes
- Rebuilt the directory data/filter script cleanly so countries and sample companies render again.
- Restored full EU country list and dependent city filtering.
- Restored sample directory cards/list.
- Navigation now uses a centered max-width container with logo left and public navigation right.


## V12 fixes
- Fixed the directory rendering bug: the JS referenced `cardsView` while the HTML uses `cardView`, causing a runtime error before companies could render.
- Country/city/industry/size/sort controls now respond reliably to change events.
- Navigation now keeps the logo left and all public navigation/actions right within the centered site container.
- Location section is forced to span the form width with equal Country/City columns and a full-width address field.


## V13 fix
- Directory cards and list rows are now clickable links to `company.html?id=...`.
- Company detail page reads the selected company from the URL and renders its name, founders, location, industry, size, description and website.
- Added hover/focus treatment so the directory clearly behaves as a clickable directory.


## V14 fix
- Fixed the underlying `data.js` / `app.js` conflict that was overwriting the company dataset.
- Directory company cards are now actual `<a>` links to `company.html?id=...`.
- Rebuilt the company detail page so it reads the selected company from the URL and displays its details.
- The flow works from local desktop files without requiring a server.


## V15 fix
- Fixed the root cause of the company detail page showing 'Company not found'.
- `data.js` now exposes the dataset as `window.KF`, which is what the detail page reads.
- `app.js` no longer overwrites the company dataset.
- Company cards remain real links to `company.html?id=...`.


V17: Based on stable V15. Preserved sample company data and only applied directory filter UI updates.


V18: Fixed directory empty state. Root cause was removed Recently added dropdown leaving JS trying to read null sort element, stopping render().


V19 changes:
- Explore places simplified to country-level discovery.
- Homepage now shows companies from the shared company dataset.
- Removed dependency on separate homepage sample data.
