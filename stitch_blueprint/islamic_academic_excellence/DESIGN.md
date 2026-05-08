---
name: Islamic Academic Excellence
colors:
  surface: '#f8f9ff'
  surface-dim: '#cbdbf5'
  surface-bright: '#f8f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eff4ff'
  surface-container: '#e5eeff'
  surface-container-high: '#dce9ff'
  surface-container-highest: '#d3e4fe'
  on-surface: '#0b1c30'
  on-surface-variant: '#3d4a42'
  inverse-surface: '#213145'
  inverse-on-surface: '#eaf1ff'
  outline: '#6d7a72'
  outline-variant: '#bccac0'
  surface-tint: '#006c4a'
  primary: '#006948'
  on-primary: '#ffffff'
  primary-container: '#00855d'
  on-primary-container: '#f5fff7'
  inverse-primary: '#68dba9'
  secondary: '#5d5f5f'
  on-secondary: '#ffffff'
  secondary-container: '#dfe0e0'
  on-secondary-container: '#616363'
  tertiary: '#735c00'
  on-tertiary: '#ffffff'
  tertiary-container: '#cba72f'
  on-tertiary-container: '#4e3d00'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#85f8c4'
  primary-fixed-dim: '#68dba9'
  on-primary-fixed: '#002114'
  on-primary-fixed-variant: '#005137'
  secondary-fixed: '#e2e2e2'
  secondary-fixed-dim: '#c6c6c7'
  on-secondary-fixed: '#1a1c1c'
  on-secondary-fixed-variant: '#454747'
  tertiary-fixed: '#ffe088'
  tertiary-fixed-dim: '#e9c349'
  on-tertiary-fixed: '#241a00'
  on-tertiary-fixed-variant: '#574500'
  background: '#f8f9ff'
  on-background: '#0b1c30'
  surface-variant: '#d3e4fe'
typography:
  headline-xl:
    fontFamily: Inter
    fontSize: 40px
    fontWeight: '700'
    lineHeight: 48px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  label-lg:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.02em
  label-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 8px
  xs: 4px
  sm: 12px
  md: 24px
  lg: 40px
  xl: 64px
  gutter: 24px
  margin: 32px
---

## Brand & Style

The design system is built upon the dual pillars of modern academic rigor and Islamic tradition. It aims to evoke a sense of serenity, discipline, and prestige. The aesthetic is categorized as **Corporate / Modern** with a refined, minimalist touch that prioritizes clarity for both students and educators. 

The visual narrative avoids visual clutter, favoring generous whitespace and subtle geometric patterns inspired by Islamic art (such as faint tessellations in backgrounds). This creates a premium environment that feels less like a utilitarian tool and more like a high-end digital campus.

## Colors

The color strategy for the design system utilizes a deep **Emerald Green** as the primary anchor, symbolizing growth, wisdom, and Islamic heritage. This is contrasted with a crisp **White** and a very light gray background to maintain a high level of readability and "breathability."

**Elegant Gold** is reserved strictly as an accent color for high-value interactions, such as achievement badges, "Completed" statuses, or primary Call-to-Action (CTA) highlights. Neutral tones are pulled from a slate-gray palette to ensure text remains sharp without the harshness of pure black.

## Typography

The design system employs **Inter** across all levels to ensure maximum legibility and a systematic, professional feel. The hierarchy is strictly enforced through weight variations rather than excessive size changes. 

Headlines use a tighter letter-spacing and heavier weights to command authority, while body text maintains a generous line height (1.5x) to prevent cognitive fatigue during long reading sessions or exam taking. Label styles are used for navigation and metadata, often utilizing slightly increased letter spacing for clarity at smaller sizes.

## Layout & Spacing

The design system follows a **12-column fixed grid** for desktop, centering the content to maintain a premium, editorial feel. A fluid grid is adopted for tablet and mobile views to ensure accessibility.

The spacing rhythm is based on an 8px scale. Large vertical margins (lg and xl) are used to separate distinct learning modules, giving the user's eye room to rest. Consistency in the gutter (24px) ensures that dashboard cards and lesson tiles remain perfectly aligned, creating a sense of order and discipline appropriate for an educational institution.

## Elevation & Depth

Depth in the design system is achieved through **Ambient Shadows** and **Tonal Layers**. Instead of harsh black shadows, we use a soft, diffused shadow with a subtle tint of the primary Emerald Green (#059669) at very low opacity (e.g., 4-8%). This makes elements like cards appear to float gently above the light gray background.

Layering is used to define hierarchy:
- **Level 0 (Background):** Light gray (#F8FAFC) surface.
- **Level 1 (Cards/Sidebar):** Pure white surface with a soft shadow.
- **Level 2 (Modals/Pop-overs):** Pure white with a more pronounced, wider shadow to indicate focus and priority.

## Shapes

The design system utilizes a "Rounded" shape language to soften the professional tone and make the LMS feel approachable for students. 

As specified by the `roundedness: 2` setting:
- **Standard components** (buttons, inputs) use a **0.5rem (8px)** radius.
- **Large components** (cards, containers) use a **1rem (16px)** radius.
- **Extra-large components** (modals, hero sections) use a **1.5rem (24px)** radius.

This consistency in curvature creates a cohesive visual rhythm that feels modern and friendly.

## Components

### Buttons
Primary buttons use the Emerald Green background with white text. Secondary buttons use a transparent background with an Emerald border. The "Achievement" button variant uses the Elegant Gold background to signal special actions or rewards.

### Cards
Cards are the primary container for course content. They feature a pure white background, a `rounded-xl` corner radius, and a subtle ambient shadow. A 4px top-border in Emerald Green or Gold can be used to categorize the card's status (e.g., Active vs. Completed).

### Input Fields
Inputs are clean with a light gray border that transitions to Emerald Green on focus. Labels are positioned above the field using the `label-lg` typography style.

### Chips & Badges
Chips are used for course categories (e.g., "Fiqh", "Mathematics"). They utilize a low-opacity Emerald background with dark Emerald text. Achievement badges for students always feature the Gold accent color.

### Progress Bars
Horizontal bars used for lesson completion. The track is a light neutral gray, and the progress fill is a vibrant Emerald Green.

### Navigation
The sidebar uses a clean, white surface with clear icons. The active state is indicated by an Emerald Green vertical pill on the leading edge and a subtle green tint to the menu item background.