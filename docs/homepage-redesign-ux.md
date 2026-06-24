# Homepage Redesign — UX Reasoning

## Problem Statement

The current homepage treats Tamboli Samaj Portal like a marketing landing page: hero banner with a giant icon, fake statistics that say "—" (empty), generic Bootstrap sections, and a "how to apply" card that belongs on a help page. Users visiting this portal have specific tasks—login, register, apply, track—and the current design buries those under decorative content.

## Design Philosophy

This is a **utility portal**, not a marketing website.

Users come here repeatedly to perform tasks, not to browse. The design must prioritize:

1. **Task completion** — Primary actions visible without scrolling
2. **Quick navigation** — Every major feature accessible in 1 click
3. **Information clarity** — Announcements and dates readable at a glance
4. **Mobile first** — Most users access via phone

## Reference Portals Studied

- National Scholarship Portal (scholarships.gov.in)
- University ERP dashboards
- MP Online kiosk portal
- Aadhaar self-service portal

Common pattern: compact header, card-based action grid, information below the fold.

## Content Audit & Removal Decisions

| Section | Old | New | Reason |
|---|---|---|---|
| Hero Banner | Giant gradiant with icon, CTA buttons | Compact 2-line header with key CTAs | Users don't need decoration, they need login/register |
| Statistics | 3 fake cards with "—" values | Removed | Fake stats destroy trust |
| About Section | Long paragraph + step card | Moved to a separate "Guidelines" page | Doesn't belong on homepage |
| Quick Actions | Missing | 4 utility cards | Primary task entry points |
| Announcements | 1 placeholder card | Dynamic announcement cards | This is the homepage's real value |
| Important Dates | Missing | Timeline cards | Students need deadlines first |
| Footer | Generic dark footer | Branded compact footer with real contact info | Professional finish |

## Page Component Hierarchy

```
┌─────────────────────────────────────────────────────┐
│ Top Bar                                              │
│ [Logo]  Portal Name          [Login]  [Register]     │
│                                                      │
│ Navigation Bar                                       │
│ Home | Announcements | Scholarship | Pratibha |      │
│ Samman | Contact                                     │
├─────────────────────────────────────────────────────┤
│                                                      │
│ Compact Hero Section                                 │
│ Title: Tamboli Samaj Portal                          │
│ Subtitle: Scholarship & Pratibha Samman Registration │
│                                                      │
│ [Login]  [Register]  [Track Application]             │
│                                                      │
├─────────────────────────────────────────────────────┤
│                                                      │
│ Quick Actions  (4 cards, equal width)                │
│                                                      │
│ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐ │
│ │🎓        │ │🏆        │ │🔍        │ │📄        │ │
│ │Scholarship│ │Pratibha  │ │Track     │ │Guidelines│ │
│ │Apply Now │ │Samman    │ │Status    │ │          │ │
│ │          │ │Register  │ │          │ │Read More │ │
│ └──────────┘ └──────────┘ └──────────┘ └──────────┘ │
│                                                      │
├─────────────────────────────────────────────────────┤
│                                                      │
│ Announcements  (Section)                             │
│                                                      │
│ Title       │ Title       │                          │
│ Jan 15, 2026│ Jan 10, 2026│                          │
│ Brief text  │ Brief text  │                          │
│ [Read More] │ [Read More] │                          │
│                                                      │
├─────────────────────────────────────────────────────┤
│                                                      │
│ Important Dates  (Section)                           │
│                                                      │
│ Jan 15       │ Feb 28       │ Mar 15       │ Apr 30 │
│ Applications │ Last Date    │ Verification │ Awards │
│ Open         │              │              │        │
│                                                      │
├─────────────────────────────────────────────────────┤
│                                                      │
│ Footer                                               │
│ [Logo] Description │ Links        │ Contact Info     │
│                    │              │                  │
│                    │ © Tamboli Samaj 2026            │
└─────────────────────────────────────────────────────┘
```

## Layout Specifications

### Breakpoints

| Breakpoint | Width | Grid Columns | Card Layout |
|---|---|---|---|
| Mobile | < 768px | 1 column | Stacked |
| Tablet | 768–991px | 2 columns | 2×2 grid |
| Desktop | ≥ 992px | 3 columns | 4 cards row |

### Container

Fixed-width container at 1140px max. No full-width sections—everything inside `.container` for consistent alignment.

### Spacing System

| Token | Value | Bootstrap Class |
|---|---|---|
| Section padding top/bottom | 3rem | `py-4` |
| Card gap | 1.5rem | `g-4` |
| Card inner padding | 1.5rem | `p-3` or `p-4` |
| Content padding mobile | 0.75rem | `px-3` |
| Section title margin bottom | 2rem | `mb-4` |

### Color Usage

| Element | Token | Class / Usage |
|---|---|---|
| Top bar background | `#0F6B3C` | `bg-primary` custom |
| Top bar text | `#FFFFFF` | `text-white` |
| Nav links | `#0F6B3C` or `#FFFFFF` | Text color |
| Hero background | `#0A522D` | Darker green for contrast |
| Hero text | `#FFFFFF` | White |
| Quick action card border | Bottom border `#D4AF37` | Gold accent |
| Card hover shadow | `rgba(15, 107, 60, 0.15)` | Green-tinted shadow |
| Section titles | `#0F6B3C` | Primary green |
| Body background | `#F8F9FA` | Bootstrap bg-light |
| Card background | `#FFFFFF` | White |
| Footer background | `#0A522D` | Dark green |
| Footer text | `rgba(255,255,255,0.85)` | White with opacity |
| Gold accent | `#D4AF37` | Awards, highlights, borders |

### Card Design Specifications

**Quick Action Card:**
```
┌─────────────────────┐
│          🎓         │ ← 40px icon, centered
│                     │
│  Scholarship        │ ← fw-semibold, 1.1rem
│  Application        │
│                     │
│  Apply Now →        │ ← small link, gold accent
└─────────────────────┘
- Border: none
- Border-radius: 12px
- Box shadow: 0 2px 8px rgba(0,0,0,0.08)
- Top border accent: 3px solid #D4AF37
- Hover: translateY(-4px), shadow intensifies
- Background: white
```

**Announcement Card:**
```
┌────────────────────────────────┐
│ 📢 Scholarship Open for 2026   │ ← Title, 1rem, bold
│                                │
│ Jan 15, 2026                   │ ← Date, 0.85rem, muted
│                                │
│ Short preview text...          │ ← 0.9rem
│                                │
│ Read More →                    │ ← link
└────────────────────────────────┘
- Stacked on mobile, 2 per row on desktop
- Hashtag or category chip on top-right
```

**Important Date Card:**
```
┌──────────────┐
│              │
│  Jan 15      │ ← Large date number
│              │
│ Applications │ ← Label below
│ Open         │
│              │
└──────────────┘
- Compact, minimal
- Gold gold accent on date
- 4 per row on desktop, 2 on tablet, 2 on mobile
```

## Mobile-First Behavior

### Navigation
Desktop: Full horizontal nav bar
Mobile: Hamburger collapse (Bootstrap default navbar behavior)

### Quick Actions
Desktop: 4 cards in a single row at lg breakpoint
Tablet: 2×2 grid at md breakpoint
Mobile: 1 column stacked at sm

### Announcements
Desktop: 2 or 3 cards per row
Mobile: 1 card per row

### Important Dates
Desktop: 4 date cards in a row
Tablet: 2×2 grid
Mobile: 2 per row (wrap to next row)

## Accessibility Considerations

- Color contrast ratios meet WCAG AA
- Gold accent is decorative only—never information-critical
- All buttons have visible focus states
- Cards use semantic link targets (not JavaScript-only)
- Font size minimum 0.875rem (14px) for body text
- Touch targets minimum 44×44px for mobile

## Performance Considerations

- Google Fonts Inter + Noto Sans Devanagari (subset loaded, swap display)
- Bootstrap 5 CSS (minified)
- No jQuery
- No custom icon fonts beyond Bootstrap Icons
- No external tracking scripts
- CSS inline for above-the-fold if critical (not needed with Bootstrap)
