# Clothing Shop — Clean Modern UI Redesign

Redesign the full UI using this spec. Keep all existing functionality, routes, and data — this is a visual redesign only.

## Design Direction
Minimal, editorial, boutique feel. Think Aritzia, COS, or Everlane. Calm color palette, generous white space, confident typography. Nothing flashy or "techy" — this should feel like a premium fashion brand, not a SaaS dashboard.

## Typography
- Headings: a refined serif (Playfair Display, Fraunces, or similar) — keep it, it's working
- Body/UI text: a clean sans-serif (Inter, Manrope, or similar)
- Nav links: uppercase, small, letter-spacing 0.05em, no heavy background pills — use a simple underline or color shift for the active state instead of a filled gray box
- Reduce font sizes on nav items slightly, they currently feel a bit large/bold

## Color Palette
- Base: white / off-white background (#FAFAF8 or similar warm white)
- Text: near-black (#1A1A1A), not pure black
- One accent: a single muted tone (e.g. warm terracotta, forest green, or navy) used only for buttons/links/hover states
- Avoid gray-blue gradients entirely — they read dated. Replace gradient category tiles with either:
  - A single flat neutral color per card, or
  - A real product photo per category with a light overlay for text legibility

## Navbar
- Remove the boxed background on the active nav item ("Test User" currently has a gray pill) — use an underline instead
- Add a bit more vertical padding, current navbar feels cramped
- Keep logo left, nav center/right
- Add a subtle bottom border, not a hard line

## Hero Section
- Keep the photography + serif headline approach, it's strong
- Reduce the black CTA button's harsh corners — use a small radius (4-6px) instead of sharp corners for consistency with the rest of the site
- Tighten the line-height on the headline
- Consider left-aligning the whole hero content block vertically centered, with more breathing room around the image edges (right now the photo touches the container edge oddly on the right)

## Category Cards
- Replace the dark gradient-overlay tiles with cleaner cards:
  - Real category photography, consistent aspect ratio (e.g. 3:4)
  - Text label below the image (not overlaid), or a subtle bottom gradient only if overlaid — keep contrast light, not heavy charcoal
  - Consistent corner radius across all cards
  - Hover: slight zoom on image or soft shadow lift
- Fix inconsistent card heights — TestCat and Nike currently look shorter/misaligned versus the row above
- Grid: 4 columns desktop, 2 tablet, 2 mobile, consistent gutter spacing

## Product Cards (New Arrivals)
- Consistent image aspect ratio across all cards
- Product name + price below image, clean and left-aligned
- Subtle border or shadow, not both
- Hover state: soft shadow or image zoom, keep it minimal
- Add consistent card padding — currently the borders look tight against the images

## Buttons
- Primary: solid dark fill, small radius, uppercase small text, generous horizontal padding
- Secondary/outline: 1px border, same radius, transparent background
- Consistent button height across the whole site

## Spacing & Layout
- Increase section spacing (currently sections feel a bit tight against each other)
- Consistent max-width container (e.g. 1280px) with equal side margins
- Consistent vertical rhythm: same spacing above/below each section heading

## Section Headings ("Shop by Category", "New Arrivals")
- Keep serif style
- Add a bit more margin below before the grid starts
- Consider a thin horizontal rule or small accent underline beneath section titles for consistency

## Responsiveness
- Nav collapses to hamburger on mobile
- Hero stacks image below text on mobile, full-width
- Category and product grids reflow to 2 columns on mobile, 1 if needed for readability

## Apply Site-Wide
Apply this system consistently across Home, Products, Cart, Admin, and My Addresses pages — not just the homepage.