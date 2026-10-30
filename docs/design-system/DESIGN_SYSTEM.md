# StudentHub Design System Specification

## Brand Identity & Theme: "Indigo Ink & Amber"

The StudentHub portal uses a custom, tokenized academic design system built from CSS Custom Properties (`css/tokens.css` and `css/app.css`).

---

## 1. Color Palette

### Light Mode
- **Primary / Brand Ink**: `#15163A` (Deep Academic Indigo)
- **Primary Accent**: `#2563EB` (Royal Blue)
- **Amber / Action Warning**: `#D97706` (Warm Amber for alerts, deadlines, and badges)
- **Background**: `#F8FAFC` (Slate Tint)
- **Surface**: `#FFFFFF` (Card White)
- **Border**: `#E2E8F0` (Crisp Slate Border)
- **Text Main**: `#0F172A` (High Contrast Slate 900)
- **Text Muted**: `#64748B` (Readable Slate 500)

### Dark Mode (`[data-theme="dark"]` / `.dark`)
- **Background**: `#0F1020` (Dark Indigo Ink)
- **Surface**: `#181A30` (Elevated Card Indigo)
- **Border**: `#282A4A` (Subtle Contrast Indigo)
- **Text Main**: `#F1F5F9` (High Contrast Slate 50)
- **Text Muted**: `#94A3B8` (Readable Slate 400)
- **Primary / Accent**: `#3B82F6` (Vibrant Blue)

---

## 2. Institutional CHARUSAT Logo Integration

To prevent background clashes between dark backgrounds and the university logo (which contains dark blue lettering), the logo is housed inside a dedicated brand capsule:

```css
.charusat-topbar-brand {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 4px 12px;
  background-color: #FFFFFF !important;
  border-radius: var(--radius-pill);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}
```

This guarantees high contrast, zero color clash, and clear visibility on every screen across both light and dark themes.

---

## 3. Typography & Spacing Hierarchy

- **Font Family**: Modern native system stack (`-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif`).
- **Headings**:
  - `h1`: 24px–28px, Bold 700, Sentence Case.
  - `h2`: 16px–18px, Semi-bold 600, Sentence Case.
  - `h3`: 14px–15px, Medium 600.
- **Body & Controls**: 13px–14px, Line-height 1.5.
- **Numbers & Metrics**: Tabular figures (`font-variant-numeric: tabular-nums;`).

---

## 4. UI Components

- **Collapsible Sidebar**: Supports expanded (240px) and collapsed icon rail (68px) with `Ctrl+B` / `⌘B` keyboard toggle and persistent `localStorage` memory.
- **Segmented Control**: Pill-style view switcher for tabs (e.g., Weekly Timetable Matrix vs. Daily Agenda).
- **Stat Cards**: Icon badge, large tabular number, helper subtitle without redundant numbering.
- **Batch Cards**: Distinct colored badges for laboratory practical batches (`A1` Blue, `B1` Teal, `C1` Amber).

